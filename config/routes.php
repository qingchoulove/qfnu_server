<?php

use controllers\HomeController;

$app->any('/', HomeController::class . ':index');
$app->get('/home', HomeController::class . ':index');
$app->get('/auth', HomeController::class . ':index')->add(new middlewares\AuthMiddleware);
