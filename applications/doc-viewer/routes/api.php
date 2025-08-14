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

// Health check route
Route::get('/health', function () {
    $health = [
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
        'services' => []
    ];

    // Check database connection
    try {
        \Illuminate\Support\Facades\DB::select('SELECT 1');
        $health['services']['database'] = [
            'status' => 'ok',
            'message' => 'Database connection successful'
        ];
    } catch (\Exception $e) {
        $health['services']['database'] = [
            'status' => 'error',
            'message' => 'Database connection failed: ' . $e->getMessage()
        ];
        $health['status'] = 'error';
    }

    // Check if we can query some basic data
    try {
        $areasCount = \Illuminate\Support\Facades\DB::table('areas')->count();
        $processesCount = \Illuminate\Support\Facades\DB::table('processes')->count();
        
        $health['services']['data'] = [
            'status' => 'ok',
            'message' => 'Data queries successful',
            'counts' => [
                'areas' => $areasCount,
                'processes' => $processesCount
            ]
        ];
    } catch (\Exception $e) {
        $health['services']['data'] = [
            'status' => 'error',
            'message' => 'Data queries failed: ' . $e->getMessage()
        ];
        $health['status'] = 'error';
    }

    $statusCode = $health['status'] === 'ok' ? 200 : 503;
    return response()->json($health, $statusCode);
});

Route::apiResource('areas', AreaController::class);
Route::apiResource('processes', ProcessController::class);
Route::apiResource('people', PersonController::class);
Route::apiResource('tools', ToolController::class);
Route::apiResource('documents', DocumentController::class);

Route::get('processes/{id}/tree', [ProcessController::class, 'tree']);
Route::get('areas/{id}/processes/tree', [AreaController::class, 'processesTree']);
