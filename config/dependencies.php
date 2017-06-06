<?php
// DIC configuration

$container = $app->getContainer();

// error handler
$container['errorHandler'] = function () {
    return new common\exception\ErrorHandler;
};
$container['phpErrorHandler'] = function () {
    return new common\exception\ErrorHandler;
};

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
// redis
$container['cache'] = function ($c) {
    $settings = $c->get('settings')['redis'];
    return new Predis\Client($settings);
};
