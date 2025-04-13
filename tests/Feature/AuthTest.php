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

    $verification = PhoneVerification::create([
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
    PhoneVerification::create([
        'phone' => '09123456789',
        'code' => '123456',
        'expires_at' => now()->addMinutes(5),
    ]);

    $response = postJson('/api/auth/verify-otp', [
        'phone' => '09123456789',
        'code' => '000000',
    ]);

    $response->assertUnauthorized()
             ->assertJson([
                 'message' => 'Invalid or expired verification code.',
             ]);
});

it('registers a new user with a valid verification code', function () {
    $verification = PhoneVerification::create([
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
                 'user' => ['id', 'phone'],
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
    $token = $user->createToken('auth-token')->plainTextToken;
    
    $response = getJson('/api/auth/user', [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertOk()
             ->assertJson([
                 'id' => $user->id,
                 'name' => $user->name,
                 'phone' => $user->phone,
             ]);
});

it('allows logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token');
    
    $response = postJson('/api/auth/logout', [], [
        'Authorization' => 'Bearer ' . $token->plainTextToken,
    ]);

    $response->assertOk()
             ->assertJson([
                 'message' => __('Logged out successfully'),
             ]);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'name' => 'auth-token',
        'token' => hash('sha256', $token->plainTextToken), 
    ]);
});
