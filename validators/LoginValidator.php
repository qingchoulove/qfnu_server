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

    protected function isPositiveInteger($value, $rule = '', $data = '', $field = '')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        return $field . '不合法';
    }

    protected function withoutValidator()
    {
        return true;
    }

}