<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class VerifyWithdrawal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactionId;
    protected $attempts;

    /**
     * Create a new job instance.
     *
     * @param string $transactionId
     */
    public function __construct(string $transactionId)
    {
        $this->transactionId = $transactionId;
        $this->attempts = 0;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        while ($this->attempts < 5) {
            try {
                $response = Http::withOptions(['verify' => false])
                    ->withToken(env('CHAPA_SECRET_KEY'))
                    ->get("https://api.chapa.co/v1/transfers/verify/{$this->transactionId}");

                $result = $response->json();
                Log::info('Chapa transfer verification response: ', ['response' => $result]);

                if (isset($result['status']) && $result['status'] === 'success') {
                    DB::transaction(function () {
                        $payment = Payment::where('transaction_id', $this->transactionId)->first();

                        if ($payment) {
                            $payment->status = Payment::STATUS_PAID;
                            $payment->save();
                        }
                    });

                    return;
                } else {
                    throw new RequestException($response);
                }
            } catch (RequestException $e) {
                Log::info("Attempt {$this->attempts}: Withdrawal verification not found, retrying...");
                $this->attempts++;
                sleep(5); // Delay before retrying
            }
        }

        // If we reached here, all attempts have failed
        Log::error("Withdrawal verification failed after {$this->attempts} attempts for transaction ID: {$this->transactionId}");
        $payment = Payment::where('transaction_id', $this->transactionId)->first();
        if ($payment) {
            $payment->status = Payment::STATUS_FAILED;
            $payment->save();
        }
    }
}
