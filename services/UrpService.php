<?php
namespace services;

use common\Util;
use common\Constants;
use Exception;
/**
* 教务服务
*/
class UrpService extends BaseService {

    /**
     * 获取cookie
     * @param  string
     * @return [type]
     */
    private function getCookie(string $userId):string {
        $account = $this->accountService->getAccountByUserId($userId);
        if (empty($account)) {
            throw new Exception("账户不存在", 1);
        }
        $password = $account['password'];
        $result = $this->casService->login($userId, $password, Constants::AUTHSERVER_TYPE_URP);
        if (!$result) {
            throw new Exception("登录失败", 1);
        }
        return $this->cache->get(Constants::CAS_COOKIE_PREFIX . Constants::AUTHSERVER_TYPE_URP .$userId);
    }

    /**
     * 获取学籍信息
     * @param  string
     * @return [type]
     */
    public function getUserInfo(string $userId): array {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/xjInfoAction.do?oper=xjxx';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        $fields = [
            'name' => '姓名',
            'faculty' => '系所',
            'profession' => '专业方向'
        ];
        foreach ($fields as $key => $value) {
            $value = substr($content, strpos($content, $value));
            $firstTd = strpos($value, '</td>') + 5;
            $value = substr($value, $firstTd, strpos($value, '</td>', $firstTd));
            $value = trim(strip_tags($value));
            $fieldValue[$key] = $value;
        }
        return $fieldValue;
    }

    /**
     * 获取全部成绩
     * @param  string
     * @return [type]
     */
    public function getAllGrade(string $userId):array {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/gradeLnAllAction.do?type=ln&oper=qbinfo';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);

        $table = explode('<td valign=', $content);
        foreach ($table as $key => $value) {
            $tableArr[] = $this->parseTable('<td valign=' . $value);
        }
        unset($tableArr[0]);
        foreach ($tableArr as $key => &$value) {
            foreach ($value as $k => &$v) {
                $v = (0 == $k) ? $v[0] : $v;
                if (count($v) < 6 && 0 !== $k) {
                    unset($value[$k]);
                }
            }
        }
        return $tableArr;
    }

    /**
     * 查询本学期成绩
     * @param  string
     * @return [type]
     */
    public function getCurrentGrade(string $userId): array {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/bxqcjcxAction.do';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        if (strstr($content, "开关已关闭")) {
            throw new Exception("成绩查询已关闭");
        } else if (strstr($content, '评教')) {
            throw new Exception("请评教后再查询");
        }
        preg_match_all("'<tr class=[^>]*?>.*?</tr>'si", $content, $table);
        foreach ($table[0] as $key => $value) {
            $tableArr[] = $this->parseTable($value);
        }
        if (empty($tableArr)) {
            return [];
        }
        foreach ($tableArr as $key => &$value) {
            $value = $value[0];
        }
        return $tableArr;
    }

    /**
     * 获取不及格成绩
     * @param  string
     * @return [type]
     */
    public function getFailingGrade(string $userId): array {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/gradeLnAllAction.do?type=ln&oper=bjg';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all("'<tr class=[^>]*?>.*?</tr>'si", $content, $table);
        foreach ($table[0] as $key => $value) {
            $tableArr[] = $this->parseTable($value);
        }
        foreach ($tableArr as $key => &$value) {
            $value = $value[0];
        }
        return $tableArr;
    }

    /**
     * TODO:空闲自习室查询
     * @param  string
     * @param  int
     * @param  int
     * @param  int
     * @param  int
     * @return [type]
     */
    public function getFreeRoom(string $userId, int $campus, int $building, int $week, int $time): array {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/xszxcxAction.do?oper=tjcx';
        $params = [
            'zxxnxq' => '2016-2017-2-1',
            'zxXaq' => '2',
            'zxJxl' => 'R01',
            'zxJc' => '1',
            'zxxq' => '1',
            'zxZc' => '1'
        ];
        $content = Util::Curl($url, $cookie, $params);
        $content = iconv('GB2312', 'UTF-8', $content);
        Util::Dump($content);
        return [];
    }

    /**
     * 获取评教列表
     * @param  string
     * @return [type]
     */
    public function getEvaluationList(string $userId): array {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/jxpgXsAction.do';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all('/\d+#@\d+#@\S+#@\S+#@\S+#@\d+/', $content, $list);
        foreach ($list[0] as $key => &$value) {
            $value[] = explode("#@", $value);
        }
        return $list[0];
    }

    /**
     * 解析table
     * @param  string
     * @return [type]
     */
    private function parseTable(string $html):array {

        $table = preg_replace("'<tr[^>]*?>'si", "", $html);
        $table = preg_replace("'<td[^>]*?>'si", "", $table);
        $table = str_replace("</tr>", "{tr}", $table);
        $table = str_replace("</td>", "{td}", $table);
        //去 HTML 标记
        $table = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $table);
        $table=str_replace("&nbsp;", "", $table);
        //去空白字符
        $table = preg_replace("'([\r\n])[\s]+'", "", $table);
        $table = str_replace(" ", "", $table);
        $table = str_replace(" ", "", $table);
        $table = explode('{tr}', $table);
        array_pop($table);
        foreach ($table as $key => $tr)
        {
            $td = explode('{td}', $tr);
            array_pop($td);
            $td = str_replace(" ", "", $td);
            $tdArr[] = $td;
        }
        return $tdArr;
    }
}