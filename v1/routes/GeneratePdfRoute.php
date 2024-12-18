<?php

use App\Controllers\GeneratePdfController;

$app->get('/generatepdftest', GeneratePdfController::class . ':generatePdfTest');
