<?php

namespace validators;

/**
 * 登录验证器
 * Class LoginValidator
 * @package validators
 */
class LoginValidator extends BaseValidator
{
    public function attributes(): array
    {
        return [
            'user_id' => '学号',
            'password' => '密码',
            'captcha' => '验证码'
        ];
    }

    public function rules(): array
    {
        return [
            [['user_id', 'password'], 'trim'],
            [['user_id', 'password'], 'required', 'message' => '账号密码必须填写'],
            ['user_id', 'number'],
        ];
    }

    public function scenarios(): array
    {
        return [];
    }
}
