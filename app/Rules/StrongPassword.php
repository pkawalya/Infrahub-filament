<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Enforces the password policy defined in config/security.php.
 *
 * Used by: User creation forms, password change, password reset.
 */
class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $config = config('security.password', []);

        $minLength = $config['min_length'] ?? 10;
        if (mb_strlen($value) < $minLength) {
            $fail("Password must be at least {$minLength} characters long.");
            return;
        }

        if (($config['require_uppercase'] ?? true) && !preg_match('/[A-Z]/', $value)) {
            $fail('Password must contain at least one uppercase letter.');
            return;
        }

        if (($config['require_lowercase'] ?? true) && !preg_match('/[a-z]/', $value)) {
            $fail('Password must contain at least one lowercase letter.');
            return;
        }

        if (($config['require_numbers'] ?? true) && !preg_match('/[0-9]/', $value)) {
            $fail('Password must contain at least one number.');
            return;
        }

        if (($config['require_symbols'] ?? true) && !preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('Password must contain at least one special character (e.g., !@#$%^&*).');
            return;
        }
    }
}
