<?php

use Illuminate\Support\Facades\Route;

it('displays custom 404 page', function () {
    $response = $this->get('/non-existent-route-for-testing-xyz');
    $response->assertStatus(404);
    $response->assertSee('Page Not Found');
});

it('displays custom 403 page', function () {
    Route::get('/test-403', function () {
        abort(403);
    });

    $response = $this->get('/test-403');
    $response->assertStatus(403);
    $response->assertSee('Access Denied');
});

it('displays custom 500 page', function () {
    Route::get('/test-500', function () {
        throw new \RuntimeException("Something broke!");
    });

    $response = $this->get('/test-500');
    $response->assertStatus(500);
    $response->assertSee('Internal Server Error');
});

it('displays custom 419 page', function () {
    Route::get('/test-419', function () {
        abort(419);
    });

    $response = $this->get('/test-419');
    $response->assertStatus(419);
    $response->assertSee('Page Expired');
});

it('displays custom 429 page', function () {
    Route::get('/test-429', function () {
        abort(429);
    });

    $response = $this->get('/test-429');
    $response->assertStatus(429);
    $response->assertSee('Too Many Requests');
});

it('displays custom 503 page', function () {
    Route::get('/test-503', function () {
        abort(503);
    });

    $response = $this->get('/test-503');
    $response->assertStatus(503);
    $response->assertSee('Under Maintenance');
});
