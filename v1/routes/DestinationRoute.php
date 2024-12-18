<?php

use App\Controllers\DestinationController;

$app->get('/destination/like', DestinationController::class . ':getDestinationLikePg');
$app->post('/destination/like-save', DestinationController::class . ':DestinationLikeSave');
$app->get('/destination/find', DestinationController::class . ':getDestinationFind');
