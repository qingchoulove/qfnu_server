<?php
/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/5/27
 * Time: 4:45
 */

namespace validators;


class MyValidator extends BaseValidator
{
    protected $rule = [
        'name' => 'require|max:8',
        'email'=>'email'
    ];

    protected $message = [
        'name' => 'name必须小于8位',
        'email'=>'email不合法'
    ];
}