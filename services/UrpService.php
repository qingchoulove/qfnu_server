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
        $url = 'http://202.194.188.21/xjInfoAction.do?oper=xjxx';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        $fields = [
            'name' => '姓名',
            'campus' => '校区',
            'faculty' => '系所',
            'profession' => '专业',
            'clazz' => '班级'
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
        // 下载头像
        $url = 'http://202.194.188.21/xjInfoAction.do?oper=img';
        $portrait = Util::GetFile($url, $cookie);
        $fieldValue['portrait'] = base64_encode($portrait);
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
        $url = 'http://202.194.188.21/gradeLnAllAction.do?type=ln&oper=qbinfo';
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
        $model = [];
        foreach ($grade as $key => $value) {
            $list = [];
            foreach ($value as $k => $v) {
                $list[] = [
                    'lesson_id' => $v[0] . $v[1],
                    'name' => $v[2],
                    'credit' => $v[4],
                    'type' => $v[5],
                    'score' => $v[6]
                ];
            }
            $model[] = [
                'term' => $key,
                'list' => $list
            ];
        }
        return $model;
    }

    /**
     * 查询本学期成绩
     * @param string $userId
     * @return array
     * @throws Exception
     */
    public function getCurrentGrade(string $userId):array
    {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.21/bxqcjcxAction.do';
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
            $lesson = $value[0];
            $value = [
                'lesson_id' => $lesson[0] . trim($lesson[1]),
                'name' => $lesson[2],
                'credit' => $lesson[4],
                'type' => $lesson[5],
                'score' => $lesson[9],
                'rank' => $lesson[10]
            ];
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
        $url = 'http://202.194.188.21/gradeLnAllAction.do?type=ln&oper=bjg';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all("'<tr class=[^>]*?>.*?</tr>'si", $content, $table);
        $grade = [];
        foreach ($table[0] as $key => $value) {
            $grade[] = Util::ParseTable($value);
        }
        foreach ($grade as $key => &$value) {
            $lesson = $value[0];
            $value = [
                'lesson_id' => $lesson[0] . $lesson[1],
                'name' => $lesson[2],
                'credit' => $lesson[4],
                'type' => $lesson[5],
                'score' => $lesson[6],
            ];
        }
        return $grade;
    }

    /**
     * 自习室查询
     * @param string $userId
     * @param int $campus 校区
     * @param int $building 教学楼
     * @param int $week 周次
     * @param int $time 星期
     * @param int $session 节次
     * @return array
     * @throws Exception
     */
    public function getFreeRoom(string $userId, int $campus, int $building, int $week, int $time, int $session):array
    {
        $schoolYear = Util::SchoolYear();
        $paramKey = 'free_' . implode("_", [$schoolYear, $campus, $building, $week, $time, $session]);

        if ($this->cache->exists($paramKey)) {
            return unserialize($this->cache->get($paramKey));
        }
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.21/xszxcxAction.do?oper=xszxcx_lb';
        Util::Curl($url, $cookie);
        $url = 'http://202.194.188.21/xszxcxAction.do?oper=tjcx';
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
            $room[] = Util::ParseTable($value)[0];
        }
        foreach ($room as $key => $value) {
            $temp = [
                'campus' => $value[1],
                'building' => $value[2],
                'classroom' => $value[3],
                'type' => $value[4],
                'number' => $value[5]
            ];
            $room[$key] = $temp;
        }
        $this->cache->set($paramKey, serialize($room));
        return $room;
    }

    /**
     * 获取评教列表
     * @param  string
     * @return array
     */
    public function getEvaluationList(string $userId):array
    {
        $cookie = $this->getCookie($userId);
        $url = 'http://202.194.188.21/jxpgXsAction.do';
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
        $url = 'http://202.194.188.21/xkAction.do?actionType=6';
        $content = Util::Curl($url, $cookie);
        $content = iconv('GB2312', 'UTF-8', $content);
        preg_match_all('#<tr.*?onMouseOut[^>]*?>[\s\S]*?</tr>#i', $content, $table);
        $lessons = [];
        foreach ($table[0] as $key => $value) {
            $tr = reset(Util::ParseTable($value));
            if (count($tr) == 18) {
                $lesson = [
                    'name' => $tr[2],
                    'teacher' => str_replace('*', '', $tr[7]),
                    'range' => str_replace('周上', '', $tr[11]),
                    'week' => intval($tr[12]) - 1,
                    'session' => intval($tr[13]) - 1,
                    'num' => intval($tr[14]),
                    'building' => $tr[16],
                    'classroom' => $tr[17],
                    'type' => $tr[5],
                    'source' => $tr[4]
                ];
            } else {
                $lesson = array_slice($lessons, -1)[0];
                $lesson['range'] = str_replace('周上', '', $tr[0]);
                $lesson['week'] = intval($tr[1]) - 1;
                $lesson['session'] = intval($tr[2]) - 1;
                $lesson['num'] = intval($tr[3]);
                $lesson['building'] = $tr[5];
                $lesson['classroom'] = $tr[6];
            }
            $lessons[] = $lesson;
        }
        // 构造课表结构
        $curriculum = array_fill(0, 7, []);
        foreach ($curriculum as $key => $value) {
            $curriculum[$key] = array_fill(0, 11, []);
        }
        foreach ($lessons as $key => $value) {
            $value['class_id'] = md5(($value['name'] . $value['teacher']));
            $range = $value['range'];
            if ($range == '单周') {
                $range = range(1, 18, 2);
            } else if ($range == '双周') {
                $range = range(2, 18, 2);
            } else if (strstr($range, '-')) {
                $rangeArray = explode("-", $range);
                $range = range($rangeArray[0], $rangeArray[1]);
            } else {
                $range[] = $range;
            }
            $num = $value['num'];
            $week = $value['week'];
            $session = $value['session'];
            array_splice($value, 3, 3);
            $value['week'] = $range;

            for ($i = 0; $i < $num; $i++) {
                $curriculum[$week][$session + $i][] = $value;
            }
        }
        return $curriculum;
    }
}
