<?php
namespace services;

use Exception;
use common\Util;
use common\Constants;

/**
* 日照校区图书馆
*/
class LibService extends BaseService
{

    public function getCookie(string $userId):string
    {
        $account = $this->accountService->getAccountByUserId($userId);
        if (empty($account)) {
            throw new Exception("账户不存在");
        }
        $password = $account['password'];
        $result = $this->casService->login($userId, $password, Constants::AUTHSERVER_TYPE_LIB_RZ);
        if (!$result) {
            throw new Exception("登录失败");
        }
        return $this->cache->get(Constants::CAS_COOKIE_PREFIX . Constants::AUTHSERVER_TYPE_LIB_RZ . '_' . $userId);
    }

    /**
     * 查询借阅信息
     * @param  string
     * @return array
     */
    public function getBorrowBooks(string $userId):array
    {
        $cookie = $this->getCookie($userId);
        $url = 'http://219.218.26.4:85/opac_two/reader/jieshuxinxi.jsp';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all('#<tr\s+class[^>]*?>[\s\S]*?</tr>#i', $content, $table);
        if (empty($table[0])) {
            return [];
        }
        foreach ($table[0] as $key => $value) {
            $tableArr[] = Util::ParseTable($value)[0];
        }
        return $tableArr;
    }

    /**
     * 查询借阅历史
     * @param string
     * @return array
     */
    public function getBorrowHistroy(string $userId):array
    {
        $cookie = $this->getCookie($userId);
        $params = [
            'library_id' => '%C3%8B%C3%B9%C3%93%C3%90%C2%B7%C3%96%C2%B9%C3%9D',
            'fromdate' => '2016-5-18',
            'todate' => '2017-5-18'
        ];
        $url = 'http://219.218.26.4:85/opac_two/reader/jieshulishi.jsp?' . http_build_query($params);
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all('#<tr\s+class[^>]*?>[\s\S]*?</tr>#i', $content, $table);
        if (empty($table[0])) {
            return [];
        }
        foreach ($table[0] as $key => $value) {
            $tableArr[] = Util::ParseTable($value)[0];
        }
        return $tableArr;
    }
}
