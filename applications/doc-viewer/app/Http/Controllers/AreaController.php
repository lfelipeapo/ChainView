<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Http\Resources\AreaResource;
use App\Http\Requests\Area\StoreAreaRequest;
use App\Http\Requests\Area\UpdateAreaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Áreas",
 *     description="Endpoints para gerenciamento de áreas"
 * )
 */
class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @OA\Get(
     *     path="/areas",
     *     summary="Listar áreas",
     *     tags={"Áreas"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Termo de busca",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de áreas",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
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
     * 
     * @OA\Post(
     *     path="/areas",
     *     summary="Criar área",
     *     tags={"Áreas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Nome da Área")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Área criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Área criada com sucesso!"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
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
     * 
     * @OA\Get(
     *     path="/areas/{id}",
     *     summary="Obter área específica",
     *     tags={"Áreas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da área",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados da área",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Área não encontrada"
     *     )
     * )
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
     * 
     * @OA\Put(
     *     path="/areas/{id}",
     *     summary="Atualizar área",
     *     tags={"Áreas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da área",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Nome da Área")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Área atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Área atualizada com sucesso!"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Área não encontrada"
     *     )
     * )
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
