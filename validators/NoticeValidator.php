<?php

namespace validators;

class NoticeValidator extends BaseValidator
{
    public function attributes(): array
    {
        return [
            'new_id' => '通知id',
            'title' => '标题',
            'content' => '内容'
        ];
    }

    public function rules(): array
    {
        return [
            ['new_id', 'required', 'on' => ['update', 'delete']],
            [['title', 'content'], 'required'],
            [['title', 'content'], 'trim'],
            ['title', 'string', 'max' => 50],
            ['content', 'string', 'max' => 500],
            ['page', 'number', 'min' => 1, 'on' => ['list']],
            ['page', 'default', 'value' => 1, 'on' => ['list']]
        ];
    }

    public function scenarios(): array
    {
        return [
            'add' => ['title', 'content'],
            'update' => ['new_id', 'title', 'content'],
            'delete' => ['new_id'],
            'list' => ['page']
        ];
    }
}