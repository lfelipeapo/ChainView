<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\Auth\ApiAuthController;

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

// Auth Routes
Route::post('/auth/login', [ApiAuthController::class, 'login'])->name('auth.login');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [ApiAuthController::class, 'logout'])->name('auth.logout');
    Route::get('/auth/user', [ApiAuthController::class, 'user'])->name('auth.user');
});

// Public Routes (GET only)
Route::prefix('areas')->group(function () {
    Route::get('/tree', [AreaController::class, 'tree'])->name('areas.tree');
    Route::get('/{area}/processes/tree', [AreaController::class, 'processesTree'])->name('areas.processes.tree');
    Route::get('/', [AreaController::class, 'index'])->name('areas.index');
    Route::get('/{area}', [AreaController::class, 'show'])->name('areas.show');
});

Route::prefix('processes')->group(function () {
    Route::get('/stats', [ProcessController::class, 'stats'])->name('processes.stats');
    Route::get('/{process}/tree', [ProcessController::class, 'tree'])->name('processes.tree');
    Route::get('/', [ProcessController::class, 'index'])->name('processes.index');
    Route::get('/{process}', [ProcessController::class, 'show'])->name('processes.show');
});

// Protected Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Areas CRUD (POST, PUT, DELETE)
    Route::prefix('areas')->group(function () {
        Route::post('/', [AreaController::class, 'store'])->name('areas.store');
        Route::put('/{area}', [AreaController::class, 'update'])->name('areas.update');
        Route::delete('/{area}', [AreaController::class, 'destroy'])->name('areas.destroy');
    });

    // Processes CRUD (POST, PUT, DELETE)
    Route::prefix('processes')->group(function () {
        Route::post('/', [ProcessController::class, 'store'])->name('processes.store');
        Route::put('/{process}', [ProcessController::class, 'update'])->name('processes.update');
        Route::delete('/{process}', [ProcessController::class, 'destroy'])->name('processes.destroy');
    });
});

// Legacy Routes (for backward compatibility)
Route::apiResource('people', PersonController::class);
Route::apiResource('tools', ToolController::class);
Route::apiResource('documents', DocumentController::class);
