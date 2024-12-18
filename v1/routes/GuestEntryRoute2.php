<?php

use App\Controllers\GuestEntryController2;

$app->get('/view-guests2', GuestEntryController2::class . ':viewGuests');
$app->get('/get-single-guest2/{id}', GuestEntryController2::class . ':getSingleGuest');
$app->get('/search-guest2', GuestEntryController2::class . ':searchGuest');
$app->post('/create-guest2', GuestEntryController2::class . ':createGuest');
$app->post('/edit-single-guest2/{id}', GuestEntryController2::class . ':editGuest');
$app->delete('/delete-guest2/{id}', GuestEntryController2::class . ':deleteGuest');
$app->get('/count-guests2', GuestEntryController2::class . ':countGuests');

$app->get('/view-guests-pg', GuestEntryController2::class . ':viewGuestsPg')->add($authenticate);
// $app->get('/view-guests-pg',GuestEntryController2::class . ':cobaGon')->add($authenticate);

// Define app routes
$app->get('/hello/{name}', function ($request, $response, $args) {
    return $response->write("Hello " . $args['name']);
});

$app->get('/page_verif_email', function ($request, $response, $args) {
    return page_verif_email();
});

$app->get('/test', function ($request, $response, $args) {

    // $response["success"] = true;
    // $response["code"] = 200;
    // $response["status"] = "OK";
    // $response["total"] = $total;
    // $response["page"] = (int)$page;
    // $response["pages"] = $pages;
    // $response["per_page"] = (int)$raw_per_page;
    // $response["data"] = $tmp;
    // $response["message"] = "Data found";

    $responseMessage = "fdf";
    $status = "OK";
    $message = "success";

    $rPagination["total"] = 150;
    $rPagination["page"] = (int)2;
    $rPagination["pages"] = 20;
    $rPagination["per_page"] = (int)12;

    $_arrayStatus = ["success" => true, "code" => 200, "status" => $status];
    $_arrayData = ["data" => $responseMessage, "message" => $message];
    $_arrayAll = array_merge($_arrayStatus, $rPagination, $_arrayData);

    $responseMessage = json_encode($_arrayAll);
    $response->getBody()->write($responseMessage);
    return $response->withHeader("Content-Type", "application/json")
        ->withStatus(200);
});
