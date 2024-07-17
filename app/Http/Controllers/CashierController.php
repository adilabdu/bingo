<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Cashier;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CashierController extends Controller
{
    public function index()
    {
        return Inertia::render('Cashier/Index');
    }

    public function finance()
    {
        return Inertia::render('Cashier/Finance');
    }

    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => bcrypt($request->password),
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
            ->log('added a cashier to a branch');

        return redirect()->route('cashier.store')->with('success', 'Cashier created successfully.');

    }
}
