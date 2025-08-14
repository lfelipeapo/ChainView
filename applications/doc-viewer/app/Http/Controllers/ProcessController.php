<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Http\Resources\ProcessResource;
use App\Http\Requests\Process\StoreProcessRequest;
use App\Http\Requests\Process\UpdateProcessRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class ProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Process::query()
            ->with(['area', 'parent'])
            ->withCount('children')
            ->orderByName();

        // Filtros
        if ($request->has('area_id')) {
            $query->byArea($request->area_id);
        }

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('criticality')) {
            $query->byCriticality($request->criticality);
        }

        if ($request->has('type')) {
            $query->byType($request->type);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('root_only')) {
            $query->root();
        }

        if ($request->has('subprocesses_only')) {
            $query->subprocesses();
        }

        // Ordenação
        if ($request->has('sort_by')) {
            $direction = $request->get('sort_direction', 'asc');
            
            switch ($request->sort_by) {
                case 'name':
                    $query->orderByName($direction);
                    break;
                case 'criticality':
                    $query->orderByCriticality($direction);
                    break;
                case 'created_at':
                    $query->orderBy('created_at', $direction);
                    break;
            }
        }

        // Paginação
        $perPage = $request->get('per_page', 15);
        $processes = $query->paginate($perPage);

        return ProcessResource::collection($processes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProcessRequest $request): JsonResponse
    {
        $process = Process::create($request->validated());

        $process->load(['area', 'parent']);

        return response()->json([
            'message' => 'Processo criado com sucesso!',
            'data' => new ProcessResource($process)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Process $process): JsonResponse
    {
        $process->load(['area', 'parent', 'children' => function ($query) {
            $query->orderByName();
        }]);

        return response()->json([
            'data' => new ProcessResource($process)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProcessRequest $request, Process $process): JsonResponse
    {
        $process->update($request->validated());

        $process->load(['area', 'parent']);

        return response()->json([
            'message' => 'Processo atualizado com sucesso!',
            'data' => new ProcessResource($process)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Process $process): JsonResponse
    {
        // Verificar se há subprocessos
        if ($process->children()->count() > 0) {
            return response()->json([
                'message' => 'Não é possível remover um processo que possui subprocessos.'
            ], 422);
        }

        $process->delete();

        return response()->json([
            'message' => 'Processo removido com sucesso!'
        ]);
    }

    /**
     * Get process tree for a specific process.
     */
    public function tree(Process $process): JsonResponse
    {
        $process->load(['children' => function ($query) {
            $query->orderByName();
        }]);

        return response()->json([
            'data' => $process->buildTree()
        ]);
    }

    /**
     * Get statistics for processes.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Process::count(),
            'active' => Process::byStatus('active')->count(),
            'inactive' => Process::byStatus('inactive')->count(),
            'high_criticality' => Process::byCriticality('high')->count(),
            'medium_criticality' => Process::byCriticality('medium')->count(),
            'low_criticality' => Process::byCriticality('low')->count(),
            'root_processes' => Process::root()->count(),
            'subprocesses' => Process::subprocesses()->count(),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}
