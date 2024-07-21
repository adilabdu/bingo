<?php

namespace App\Http\Controllers;

use App\Events\AddCashierPlayerEvent;
use App\Models\Cartela;
use App\Models\Cashier;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GamePlayer;
use App\Services\DrawGameService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class CashierController extends Controller
{
    public function finance()
    {
        return Inertia::render('Cashier/Finance');
    }

    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make('password'),
            'type' => 'cashier',
        ]);

        $cashier = Cashier::create([
            'user_id' => $user->id,
            'branch_id' => $request->branch_id,
            'balance' => 0,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($cashier)
            ->log('Cashier created successfully.');

        return redirect()->route('cashier.store')->with('success', 'Cashier created successfully.');
    }
}
