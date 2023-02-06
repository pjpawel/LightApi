<?php

namespace pjpawel\LightApi\Http\Exception;

use Exception;
use Throwable;

abstract class HttpException extends Exception
{

    protected const CODE = 0;

    public function __construct(string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, static::CODE, $previous);
    }

}