<?php

use controllers\HomeController;

$app->any('/', HomeController::class . ':index');
$app->post('/login', HomeController::class . ':login');
$app->get('/auth', HomeController::class . ':index')->add(new middlewares\AuthMiddleware);

