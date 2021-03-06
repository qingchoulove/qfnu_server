<?php

namespace validators;

class Validators
{
    // 实例
    protected static $instance;

    // 自定义的验证类型
    protected static $type = [];

    // 验证类型别名
    private $alias = [
        '>' => 'gt', '>=' => 'egt', '<' => 'lt', '<=' => 'elt', '=' => 'eq', 'same' => 'eq',
    ];

    // 当前验证的规则
    protected $rule = [];

    // 验证提示信息
    protected $message = [];

    // 验证字段描述
    protected $field = [];

    //验证数据
    protected $data = [];

    // 当前验证场景
    private $currentScene = null;

    // 正则表达式 regex = ['zip'=>'\d{6}',...]
    protected $regex = [];

    // 验证场景 scene = ['edit'=>'name1,name2,...']
    protected $scene = [];

    // 验证失败错误信息
    protected $error = [];

    // 验证规则默认提示信息
    protected static $typeMsg = [
        'require' => ':attribute不能为空',
        'number' => ':attribute必须是数字',
        'integer' => ':attribute必须是整数',
        'float' => ':attribute必须是浮点数',
        'boolean' => ':attribute必须是布尔值',
        'email' => ':attribute格式不符',
        'array' => ':attribute必须是数组',
        'accepted' => ':attribute必须是yes、on或者1',
        'date' => ':attribute格式不符合',
        'alpha' => ':attribute只能是字母',
        'alphaNum' => ':attribute只能是字母和数字',
        'alphaDash' => ':attribute只能是字母、数字和下划线_及破折号-',
        'chs' => ':attribute只能是汉字',
        'chsAlpha' => ':attribute只能是汉字、字母',
        'chsAlphaNum' => ':attribute只能是汉字、字母和数字',
        'chsDash' => ':attribute只能是汉字、字母、数字和下划线_及破折号-',
        'dateFormat' => ':attribute必须使用日期格式 :rule',
        'in' => ':attribute必须在 :rule 范围内',
        'notIn' => ':attribute不能在 :rule 范围内',
        'between' => ':attribute只能在 :1 - :2 之间',
        'notBetween' => ':attribute不能在 :1 - :2 之间',
        'length' => ':attribute长度不符合要求 :rule',
        'max' => ':attribute长度不能超过 :rule',
        'min' => ':attribute长度不能小于 :rule',
        'after' => ':attribute日期不能小于 :rule',
        'before' => ':attribute日期不能超过 :rule',
        'expire' => '不在有效期内 :rule',
        'confirm' => ':attribute和确认字段:2不一致',
        'different' => ':attribute和比较字段:2不能相同',
        'egt' => ':attribute必须大于等于 :rule',
        'gt' => ':attribute必须大于 :rule',
        'elt' => ':attribute必须小于等于 :rule',
        'lt' => ':attribute必须小于 :rule',
        'eq' => ':attribute必须等于 :rule',
        'unique' => ':attribute已存在',
        'regex' => ':attribute不符合指定规则',
    ];

    /**
     * 构造函数
     * @access public
     * @param $data
     * @internal param array $rules 验证规则
     * @internal param array $message 验证提示信息
     * @internal param array $field 验证字段描述信息
     */
    public function __construct($data)
    {
        $_data = [];
        foreach ($this->field as $value) {
            $_data[$value] = isset($data[$value]) ? $data[$value] : null;
        }
        $this->data = $_data;
    }

    /**
     * 设置验证场景
     * @access public
     * @param string|array $name 场景名或者场景设置数组
     * @return Validators
     */
    public function scene($name)
    {
        if (!empty($name)) {
            $this->currentScene = $name;
        }
        return $this;
    }

    /**
     * 验证数据
     * @return bool
     */
    public function validate():bool
    {
        $this->error = [];
        // 读取验证规则
        $rules = $this->rule;
        // 分析验证规则
        $scene = $this->getScene();
        if (is_array($scene)) {
            // 处理场景验证字段
            $change = [];
            $array = [];
            foreach ($scene as $k => $val) {
                if (is_numeric($k)) {
                    $array[] = $val;
                } else {
                    $array[] = $k;
                    $change[$k] = $val;
                }
            }
        }

        foreach ($rules as $key => $item) {
            // field => rule1|rule2... field=>['rule1','rule2',...]
            if (is_numeric($key)) {
                // [field,rule1|rule2,msg1|msg2]
                $key = $item[0];
                $rule = $item[1];
                if (isset($item[2])) {
                    $msg = is_string($item[2]) ? explode('|', $item[2]) : $item[2];
                } else {
                    $msg = [];
                }
            } else {
                $rule = $item;
                $msg = [];
            }
            if (strpos($key, '|')) {
                // 字段|描述 用于指定属性名称
                list($key, $title) = explode('|', $key);
            } else {
                $title = isset($this->field[$key]) ? $this->field[$key] : $key;
            }

            // 场景检测
            if (!empty($scene)) {
                if ($scene instanceof \Closure && !call_user_func_array($scene, [$key, $this->data])) {
                    continue;
                } elseif (is_array($scene)) {
                    if (!in_array($key, $array)) {
                        continue;
                    } elseif (isset($change[$key])) {
                        // 重载某个验证规则
                        $rule = $change[$key];
                    }
                }
            }

            // 获取数据 支持二维数组
            $value = $this->getDataValue($this->data, $key);

            // 字段验证
            if ($rule instanceof \Closure) {
                // 匿名函数验证 支持传入当前字段和所有字段两个数据
                $result = call_user_func_array($rule, [$value, $this->data]);
            } else {
                $result = $this->checkItem($key, $value, $rule, $this->data, $title, $msg);
            }

            if (true !== $result) {
                // 没有返回true 则表示验证失败
                if (is_array($result)) {
                    $this->error = array_merge($this->error, $result);
                } else {
                    $this->error[$key] = $result;
                }
            }
        }
        return !empty($this->error) ? false : true;
    }

    /**
     * @param 过滤器
     * @return array
     */
    public function getAvailableAttribute()
    {
        return $this->data;
    }


    /**
     * 验证单个字段规则
     * @access protected
     * @param string $field 字段名
     * @param mixed $value 字段值
     * @param mixed $rules 验证规则
     * @param array $data 数据
     * @param string $title 字段描述
     * @param array $msg 提示信息
     * @return mixed
     */
    protected function checkItem($field, $value, $rules, $data, $title = '', $msg = [])
    {
        // 支持多规则验证 require|in:a,b,c|... 或者 ['require','in'=>'a,b,c',...]
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }
        $i = 0;
        foreach ($rules as $key => $rule) {
            if ($rule instanceof \Closure) {
                $result = call_user_func_array($rule, [$value, $data]);
                $info = is_numeric($key) ? '' : $key;
            } else {
                // 判断验证类型
                if (is_numeric($key)) {
                    if (strpos($rule, ':')) {
                        list($type, $rule) = explode(':', $rule, 2);
                        if (isset($this->alias[$type])) {
                            // 判断别名
                            $type = $this->alias[$type];
                        }
                        $info = $type;
                    } elseif (method_exists($this, $rule)) {
                        $type = $rule;
                        $info = $rule;
                        $rule = '';
                    } else {
                        $type = 'is';
                        $info = $rule;
                    }
                } else {
                    $info = $type = $key;
                }

                // 如果不是require 有数据才会行验证
                if (0 === strpos($info, 'require') || (!is_null($value) && '' !== $value)) {
                    // 验证类型
                    $callback = isset(self::$type[$type]) ? self::$type[$type] : [$this, $type];
                    // 验证数据
                    $result = call_user_func_array($callback, [$value, $rule, $data, $field, $title]);
                } else {
                    $result = true;
                }
            }

            if (false === $result) {
                // 验证失败 返回错误信息
                if (isset($msg[$i])) {
                    $message = $msg[$i];
                    if (is_string($message) && strpos($message, '{%') === 0) {
                        $message = substr($message, 2, -1);
                    }
                } else {
                    $message = $this->getRuleMsg($field, $title, $info, $rule);
                }
                return $message;
            } elseif (true !== $result) {
                // 返回自定义错误信息
                if (is_string($result) && false !== strpos($result, ':')) {
                    $result = str_replace([':attribute', ':rule'], [$title, (string)$rule], $result);
                }
                return $result;
            }
            $i++;
        }
        return $result;
    }

    /**
     * 验证是否和某个字段的值一致
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @param array $data 数据
     * @param string $field 字段名
     * @return bool
     */
    protected function confirm($value, $rule, $data, $field = '')
    {
        if ('' == $rule) {
            if (strpos($field, '_confirm')) {
                $rule = strstr($field, '_confirm', true);
            } else {
                $rule = $field . '_confirm';
            }
        }
        return $this->getDataValue($data, $rule) === $value;
    }

    /**
     * 验证是否和某个字段的值是否不同
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @param array $data 数据
     * @return bool
     */
    protected function different($value, $rule, $data)
    {
        return $this->getDataValue($data, $rule) != $value;
    }

    /**
     * 验证是否大于等于某个值
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function egt($value, $rule)
    {
        return $value >= $rule;
    }

    /**
     * 验证是否大于某个值
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function gt($value, $rule)
    {
        return $value > $rule;
    }

    /**
     * 验证是否小于等于某个值
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function elt($value, $rule)
    {
        return $value <= $rule;
    }

    /**
     * 验证是否小于某个值
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function lt($value, $rule)
    {
        return $value < $rule;
    }

    /**
     * 验证是否等于某个值
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function eq($value, $rule)
    {
        return $value == $rule;
    }

    /**
     * 验证字段值是否为有效格式
     * @access protected
     * @param mixed $value 字段值
     * @param string $rule 验证规则
     * @param array $data 验证数据
     * @return bool
     */
    protected function is($value, $rule)
    {
        switch ($rule) {
            case 'require':
                // 必须
                $result = !empty($value) || '0' == $value;
                break;
            case 'accepted':
                // 接受
                $result = in_array($value, ['1', 'on', 'yes']);
                break;
            case 'date':
                // 是否是一个有效日期
                $result = false !== strtotime($value);
                break;
            case 'alpha':
                // 只允许字母
                $result = $this->regex($value, '/^[A-Za-z]+$/');
                break;
            case 'alphaNum':
                // 只允许字母和数字
                $result = $this->regex($value, '/^[A-Za-z0-9]+$/');
                break;
            case 'alphaDash':
                // 只允许字母、数字和下划线 破折号
                $result = $this->regex($value, '/^[A-Za-z0-9\-\_]+$/');
                break;
            case 'chs':
                // 只允许汉字
                $result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}]+$/u');
                break;
            case 'chsAlpha':
                // 只允许汉字、字母
                $result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}a-zA-Z]+$/u');
                break;
            case 'chsAlphaNum':
                // 只允许汉字、字母和数字
                $result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u');
                break;
            case 'chsDash':
                // 只允许汉字、字母、数字和下划线_及破折号-
                $result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\_\-]+$/u');
                break;
            case 'float':
                // 是否为float
                $result = $this->filter($value, FILTER_VALIDATE_FLOAT);
                break;
            case 'number':
                $result = is_int($value);
                break;
            case 'integer':
                // 是否为整型
                $result = $this->filter($value, FILTER_VALIDATE_INT);
                break;
            case 'email':
                // 是否为邮箱地址
                $result = $this->filter($value, FILTER_VALIDATE_EMAIL);
                break;
            case 'boolean':
                // 是否为布尔值
                $result = in_array($value, [true, false, 0, 1, '0', '1'], true);
                break;
            case 'array':
                // 是否为数组
                $result = is_array($value);
                break;
            default:
                if (isset(self::$type[$rule])) {
                    // 注册的验证规则
                    $result = call_user_func_array(self::$type[$rule], [$value]);
                } else {
                    // 正则验证
                    $result = $this->regex($value, $rule);
                }
        }
        return $result;
    }


    /**
     * 验证时间和日期是否符合指定格式
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function dateFormat($value, $rule)
    {
        $info = date_parse_from_format($rule, $value);
        return 0 == $info['warning_count'] && 0 == $info['error_count'];
    }


    /**
     * 使用filter_var方式验证
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function filter($value, $rule)
    {
        if (is_string($rule) && strpos($rule, ',')) {
            list($rule, $param) = explode(',', $rule);
        } elseif (is_array($rule)) {
            $param = isset($rule[1]) ? $rule[1] : null;
            $rule = $rule[0];
        } else {
            $param = null;
        }
        return false !== filter_var($value, is_int($rule) ? $rule : filter_id($rule), $param);
    }

    /**
     * 验证某个字段等于某个值的时候必须
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @param array $data 数据
     * @return bool
     */
    protected function requireIf($value, $rule, $data)
    {
        list($field, $val) = explode(',', $rule);
        if ($this->getDataValue($data, $field) == $val) {
            return !empty($value);
        } else {
            return true;
        }
    }

    /**
     * 通过回调方法验证某个字段是否必须
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @param array $data 数据
     * @return bool
     */
    protected function requireCallback($value, $rule, $data)
    {
        $result = call_user_func_array($rule, [$value, $data]);
        if ($result) {
            return !empty($value);
        } else {
            return true;
        }
    }

    /**
     * 验证某个字段有值的情况下必须
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @param array $data 数据
     * @return bool
     */
    protected function requireWith($value, $rule, $data)
    {
        $val = $this->getDataValue($data, $rule);
        if (!empty($val)) {
            return !empty($value);
        } else {
            return true;
        }
    }

    /**
     * 验证是否在范围内
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function in($value, $rule)
    {
        return in_array($value, is_array($rule) ? $rule : explode(',', $rule));
    }

    /**
     * 验证是否不在某个范围
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function notIn($value, $rule)
    {
        return !in_array($value, is_array($rule) ? $rule : explode(',', $rule));
    }

    /**
     * between验证数据
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function between($value, $rule)
    {
        if (is_string($rule)) {
            $rule = explode(',', $rule);
        }
        list($min, $max) = $rule;
        return $value >= $min && $value <= $max;
    }

    /**
     * 使用notbetween验证数据
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function notBetween($value, $rule)
    {
        if (is_string($rule)) {
            $rule = explode(',', $rule);
        }
        list($min, $max) = $rule;
        return $value < $min || $value > $max;
    }

    /**
     * 验证数据长度
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function length($value, $rule)
    {
        if (is_array($value)) {
            $length = count($value);
        } elseif ($value instanceof File) {
            $length = $value->getSize();
        } else {
            $length = mb_strlen((string)$value);
        }

        if (strpos($rule, ',')) {
            // 长度区间
            list($min, $max) = explode(',', $rule);
            return $length >= $min && $length <= $max;
        } else {
            // 指定长度
            return $length == $rule;
        }
    }

    /**
     * 验证数据最大长度
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function max($value, $rule)
    {
        if (is_array($value)) {
            $length = count($value);
        } elseif ($value instanceof File) {
            $length = $value->getSize();
        } else {
            $length = mb_strlen((string)$value);
        }
        return $length <= $rule;
    }

    /**
     * 验证数据最小长度
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function min($value, $rule)
    {
        if (is_array($value)) {
            $length = count($value);
        } elseif ($value instanceof File) {
            $length = $value->getSize();
        } else {
            $length = mb_strlen((string)$value);
        }
        return $length >= $rule;
    }

    /**
     * 验证日期
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function after($value, $rule)
    {
        return strtotime($value) >= strtotime($rule);
    }

    /**
     * 验证日期
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则
     * @return bool
     */
    protected function before($value, $rule)
    {
        return strtotime($value) <= strtotime($rule);
    }


    /**
     * 使用正则验证数据
     * @access protected
     * @param mixed $value 字段值
     * @param mixed $rule 验证规则 正则规则或者预定义正则名
     * @return mixed
     */
    protected function regex($value, $rule)
    {
        if (isset($this->regex[$rule])) {
            $rule = $this->regex[$rule];
        }
        if (0 !== strpos($rule, '/') && !preg_match('/\/[imsU]{0,4}$/', $rule)) {
            // 不是正则表达式则两端补上/
            $rule = '/^' . $rule . '$/';
        }
        return 1 === preg_match($rule, (string)$value);
    }


    // 获取错误信息
    public function getError()
    {
        return $this->error;
    }

    /**
     * 获取数据值
     * @access protected
     * @param array $data 数据
     * @param string $key 数据标识 支持二维
     * @return mixed
     */
    protected function getDataValue($data, $key)
    {
        if (strpos($key, '.')) {
            // 支持二维数组验证
            list($name1, $name2) = explode('.', $key);
            $value = isset($data[$name1][$name2]) ? $data[$name1][$name2] : null;
        } else {
            $value = isset($data[$key]) ? $data[$key] : null;
        }
        return $value;
    }

    /**
     * 获取验证规则的错误提示信息
     * @access protected
     * @param string $attribute 字段英文名
     * @param string $title 字段描述名
     * @param string $type 验证规则名称
     * @param mixed $rule 验证规则数据
     * @return string
     */
    protected function getRuleMsg($attribute, $title, $type, $rule)
    {
        if (isset($this->message[$attribute . '.' . $type])) {
            $msg = $this->message[$attribute . '.' . $type];
        } elseif (isset($this->message[$attribute][$type])) {
            $msg = $this->message[$attribute][$type];
        } elseif (isset($this->message[$attribute])) {
            $msg = $this->message[$attribute];
        } elseif (isset(self::$typeMsg[$type])) {
            $msg = self::$typeMsg[$type];
        } else {
            $msg = $title . '规则错误';
        }

        if (is_string($msg) && 0 === strpos($msg, '{%')) {
            $msg = substr($msg, 2, -1);
        }

        if (is_string($msg) && is_scalar($rule) && false !== strpos($msg, ':')) {
            // 变量替换
            if (is_string($rule) && strpos($rule, ',')) {
                $array = array_pad(explode(',', $rule), 3, '');
            } else {
                $array = array_pad([], 3, '');
            }
            $msg = str_replace(
                [':attribute', ':rule', ':1', ':2', ':3'],
                [$title, (string)$rule, $array[0], $array[1], $array[2]],
                $msg);
        }
        return $msg;
    }

    /**
     * 获取真正要验证的字段
     * @return array
     */
    private function getScene():array
    {
        $scene = $this->currentScene;

        if (!empty($scene) && isset($this->scene[$scene])) {
            // 如果设置了验证适用场景
            $scene = $this->scene[$scene];
            if (is_string($scene)) {
                $scene = explode(',', $scene);
            }
        } else {
            $scene = [];
        }
        return $scene;
    }
}
