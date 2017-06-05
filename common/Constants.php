<?php

namespace common;

/**
* 常量表
*/
class Constants
{
    const START_DATE = '2017-02-20';
    const CAS_COOKIE_PREFIX = 'cas_cookie_';
    const AUTH_PREFIX = 'TOKEN:';
    const AUTHSERVER_TYPE_HOME = 0;
    const AUTHSERVER_TYPE_URP = 1;
    const AUTHSERVER_TYPE_LIB_RZ = 2;
    const AUTHSERVER_TYPE_LIB_QF = 3;

    public static $authServerTypeUrl = [
        self::AUTHSERVER_TYPE_HOME => 'http://my.qfnu.edu.cn/index.portal',
        self::AUTHSERVER_TYPE_URP => 'http://202.194.188.19/caslogin.jsp',
        self::AUTHSERVER_TYPE_LIB_RZ => 'http://219.218.26.4:85/opac_two/login/caslogin.jsp',
        self::AUTHSERVER_TYPE_LIB_QF => 'http://202.194.184.2:808/museweb/dzjs/caslogin.asp'
    ];

    const CAMPUS_QF = 1;
    const CAMPUS_RZ = 2;

    public static $buildings = [
        self::CAMPUS_QF => [
            0 => 'BH', 1 => 'Q01', 2 => 'Q02',
            3 => 'Q03', 4 => 'Q04', 5 => 'Q05',
            6 => 'Q06', 7 => 'Q07', 8 => 'Q08',
            9 => 'Q09', 10 => 'Q10', 11 => 'Q11',
            12 => 'Q12', 13 => 'Q13', 14 => 'Q14',
            15 => 'Q151', 16 => 'Q152', 17 => 'Q153',
            18 => 'Q154', 19 => 'Q16', 20 => 'Q21',
            21 => 'Q30', 22 => 'Q4731'
        ],
        self::CAMPUS_RZ => [
            0 => 'R01', 1 => 'R02', 2 => 'R03',
            3 => 'R04', 4 => 'R05', 5 => 'R06',
            6 => 'R07', 7 => 'R08', 8 => 'R14'
        ]
    ];
}
