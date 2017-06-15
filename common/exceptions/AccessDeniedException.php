<?php
namespace common\exceptions;

class AccessDeniedException extends BaseException
{
    protected $message = "无权访问";
    protected $httpCode = 401;
    protected $code = 401;
}