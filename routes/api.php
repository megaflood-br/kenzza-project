<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BaseComWebhookController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Rota oficial para receber os pedidos do Base.com
Route::any('/webhooks/base-orders', [BaseComWebhookController::class, 'handle']);
