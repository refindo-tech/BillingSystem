<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccurateController;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/', 'pages.home');

//admin page route
Route::view('/admin', 'pages.admin.index')->name('admin.index');
Route::view('/admin/server', 'pages.admin.server')->name('admin.server');
Route::view('/admin/mitra', 'pages.admin.mitra')->name('admin.mitra');
Route::view('/admin/olt', 'pages.admin.olt')->name('admin.olt');
Route::view('/admin/odp', 'pages.admin.odp')->name('admin.odp');
Route::view('/admin/voucher/', 'pages.admin.voucher.profile')->name('admin.voucher.index');
Route::view('/admin/voucher/profile', 'pages.admin.voucher.profile')->name('admin.voucher.profile');
Route::view('/admin/tiket', 'pages.admin.tiket')->name('admin.tiket');

//Accurate Endpoint
Route::get('/auth/accurate', [AccurateController::class, 'redirectToAccurate']);
Route::get('/auth/accurate/callback', [AccurateController::class, 'handleAccurateCallback']);
Route::get('/accurate/data', [AccurateController::class, 'getAccurateData']);

//Tripay Endpoint
Route::get('/tripay/channels', [TripayController::class, 'showPaymentChannels']);
Route::post('/tripay/transaction', [TripayController::class, 'createTransaction']);

//Mikrotik Endpoint
Route::get('/mikrotik/interfaces', [MikrotikController::class, 'showInterfaces']);
