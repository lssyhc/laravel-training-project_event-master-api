<?php

use App\Http\Controllers\Api\AttendeeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('events', EventController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum', 'throttle:api')->group(function () {
    Route::get('/user', [AuthController::class, 'userDetails']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('events', EventController::class)->except(['index', 'show']);
    Route::apiResource('events.attendees', AttendeeController::class)
        ->only(['index', 'show', 'store', 'destroy'])
        ->scoped()
        ->shallow();
    Route::apiResource('events.reviews', ReviewController::class)
        ->scoped()
        ->shallow();
});
