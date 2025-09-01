<?php

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// routes/api.php (setelah install:api)
Route::post('/midtrans/webhook', [PaymentController::class, 'webhook']);