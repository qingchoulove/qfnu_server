<?php
namespace common\exceptions;

class NotFoundException extends BaseException
{
    protected $message = "接口不存在";
    protected $httpCode = 404;
    protected $code = 404;
}