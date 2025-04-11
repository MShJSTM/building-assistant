<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth', 'namespace' => 'App\Http\Controllers\Auth',], function () {
        Route::post('/request-otp', [AuthenticationController::class, 'requestOtp'])->name('request-otp');
        Route::post('/verify-otp', [AuthenticationController::class, 'verifyOtp'])->name('verify-otp');
    }
);