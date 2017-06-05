<?php
/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/6/3
 * Time: 10:36
 */

namespace validators;


class LoginValidator extends BaseValidator
{
    protected $rule = [
        'user_id' => 'require|PositiveInteger|length:10',
        'password' => 'require|length:6',
    ];

    protected $message = [

        'user_id.require' => '账号必须填写',
        'user_id.PositiveInteger' => '账号格式有误',
        'user_id.length' => '账号长度不合法',
        'password.require' => '密码必须填写',
        'password.length' => '密码长度不合法',
    ];


    protected $filter = [
        'user_id',
        'password',
        'captcha',
    ];


}