<?php

namespace App\Repositories;

use App\Models\Otp;

class OtpRepository
{
    protected $otpModel;

    public function __construct(Otp $otpModel)
    {
        $this->otpModel = $otpModel;
    }

    public function updateOrCreateOtp(string $phoneOrEmail, string $otpCode, $expiresAt)
    {
        return $this->otpModel->updateOrCreate(
            ['phone_or_email' => $phoneOrEmail],
            ['otp_code' => $otpCode, 'expires_at' => $expiresAt]
        );
    }

    public function findValidOtp(string $phoneOrEmail, string $otpCode)
    {
        return $this->otpModel->where('phone_or_email', $phoneOrEmail)
            ->where('otp_code', $otpCode)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function deleteOtp(Otp $otp)
    {
        $otp->delete();
    }
}
