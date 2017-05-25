<?php
namespace services;

use common\Util;
use common\Constants;
use Exception;

/**
 * 教务服务
 * @property AccountService $accountService
 * @property CasService $casService
*/
class UrpService extends BaseService
{

    /**
     * 获取cookie
     * @param  string
     * @return string
     */
    private function getCookie(string $userId):string
    {
        $password = $this->accountService->getPasswordByUserId($userId);
        $result = $this->casService->login($userId, $password, Constants::AUTHSERVER_TYPE_URP);
        if (!$result) {
            throw new Exception("登录失败");
        }
        return $this->cache->get(Constants::CAS_COOKIE_PREFIX . Constants::AUTHSERVER_TYPE_URP . '_'  . $userId);
    }

    /**
     * 获取学籍信息
     * @param  string
     * @return array
     */
    public function getUserInfo(string $userId):array
    {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/xjInfoAction.do?oper=xjxx';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        $fields = [
            'name' => '姓名',
            'campus' => '校区',
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
        $fieldValue['campus'] = $fieldValue['campus'] === '曲阜' ? Constants::CAMPUS_QF : Constants::CAMPUS_RZ;
        return $fieldValue;
    }

    /**
     * 获取全部成绩
     * @param  string
     * @return array
     */
    public function getAllGrade(string $userId):array
    {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/gradeLnAllAction.do?type=ln&oper=qbinfo';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);

        $table = explode('<td valign=', $content);
        foreach ($table as $key => $value) {
            $tableArr[] = Util::ParseTable('<td valign=' . $value);
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
     * @return array
     */
    public function getCurrentGrade(string $userId):array
    {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/bxqcjcxAction.do';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        if (strstr($content, "开关已关闭")) {
            throw new Exception("成绩查询已关闭");
        } elseif (strstr($content, '评教')) {
            throw new Exception("请评教后再查询");
        }
        preg_match_all("'<tr class=[^>]*?>.*?</tr>'si", $content, $table);
        foreach ($table[0] as $key => $value) {
            $tableArr[] = Util::ParseTable($value);
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
     * @return array
     */
    public function getFailingGrade(string $userId):array
    {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/gradeLnAllAction.do?type=ln&oper=bjg';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all("'<tr class=[^>]*?>.*?</tr>'si", $content, $table);
        foreach ($table[0] as $key => $value) {
            $tableArr[] = Util::ParseTable($value);
        }
        foreach ($tableArr as $key => &$value) {
            $value = $value[0];
        }
        return $tableArr;
    }

    /**
     * 空闲自习室查询
     * @param  string 学号
     * @param  int 校区
     * @param  int 教学楼
     * @param  int 周次
     * @param  int 星期
     * @param  int 节次
     * @return array
     */
    public function getFreeRoom(string $userId, int $campus, int $building, int $week, int $time, int $session):array
    {
        $schoolYear = Util::SchoolYear();
        $paramKey = 'free_' . implode("_", [$schoolYear, $campus, $building, $week, $time, $session]);

        if ($this->cache->exists($paramKey)) {
            return unserialize($this->cache->get($paramKey));
        }
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/xszxcxAction.do?oper=xszxcx_lb';
        Util::Curl($url, $cookie);
        $url = 'http://202.194.188.19/xszxcxAction.do?oper=tjcx';
        $params = [
            'zxxnxq' => $schoolYear,
            'zxXaq' => $campus,
            'zxJxl' => Constants::$buildings[$campus][$building],
            'zxJc' => $session,
            'zxxq' => $time,
            'zxZc' => $week,
            'pageSize' => '500'
        ];
        $content = Util::Curl($url, $cookie, $params);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all("'<tr class=[^>]*?>.*?</tr>'si", $content, $table);
        foreach ($table[0] as $key => $value) {
            $tableArr[] = Util::ParseTable($value);
        }
        foreach ($tableArr as $key => &$value) {
            $value = $value[0];
        }
        $this->cache->set($paramKey, serialize($tableArr));
        return $tableArr;
    }

    /**
     * 获取评教列表
     * @param  string
     * @return array
     */
    public function getEvaluationList(string $userId):array
    {
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
     * 获取课程表
     * @param  string
     * @return array
     */
    public function getCurriculum(string $userId):array
    {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/xkAction.do?actionType=6';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        $tableArr = Util::ParseTable($content);
        //TODO: 待完善
        Util::Dump($tableArr);
        return [];
    }
}
