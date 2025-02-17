<?php

namespace App\Exceptions\OtpException;

use App\Exceptions\CustomException;
use App\Constants\ExceptionCodes;

class EmailAlreadyExistsException extends CustomException
{
    public function __construct()
    {
        parent::__construct(__('messages.email_already_exists'), ExceptionCodes::EMAIL_ALREADY_EXISTS, 409);
    }
}
