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
    public $httpCode = 500;
    public $message = '参数错误';
    public $code = 10002;
}