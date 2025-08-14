<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcessSeeder extends Seeder
{
    public function run(): void
    {
        // Processos principais (nível 1)
        $process1 = DB::table('processes')->insertGetId([
            'area_id' => 1, 
            'name' => 'Folha de Pagamento',
            'description' => 'Processo de gestão da folha de pagamento',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        $process2 = DB::table('processes')->insertGetId([
            'area_id' => 1, 
            'name' => 'Gestão de Benefícios',
            'description' => 'Processo de gestão de benefícios dos funcionários',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        $process3 = DB::table('processes')->insertGetId([
            'area_id' => 2, 
            'name' => 'Triagem de Currículos',
            'description' => 'Processo de triagem e análise de currículos',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        $process4 = DB::table('processes')->insertGetId([
            'area_id' => 2, 
            'name' => 'Entrevistas',
            'description' => 'Processo de realização de entrevistas',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        // Subprocessos do Folha de Pagamento (nível 2)
        $subprocess1_1 = DB::table('processes')->insertGetId([
            'area_id' => 1,
            'parent_id' => $process1,
            'name' => 'Cálculo de Salários',
            'description' => 'Subprocesso para cálculo de salários e descontos',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        $subprocess1_2 = DB::table('processes')->insertGetId([
            'area_id' => 1,
            'parent_id' => $process1,
            'name' => 'Emissão de Holerites',
            'description' => 'Subprocesso para emissão e distribuição de holerites',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        // Sub-subprocessos do Cálculo de Salários (nível 3)
        $subsubprocess1_1_1 = DB::table('processes')->insertGetId([
            'area_id' => 1,
            'parent_id' => $subprocess1_1,
            'name' => 'Cálculo de Horas Extras',
            'description' => 'Subprocesso para cálculo de horas extras e adicional noturno',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        $subsubprocess1_1_2 = DB::table('processes')->insertGetId([
            'area_id' => 1,
            'parent_id' => $subprocess1_1,
            'name' => 'Cálculo de Descontos',
            'description' => 'Subprocesso para cálculo de INSS, IRRF e outros descontos',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        // Sub-sub-subprocessos (nível 4)
        $subsubsubprocess1_1_1_1 = DB::table('processes')->insertGetId([
            'area_id' => 1,
            'parent_id' => $subsubprocess1_1_1,
            'name' => 'Validação de Banco de Horas',
            'description' => 'Subprocesso para validação e controle do banco de horas',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        // Subprocessos do Gestão de Benefícios (nível 2)
        $subprocess2_1 = DB::table('processes')->insertGetId([
            'area_id' => 1,
            'parent_id' => $process2,
            'name' => 'Gestão de Vale Refeição',
            'description' => 'Subprocesso para gestão do vale refeição',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        $subprocess2_2 = DB::table('processes')->insertGetId([
            'area_id' => 1,
            'parent_id' => $process2,
            'name' => 'Gestão de Plano de Saúde',
            'description' => 'Subprocesso para gestão do plano de saúde',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        // Subprocessos do Triagem de Currículos (nível 2)
        $subprocess3_1 = DB::table('processes')->insertGetId([
            'area_id' => 2,
            'parent_id' => $process3,
            'name' => 'Análise de Experiência',
            'description' => 'Subprocesso para análise da experiência profissional',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        $subprocess3_2 = DB::table('processes')->insertGetId([
            'area_id' => 2,
            'parent_id' => $process3,
            'name' => 'Verificação de Referências',
            'description' => 'Subprocesso para verificação de referências profissionais',
            'type' => 'internal',
            'criticality' => 'low',
            'status' => 'active'
        ]);

        // Subprocessos do Entrevistas (nível 2)
        $subprocess4_1 = DB::table('processes')->insertGetId([
            'area_id' => 2,
            'parent_id' => $process4,
            'name' => 'Entrevista Técnica',
            'description' => 'Subprocesso para realização de entrevista técnica',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        $subprocess4_2 = DB::table('processes')->insertGetId([
            'area_id' => 2,
            'parent_id' => $process4,
            'name' => 'Entrevista Comportamental',
            'description' => 'Subprocesso para realização de entrevista comportamental',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        // Sub-subprocessos da Entrevista Técnica (nível 3)
        $subsubprocess4_1_1 = DB::table('processes')->insertGetId([
            'area_id' => 2,
            'parent_id' => $subprocess4_1,
            'name' => 'Avaliação de Código',
            'description' => 'Subprocesso para avaliação de código do candidato',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        $subsubprocess4_1_2 = DB::table('processes')->insertGetId([
            'area_id' => 2,
            'parent_id' => $subprocess4_1,
            'name' => 'Teste Prático',
            'description' => 'Subprocesso para realização de teste prático',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        // Processos com status inativo para teste
        $process5 = DB::table('processes')->insertGetId([
            'area_id' => 3, 
            'name' => 'Processo Inativo',
            'description' => 'Processo para testar filtros de status inativo',
            'type' => 'external',
            'criticality' => 'low',
            'status' => 'inactive'
        ]);

        $subprocess5_1 = DB::table('processes')->insertGetId([
            'area_id' => 3,
            'parent_id' => $process5,
            'name' => 'Subprocesso Inativo',
            'description' => 'Subprocesso inativo para teste',
            'type' => 'external',
            'criticality' => 'low',
            'status' => 'inactive'
        ]);

        // Mais processos para área 3
        $process6 = DB::table('processes')->insertGetId([
            'area_id' => 3, 
            'name' => 'Gestão de Projetos',
            'description' => 'Processo de gestão e acompanhamento de projetos',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        $subprocess6_1 = DB::table('processes')->insertGetId([
            'area_id' => 3,
            'parent_id' => $process6,
            'name' => 'Planejamento',
            'description' => 'Subprocesso de planejamento de projetos',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        $subprocess6_2 = DB::table('processes')->insertGetId([
            'area_id' => 3,
            'parent_id' => $process6,
            'name' => 'Execução',
            'description' => 'Subprocesso de execução de projetos',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        $subprocess6_3 = DB::table('processes')->insertGetId([
            'area_id' => 3,
            'parent_id' => $process6,
            'name' => 'Monitoramento',
            'description' => 'Subprocesso de monitoramento de projetos',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        // Processos para área 3 (Financeiro)
        $process7 = DB::table('processes')->insertGetId([
            'area_id' => 3, 
            'name' => 'Contabilidade',
            'description' => 'Processo de contabilidade e relatórios',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        $subprocess7_1 = DB::table('processes')->insertGetId([
            'area_id' => 3,
            'parent_id' => $process7,
            'name' => 'Lançamentos',
            'description' => 'Subprocesso de lançamentos contábeis',
            'type' => 'internal',
            'criticality' => 'high',
            'status' => 'active'
        ]);

        $subprocess7_2 = DB::table('processes')->insertGetId([
            'area_id' => 3,
            'parent_id' => $process7,
            'name' => 'Relatórios',
            'description' => 'Subprocesso de geração de relatórios',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        // Mais processos para área 1 (RH)
        $process8 = DB::table('processes')->insertGetId([
            'area_id' => 1, 
            'name' => 'Treinamento',
            'description' => 'Processo de treinamento e desenvolvimento',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        $subprocess8_1 = DB::table('processes')->insertGetId([
            'area_id' => 1,
            'parent_id' => $process8,
            'name' => 'Planejamento',
            'description' => 'Subprocesso de planejamento de treinamentos',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        $subprocess8_2 = DB::table('processes')->insertGetId([
            'area_id' => 1,
            'parent_id' => $process8,
            'name' => 'Execução',
            'description' => 'Subprocesso de execução de treinamentos',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);
    }
}
