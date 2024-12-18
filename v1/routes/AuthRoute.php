<?php

use App\Controllers\AuthController;

$app->group("/auth", function () use ($app) {

    // $app->post("/login", "AuthController:Login");
    $app->post('/login', AuthController::class . ':Login');
    // $app->post("/register", "AuthController:Register");
    $app->post('/register', AuthController::class . ':Register');
});
