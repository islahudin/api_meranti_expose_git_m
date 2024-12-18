<?php

use App\Controllers\SchoolController;

$app->get('/school', SchoolController::class . ':getSchoolPg');
$app->get('/school/level', SchoolController::class . ':getSchoolLevel');
$app->get('/school/status', SchoolController::class . ':getSchoolStatus');
$app->get('/school/{slug}', SchoolController::class . ':viewSchool');
