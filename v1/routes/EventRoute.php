<?php

use App\Controllers\EventController;

$app->get('/event', EventController::class . ':getEventPg');
$app->get('/event/{slug}', EventController::class . ':viewEvent');
$app->get('/event-group', EventController::class . ':getEventGroup');
