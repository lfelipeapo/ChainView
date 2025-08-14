<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Process extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'area_id',
        'parent_id',
        'type',
        'criticality',
        'status',
        'tools',
        'responsible',
        'documentation'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com área
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relacionamento com processo pai
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Process::class, 'parent_id');
    }

    /**
     * Relacionamento com processos filhos
     */
    public function children(): HasMany
    {
        return $this->hasMany(Process::class, 'parent_id');
    }

    /**
     * Obter todos os ancestrais (pai, avô, etc.)
     */
    public function ancestors()
    {
        $ancestors = collect();
        $current = $this->parent;

        while ($current) {
            $ancestors->push($current);
            $current = $current->parent;
        }

        return $ancestors->reverse();
    }

    /**
     * Obter todos os descendentes (filhos, netos, etc.)
     */
    public function descendants()
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }

    /**
     * Verificar se é um processo raiz
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Verificar se é um subprocesso
     */
    public function isSubprocess(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Obter o nível de profundidade na hierarquia
     */
    public function getDepth(): int
    {
        return $this->ancestors()->count();
    }

    /**
     * Construir árvore hierárquica
     */
    public function buildTree(): array
    {
        $tree = $this->toArray();
        $tree['children'] = $this->children->map(function ($child) {
            return $child->buildTree();
        })->toArray();

        return $tree;
    }

    /**
     * Scope para filtrar por área
     */
    public function scopeByArea($query, $areaId)
    {
        return $query->where('area_id', $areaId);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por criticidade
     */
    public function scopeByCriticality($query, $criticality)
    {
        return $query->where('criticality', $criticality);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para buscar por nome
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Scope para processos raiz
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope para subprocessos
     */
    public function scopeSubprocesses($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Scope para ordenar por nome
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('name', $direction);
    }

    /**
     * Scope para ordenar por criticidade
     */
    public function scopeOrderByCriticality($query, $direction = 'asc')
    {
        return $query->orderBy('criticality', $direction);
    }
}
