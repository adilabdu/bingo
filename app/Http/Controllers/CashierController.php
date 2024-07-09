<?php

namespace App\Http\Controllers;

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
}
