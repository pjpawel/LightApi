<?php

namespace pjpawel\LightApi\Http\Exception;

class NotFoundHttpException extends HttpException
{
    protected const CODE = 404;
}