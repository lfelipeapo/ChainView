<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Http\Resources\ProcessResource;
use App\Http\Requests\Process\StoreProcessRequest;
use App\Http\Requests\Process\UpdateProcessRequest;
use Illuminate\Http\Request;
// use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Processos",
 *     description="Endpoints para gerenciamento de processos"
 * )
 */
class ProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @OA\Get(
     *     path="/processes",
     *     summary="Listar processos",
     *     tags={"Processos"},
     *     @OA\Parameter(
     *         name="area_id",
     *         in="query",
     *         description="ID da área",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status do processo",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Parameter(
     *         name="criticality",
     *         in="query",
     *         description="Criticidade do processo",
     *         required=false,
     *         @OA\Schema(type="string", enum={"low", "medium", "high"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Termo de busca",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="root_only",
     *         in="query",
     *         description="Apenas processos raiz",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="subprocesses_only",
     *         in="query",
     *         description="Apenas subprocessos",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de processos",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
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

        // Se não há per_page, retornar todos sem paginação
        if (!$request->has('per_page')) {
            $processes = $query->get();
            return response()->json($processes);
        }

        // Paginação
        $perPage = $request->get('per_page', 15);
        $processes = $query->paginate($perPage);

        return ProcessResource::collection($processes);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @OA\Post(
     *     path="/processes",
     *     summary="Criar processo",
     *     tags={"Processos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description","area_id","type","criticality","status"},
     *             @OA\Property(property="name", type="string", example="Nome do Processo"),
     *             @OA\Property(property="description", type="string", example="Descrição do processo"),
     *             @OA\Property(property="area_id", type="integer", example=1),
     *             @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
     *             @OA\Property(property="type", type="string", enum={"internal", "external"}, example="internal"),
     *             @OA\Property(property="criticality", type="string", enum={"low", "medium", "high"}, example="medium"),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
     *             @OA\Property(property="tools", type="string", nullable=true, example="Ferramentas utilizadas"),
     *             @OA\Property(property="responsible", type="string", nullable=true, example="Responsável"),
     *             @OA\Property(property="documentation", type="string", nullable=true, example="Link da documentação")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Processo criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Processo criado com sucesso!"),
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
     * 
     * @OA\Get(
     *     path="/processes/{id}",
     *     summary="Obter processo específico",
     *     tags={"Processos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do processo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do processo",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Processo não encontrado"
     *     )
     * )
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
     * 
     * @OA\Put(
     *     path="/processes/{id}",
     *     summary="Atualizar processo",
     *     tags={"Processos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do processo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description","area_id","type","criticality","status"},
     *             @OA\Property(property="name", type="string", example="Nome do Processo"),
     *             @OA\Property(property="description", type="string", example="Descrição do processo"),
     *             @OA\Property(property="area_id", type="integer", example=1),
     *             @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
     *             @OA\Property(property="type", type="string", enum={"internal", "external"}, example="internal"),
     *             @OA\Property(property="criticality", type="string", enum={"low", "medium", "high"}, example="medium"),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
     *             @OA\Property(property="tools", type="string", nullable=true, example="Ferramentas utilizadas"),
     *             @OA\Property(property="responsible", type="string", nullable=true, example="Responsável"),
     *             @OA\Property(property="documentation", type="string", nullable=true, example="Link da documentação")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Processo atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Processo atualizado com sucesso!"),
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
     *         description="Processo não encontrado"
     *     )
     * )
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
     * 
     * @OA\Delete(
     *     path="/processes/{id}",
     *     summary="Remover processo",
     *     tags={"Processos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do processo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Processo removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Processo removido com sucesso!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Não é possível remover processo com subprocessos"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Processo não encontrado"
     *     )
     * )
     */
    public function destroy(Process $process): JsonResponse
    {
        // Remover recursivamente o processo e todos os seus descendentes
        $process->deleteWithDescendants();

        return response()->json([
            'message' => 'Processo removido com sucesso!'
        ]);
    }

    /**
     * Get process tree for a specific process.
     * 
     * @OA\Get(
     *     path="/processes/{id}/tree",
     *     summary="Obter árvore de um processo",
     *     tags={"Processos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do processo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Árvore do processo",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Processo não encontrado"
     *     )
     * )
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
     * 
     * @OA\Get(
     *     path="/processes/stats",
     *     summary="Obter estatísticas dos processos",
     *     tags={"Processos"},
     *     @OA\Response(
     *         response=200,
     *         description="Estatísticas dos processos",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total", type="integer", example=25),
     *                 @OA\Property(property="active", type="integer", example=20),
     *                 @OA\Property(property="inactive", type="integer", example=5),
     *                 @OA\Property(property="high_criticality", type="integer", example=8),
     *                 @OA\Property(property="medium_criticality", type="integer", example=12),
     *                 @OA\Property(property="low_criticality", type="integer", example=5),
     *                 @OA\Property(property="root_processes", type="integer", example=15),
     *                 @OA\Property(property="subprocesses", type="integer", example=10)
     *             )
     *         )
     *     )
     * )
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
