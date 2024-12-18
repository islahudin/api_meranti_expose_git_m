<?php

use App\Controllers\MarketController;

$app->get('/market', MarketController::class . ':getMarketPg');
$app->get('/market/{slug}', MarketController::class . ':viewMarket');
