<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

/**
 * Google OAuth is DISABLED.
 * All users are invited by company admins — no self-registration via Google.
 */
class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        abort(403, 'Google authentication is disabled. Please log in with your email and password.');
    }

    public function handleGoogleCallback()
    {
        abort(403, 'Google authentication is disabled.');
    }
}

