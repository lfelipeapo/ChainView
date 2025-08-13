<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\DocumentController;

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

Route::apiResource('areas', AreaController::class)->only(['index', 'show']);
Route::apiResource('processes', ProcessController::class)->only(['index', 'show']);
Route::apiResource('documents', DocumentController::class)->only(['index', 'show']);
Route::get('areas/tree', [AreaController::class, 'tree']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('areas', AreaController::class)->except(['index', 'show']);
    Route::apiResource('processes', ProcessController::class)->except(['index', 'show']);
    Route::apiResource('documents', DocumentController::class)->except(['index', 'show']);
});
