<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => true, // Allow the web server to send the content-length header

        // Monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        // Wechat settings
        'wechat' => [
            'debug'  => true,
            'app_id' => 'wx4d7bb7798f32eead',//wx84a9581b9181bc8e//wx4d7bb7798f32eead
            'secret' => 'bd0e8e50a77117ff28b25e0351e6cb5f',//cbc791a2e11e1fd6953a2a5e813f8e86//bd0e8e50a77117ff28b25e0351e6cb5f
            'token'  => 'easywechat',
            'log' => [
              'level' => 'debug',
              'file'  => __DIR__. '/../logs/easywechat.log',
            ]
        ],
        // Db
        'db' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'zsqy',
            'username' => 'root',
            'password' => '123456',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
        'redis' => [
            'scheme'   => 'tcp',
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'database' => 0
        ]
    ],
];
