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

// API root route
Route::get('/', function () {
    return response()->json([
        'name' => 'ChainView API',
        'description' => 'API para gestão de processos e documentos',
        'version' => '1.0.0',
        'endpoints' => [
            'health' => '/api/health',
            'areas' => '/api/areas',
            'processes' => '/api/processes',
            'people' => '/api/people',
            'tools' => '/api/tools',
            'documents' => '/api/documents',
            'process_tree' => '/api/processes/{id}/tree',
            'area_processes_tree' => '/api/areas/{id}/processes/tree'
        ],
        'documentation' => 'Consulte a documentação para mais detalhes sobre cada endpoint'
    ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
});

// Health check route
Route::get('/health', function () {
    $health = [
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
        'identification' => [
            'name' => 'ChainView API',
            'description' => 'API para gestão de processos e documentos',
            'environment' => config('app.env', 'production'),
            'framework' => 'Laravel ' . app()->version(),
            'php_version' => PHP_VERSION,
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'hostname' => gethostname(),
            'uptime' => [
                'started_at' => now()->subSeconds(time() - $_SERVER['REQUEST_TIME'])->toISOString(),
                'duration' => time() - $_SERVER['REQUEST_TIME'] . ' seconds'
            ]
        ],
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
    return response()->json($health, $statusCode, ['Content-Type' => 'application/json; charset=utf-8']);
});

Route::apiResource('areas', AreaController::class);
Route::apiResource('processes', ProcessController::class);
Route::apiResource('people', PersonController::class);
Route::apiResource('tools', ToolController::class);
Route::apiResource('documents', DocumentController::class);

Route::get('processes/{id}/tree', [ProcessController::class, 'tree']);
Route::get('areas/{id}/processes/tree', [AreaController::class, 'processesTree']);
