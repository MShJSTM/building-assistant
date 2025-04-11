<?php

use App\Models\User;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('sends a verification code to the phone number', function () {
    $response = postJson('/api/auth/request-otp', [
        'phone' => '09123234567',
    ]);

    $response->assertOk()
             ->assertJson([
                 'message' => __('OTP sent successfully'),
             ]);
});

it('logs in the user with a valid verification code', function () {
    // Fake a user and code in your test db
    $user = User::factory()->create([
        'phone' => '09123234567',
        'verification_code' => '123456', // assuming you store the code temporarily
    ]);

    $response = postJson('/api/auth/verify-otp', [
        'phone' => '09123234567',
        'code' => '123456',
    ]);

    $response->assertOk()
             ->assertJsonStructure([
                 'token',
                 'user' => ['id', 'name', 'phone'],
             ]);
});

it('does not log in user with incorrect verification code', function () {
    User::factory()->create([
        'phone' => '1234567890',
        'verification_code' => '123456',
    ]);

    $response = postJson('/api/verify-otp', [
        'phone' => '1234567890',
        'code' => '000000',
    ]);

    $response->assertUnauthorized()
             ->assertJson([
                 'message' => 'Invalid verification code.',
             ]);
});

it('allows access to protected route with valid token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('mobile-login')->plainTextToken;

    $response = getJson('/api/user', [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertOk();
});

