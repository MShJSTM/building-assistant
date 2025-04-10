<?php

use App\Models\PhoneVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;


use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('sends a verification code to the phone number', function () {
    $response = postJson('/api/auth/request-otp', [
        'phone' => '09123234567',
    ]);

    $response->assertOk()
             ->assertJson([
                 'message' => __('OTP sent successfully'),
             ]);

    // Check if the code is stored in the database
    $this->assertDatabaseHas('phone_verifications', [
        'phone' => '09123234567',
        'code' => PhoneVerification::first()->code, // assuming you have a way to retrieve the code
    ]);
});

it('logs in the user with a valid verification code', function () {
    User::factory()->create([
        'phone' => '09123234567',
    ]);

    $verification = PhoneVerification::factory()->create([
        'phone' => '09123234567',
        'code' => '123456',
        'expires_at' => now()->addMinutes(5),
    ]);

    $response = postJson('/api/auth/verify-otp', [
        'phone' => '09123234567',
        'code' => $verification->code,
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

it('registers a new user with a valid verification code', function () {
    $verification = PhoneVerification::factory()->create([
        'phone' => '09123234567',
        'code' => '123456',
        'expires_at' => now()->addMinutes(5),
    ]);

    $response = postJson('/api/auth/verify-otp', [
        'phone' => '09123234567',
        'code' => $verification->code,
    ]);

    $response->assertOk()
             ->assertJsonStructure([
                 'token',
                 'user' => ['id', 'name', 'phone'],
             ]);

    $this->assertDatabaseHas('users', [
        'phone' => '09123234567',
    ]);

    $this->assertDatabaseMissing('phone_verifications', [
        'phone' => '09123234567',
        'code' => $verification->code,
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

