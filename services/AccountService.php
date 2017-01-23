<?php
namespace services;
use common\Util;
use models\AccountModel;

class AccountService extends BaseService {

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
        $data = AccountModel::where('user_id', $account['user_id'])->first();
        if (!empty($data)) {
            return;
        }
        $model = new AccountModel();
        $model->account_id = Util::UUID();
        $model->user_id = $account['user_id'];
        $model->password = $account['password'];
        $model->save();
    }
}