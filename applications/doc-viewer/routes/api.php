<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ToolController;

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

// API Overview
Route::get('/', function () {
    return response()->json([
        'message' => 'ChainView API',
        'version' => '1.0.0',
        'description' => 'API para gestão de processos hierárquicos',
        'endpoints' => [
            'areas' => '/api/areas',
            'processes' => '/api/processes',
            'health' => '/api/health'
        ]
    ]);
});

// Health Check
Route::get('/health', function () {
    $dbStatus = 'connected';
    $dbCounts = [];
    
    try {
        $dbCounts = [
            'areas' => \App\Models\Area::count(),
            'processes' => \App\Models\Process::count(),
        ];
    } catch (\Exception $e) {
        $dbStatus = 'error: ' . $e->getMessage();
    }

    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'database' => [
            'status' => $dbStatus,
            'counts' => $dbCounts
        ],
        'system' => [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => config('app.env'),
            'timezone' => config('app.timezone')
        ],
        'identification' => [
            'name' => 'ChainView API',
            'description' => 'Sistema de Gestão de Processos Hierárquicos',
            'author' => 'Felipe Apo',
            'contact' => 'felipe@example.com'
        ]
    ]);
});

// Areas Routes
Route::prefix('areas')->group(function () {
    Route::get('/tree', [AreaController::class, 'tree'])->name('areas.tree');
    Route::get('/{area}/processes/tree', [AreaController::class, 'processesTree'])->name('areas.processes.tree');
    Route::apiResource('/', AreaController::class)->parameters(['' => 'area']);
});

// Processes Routes
Route::prefix('processes')->group(function () {
    Route::get('/stats', [ProcessController::class, 'stats'])->name('processes.stats');
    Route::get('/{process}/tree', [ProcessController::class, 'tree'])->name('processes.tree');
    Route::apiResource('/', ProcessController::class)->parameters(['' => 'process']);
});

// Legacy Routes (for backward compatibility)
Route::apiResource('people', PersonController::class);
Route::apiResource('tools', ToolController::class);
Route::apiResource('documents', DocumentController::class);
