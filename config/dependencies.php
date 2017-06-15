<?php
// DIC configuration
use Slim\Container;

$container = $app->getContainer();

// error handler
$container['errorHandler'] = function () {
    return new common\exceptions\ErrorHandler();
};
$container['phpErrorHandler'] = function () {
    return new common\exceptions\ErrorHandler();
};
$container['notFoundHandler'] = function () {
    return new common\exceptions\RouteErrorHandler();
};
$container['notAllowedHandler'] = function () {
    return new \common\exceptions\RouteErrorHandler();
};

// view renderer
$container['renderer'] = function (Container $c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function (Container $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
// redis
$container['cache'] = function (Container $c) {
    $settings = $c->get('settings')['redis'];
    return new Predis\Client($settings);
};
