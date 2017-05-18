<?php

namespace validators;

use common\Util;

/**
* 测试表单
*/
class TestValidator extends BaseValidator
{

    public function attributes():array
    {
        return [
            'username' => '用户名',
            'password' => '密码'
        ];
    }

    public function rules():array
    {
        return [
            [['username', 'password'], 'require'],
            ['password', function($attribute) {
                $data = $this->getData();
                if ($data[$attribute] == '123456') {
                    $this->addError($attribute, '密码不能是123456');
                }
            }]
        ];
    }
}