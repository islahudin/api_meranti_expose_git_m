<?php

use App\Controllers\InstitutionController;

$app->get('/institution/{id}', InstitutionController::class . ':getInstitutionDetail');
