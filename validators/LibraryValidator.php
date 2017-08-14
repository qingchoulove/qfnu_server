<?php
namespace validators;

/**
 * 图书查询验证器
 * Class LibraryValidator
 * @package validators
 */
class LibraryValidator extends BaseValidator
{
    public function attributes(): array
    {
        return [
            'keyword' => '关键字',
            'page' => '页码'
        ];
    }

    public function rules(): array
    {
        return [
            ['keyword', 'required'],
            ['page', 'number', 'min' => 1],
            ['page', 'default', 'value' => 1]
        ];
    }
}
