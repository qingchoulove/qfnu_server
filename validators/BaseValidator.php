<?php
namespace validators;

use common\Component;
use common\exceptions\OperationNotValidException;

class BaseValidator extends Component
{
    private $_errors = [];
    private $_scenario = null;
    private $_data = [];

    /**
     * 内建验证器
     * @var array
     */
    public static $builtInValidators = [
        'required',
        'string',
        'number',
        'default',
        'filter',
        'in',
        'trim',
    ];

    public function __construct($data, $scenario = null)
    {
        if (!empty($scenario) && !isset($this->scenarios()[$scenario])) {
            throw new OperationNotValidException("请指定正确的场景");
        }
        $this->_scenario = $scenario;
        $fields = array_keys($this->attributes());
        foreach ($fields as $field) {
            $this->_data[$field] = isset($data[$field]) ? $data[$field] : null;
        }
    }

    /**
     * 待验证
     * @return array
     */
    public function attributes():array
    {
        return [];
    }

    /**
     * 验证规则
     * @return array
     */
    public function rules():array
    {
        return [];
    }

    /**
     * 验证场景
     * @return array
     */
    public function scenarios():array
    {
        return [];
    }

    public function validate()
    {
        foreach ($this->activeRules() as $rule) {
            $_attributes = (array)$rule[0];
            $type = $rule[1];
            $params = array_slice($rule, 2);
            $message = isset($rule['message']) ? $rule['message'] : null;
            $this->validateAttributes($_attributes, $type, $params, $message);
        }
        return empty($this->_errors);
    }

    private function validateAttributes($attributes, $type, $params, $message)
    {
        $attributeNames = $this->activeAttributes();
        foreach ($attributes as $attribute) {
            if (!in_array($attribute, $attributeNames, true)) {
                continue;
            }
            $attributeValue = $this->attributes()[$attribute];
            if ($type instanceof \Closure) {
                call_user_func_array($type, [$attribute]);
            } else {
                // 如果非必填且值为空,跳过检查
                if (!$this->isRequiredAttribute($attribute) && empty($attributeValue)) {
                    continue;
                }
                $method = $type . 'Validator';
                if (!method_exists($this, $method)) {
                    throw new OperationNotValidException("请指定正确的验证器");
                }
                $this->$method($attribute, $params, $message);
            }
        }
    }

    /**
     * 必填项验证
     * @param $attribute
     * @param $params
     * @param $message
     */
    private function requiredValidator($attribute, $params, $message)
    {
        $value = $this->_data[$attribute];
        $label = $this->attributes()[$attribute];
        if (empty($value)) {
            $message = $message ?? $label . '不能为空';
            $this->addError($attribute, $message);
        }
    }

    /**
     * 字符串验证
     * @param $attribute
     * @param $params
     * @param $message
     */
    private function stringValidator($attribute, $params, $message)
    {
        $value = $this->_data[$attribute];
        $label = $this->attributes()[$attribute];
        // 类型检查
        if (!is_string($value)) {
            $message = $message ?? $label . '必须是字符串';
            $this->addError($attribute, $message);
        }
        // 长度检查
        if (isset($params['min']) && mb_strlen($value) < $params['min']) {
            $message = $message ?? $label . '长度必须大于' . $params['min'];
            $this->addError($attribute, $message);
        }
        if (isset($params['max']) && mb_strlen($value) > $params['max']) {
            $message = $message ?? $label . '长度必须小于' . $params['max'];
            $this->addError($attribute, $message);
        }
    }

    /**
     * 数字验证
     * @param $attribute
     * @param $params
     * @param $message
     */
    private function numberValidator($attribute, $params, $message)
    {
        $integerOnly = isset($params['integerOnly']) ?? false;
        $integerPattern = '/^\s*[+-]?\d+\s*$/';
        $numberPattern = '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';
        $value = $this->_data[$attribute];
        $label = $this->attributes()[$attribute];
        $pattern = $integerOnly ? $integerPattern : $numberPattern;
        if (!preg_match($pattern, "$value")) {
            $message = $message ?? $label . '必须是数组';
            $this->addError($attribute, $message);
        }
        if (isset($params['min']) && $value < $params['min']) {
            $message = $message ?? $label . '必须大于' . $params['min'];
            $this->addError($attribute, $message);
        }
        if (isset($params['max']) && $value < $params['max']) {
            $message = $message ?? $label . '必须小于' . $params['max'];
            $this->addError($attribute, $message);
        }
    }

    /**
     * 默认验证器
     * @param $attribute
     * @param $params
     * @param $message
     * @throws OperationNotValidException
     */
    private function defaultValidator($attribute, $params, $message)
    {
        $value = $this->_data[$attribute];
        $default = $params['value'];
        if (empty($default)) {
            throw new OperationNotValidException('default验证器参数错误');
        }
        if (empty($value)) {
            if ($default instanceof \Closure) {
                $this->_data[$attribute] = call_user_func($default, $attribute);
            } else {
                $this->_data[$attribute] = $default;
            }
        }
    }

    /**
     * trim
     * @param $attribute
     * @param $params
     * @param $message
     */
    private function trimValidator($attribute, $params, $message)
    {
        $this->_data[$attribute] = trim($this->_data[$attribute]);
    }

    /**
     * 范围验证器
     * @param $attribute
     * @param $params
     * @param $message
     * @throws OperationNotValidException
     */
    private function inValidator($attribute, $params, $message)
    {
        $value = $this->_data[$attribute];
        $label = $this->attributes()[$attribute];
        $range = $params['range'] ?? [];
        if (empty($range)) {
            throw new OperationNotValidException('范围验证时参数错误');
        }
        if (!in_array($value, $range)) {
            $message = $message ?? $label . '必须属于[' . implode(',', $range) . ']';
            $this->addError($attribute, $message);
        }
    }

    /**
     * 符合场景的字段
     * @return array|mixed
     */
    private function activeAttributes()
    {
        $scenario = $this->_scenario;
        $scenarios = $this->scenarios();
        if (empty($scenario)) {
            return array_keys($this->attributes());
        }
        if (!empty($scenario) && !isset($scenarios[$scenario])) {
            return [];
        }
        return $scenarios[$scenario];
    }

    /**
     * 符合场景的规则
     * @param $attribute
     * @return array
     */
    private function activeRules($attribute = null)
    {
        $rules = [];
        $scenario = $this->_scenario;
        foreach ($this->rules() as $key => $rule) {
            $on = isset($rule['on']) ? (array)$rule['on'] : null;
            $attributes = (array)$rule[0];
            // 未指定场景或指定场景包含当前场景
            // 未指定attribute或attribute在当前规则中
            if ((empty($on) || in_array($scenario, $on, true))
                && ($attribute === null || in_array($attribute, $attributes, true))) {
                $rules[] = $rule;
            }
        }
        return $rules;
    }

    /**
     * 检查时候有必填规则
     * @param string $attribute
     * @return bool
     */
    private function isRequiredAttribute(string $attribute):bool
    {
        $activeRules = $this->activeRules($attribute);
        foreach ($activeRules as $rule) {
            if ($rule[1] == 'required') {
                return true;
            }
        }
        return false;
    }

    /**
     * 添加错误信息
     * @param string $attribute
     * @param string $message
     */
    public function addError(string $attribute, string $message)
    {
        if (isset($this->attributes()[$attribute])) {
            $this->_errors[$attribute] = $message;
        }
    }

    /**
     * 获取错误信息
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * 获取校验后数据
     */
    public function getAvailableAttribute()
    {
        return $this->_data;
    }
}
