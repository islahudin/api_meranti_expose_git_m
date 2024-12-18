<?php

use App\Controllers\GuestEntryController;

// $app->post("/create-guest", "GuestEntryController:createGuest");
$app->post('/create-guest', GuestEntryController::class . ':createGuest');

// $app->get("/view-guests" ,"GuestEntryController:viewGuests");
$app->get('/view-guests', GuestEntryController::class . ':viewGuests');

// $app->get("/get-single-guest/{id}", "GuestEntryController:getSingleGuest");
$app->get('/get-single-guest/{id}', GuestEntryController::class . ':getSingleGuest');

// $app->patch("/edit-single-guest/{id}", "GuestEntryController:editGuest");
$app->get('/edit-single-guest/{id}', GuestEntryController::class . ':editGuest');

// $app->delete("/delete-guest/{id}", "GuestEntryController:deleteGuest");
$app->get('/delete-guest/{id}', GuestEntryController::class . ':deleteGuest');

// $app->get("/count-guests", "GuestEntryController:countGuests");
$app->get('/count-guests', GuestEntryController::class . ':countGuests');
