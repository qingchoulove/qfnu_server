<?php
namespace services;

use Exception;
use common\Util;
use common\Constants;

/**
 * 图书借阅查询
 * @package services
 * @property CasService $casService
 * @property AccountService $accountService
 */
class LibService extends BaseService
{

    private function getCookie(string $userId, int $type):string
    {
        $password = $this->accountService->getPasswordByUserId($userId);
        $result = $this->casService->login($userId, $password, $type);
        if (!$result) {
            throw new Exception("登录失败");
        }
        return $this->cache->get(Constants::CAS_COOKIE_PREFIX . $type . '_' . $userId);
    }

    /**
     * 查询借阅信息
     * @param  string
     * @return array
     */
    public function getBorrowBooks(string $userId):array
    {
        $userInfo = $this->accountService->getAccountByUserId($userId);
        $type = $userInfo['campus'] === Constants::CAMPUS_QF ? Constants::AUTHSERVER_TYPE_LIB_QF : Constants::AUTHSERVER_TYPE_LIB_RZ;
        $cookie = $this->getCookie($userId, $type);
        if ($type === Constants::AUTHSERVER_TYPE_LIB_QF) {
            $books = $this->getBorrowBooksQF($cookie);
        } else {
            $books = $this->getBorrowBooksRZ($cookie);
        }
        return $books;
    }

    /**
     * 查询借阅历史
     * @param string
     * @return array
     */
    public function getBorrowHistory(string $userId):array
    {
        $userInfo = $this->accountService->getAccountByUserId($userId);
        $type = $userInfo['campus'] === Constants::CAMPUS_QF ? Constants::AUTHSERVER_TYPE_LIB_QF : Constants::AUTHSERVER_TYPE_LIB_RZ;
        $cookie = $this->getCookie($userId, $type);
        $params = [
            'library_id' => '%C3%8B%C3%B9%C3%93%C3%90%C2%B7%C3%96%C2%B9%C3%9D',
            'fromdate' => '2013-5-18',
            'todate' => '2017-5-18'
        ];
        $url = 'http://219.218.26.4:85/opac_two/reader/jieshulishi.jsp?' . http_build_query($params);
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all('#<tr\s+class[^>]*?>[\s\S]*?</tr>#i', $content, $table);
        if (empty($table[0])) {
            return [];
        }
        $history = [];
        foreach ($table[0] as $key => $value) {
            $history[] = Util::ParseTable($value)[0];
        }
        return $history;
    }

    /**
     * 搜索图书
     * @param string $userId
     * @param string $keyword
     * @return array
     */
    public function searchBook(string $userId, string $keyword):array
    {

    }

    /**
     * 获取曲阜校区图书馆借阅信息
     * @param string $cookie
     * @return array
     */
    private function getBorrowBooksQF(string $cookie):array
    {
        $url = 'http://202.194.184.2:808/museweb/dzjs/jhcx.asp';
        $params = [
            'nCxfs' => 1,
            'submit1' => '检 索'
        ];
        $content = Util::Curl($url, $cookie, $params);
        $parseTable = Util::ParseTable($content);
        foreach ($parseTable as $key => $value) {
            if (count($value) != 8) {
                unset($parseTable[$key]);
            }
        }
        $books = [];
        array_shift($parseTable);
        foreach ($parseTable as $value) {
            array_pop($value);
            $books[] = $value;
        }
        return $books;
    }

    /**
     * 获取日照校区图书馆借阅信息
     * @param string $cookie
     * @return array
     */
    private function getBorrowBooksRZ(string $cookie):array
    {
        $url = 'http://219.218.26.4:85/opac_two/reader/jieshuxinxi.jsp';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all('#<tr\s+class[^>]*?>[\s\S]*?</tr>#i', $content, $table);
        if (empty($table[0])) {
            return [];
        }
        $books = [];
        foreach ($table[0] as $key => $value) {
            $books[] = Util::ParseTable($value)[0];
        }
        return $books;
    }
}

