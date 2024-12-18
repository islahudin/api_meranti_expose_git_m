<?php

use App\Controllers\RestoController;

$app->get('/resto', RestoController::class . ':getRestoPg');
$app->get('/resto/{slug}', RestoController::class . ':viewResto');
