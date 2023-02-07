<?php

namespace pjpawel\LightApi\Http\Exception;

class MethodNotAllowedHttpException extends HttpException
{
    protected const CODE = 405;
}