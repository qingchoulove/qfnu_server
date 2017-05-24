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
    public function goCheck()
    {
        //获取http传入参数
        //对这些参数做校验
//        $request = Request::instance();
//        $params = $request->param();

        $data = [
            'id'=>'123456789',
            //'email'=>'kang_hui1314@126.com'
        ];

        //设置批量验证 并检验
        $result = $this->batch()->check($data);
        if (!$result) {
            //抛出相应异常
            //text
            //throw new xxxxxxxxxxxException();
            //$msg = $this->error;
            return false;//测试用
        } else {
            return true;
        }
    }
}