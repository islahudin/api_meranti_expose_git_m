<?php

use App\Controllers\MydomController;

$app->get('/domNews', MydomController::class . ':domNews2');
$app->get('/domNewsDetail', MydomController::class . ':domNewsDetail');
