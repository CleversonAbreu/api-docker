<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    protected $errorType;

    public function __construct(string $message, string $errorType = null, int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorType = $errorType;
    }

    public function getErrorType(): ?string
    {
        return $this->errorType;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'error_type' => $this->getErrorType(),
        ];
    }
}
