<?php
namespace services;

use common\Util;
use common\Constants;
use models\AccountModel;
use Predis\Client;
use Exception;

/**
 * Class AccountService
 * @package services
 * @property Client $cache
 */
class AccountService extends BaseService
{

    /**
     * 根据学号查询对应密码
     * @param string $userId
     * @return string
     * @throws Exception
     */
    public function getPasswordByUserId($userId)
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
     * @param  string $userId 学号
     * @return array  用户信息
     * @throws Exception
     */
    public function getAccountByUserId($userId)
    {
        $data = AccountModel::where('user_id', $userId)
            ->first();
        if (empty($data)) {
            throw new Exception('用户不存在');
        }
        $data = $data->toArray();
        if (empty($data['name'])) {
            $userInfo = $this->urpService->getUserInfo($userId);
            $userInfo['user_id'] = $userId;
            $this->updateAccount($userInfo);
            $data = array_merge($data, $userInfo);
        }
        return $data;
    }

    /**
     * 根据token获取用户
     * @param string $token
     * @return array
     */
    public function getAccountByToken($token)
    {
        $account = AccountModel::where('token', $token)
            ->first();
        return empty($account) ? [] : $account->toArray();
    }

    /**
     * 添加用户
     * @param array $account
     */
    public function addAccount(array $account)
    {
        $model = AccountModel::where('user_id', $account['user_id'])
            ->first();
        if (empty($model)) {
            $model = new AccountModel();
            $model->account_id = Util::UUID();
            $model->user_id = $account['user_id'];
        }
        $model->password = $account['password'];
        $model->save();
    }

    /**
     * 更新用户信息
     * @param array $account
     * @throws Exception
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

    /**
     * 更新用户token
     * @param string $userId
     * @param string $token
     * @throws Exception
     */
    public function updateAccountToken($userId, $token)
    {
        $account = $this->getAccountByUserId($userId);
        $this->cache->del(Constants::AUTH_PREFIX . $account['token']);
        AccountModel::where('user_id', $userId)
            ->update(['token' => $token]);
        $this->cache->set(Constants::AUTH_PREFIX . $token, serialize($account));
    }
}
