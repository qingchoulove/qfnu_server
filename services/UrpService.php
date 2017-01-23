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
        $cookie = $this->cache->get(Constants::CAS_COOKIE_PREFIX . Constants::AUTHSERVER_TYPE_URP .$userId);
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

        $table = explode("<td valign=",$content);
        foreach ($table as $key => $value) {
            $tableArr[] = $this->parseTable($value);
        }
        unset($tableArr[0]);
        foreach ($tableArr as $key => &$value) {
            foreach ($value as $k => &$v) {
                $v = (0 == $k) ? substr($v[0], 9) : $v;
                if (count($v) < 6 && 0 !== $k) {
                    unset($value[$k]);
                }
            }
        }
        return $tableArr;
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