<?php

use App\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/auth/request-otp', [AuthenticationController::class, 'requestOtp'])->name('request-otp');
    Route::post('/auth/verify-otp', [AuthenticationController::class, 'verifyOtp'])->name('verify-otp');
})->prefix('auth');
