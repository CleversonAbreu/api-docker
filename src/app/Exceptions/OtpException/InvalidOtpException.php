<?php

namespace App\Exceptions\OtpException;

use App\Exceptions\CustomException;
use App\Constants\ExceptionCodes;

class InvalidOtpException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('messages.otp_invalid'), ExceptionCodes::INVALID_OTP, 400);
    }
}