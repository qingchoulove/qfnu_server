<?php
/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/6/6
 * Time: 3:59
 */

namespace common;


class BaseException extends \Exception
{
//HTTP 状态码 404,200
    public $code = 400;

    //错误具体信息 根据子类具体改变
    public $msg = '发生错误';

    //自定义的错误码
    public $errorCode = 10000;

    public function __construct($parmas = [])
    {
        if (!is_array($parmas)) {
            return;
            //或者抛出异常__construct
        }
        if (array_key_exists('code', $parmas)) {
            $this->code = $parmas['code'];
        }
        if (array_key_exists('msg', $parmas)) {
            $this->msg = $parmas['msg'];
        }
        if (array_key_exists('errorCode', $parmas)) {
            $this->errorCode = $parmas['errorCode'];
        }
    }
}