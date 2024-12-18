<?php

use App\Controllers\PublicPlaceController;

$app->get('/public-place', PublicPlaceController::class . ':getPublicPlacePg');
$app->get('/public-place/{slug}', PublicPlaceController::class . ':viewPublicPlace');
