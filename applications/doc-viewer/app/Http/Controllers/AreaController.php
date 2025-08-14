<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Http\Resources\AreaResource;
use App\Http\Requests\Area\StoreAreaRequest;
use App\Http\Requests\Area\UpdateAreaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Area::query()
            ->withCount('processes')
            ->orderByName();

        // Filtros
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Paginação
        $perPage = $request->get('per_page', 15);
        $areas = $query->paginate($perPage);

        return AreaResource::collection($areas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAreaRequest $request): JsonResponse
    {
        $area = Area::create($request->validated());

        return response()->json([
            'message' => 'Área criada com sucesso!',
            'data' => new AreaResource($area)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area): JsonResponse
    {
        $area->load(['processes' => function ($query) {
            $query->orderByName();
        }]);

        return response()->json([
            'data' => new AreaResource($area)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAreaRequest $request, Area $area): JsonResponse
    {
        $area->update($request->validated());

        return response()->json([
            'message' => 'Área atualizada com sucesso!',
            'data' => new AreaResource($area)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area): JsonResponse
    {
        // Verificar se há processos associados
        if ($area->processes()->count() > 0) {
            return response()->json([
                'message' => 'Não é possível remover uma área que possui processos associados.'
            ], 422);
        }

        $area->delete();

        return response()->json([
            'message' => 'Área removida com sucesso!'
        ]);
    }

    /**
     * Get areas in tree format.
     */
    public function tree(): JsonResponse
    {
        $areas = Area::withCount('processes')
            ->orderByName()
            ->get();

        return response()->json(AreaResource::collection($areas));
    }

    /**
     * Get processes tree for a specific area.
     */
    public function processesTree(Area $area): JsonResponse
    {
        $processes = $area->rootProcesses()
            ->with(['children' => function ($query) {
                $query->orderByName();
            }])
            ->orderByName()
            ->get();

        return response()->json([
            'area' => new AreaResource($area),
            'processes' => $processes->map(function ($process) {
                return $process->buildTree();
            })
        ]);
    }
}
