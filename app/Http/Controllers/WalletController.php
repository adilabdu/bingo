<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class WalletController extends Controller
{

    public function index(): Response
    {
        $user = auth()->user();
        $player = $user->load('player')->player;

        return Inertia::render('Wallet/Index', [
            'balance' => $player->balance,
        ]);
    }

    public function transfer(Request $request)
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
            return redirect()->back()->with('error', 'Cannot sent funds to yourself');
        }

        $recipientPlayer->balance += $request->integer('amount');
        $player->balance -= $request->integer('amount');

        $recipientPlayer->save();
        $player->save();

        DB::commit();

        return redirect()->back()->with('success', 'Transfer successful.');
    }
}
