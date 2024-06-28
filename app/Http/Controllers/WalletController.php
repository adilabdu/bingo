<?php
//
//namespace App\Http\Controllers;
//
//use App\Models\Transaction;
//use App\Models\User;
//use Illuminate\Http\RedirectResponse;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;
//use Inertia\Inertia;
//use Inertia\Response;
//
//class WalletController extends Controller
//{
//
//    public function index(): Response
//    {
//        $user = auth()->user();
//        $player = $user->load('player')->player;
//
//        return Inertia::render('Wallet/Index', [
//            'balance' => $player->balance,
//            'transactions' => Transaction::with('to:id,phone_number', 'from:id,phone_number')
//                ->where('from', $user->id)
//                ->orWhere('to', $user->id)
//                ->orderBy('created_at', 'desc')
//                ->paginate(5)
//                ->withQueryString(),
//        ]);
//    }
//
//    public function transfer(Request $request): RedirectResponse
//    {
//        $request->validate([
//            'amount' => 'required|integer|min:1',
//            'recipient' => 'required|exists:users,phone_number',
//        ]);
//
//        $user = auth()->user();
//        $player = $user->load('player')->player;
//
//        if ($player->balance < $request->amount) {
//            return redirect()->back()->with('error', 'Insufficient funds.');
//        }
//
//        DB::beginTransaction();
//
//        $recipient = User::where('phone_number', $request->string('recipient'))->sole();
//        $recipientPlayer = $recipient->load('player')->player;
//
//        if ($recipientPlayer->id === $player->id) {
//            DB::rollBack();
//            return redirect()->back()->with('error', 'Cannot sent funds to yourself.');
//        }
//
//        $recipientPlayer->balance += $request->integer('amount');
//        $player->balance -= $request->integer('amount');
//
//        $recipientPlayer->save();
//        $player->save();
//
//        Transaction::create([
//            'type' => Transaction::TRANSFER,
//            'amount' => $request->integer('amount'),
//            'from' => $user->id,
//            'to' => $recipient->id,
//        ]);
//
//        DB::commit();
//
//        return redirect()->back()->with('success', 'Transfer successful.');
//    }
//}


// updated one

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class WalletController extends Controller
{
//    public function index(): Response
//    {
//        $user = auth()->user();
//        $player = $user->load('player')->player;
//        $banks = $this->fetchBankCodes();
//
//        return Inertia::render('Wallet/Index', [
//            'balance' => $player->balance,
//            'transactions' => Transaction::with('to:id,phone_number', 'from:id,phone_number')
//                ->where('from', $user->id)
//                ->orWhere('to', $user->id)
//                ->orderBy('created_at', 'desc')
//                ->paginate(5)
//                ->withQueryString(),
//            'banks' => $banks,
//            'flash' => session('flash', []), // Pass flash messages
//        ]);
//    }

    public function index(): Response
    {
        $user = auth()->user();
        $player = $user->load('player')->player;

        $client = new Client();
        $response = $client->get('https://api.chapa.co/v1/banks', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('CHAPA_SECRET_KEY'),
            ],
            'verify' => false,
        ]);

        $result = json_decode($response->getBody(), true);
        $banks = $result['data'] ?? [];

        return Inertia::render('Wallet/Index', [
            'balance' => $player->balance,
            'transactions' => Transaction::with('to:id,phone_number', 'from:id,phone_number')
                ->where('from', $user->id)
                ->orWhere('to', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(5)
                ->withQueryString(),
            'banks' => $banks,
        ]);
    }


    private function fetchBankCodes()
    {
        try {
            $client = new Client();
            $response = $client->get('https://api.chapa.co/v1/banks', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('CHAPA_SECRET_KEY'),
                ],
                'verify' => false,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['data'])) {
                return $result['data'];
            } else {
                return [];
            }
        } catch (Exception $e) {
            return [];
        }
    }

    public function transfer(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'recipient' => 'required|exists:users,phone_number',
        ]);

        $user = auth()->user();
        $player = $user->load('player')->player;

        if ($player->balance < $request->amount) {
            return redirect()->back()->with('error', 'Insufficient funds.');
        }

        DB::beginTransaction();

        $recipient = User::where('phone_number', $request->string('recipient'))->sole();
        $recipientPlayer = $recipient->load('player')->player;

        if ($recipientPlayer->id === $player->id) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Cannot send funds to yourself.');
        }

        $recipientPlayer->balance += $request->integer('amount');
        $player->balance -= $request->integer('amount');

        $recipientPlayer->save();
        $player->save();

        Transaction::create([
            'type' => Transaction::TRANSFER,
            'amount' => $request->integer('amount'),
            'from' => $user->id,
            'to' => $recipient->id,
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Transfer successful.');
    }
}
