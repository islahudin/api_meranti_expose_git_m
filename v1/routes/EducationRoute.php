<?php

use App\Controllers\EducationController;

$app->get('/ebook', EducationController::class . ':getEBookPg');
$app->get('/ebook/class/{slug}', EducationController::class . ':getEBookPg');
$app->get('/ebook/{slug}', EducationController::class . ':viewEBook');
$app->get('/class', EducationController::class . ':getClass');
