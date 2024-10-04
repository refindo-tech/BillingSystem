<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccurateController;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/', 'pages.home');

//Accurate Endpoint
Route::get('/auth/accurate', [AccurateController::class, 'redirectToAccurate']);
Route::get('/auth/accurate/callback', [AccurateController::class, 'handleAccurateCallback']);
Route::get('/accurate/data', [AccurateController::class, 'getAccurateData']);

//Tripay Endpoint
Route::get('/tripay/channels', [TripayController::class, 'showPaymentChannels']);
Route::post('/tripay/transaction', [TripayController::class, 'createTransaction']);

//Mikrotik Endpoint
Route::get('/mikrotik/interfaces', [MikrotikController::class, 'showInterfaces']);
