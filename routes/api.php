<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\SubscriptionController;

// Группа маршрутов, доступных только для роли "admin"
Route::group(['middleware' => ['keycloak:admin']], function () {
    Route::post('subscribers', [SubscriberController::class, 'store']); // Создание подписчика
    Route::put('subscribers/{subscriber}', [SubscriberController::class, 'update']); // Обновление подписчика
    Route::delete('subscribers/{subscriber}', [SubscriberController::class, 'destroy']); // Удаление подписчика
    Route::post('subscriptions', [SubscriptionController::class, 'store']); // Создание подписки
    Route::put('subscriptions/{subscription}', [SubscriptionController::class, 'update']); // Обновление подписки
    Route::delete('subscriptions/{subscription}', [SubscriptionController::class, 'destroy']); // Удаление подписки
});

// Группа маршрутов, доступных для роли "user"
Route::group(['middleware' => ['keycloak:user']], function () {
    Route::get('subscribers', [SubscriberController::class, 'index']); // Получение списка подписчиков
    Route::get('subscribers/{subscriber}', [SubscriberController::class, 'show']); // Просмотр подписчика
    Route::get('subscriptions', [SubscriptionController::class, 'index']); // Получение списка подписок
    Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show']); // Просмотр подписки
});
