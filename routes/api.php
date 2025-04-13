<?php

use App\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Support\Facades\Route;

//authentication routes
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/auth/request-otp', [AuthenticationController::class, 'requestOtp'])->name('request-otp');
    Route::post('/auth/verify-otp', [AuthenticationController::class, 'verifyOtp'])->name('verify-otp');
})->prefix('auth');
//protected authentication routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/auth/user', [AuthenticationController::class, 'user'])->name('user');
    Route::post('/auth/logout', [AuthenticationController::class, 'logout'])->name('logout');
});


//projects routes
Route::resource('projects', \App\Http\Controllers\ProjectController::class)->except(['create', 'edit']);