<?php

use App\Http\Controllers\Api\AttendeeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'userDetails']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('events', EventController::class);
    Route::apiResource('events.attendees', AttendeeController::class)->only(['index', 'show', 'store', 'destroy'])->scoped()->shallow();
});
