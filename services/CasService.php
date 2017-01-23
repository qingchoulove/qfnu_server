<?php
namespace services;

use common\Util;

/**
 * 曲阜师范大学信息门户
 **/
class CasService extends BaseService {
    const CACHE_PREFIX = 'cookie_';
    const AUTHSERVER_BASE = 'http://ids.qfnu.edu.cn/authserver/login?service=';
    const AUTHSERVER_HOME = 'http://my.qfnu.edu.cn/index.portal';

    public function login(string $user, int $password): bool {
        // 首先尝试cookie登录
        if ($this->cache->exists(self::CACHE_PREFIX . $user)) {
            $cookie = $this->cache->get(self::CACHE_PREFIX . $user);
            $content = Util::Curl(self::AUTHSERVER_HOME, $cookie);
        }
        if (strstr($content, 'welcomeMsg')) {
            $this->logger->info($user . ':cookie登录成功');
            return true;
        } else {
            $url = self::AUTHSERVER_BASE . self::AUTHSERVER_HOME;
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
                $this->cache->set(self::CACHE_PREFIX . $user, $cookie[0] . $casCookie[0]);
                return true;
            }
            return false;
        }
    }
}