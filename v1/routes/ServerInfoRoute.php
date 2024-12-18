<?php

use App\Controllers\ServerInfoController;

$app->post('/check-server-info', ServerInfoController::class . ':checkServerInfo');
$app->post('/check-server-infoGet', ServerInfoController::class . ':checkServerInfoGet');
