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

    /**
     * 查询借阅信息 TODO
     * @param  string
     * @return [type]
     */
    public function getBorrowBooks(string $userId):array {
        $cookie = $this->getCookie($userId);
        $url = 'http://219.218.26.4:85/opac_two/reader/jieshuxinxi.jsp';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        Util::Dump(htmlspecialchars($content));
        return [];
    }

}