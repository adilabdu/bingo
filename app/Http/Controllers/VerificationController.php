<?php

namespace App\Http\Controllers;

use App\Helpers\SMS;
use App\Models\OTP;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class VerificationController extends Controller
{

    /**
     * @throws ConnectionException
     */
    public function phone(): Response
    {
        // 1. Generate OTP
        // 2. Store OTP to database
        // 3. Send OTP to user's phone number

        Log::info('Generating OTP');

        $OTP = mt_rand(100000, 999999);

        $user = auth()->user();
        $userOtp = $user->load('otp')->otp;

        if ($userOtp) {

            $isOtpExpired = $userOtp->updated_at->diffInMinutes(now()) > 2;

            if ($isOtpExpired) {
                $userOtp->update([
                    'otp' => $OTP
                ]);
            }
        } else {
            OTP::create([
                'user_id' => auth()->id(),
                'otp' => $OTP
            ]);

            $userOtp = $user->load('otp')->otp;
        }

        SMS::send($user->phone_number, "Your OTP is: $userOtp->otp");

        return Inertia::render('Verify/PhoneNumber', [
            'expires_in' => 120 - $userOtp->updated_at->diffInSeconds(now())
        ]);
    }

    public function sendOTP(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6'
        ]);

        $user = auth()->user();
        $userOtp = $user->load('otp')->otp;

        if ($userOtp->updated_at->diffInMinutes(now()) > 2) {
            return redirect()->back()->with('error', 'OTP expired');
        }

        if ($userOtp->otp != $request->integer('otp')) {
            return redirect()->back()->with('error', 'Invalid OTP');
        }

        $user->phone_verified_at = now();
        $user->save();

        return redirect()->route('game.initiate')->with('success', 'Phone number verified');
    }
}
