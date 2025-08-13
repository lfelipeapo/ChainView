<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('areas')->insert([
            ['id' => 1, 'name' => 'Recursos Humanos'],
            ['id' => 2, 'name' => 'Recrutamento'],
            ['id' => 3, 'name' => 'Financeiro'],
        ]);
    }
}
