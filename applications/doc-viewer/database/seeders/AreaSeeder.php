<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('areas')->insert([
            ['name' => 'Recursos Humanos'],
            ['name' => 'Recrutamento'],
            ['name' => 'Financeiro'],
        ]);
    }
}
