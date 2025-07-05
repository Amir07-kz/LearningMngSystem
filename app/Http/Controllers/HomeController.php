<?php

namespace App\Http\Controllers;

use GeminiAPI\Client;
use Illuminate\Support\Facades\Log;
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