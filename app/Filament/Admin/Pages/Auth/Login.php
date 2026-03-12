<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Filament\Concerns\SecureLogin;
use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    use SecureLogin;
}
