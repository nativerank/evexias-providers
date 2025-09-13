<?php

use App\Http\Controllers\WPCallbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/practice/wp-callback', [WPCallbackController::class, 'receiveCallback']);