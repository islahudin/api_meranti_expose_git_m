<?php

use App\Controllers\MosqueController;

$app->get('/mosque', MosqueController::class . ':getMosquePg');
$app->get('/mosque/{slug}', MosqueController::class . ':viewMosque');
