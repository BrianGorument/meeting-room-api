<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;

Route::post('/auth/register', [AuthController::class,  'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    // Routes that require authentication can be placed here
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::get('/rooms/{id}', [RoomController::class, 'show']);
    Route::get('/rooms/{id}/availability', [RoomController::class, 'availability']);

    // Admin only
    Route::post('/rooms', [RoomController::class, 'create'])->middleware('admin');
    Route::put('/rooms/{id}', [RoomController::class, 'update'])->middleware('admin');
    Route::delete('/rooms/{id}', [RoomController::class, 'delete'])->middleware('admin');
});