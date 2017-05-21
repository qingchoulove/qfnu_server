<?php
namespace services;

use common\Util;
use models\AccountModel;
use Exception;

class AccountService extends BaseService
{

    /**
     * 根据学号查询对应密码
     * @param  string 学号
     * @return string 密码
     */
    public function getPasswordByUserId(string $userId):string
    {
        $data = AccountModel::where('user_id', $userId)
            ->select('password')
            ->first();
        if (empty($data)) {
            throw new Exception('用户不存在');
        }
        return $data->password;
    }

    /**
     * 根据学号查询用户信息
     * @param  string 学号
     * @return array  用户信息
     */
    public function getAccountByUserId(string $userId):array
    {
        $data = AccountModel::where('user_id', $userId)
            ->first();
        if (empty($data)) {
            throw new Exception('用户不存在');
        }
        $data = $data->toArray();
        if (!isset($data['name'])) {
            $userInfo = $this->urpService->getUserInfo($userId);
            $userInfo['user_id'] = $userId;
            $this->updateAccount($userInfo);
            $data = array_merge($data, $userInfo);
        }
        return $data;
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
