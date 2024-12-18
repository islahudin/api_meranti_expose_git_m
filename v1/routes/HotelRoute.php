<?php

use App\Controllers\HotelController;

$app->get('/hotel', HotelController::class . ':getHotelPg');
$app->get('/hotel/{slug}', HotelController::class . ':viewHotel');
