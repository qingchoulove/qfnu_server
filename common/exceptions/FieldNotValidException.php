<?php
namespace common\exceptions;

use Throwable;

class FieldNotValidException extends BaseException
{
    protected $message = "字段校验失败";
    protected $httpCode = 500;
    protected $errorInfo = [];
    protected $code = 1002;

    public function __construct(string $message = "", array $errorInfo, int $code = 0, Throwable $previous = null)
    {
        $this->errorInfo = $errorInfo;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrorInfo(): array
    {
        return $this->errorInfo;
    }
}
