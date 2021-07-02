<?php

namespace App\Exceptions;

use App\Enums\StatusCodeEnum;
use App\Facade\AppUtils;
use Exception;

class CustomException extends Exception
{
    protected $message;
    public $status, $data;

    public function __construct(int $status, $data = null, string $message = null)
    {
        $this->message = $message;
        $this->status_code = $status;
        $this->data = $data;
    }

    public function render()
    {
        return AppUtils::setResponse($this->status_code, null, $this->message);
    }
}
