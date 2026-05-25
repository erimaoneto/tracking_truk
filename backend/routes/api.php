<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\GpsLogController;
use App\Http\Controllers\Api\FuelLogController;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/trips/start', [TripController::class, 'startTrip']);
    Route::post('/trips/end/{id}', [TripController::class, 'endTrip']);
    
    Route::post('/gps/sync', [GpsLogController::class, 'sync']);
    Route::post('/fuel/report', [FuelLogController::class, 'report']);
});
