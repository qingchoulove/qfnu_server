<?php

namespace validators;

use common\Component;
use common\Util;
use Exception;

/**
* model基类
*/
class BaseValidator extends Component
{
    // 待验证数据
    private $data;
    // 错误信息
    private $errors = [];
    // 支持的验证器
    const VALIDATOR = ['require', 'length', 'number', 'in', 'filter'];

    function __construct($data = [])
    {
        foreach ($this->attributes() as $key => $value) {
            $this->data[$key] = isset($data[$key]) ? $data[$key] : null;
        }
    }

    public function attributes():array
    {
        return [];
    }

    public function rules():array
    {
        return [];
    }

    public function validate():bool
    {
        $attributes = $this->attributes();
        $data = $this->data;
        foreach ($this->rules() as $key => $value) {
            $this->validateValue($value);
        }
        // 如果errors为空直接返回true
        return empty($this->errors);
    }

    public function getData()
    {
        return $this->data;
    }

    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute] = $message;
    }

    public function getErrors():array
    {
        return $this->errors;
    }

    private function validateValue(array $value)
    {
        if (!is_array($value) || count($value) < 2) {
            throw new Exception("参数错误");
        }
        $fields = $value[0];
        $rule = $value[1];
        $params = array_slice($value, 2);
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        foreach ($fields as $field) {
            if (!array_key_exists($field, $this->data)) {
                continue;
            }
            if (is_callable($rule)) {
                call_user_func($rule, $field);
            } else {
                if (!in_array($rule, self::VALIDATOR)) {
                    throw new Exception("不支持的验证规则");
                }
                call_user_func([$this, 'validate' . ucfirst($rule)], $field, $params);
            }
        }
    }

    private function validateRequire(string $field, array $params)
    {
        $label = $this->attributes()[$field];
        if (empty($this->data[$field])) {
            $this->addError($field, $label . '不能为空');
        }
    }
}
