<?php
/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/5/24
 * Time: 13:09
 */

namespace validate;


class IDMustBePositiveInt extends BaseValidate
{
    //验证规则
    protected $rule = [
        'id' => 'require|max:8',//这里还可以是自定义的验证规则
        //'email' => 'email'
    ];
    //验证失败，提示信息  做好结合异常
//    protected $message = [
//        'id' => 'id必须是正整数'
//    ];

}

