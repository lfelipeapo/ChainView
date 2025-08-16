<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Area;
use App\Models\Process;

class CheckDatabaseStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar estatísticas do banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== ESTATÍSTICAS DO BANCO DE DADOS ===');
        
        // Total de áreas
        $totalAreas = Area::count();
        $this->info("Total de Áreas: {$totalAreas}");
        
        // Total de processos
        $totalProcesses = Process::count();
        $this->info("Total de Processos: {$totalProcesses}");
        
        // Processos por área
        $this->info("\nProcessos por Área:");
        $areas = Area::withCount('processes')->get();
        
        foreach ($areas as $area) {
            $this->line("- {$area->name}: {$area->processes_count} processos");
        }
        
        // Processos por nível hierárquico (parent_id)
        $this->info("\nProcessos por Nível Hierárquico:");
        $rootProcesses = Process::whereNull('parent_id')->count();
        $childProcesses = Process::whereNotNull('parent_id')->count();
        $this->line("- Processos Raiz (sem pai): {$rootProcesses}");
        $this->line("- Subprocessos (com pai): {$childProcesses}");
        
        // Processos por criticidade
        $this->info("\nProcessos por Criticidade:");
        $processesByCriticality = Process::selectRaw('criticality, COUNT(*) as count')
            ->groupBy('criticality')
            ->get();
            
        foreach ($processesByCriticality as $criticality) {
            $this->line("- {$criticality->criticality}: {$criticality->count} processos");
        }
        
        // Processos por status
        $this->info("\nProcessos por Status:");
        $processesByStatus = Process::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
            
        foreach ($processesByStatus as $status) {
            $this->line("- {$status->status}: {$status->count} processos");
        }
        
        $this->info("\n=== FIM DAS ESTATÍSTICAS ===");
    }
}
