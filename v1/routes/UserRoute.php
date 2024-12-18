<?php

use App\Controllers\UserController;

$app->post('/check-account', UserController::class . ':checkAccount');
$app->get('/notification', UserController::class . ':getNotificationPg');
