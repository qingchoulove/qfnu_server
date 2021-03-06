<?php

use controllers\HomeController;
use controllers\UrpController;
use controllers\LibraryController;
use controllers\ClazzController;
use controllers\NoticeController;

$authMiddleware = new middlewares\AuthMiddleware();

// 测试路由
$app->any('/', HomeController::class . ':index');

// 登录路由
$app->group('/login', function () {
    $this->post('', HomeController::class . ':login');
    $this->post('/captcha', HomeController::class . ':captcha');
});
// 教务服务路由
$app->group('/urp', function () {
    $this->get('/grade', UrpController::class . ':getGrade');
    $this->get('/grade/current', UrpController::class . ':getCurrentGrade');
    $this->get('/grade/fail', UrpController::class . ':getFailGrade');
    $this->get('/curriculum', UrpController::class . ':getCurriculum');
    $this->post('/free-room', UrpController::class . ':getFreeRoom');
    $this->get('/info', UrpController::class . ':getInfo');
})->add($authMiddleware);
// 图书馆服务路由
$app->group('/lib', function () {
    $this->get('/borrow', LibraryController::class . ':getBorrowBooks');
    $this->post('/search', LibraryController::class . ':searchBook');
})->add($authMiddleware);
// 分班查询路由
$app->group('/clazz', function () {
    $this->post('/search', ClazzController::class . ':getClazz');
});
// 通知管理
$app->group('/notice', function() {
    $this->get('', NoticeController::class . ':getNoticeList');
    $this->post('/add', NoticeController::class . ':addNotice');
    $this->post('/update', NoticeController::class . ':updateNotice');
    $this->post('/delete', NoticeController::class . ':deleteNotice');
});
