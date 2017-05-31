<?php

use controllers\HomeController;
use controllers\UrpController;

// 测试路由
$app->any('/', HomeController::class . ':index');

// 登录路由
$app->group('/login', function() {
    $this->post('', HomeController::class . ':login');
    $this->post('/needCaptcha', HomeController::class . ':needCaptcha');
    $this->post('/captcha', HomeController::class . ':captcha');
});

$app->group('/urp', function() {
    $this->post('/grade', UrpController::class . ':getGrade');
    $this->post('/grade/current', UrpController::class . ':getCurrentGrade');
})->add(new middlewares\AuthMiddleware);