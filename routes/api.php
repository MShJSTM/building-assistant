<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\ProjectController;
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

Route::post('/users/find', [AuthenticationController::class, 'findUser'])->name('find-user')->middleware(['auth:sanctum']);
//projects routes
Route::resource('projects', ProjectController::class)->except(['edit','create'])->middleware(['auth:sanctum']);
Route::get('projects/{project}/users', [ProjectController::class, 'users'])->name('projects.users.index')->middleware(['auth:sanctum']);
Route::post('projects/{project}/users', [ProjectController::class, 'attachUser'])->name('projects.users.store')->middleware(['auth:sanctum']);
Route::delete('projects/{project}/users', [ProjectController::class, 'detachUser'])->name('projects.users.destroy')->middleware(['auth:sanctum']);