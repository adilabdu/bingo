<?php

namespace App\Http\Controllers\Chapa;

use App\Jobs\VerifyWithdrawal;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request): Response|\Symfony\Component\HttpFoundation\Response
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $amount = $request->input('amount');

        // Create a new payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'transaction_id' => uniqid(),
            'amount' => $amount,
            'currency' => 'ETB',
            'status' => 'pending',
            'payment_type' => 'deposit'
        ]);

        $callbackUrl = route('payment.callback', [
            'transactionId' => $payment->transaction_id,
        ]);

        $client = new Client();
        $response = $client->post('https://api.chapa.co/v1/transaction/initialize', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('CHAPA_SECRET_KEY'),
            ],
            'json' => [
                'amount' => $amount,
                'currency' => 'ETB',
                'email' => $user->email,
                'tx_ref' => $payment->transaction_id,
                'callback_url' => $callbackUrl,
                'return_url' => $callbackUrl,
            ],
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        if ($result['status'] === 'success') {
            // Use Inertia::location for external URLs redirection
            return Inertia::location($result['data']['checkout_url']);
        }

        return Inertia::render('Chapa/Deposit', ['error' => 'Unable to initiate payment']);
    }

    public function handleCallback(Request $request, string $transactionId): RedirectResponse
    {
        $client = new Client();
        $response = $client->get('https://api.chapa.co/v1/transaction/verify/' . $transactionId, [
            'headers' => [
                'Authorization' => 'Bearer ' . env('CHAPA_SECRET_KEY'),
            ],
            'verify' => false,
        ]);

        $result = json_decode($response->getBody(), true);

        if ($result['status'] === 'success') {
            $payment = Payment::where('transaction_id', $transactionId)->first();

            if ($payment) {
                try {
                    DB::transaction(function () use ($payment) {

                        $payment->status = 'pending';
                        $payment->save();

                        $user = auth()->user();

                        $player = $user->player;
                        if (!$player) {
                            Log::error("Player not found for user ID: {$user->id}");
                            throw new \Exception("Player not found.");
                        }

                        $amount = $payment->amount;

                        // Explicitly increment balance and save
                        $player->balance += $amount;
                        $player->save();

                        Log::info("After update - Player ID: {$player->id}, Balance: {$player->balance}");

                        // Refresh authentication to update session
                        auth()->login($user, true);
                    });

                    $payment->status = Payment::STATUS_PAID;
                    $payment->save();

                    return redirect()->route('wallet.index');
                } catch (Exception $e) {
                    Log::error("Transaction failed: " . $e->getMessage());
                    $payment->status = Payment::STATUS_FAILED;
                    $payment->save();
                    return redirect()->route('payment.failure')->with('error', 'Failed to process the payment.');

                }
            }
        }

        return redirect()->route('payment.failure')->with('error', 'Payment verification failed.');
    }

    public function initiateWithdrawal(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'recipient_bank' => 'required|string',
            'recipient_account' => 'required|string',
            'recipient_name' => 'required|string',
        ]);

        $user = Auth::user();
        $player = $user->player;

        if ($player->balance < $request->amount) {
            return redirect()->back()->with('error', 'Insufficient balance.');
        }

        Log::info('Initiating withdrawal for user: ', [
            'user_id' => $user->id,
            'amount' => $request->amount,
            'balance' => $player->balance
        ]);

        DB::beginTransaction();

        try {
            $player->balance -= $request->amount;
            $player->save();

            $payment = Payment::create([
                'user_id' => $user->id,
                'transaction_id' => uniqid(),
                'amount' => $request->amount,
                'currency' => 'ETB',
                'status' => 'pending',
                'payment_type' => 'withdrawal'
            ]);

            $response = Http::withToken(env('CHAPA_SECRET_KEY'))
                ->post('https://api.chapa.co/v1/transfers', [
                    'account_name' => $request->recipient_name,
                    'account_number' => $request->recipient_account,
                    'amount' => $request->amount,
                    'currency' => 'ETB',
                    'reference' => $payment->transaction_id,
                    'bank_code' => $request->recipient_bank,
                ]);

            $result = $response->json();
            Log::info('Chapa transfer initiation response: ', ['response' => $result]);

            if (isset($result['status']) && $result['status'] === 'success') {
                DB::commit();

                VerifyWithdrawal::dispatch($payment->transaction_id);

                return redirect()->route('wallet.index')->with('info', 'Withdrawal initiated. Verification in progress.');
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Failed to initiate withdrawal.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Withdrawal failed.');
        }
    }

    public function handleWithdrawalCallback(Request $request, string $transactionId): RedirectResponse
    {
        $maxRetries = 5;
        $retryDelay = 5; // in seconds
        $attempt = 0;

        while ($attempt < $maxRetries) {
            $response = Http::withToken(env('CHAPA_SECRET_KEY'))
                ->get("https://api.chapa.co/v1/transfers/verify/{$transactionId}");

            $result = $response->json();

            if ($response->ok() && $result['status'] === 'success') {
                $payment = Payment::where('transaction_id', $transactionId)->first();

                if ($payment) {
                    try {
                        DB::transaction(function () use ($payment) {
                            $payment->status = Payment::STATUS_PAID;
                            $payment->save();
                            // No need to update balance as it's already deducted
                        });

                        return redirect()->route('wallet.index')->with('success', 'Withdrawal confirmed.');
                    } catch (Exception $e) {
                        Log::error("Withdrawal confirmation failed: " . $e->getMessage());
                        $payment->status = Payment::STATUS_FAILED;
                        $payment->save();
                        return redirect()->route('withdraw.failure')->with('error', 'Failed to confirm the withdrawal.');
                    }
                }
            } else {
                Log::info("Attempt {$attempt}: Withdrawal verification not found, retrying...");
                sleep($retryDelay);
                $attempt++;
            }
        }

        Log::error("Withdrawal verification failed after {$maxRetries} attempts.");
        return redirect()->route('withdraw.failure')->with('error', 'Withdrawal verification failed.');
    }

    public function paymentFailure(): Response
    {
        return Inertia::render('Wallet/Deposit/Fail');
    }

    public function withdrawalFailure(): Response
    {
        return Inertia::render('Wallet/Withdraw/Fail');
    }
}
