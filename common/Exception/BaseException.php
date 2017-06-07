<?php
/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/6/6
 * Time: 3:59
 */

namespace common\exception;

use Exception;

class BaseException extends Exception
{
    //HTTP 状态码 404,200,400
    protected $httpCode = 500;
    //错误具体信息 根据子类具体改变
    protected $message = '发生错误';
    //自定义的错误码
    protected $code = 10000;
    
    public function getHttpCode()
    {
        return $this->httpCode;
    }
}