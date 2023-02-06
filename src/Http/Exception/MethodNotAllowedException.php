<?php

namespace pjpawel\LightApi\Http\Exception;

class MethodNotAllowedException extends HttpException
{
    protected const CODE = 405;
}