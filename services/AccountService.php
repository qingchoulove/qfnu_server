<?php
namespace services;

use common\Util;
use models\AccountModel;
use Exception;

class AccountService extends BaseService
{

    /**
     * 根据学号查询用户信息
     * @param  string 学号
     * @return array 用户信息
     */
    public function getAccountByUserId(string $userId): array
    {
        $data = AccountModel::where('user_id', $userId)
            ->first();
        return empty($data) ? null : $data->toArray();
    }

    /**
     * 添加用户
     * @param array 用户信息
     */
    public function addAccount(array $account)
    {
        $data = AccountModel::where('user_id', $account['user_id'])
            ->first();
        if (!empty($data)) {
            throw new Exception('用户不存在');
        }
        $model = new AccountModel();
        $model->account_id = Util::UUID();
        $model->user_id = $account['user_id'];
        $model->password = $account['password'];
        $model->save();
    }

    /**
     * 更新用户信息
     * @param  array 用户信息
     */
    public function updateAccount(array $account)
    {
        if (empty($account['user_id'])) {
            throw new Exception('用户id不能为空');
        }
        $data = AccountModel::where('user_id', $account['user_id'])
            ->first();
        if (empty($data)) {
            throw new Exception('用户不存在');
        }
        foreach ($account as $key => $value) {
            $data->$key = $value;
        }
        $data->save();
    }
}
