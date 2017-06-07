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
    public $httpCode = 500;

    //错误具体信息 根据子类具体改变
    public $message = '发生错误';

    //自定义的错误码
    public $code = 10000;

    public function __construct($parmas = [])
    {
        if (!is_array($parmas)) {
            return;
        }
        if (array_key_exists('message', $parmas)) {
            $this->message = $parmas['message'];
        }
        if (array_key_exists('detail', $parmas) && is_array($parmas['detail'])) {
            $this->detail = $parmas['detail'];
        }
    }

}