<?php
namespace common\exceptions;

class ParamNotValidException extends BaseException
{
    protected $message = "参错错误";
    protected $httpCode = 500;
    protected $code = 1001;
}