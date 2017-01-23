<?php
// Routes

use controllers\HomeController;

$app->any('/', HomeController::class . ':index');
$app->get('/home', HomeController::class . ':index');