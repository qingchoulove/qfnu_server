<?php
namespace services;

use common\Util;
use common\Constants;
use Exception;
use Predis\Client;

/**
 * 教务服务
 * @property AccountService $accountService
 * @property CasService $casService
 * @property Client $cache
*/
class UrpService extends BaseService
{

    /**
     * 获取cookie
     * @param string $userId
     * @return string
     * @throws Exception
     */
    private function getCookie( $userId)
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
    public function getUserInfo($userId)
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
        $fieldValue = [];
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
    public function getAllGrade($userId)
    {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/gradeLnAllAction.do?type=ln&oper=qbinfo';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);

        $table = explode('<td valign=', $content);
        $tableArr = [];
        foreach ($table as $key => $value) {
            $tableArr[] = Util::ParseTable('<td valign=' . $value);
        }
        unset($tableArr[0]);
        $grade = [];
        foreach ($tableArr as $key => $value) {
            $item = [];
            foreach ($value as $k => $v) {
                if (count($v) >= 6) {
                    $item[] = $v;
                }
            }
            $grade[$value[0][0]] = $item;
        }
        return $grade;
    }

    /**
     * 查询本学期成绩
     * @param string $userId
     * @return array
     * @throws Exception
     */
    public function getCurrentGrade($userId)
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
    public function getFailingGrade($userId)
    {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/gradeLnAllAction.do?type=ln&oper=bjg';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all("'<tr class=[^>]*?>.*?</tr>'si", $content, $table);
        $grade = [];
        foreach ($table[0] as $key => $value) {
            $grade[] = Util::ParseTable($value);
        }
        foreach ($grade as $key => &$value) {
            $value = $value[0];
        }
        return $grade;
    }

    /**
     * 自习室查询
     * @param string $userId
     * @param int $campus
     * @param int $building
     * @param int $week
     * @param int $time
     * @param int $session
     * @return array
     * @throws Exception
     */
    public function getFreeRoom($userId,$campus,  $building,  $week,$time,  $session)
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
        $room = [];
        foreach ($table[0] as $key => $value) {
            $room[] = Util::ParseTable($value);
        }
        foreach ($room as $key => &$value) {
            $value = $value[0];
        }
        $this->cache->set($paramKey, serialize($room));
        return $room;
    }

    /**
     * 获取评教列表
     * @param  string
     * @return array
     */
    public function getEvaluationList($userId)
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
    public function getCurriculum($userId)
    {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.19/xkAction.do?actionType=6';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all('#<tr bgcolor=[^>]*?>[\s\S]*?</tr>#i', $content, $table);
        $curriculum = [];
        foreach ($table[0] as $key => $value) {
            $tr = reset(Util::ParseTable($value));
            if ($key % 4 == 0) {
                $tr = array_slice($tr, 2);
            } else if ($key == 3 || $key == 7) {
                $tr = array_slice($tr, 1, 7);
            } else {
                $tr = array_slice($tr, 1);
            }
            $curriculum[] = $tr;
        }
        return $curriculum;
    }
}
