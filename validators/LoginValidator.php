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
        'user_id' => 'require|number|gt:0|length:10',
        'password' => 'require',
    ];

    protected $message = [

        'user_id.require' => '账号必须填写',
        'user_id.number' => '账号格式有误',
        'user_id.gt' => '账号格式有误',
        'user_id.length' => '账号长度不合法',
        'password.require' => '密码必须填写',
    ];

    protected $field = [
        'user_id' => '学号',
        'password' => '密码',
        'captcha' => '验证码'
    ];
}
