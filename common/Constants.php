<?php

namespace common;

/**
* 常量表
*/
class Constants
{
    const CAS_COOKIE_PREFIX = 'cas_cookie_';
    const AUTHSERVER_TYPE_HOME = 0;
    const AUTHSERVER_TYPE_URP = 1;

    public static $authServerTypeUrl = [
        self::AUTHSERVER_TYPE_HOME => 'http://my.qfnu.edu.cn/index.portal',
        self::AUTHSERVER_TYPE_URP => 'http://202.194.188.19/caslogin.jsp'
    ];
}