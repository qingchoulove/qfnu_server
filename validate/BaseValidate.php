<?php

/**
 * Created by PhpStorm.
 * User: SoftpaseFar
 * Date: 2017/5/24
 * Time: 13:09
 */
namespace validate;

use common\Validate;

class BaseValidate extends Validate
{
    public function validate()
    {
        //获取http传入参数
        //模拟数据
        $data = [
            'id' => '12345678',
            //'email'=>'kang_hui1314@126.com'
        ];

        //检验
        $result = $this->check($data);
        if (!$result) {
            throw new \Exception([
                $this->error
            ]);
        } else {
            $newArray = $this->getAvailableAttributes($data);
            return $newArray;
        }
    }

    /**
     * 过滤相应参规则下的数据
     * @param $arrays
     * @return array
     */
    public function getAvailableAttributes($arrays)
    {
        $newArray = [];
        foreach ($this->rule as $key => $value) {
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }

}