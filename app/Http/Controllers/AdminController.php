<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard/Index');
    }

    public function users(): Response
    {
        return Inertia::render('Admin/Users/Index');
    }
}
