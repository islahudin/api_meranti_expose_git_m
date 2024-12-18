<?php

use App\Controllers\UtilController;


$app->get('/banner', UtilController::class . ':getBanner');
$app->get('/main-menu', UtilController::class . ':getMainMenu');
// $app->get('/adm-subdivision/{id_regency}',UtilController::class . ':getDistrict');
$app->get('/district/{id_regency}', UtilController::class . ':getDistrict');
$app->get('/getapi', UtilController::class . ':getApi');
$app->get('/getApiSchool', UtilController::class . ':getApiSchool');
$app->get('/scripingMasjid', UtilController::class . ':scripingMasjid');
$app->get('/scripingMasjidDetail', UtilController::class . ':scripingMasjidDetail2');
$app->get('/getCurrency', UtilController::class . ':getCurrency');

$app->get('/getMe', \App\Controllers\UtilController::class . ':getMe')->add($authenticate);

$app->get('/getMe2', function ($request, $response, $args) {
    $user_id = $request->getAttribute('user_id');
    return $response->write("Hello " . $user_id);
})->add($authenticate);
