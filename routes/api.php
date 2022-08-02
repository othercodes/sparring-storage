<?php

use App\Application\ResellersController;
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

Route::group([
    'prefix' => 'v1',
    'middleware' => ['client', 'account']
], function () {
    Route::get("health-check", function () {
        return response(200);
    });

    Route::group([
        'prefix' => 'resellers'
    ], function () {
        Route::get("/", [ResellersController::class, 'search']);
        Route::get("/{id}", [ResellersController::class, 'find']);
        Route::post("/", [ResellersController::class, 'create']);
        Route::patch("/{id}", [ResellersController::class, 'update']);
    });
});
