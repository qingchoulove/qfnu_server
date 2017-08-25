<?php
namespace services;

use models\ClazzModel;

/**
 * 分班查询服务
 */
class ClazzService extends BaseService
{

    /**
     * 根据身份证及姓名查询分班信息
     * @param string $code
     * @param string $name
     * @return array
     */
    public function getClazzByCodeAndName(string $code, string $name)
    {
        $clazz = ClazzModel::select(['std_id', 'name', 'code', 'class', 'major', 'institute'])
            ->where(['code' => $code, 'name' => $name])
            ->first();
        return empty($clazz) ? [] : $clazz->toArray();
    }

}