<?php
// Routes

use controllers\HomeController;

$app->get('/', HomeController::class . ':index');
$app->get('/home', HomeController::class . ':index');