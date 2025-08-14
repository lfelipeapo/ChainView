<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com processos
     */
    public function processes(): HasMany
    {
        return $this->hasMany(Process::class);
    }

    /**
     * Obter processos raiz (sem parent_id)
     */
    public function rootProcesses(): HasMany
    {
        return $this->hasMany(Process::class)->whereNull('parent_id');
    }

    /**
     * Obter estatísticas da área
     */
    public function getStats(): array
    {
        $totalProcesses = $this->processes()->count();
        $activeProcesses = $this->processes()->where('status', 'active')->count();
        $highCriticality = $this->processes()->where('criticality', 'high')->count();

        return [
            'total_processes' => $totalProcesses,
            'active_processes' => $activeProcesses,
            'high_criticality' => $highCriticality,
            'inactive_processes' => $totalProcesses - $activeProcesses
        ];
    }

    /**
     * Scope para buscar por nome
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Scope para ordenar por nome
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('name', $direction);
    }
}
