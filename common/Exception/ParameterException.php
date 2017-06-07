<?php
/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/6/6
 * Time: 6:50
 */

namespace common\exception;


class ParameterException extends BaseException
{
    protected $httpCode = 500;
    protected $message = '参数错误';
    protected $code = 10002;
    protected $detail = [];

    /**
     * ParameterException constructor.
     * @param string $message
     * @param array $detail
     */
    public function __construct(string $message, array $detail = [])
    {
        $this->message = $message;
        $this->detail = $detail;
    }

    /**
     * 获取错误详情
     * @return array
     */
    public function getDetail()
    {
        return $this->detail;
    }
}