<?php

use App\Controllers\MoreController;

$app->get('/getNews', MoreController::class . ':getNews');
