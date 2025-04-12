<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    protected $fillable = [
        'phone',
        'code',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function generate($phone){
        // check that phone doesnt exist in the database
        $existingVerification = self::where('phone', $phone)->first();
        if ($existingVerification && $existingVerification->expires_at > now()) {
            // If a verification code already exists, return it
            return $existingVerification;
        }else{
            // If the code has expired or doesn't exist, create a new one
            $existingVerification?->delete(); // Clean up expired code
        }

        $code = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        return self::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => $expiresAt,
        ]);
    }

    public static function validate($phone, $code){
        $verification = self::where('phone', $phone)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->first();

        if ($verification) {
            $verification->delete(); // Clean up code
            return true;
        }

        return false;
    }

    public function send(){
        // Logic to send the verification code via SMS
        // This could be an API call to a third-party service
        try {
            // Simulate sending SMS
            // In a real application, you would use a service like Twilio or Nexmo
            // For example: Twilio::message($this->phone, "Your verification code is: {$this->code}");
        } catch (\Exception $e) {
            // Handle any errors that occur during the sending process
            return false;
        }
        return true;
    }
}
