<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PhoneVerification;
use App\Models\User;
use Illuminate\Http\Request;
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
            return response()->json([
                'message' => __('Invalid or expired verification code'),
            ], 422);
        }

        $user = User::firstOrCreate(['phone' => $request->phone]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
}
