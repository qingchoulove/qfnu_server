<?php

namespace validators;


class ClazzValidator extends BaseValidator
{
    public function attributes(): array
    {
        return [
            'code' => '身份证号',
            'name' => '姓名'
        ];
    }

    public function rules(): array
    {
        return [
            [['code', 'name'], 'required'],
            [['code', 'name'], 'trim'],
            ['code', 'string', 'min' => 18, 'message' => '身份证长度错误'],
        ];
    }
}