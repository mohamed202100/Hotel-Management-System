<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\CustomerController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('reservations', ReservationController::class);
        Route::apiResource('rooms', RoomController::class);
    });

    Route::middleware('role:guest')->group(function () {
        Route::get('my-reservations', [ReservationController::class, 'myReservations']);
    });

    Route::apiResource('customers', CustomerController::class)->middleware('role:admin');
});
