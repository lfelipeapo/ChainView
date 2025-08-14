<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcessSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('processes')->insert([
            [
                'area_id' => 1, 
                'name' => 'Folha de Pagamento',
                'description' => 'Processo de gestão da folha de pagamento',
                'type' => 'internal',
                'criticality' => 'high',
                'status' => 'active'
            ],
            [
                'area_id' => 1, 
                'name' => 'Gestão de Benefícios',
                'description' => 'Processo de gestão de benefícios dos funcionários',
                'type' => 'internal',
                'criticality' => 'medium',
                'status' => 'active'
            ],
            [
                'area_id' => 2, 
                'name' => 'Triagem de Currículos',
                'description' => 'Processo de triagem e análise de currículos',
                'type' => 'internal',
                'criticality' => 'medium',
                'status' => 'active'
            ],
            [
                'area_id' => 2, 
                'name' => 'Entrevistas',
                'description' => 'Processo de realização de entrevistas',
                'type' => 'internal',
                'criticality' => 'high',
                'status' => 'active'
            ],
        ]);
    }
}
