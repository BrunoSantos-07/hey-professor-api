<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $data = request()->validate([
            'name'     => ['required', 'string', 'min:3', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'confirmed'],
            'password' => ['required', 'string', 'min:8', 'max:40'],
        ]);

        $user = User::create($data);

        return response()->json([
            'user'  => $user,
            'token' => $user->createToken('auth-token')->plainTextToken,
        ], 201);
    }
}
