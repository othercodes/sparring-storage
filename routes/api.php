<?php

use App\Application\CustomersController;
use App\Application\ResellersController;
use Illuminate\Support\Facades\Route;

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

    Route::group([
        'prefix' => 'customers'
    ], function () {
        Route::get("/", [CustomersController::class, 'search']);
        Route::get("/{id}", [CustomersController::class, 'find']);
        Route::post("/", [CustomersController::class, 'create']);
        Route::patch("/{id}", [CustomersController::class, 'update']);
    });
});
