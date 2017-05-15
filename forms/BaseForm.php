<?php

namespace forms;

use common\Component;
use common\Util;

/**
* model基类
*/
class BaseForm extends Component
{
    private $data;
    private $errors = [];

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
        $errors = [];
        foreach ($this->rules() as $key => $value) {
            if (!is_array($value) || count($value) < 2) {
                //TODO: 需要抛出异常
                return false;
            }
            $fields = $value[0];
            $rule = $value[1];
            if (!is_array($fields)) {
                $fields = [$fields];
            }
            foreach ($fields as $field) {
                if (!array_key_exists($field, $data)) {
                    continue;
                }
                // 分拆独立函数处理
                // require number length range callable
                switch ($rule) {
                    case 'require':
                        if (empty($data[$field])) {
                            $errors[$field] = '不能为空';
                        }
                        break;
                    case 'number':
                        if (!is_int($data[$field])) {
                            $errors[$field] = '必须是整数';
                        }
                        break;
                    default:
                        if (is_callable($rule)) {
                            call_user_func($rule, $field);
                        }
                        break;
                }
            }
        }
        // 如果errors为空直接返回true
        if (empty($errors)) {
            return true;
        }
        foreach ($errors as $key => &$value) {
            $attribute = isset($attributes[$key]) ? $attributes[$key] : $key;
            $value = $attribute . $value;
        }
        $this->errors = array_merge($this->errors, $errors);
        return false;
    }

    public function getData()
    {
        return $this->data;
    }

    public function addError($attribute, $message) {
        $this->errors[$attribute] = $message;
    }

    public function getErrors():array
    {
        return $this->errors;
    }
}
