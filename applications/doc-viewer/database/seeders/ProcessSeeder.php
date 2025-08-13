<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcessSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('processes')->insert([
            ['area_id' => 1, 'name' => 'Folha de Pagamento'],
            ['area_id' => 1, 'name' => 'Gestão de Benefícios'],
            ['area_id' => 2, 'name' => 'Triagem de Currículos'],
            ['area_id' => 2, 'name' => 'Entrevistas'],
        ]);
    }
}
