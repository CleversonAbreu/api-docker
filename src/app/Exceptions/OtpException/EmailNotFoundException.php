<?php

namespace App\Exceptions\OtpException;

use App\Exceptions\CustomException;
use App\Constants\ExceptionCodes;

class EmailNotFoundException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('messages.email_not_found'), ExceptionCodes::EMAIL_NOT_FOUND, 404);
    }
}
