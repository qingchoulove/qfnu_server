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

    private function getCookie(string $userId, int $campus):string
    {
        $type = $campus === Constants::CAMPUS_QF ? Constants::AUTHSERVER_TYPE_LIB_QF : Constants::AUTHSERVER_TYPE_LIB_RZ;
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
        $cookie = $this->getCookie($userId, $userInfo['campus']);
        if ($userInfo['campus'] === Constants::CAMPUS_QF) {
            $books = $this->getBorrowBooksQF($cookie);
        } else {
            $books = $this->getBorrowBooksRZ($cookie);
        }
        return $books;
    }

    /**
     * 搜索图书
     * @param string $userId
     * @param string $keyword
     * @param int $page
     * @return array
     */
    public function searchBook(string $userId, string $keyword, int $page = 1):array
    {
        $userInfo = $this->accountService->getAccountByUserId($userId);
        if ($userInfo['campus'] == Constants::CAMPUS_QF) {
            $books = $this->searchBookQF($keyword, $page);
        } else {
            $books = $this->searchBookRZ($keyword, $page);
        }
        return $books;
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
     * 检索曲阜校区图书馆
     * @param string $keyword
     * @param int $page
     * @return array
     */
    private function searchBookQF(string $keyword, int $page):array
    {
        $url = 'http://202.194.184.29:808/museweb/wxjs/tmjs.asp';
        $params = [
            'txtTm' => mb_convert_encoding($keyword, 'GB2312', 'UTF-8'),
            'txtLx' => '%',
            'txtSearchType' => 1,
            'nSetPageSize' => 20,
            'page' => $page
        ];
        $content = Util::Curl($url, null, $params);
        $content = mb_convert_encoding($content, 'UTF-8', 'GB2312');
        if (strpos($content, '暂时没有内容')) {
            return [
                'page' => ['total' => 0, 'current' => 0],
                'books' => []
            ];
        }
        preg_match('#<font color[\s\S]*?</font>\/\d+#i', $content, $pageHtml);
        $pageHtml = strip_tags($pageHtml[0]);
        $pageHtml = explode('/', $pageHtml);
        $page = [
            'total' => $pageHtml[1],
            'current' => $pageHtml[0]
        ];
        preg_match_all('#<tr>\s[\s\S]*?</tr>#', $content, $tr);
        $tr = array_slice($tr[0], 3);
        array_pop($tr);
        $books = [];
        foreach ($tr as $key => $value) {
            $book = Util::ParseTable($value)[0];
            $books[] = [
                $book[2], $book[3], $book[4], $book[1]
            ];
        }
        return [
            'page' => $page,
            'books' => $books
        ];
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

    /**
     * 日照校区图书检索
     * @param string $keyword
     * @param int $page
     * @return array
     */
    private function searchBookRZ(string $keyword, int $page):array
    {
        $url = 'http://219.218.26.4:85/opac_two/search2/searchout.jsp';
        $params = [
            'kind' => 'simple',
            'show_type' => 'wenzi',
            'snumber_type' => 'Y',
            'search_no_type' =>'Y',
            'suchen_type' => 1,
            'suchen_word' => mb_convert_encoding($keyword, 'GB2312', 'UTF-8'),
            'suchen_match' => 'qx',
            'recordtype' => 'all',
            'searchtimes' => 1,
            'library_id' => 'all',
            'size' => 20,
            'curpage' => $page
        ];
        $content = Util::Curl($url, null, $params);
        $content = mb_convert_encoding($content, 'UTF-8', 'GB2312');
        preg_match_all('#<span class=.*?</span>#i', $content, $pageHtml);
        if (count($pageHtml[0]) !== 6) {
            return [
                'page' => ['total' => 0, 'current' => 0],
                'books' => []
            ];
        }
        $page = [
            'current' => strip_tags($pageHtml[0][2]),
            'total' => strip_tags($pageHtml[0][3])
        ];
        preg_match_all('#<tr height=[\s\S]+?class=\'td_color_[\s\S]+?</tr>#i', $content, $tr);
        $books = [];
        foreach ($tr[0] as $key => $value) {
            $book = Util::ParseTable($value)[0];
            $book = [$book[1], $book[2], $book[3], $book[6]];
            $books[] = $book;
        }
        return [
            'page' => $page,
            'books' => $books
        ];
    }
}

