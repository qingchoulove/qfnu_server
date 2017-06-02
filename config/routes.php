<?php

use controllers\HomeController;
use controllers\UrpController;

// 测试路由
$app->any('/', HomeController::class . ':index');

// 登录路由
$app->group('/login', function() {
    $this->post('', HomeController::class . ':login');
    $this->post('/captcha', HomeController::class . ':captcha');
});
// 教务服务路由
$app->group('/urp', function() {
    $this->post('/grade', UrpController::class . ':getGrade');
    $this->post('/grade/current', UrpController::class . ':getCurrentGrade');
    $this->post('/grade/fail', UrpController::class . ':getFailGrade');
    $this->post('/curriculum', UrpController::class . ':getCurriculum');
})->add(new middlewares\AuthMiddleware);