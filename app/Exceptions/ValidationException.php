<?php

namespace App\Exceptions;

use Exception;
use App\Facade\AppUtils;
use App\Enums\StatusCodeEnum;

class ValidationException extends Exception
{
    public $errorBag = [];

    public function __construct(array $errorBag)
    {
        $this->errorBag = $errorBag;
    }

    public function render($request)
    {
        return AppUtils::setResponse(
            $status = StatusCodeEnum::VALIDATION,
            $data = null,
            $message = 'Validation failed',
            $errors = $this->errorBag,
        );
    }
}