<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function requestOtp(Request $request)
    {
        // Logic to send OTP to the user
        return response()->json(['message' => __('OTP sent successfully')]);
    }

    public function verifyOtp(Request $request)
    {
        
    }
}
