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
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class CashierController extends Controller
{
    public function finance()
    {
        // Get cashier transactions
        $transactions = Activity::where('causer_type', 'App\Models\Cashier')
            ->where('causer_id', auth()->user()->cashier->id)
            ->get();

        return Inertia::render('Cashier/Finance', [
            'transactions' => $transactions,
        ]);
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
