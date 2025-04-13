<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PhoneVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthenticationController extends Controller
{
    public function requestOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|ir_mobile:zero',
        ]);

        $verification = PhoneVerification::generate($request->phone);

        $verification->send(); // Send the OTP

        return response()->json(['message' => __('OTP sent successfully')]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|ir_mobile:zero',
            'code' => 'required|digits:6',
        ]);

        if (!PhoneVerification::validate($request->phone, $request->code)) {
            return response()->json(['message' => __('Invalid or expired verification code.')], 401);
        }

        // This automatically handles both cases correctly
        $user = User::firstOrCreate(['phone' => $request->phone]);

        // Mark phone as verified (important for new registrations)
        if (!$user->phone_verified_at) {
            $user->update(['phone_verified_at' => now()]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => __('Logged in successfully'),
            'user' => $user
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json(['message' => __('Logged out successfully')]);
    }
}
