<?php

namespace App\Exceptions\OtpException;

use App\Exceptions\CustomException;
use App\Constants\ExceptionCodes;

class FailedToGenerateOtpException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('messages.failed_to_generate_otp'), ExceptionCodes::FAILED_TO_GENERATE_OTP, 500);
    }
}
