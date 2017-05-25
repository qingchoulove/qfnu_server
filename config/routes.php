<?php

use controllers\HomeController;

// 测试路由
$app->any('/', HomeController::class . ':index');
$app->get('/auth', HomeController::class . ':index')->add(new middlewares\AuthMiddleware);
// 登录路由
$app->group('/login', function() {
    $this->post('', HomeController::class . ':login');
    $this->post('/needCaptcha', HomeController::class . ':needCaptcha');
    $this->post('/captcha', HomeController::class . ':captcha');
});

