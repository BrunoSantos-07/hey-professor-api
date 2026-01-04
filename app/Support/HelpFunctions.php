<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

function user(): ?User
{
    if (Auth::check()) {
        return Auth::user();
    }

    return null;
}
