<?php
/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/6/6
 * Time: 6:49
 */

namespace common;


class ForbiddenException extends BaseException
{
    public $httpCode = 500;
    public $message = '权限不够';
    public $code = 10001;
}