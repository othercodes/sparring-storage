<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Parser;

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
], function(){
    Route::get("health-check", function() {
        return response(200);
    });

    Route::group([
        'prefix' => 'reseller'
    ], function() {
        Route::get("/", function() {
            return response()->json([
                "hey" => request()->user()
            ],200);
        });
    });
});
