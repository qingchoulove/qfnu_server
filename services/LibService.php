<?php
namespace services;
use Exception;
use common\Util;
use common\Constants;
/**
* 日照校区图书馆
*/
class LibService extends BaseService {

    public function getCookie(string $userId):string {
        $account = $this->accountService->getAccountByUserId($userId);
        if (empty($account)) {
            throw new Exception("账户不存在");
        }
        $password = $account['password'];
        $result = $this->casService->login($userId, $password, Constants::AUTHSERVER_TYPE_LIB_RZ);
        if (!$result) {
            throw new Exception("登录失败");
        }
        return $this->cache->get(Constants::CAS_COOKIE_PREFIX . Constants::AUTHSERVER_TYPE_LIB_RZ .$userId);
    }

}