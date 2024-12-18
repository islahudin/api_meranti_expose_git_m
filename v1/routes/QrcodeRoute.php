<?php

use App\Controllers\GenerateQrcodeController;

$app->get('/generateqrcodetest', GenerateQrcodeController::class . ':generateQrcodeTest');
