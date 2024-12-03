<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::resource('subscribers', \App\Http\Controllers\SubscriberController::class);

Route::apiResource('subscribers', SubscriberController::class);
Route::apiResource('subscriptions', SubscriptionController::class);
Route::put('/subscribers/{subscriber}', [SubscriberController::class, 'update']);
