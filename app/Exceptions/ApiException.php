<?php

namespace App\Exceptions;

use App\Support\ApiResponse;
use Exception;

class ApiException extends Exception
{
    protected $code = 500;
    protected $message = 'Internal server error!';
    public $data = [];

    public function __construct(mixed $message = null, int $code = 500, array $data = [])
    {
        if ($message) 
        {
            $this->message = $message;
        }

        $this->code = $code;
        $this->data = $data;

        parent::__construct($this->message, $this->code);
    }

    public function render()
    {
        return ApiResponse::error($this->data, $this->message, $this->code);
    }
}
