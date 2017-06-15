<?php
namespace validators;

/**
 * 图书查询验证器
 * Class LibraryValidator
 * @package validators
 */
class LibraryValidator extends BaseValidator
{
    protected $rule = [
        'keyword' => 'require',
        'page' => 'number|egt:1'
    ];

    protected $field = [
        'keyword' => '关键字',
        'page' => '页码'
    ];
}