<?php
namespace common\exceptions;

class BaseException extends \Exception
{
    protected $code = 500;
    protected $message = "服务器错误,请重试";
    protected $httpCode = 500;

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
