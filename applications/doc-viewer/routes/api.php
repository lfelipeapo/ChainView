<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ToolController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('areas', AreaController::class);
Route::apiResource('processes', ProcessController::class);
Route::apiResource('people', PersonController::class);
Route::apiResource('tools', ToolController::class);
Route::apiResource('documents', DocumentController::class);

Route::get('processes/{id}/tree', [ProcessController::class, 'tree']);
Route::get('areas/{id}/processes/tree', [AreaController::class, 'processesTree']);
