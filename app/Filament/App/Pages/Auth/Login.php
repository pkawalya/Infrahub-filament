<?php

namespace App\Filament\App\Pages\Auth;

use App\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    /**
     * Override authenticate to block inactive users with a clear message.
     */
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        // Check if user exists and is inactive BEFORE attempting authentication
        $user = User::where('email', $data['email'])->first();

        if ($user && !$user->is_active) {
            throw ValidationException::withMessages([
                'data.email' => 'Your account has been deactivated. Please contact your administrator to restore access.',
            ]);
        }

        return parent::authenticate();
    }
}
