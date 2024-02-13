<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Carbon\Exceptions\UnknownSetterException;
use Illuminate\Http\Request;


class RegisterController
{
    public function index() {
        return view('registration');
    }

    public function register(Request $request) {
        $user = new User();
        $user->name = 
        $username = $request->input('username');
        $email = $request->input('email');
        $password = $request->input('password');

        dd($request);
    }

}