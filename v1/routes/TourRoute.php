<?php

use App\Controllers\TourController;
use App\Controllers\RegisLoginController;

$app->get('/tour', TourController::class . ':getTourPg');
$app->get('/tour/{slug}', TourController::class . ':viewTour');
$app->post('/regis2', RegisLoginController::class . ':regis2');

$app->post('/auth/login-social-media', RegisLoginController::class . ':LoginSocialMedia');
$app->post('/auth/signup', RegisLoginController::class . ':signUp');
$app->post('/auth/signin', RegisLoginController::class . ':signIn');

$app->get('/tour-visit-type', TourController::class . ':getTourVisitType');
$app->get('/tour-review-type', TourController::class . ':getTourReviewType');
$app->get('/tour-review-resource', TourController::class . ':getTourReviewResource');
$app->post('/tour-review-save', TourController::class . ':tourReviewSave');
$app->post('/tour-review-update', TourController::class . ':tourReviewUpdate');
$app->get('/tour-review', TourController::class . ':getTourReviewPg');
$app->post('/tour-review-helpful-save', TourController::class . ':tourReviewHelpfulSave');
$app->get('/tour-official-photos', TourController::class . ':getTourOfficialPhotosPg');
