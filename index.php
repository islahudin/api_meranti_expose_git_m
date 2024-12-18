<?php

require 'vendor/autoload.php';

$app = new \Slim\App();

// Route dasar
$app->get('/', function ($request, $response, $args) {
    return $response->write("Hello, World!");
});

$app->run();
