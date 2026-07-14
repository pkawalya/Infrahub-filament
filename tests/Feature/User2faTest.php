<?php

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('bypasses 2fa if bypass_2fa is set to true', function () {
    // Enable 2FA globally
    Setting::setValue('enforce_2fa', '1', 'security');

    $user = User::factory()->create([
        'bypass_2fa' => true,
    ]);

    expect($user->hasEmailAuthentication())->toBeFalse();
});

it('enforces 2fa if bypass_2fa is set to false and globally enabled', function () {
    // Enable 2FA globally
    Setting::setValue('enforce_2fa', '1', 'security');

    $user = User::factory()->create([
        'bypass_2fa' => false,
    ]);

    expect($user->hasEmailAuthentication())->toBeTrue();
});
