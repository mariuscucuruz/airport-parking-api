<?php

use App\Http\Controllers\AvailabilityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// maximum of 10 requests per minute seems reasonable
Route::prefix('check-availability')->middleware('throttle:10')->group(function () {
    Route::get('/', [AvailabilityController::class, 'check']);
    Route::get('/', [AvailabilityController::class, 'index']);
    Route::post('/', [AvailabilityController::class, 'book']);
    Route::put('/{booking}', [AvailabilityController::class, 'update']);
    Route::delete('/{booking}', [AvailabilityController::class, 'delete']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
