<?php

use App\Http\Controllers\AvailabilityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return App::version();
});

// maximum of 10 requests per minute seems reasonable
Route::prefix('check-availability')->middleware(['api', 'throttle:10'])->group(function () {
    Route::get('/', [AvailabilityController::class, 'check']);
    Route::post('/', [AvailabilityController::class, 'book']);
    Route::put('/{booking}', [AvailabilityController::class, 'update']);
    Route::delete('/{booking}', [AvailabilityController::class, 'delete']);
});

// handling authentication is out of the scope of this test
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
