<?php

use App\Models\Cartela;
use Illuminate\Support\Facades\Route;

Route::get('/cartelas/{cartela?}', function (?Cartela $cartela)
{

    if ($cartela->exists) {
        return $cartela->only('numbers', 'id', 'name');
    }

    return Cartela::all()->select('id', 'name');
});
