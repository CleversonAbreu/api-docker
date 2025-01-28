<?php

namespace App\Exceptions\OtpException;

use App\Exceptions\CustomException;
use App\Constants\ExceptionCodes;

class FailedToSendOtpException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('messages.failed_to_send_otp'), ExceptionCodes::FAILED_TO_SEND_OTP, 503);
    }
}