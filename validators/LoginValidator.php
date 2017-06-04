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
        'user_id' => 'require|isPositiveInteger|length:10',
        'password' => 'require|length:6',
        'captcha' => 'withoutValidator'
    ];

    protected $message = [

        'user_id.require' => '账号必须填写',
        'user_id.isPositiveInteger' => '账号格式有误',
        'user_id.length' => '账号长度不合法',
        'password.require' => '密码必须填写',
        'password.length' => '密码长度不合法',
    ];

    /**
     * 必须是正整数(自带number)
     * @param $value
     * @return bool
     */
    protected function isPositiveInteger($value)
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        return false;
    }


    protected function withoutValidator()
    {
        return true;
    }

}