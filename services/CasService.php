<?php
namespace services;

use common\Util;
use common\Constants;

/**
 * 曲阜师范大学信息门户
 */
class CasService extends BaseService
{

    const AUTHSERVER_BASE = 'http://ids.qfnu.edu.cn/authserver/login?service=';

    /**
     * 登录信息门户
     * @param  string 学号
     * @param  int 密码
     * @return bool 是否登录成功
     */
    public function loginCas(string $user, int $password): bool
    {
        $url = self::AUTHSERVER_BASE . Constants::$authServerTypeUrl[Constants::AUTHSERVER_TYPE_HOME];

        if ($this->cache->exists(Constants::CAS_COOKIE_PREFIX . $user)) {
            $content = Util::Curl($url, $this->cache->get('Constants::CAS_COOKIE_PREFIX . $user'));
        }
        if (strstr($content, 'welcomeMsg')) {
            return true;
        }
        // 抓取页面参数
        $content = Util::Curl($url);
        preg_match('/JSESSIONID=\S+;/', $content, $cookie);
        preg_match('/LT-\d+-[A-Za-z0-9]+-\d+/', $content, $ltKey);
        $data = [
            'username' => $user,
            'password' => $password,
            'lt' => $ltKey[0],
            'execution' => 'e1s1',
            '_eventId' => 'submit',
            'submit' => '登录'
        ];
        // 发送POST请求进行登陆
        $content = Util::Curl($url, $cookie[0], $data);
        // 截取跳转地址及cookie
        preg_match('/http:\/\/\S+/', $content, $location);
        preg_match('/CASTGC=\S+;/', $content, $casCookie);
        $content = Util::Curl($location[0], $casCookie[0]);
        preg_match('/http:\/\/\S+/', $content, $location);
        preg_match('/JSESSIONID=\S+;/', $content, $cookie);
        $content = Util::Curl($location[0], $cookie[0]);
        if (strstr($content, 'welcomeMsg')) {
            $this->cache->set(Constants::CAS_COOKIE_PREFIX . $user, $cookie[0] . $casCookie[0]);
            return true;
        }
        return false;
    }
    /**
     * 根据type登录不同系统
     * @param  string
     * @param  string
     * @param  int
     * @return bool
     */
    public function login(string $user, string $password, int $type): bool
    {
        if ($this->cache->exists(Constants::CAS_COOKIE_PREFIX . $type .$user)) {
            return true;
        }
        if (!$this->loginCas($user, $password)) {
            return false;
        }
        $casCookie = $this->cache->get(Constants::CAS_COOKIE_PREFIX . $user);
        $url = self::AUTHSERVER_BASE . Constants::$authServerTypeUrl[$type];
        $content = Util::Curl($url, $casCookie);
        preg_match('/http:\/\/\S+/', $content, $location);
        $content = Util::Curl($location[0]);
        preg_match('/http:\/\/\S+/', $content, $location);
        preg_match('/JSESSIONID=\S+;/', $content, $cookie);
        $content = Util::Curl($location[0], $cookie[0]);
        if ($type == Constants::AUTHSERVER_TYPE_URP) {
            $content = iconv('GB2312', 'UTF-8', $content);
            if (strpos($content, "学分制综合教务") == -1) {
                return  false;
            }
        }
        if ($type == Constants::AUTHSERVER_TYPE_LIB_RZ) {
            $content = iconv('GB2312', 'UTF-8', $content);
            if (strpos($content, 'reader/infoList') == -1) {
                return false;
            }
        }
        $this->cache->setex(Constants::CAS_COOKIE_PREFIX . $type .$user, 1800, $cookie[0]);
        return true;
    }
}
