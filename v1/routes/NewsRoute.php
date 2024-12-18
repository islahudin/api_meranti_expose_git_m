<?php

use App\Controllers\NewsController;

$app->get('/news-media', NewsController::class . ':getNewsMedia');
$app->get('/news/{category}', NewsController::class . ':getNews');
$app->get('/news-local', NewsController::class . ':getNewsLocalPg');
$app->get('/news-local/{slug}', NewsController::class . ':viewNewsLocal');
