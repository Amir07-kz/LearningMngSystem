<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $isAuthenticated = Auth::check();
        $username = $isAuthenticated ? Auth::user()->name : null;

        return view('home', [
            'is_authenticated' => $isAuthenticated,
            'username' => $username,
        ]);
    }
}