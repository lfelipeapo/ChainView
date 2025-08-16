<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Area;
use App\Models\Process;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ProcessTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $area;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->area = Area::factory()->create();
    }

    /** @test */
    public function can_list_processes()
    {
        Process::factory()->count(3)->create(['area_id' => $this->area->id]);

        $response = $this->getJson('/api/processes');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'area_id',
                            'parent_id',
                            'name',
                            'description',
                            'type',
                            'criticality',
                            'status',
                            'created_at',
                            'updated_at',
                            'area',
                            'parent'
                        ]
                    ],
                    'timestamp'
                ]);
    }

    /** @test */
    public function can_list_processes_with_filters()
    {
        Process::factory()->create([
            'area_id' => $this->area->id,
            'name' => 'Processo Específico',
            'status' => 'active'
        ]);

        $response = $this->getJson('/api/processes?search=Específico&status=active');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function can_get_specific_process()
    {
        $process = Process::factory()->create(['area_id' => $this->area->id]);

        $response = $this->getJson("/api/processes/{$process->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            'id',
                            'area_id',
                            'parent_id',
                            'name',
                            'description',
                            'type',
                            'criticality',
                            'status',
                            'created_at',
                            'updated_at',
                            'area',
                            'parent',
                            'children'
                        ]
                    ],
                    'timestamp'
                ])
                ->assertJsonPath('data.data.id', $process->id);
    }

    /** @test */
    public function returns_404_for_nonexistent_process()
    {
        $response = $this->getJson('/api/processes/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function can_get_process_tree()
    {
        $parentProcess = Process::factory()->create([
            'area_id' => $this->area->id,
            'parent_id' => null
        ]);

        Process::factory()->create([
            'area_id' => $this->area->id,
            'parent_id' => $parentProcess->id
        ]);

        $response = $this->getJson("/api/processes/{$parentProcess->id}/tree");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            'id',
                            'name',
                            'children'
                        ]
                    ],
                    'timestamp'
                ]);
    }

    /** @test */
    public function can_get_process_stats()
    {
        Process::factory()->count(5)->create(['area_id' => $this->area->id]);

        $response = $this->getJson('/api/processes/stats');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            'total',
                            'active',
                            'inactive',
                            'high_criticality',
                            'medium_criticality',
                            'low_criticality',
                            'root_processes',
                            'subprocesses'
                        ]
                    ],
                    'timestamp'
                ]);
    }

    /** @test */
    public function can_create_process_when_authenticated()
    {
        Sanctum::actingAs($this->user);

        $processData = [
            'area_id' => $this->area->id,
            'name' => 'Novo Processo',
            'description' => 'Descrição do processo',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ];

        $response = $this->postJson('/api/processes', $processData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'area_id',
                        'name',
                        'description',
                        'type',
                        'criticality',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertJsonPath('data.name', 'Novo Processo');

        $this->assertDatabaseHas('processes', [
            'name' => 'Novo Processo',
            'area_id' => $this->area->id
        ]);
    }

    /** @test */
    public function can_create_subprocess_when_authenticated()
    {
        Sanctum::actingAs($this->user);

        $parentProcess = Process::factory()->create(['area_id' => $this->area->id]);

        $processData = [
            'area_id' => $this->area->id,
            'parent_id' => $parentProcess->id,
            'name' => 'Novo Subprocesso',
            'description' => 'Descrição do subprocesso',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ];

        $response = $this->postJson('/api/processes', $processData);

        $response->assertStatus(201)
                ->assertJsonPath('data.parent_id', $parentProcess->id);
    }

    /** @test */
    public function cannot_create_process_without_authentication()
    {
        $processData = [
            'area_id' => $this->area->id,
            'name' => 'Novo Processo'
        ];

        $response = $this->postJson('/api/processes', $processData);

        $response->assertStatus(401);
    }

    /** @test */
    public function cannot_create_process_without_required_fields()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/processes', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['area_id', 'name']);
    }

    /** @test */
    public function cannot_create_process_with_invalid_area()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/processes', [
            'area_id' => 999,
            'name' => 'Novo Processo'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['area_id']);
    }

    /** @test */
    public function cannot_create_process_with_invalid_parent()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/processes', [
            'area_id' => $this->area->id,
            'parent_id' => 999,
            'name' => 'Novo Processo'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['parent_id']);
    }

    /** @test */
    public function can_update_process_when_authenticated()
    {
        Sanctum::actingAs($this->user);

        $process = Process::factory()->create(['area_id' => $this->area->id]);

        $response = $this->putJson("/api/processes/{$process->id}", [
            'name' => 'Nome Atualizado',
            'description' => 'Descrição atualizada',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertJsonPath('data.name', 'Nome Atualizado');

        $this->assertDatabaseHas('processes', [
            'id' => $process->id,
            'name' => 'Nome Atualizado'
        ]);
    }

    /** @test */
    public function cannot_update_process_without_authentication()
    {
        $process = Process::factory()->create(['area_id' => $this->area->id]);

        $response = $this->putJson("/api/processes/{$process->id}", [
            'name' => 'Nome Atualizado'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function cannot_update_nonexistent_process()
    {
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/processes/999', [
            'name' => 'Nome Atualizado'
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function can_delete_process_when_authenticated()
    {
        Sanctum::actingAs($this->user);

        $process = Process::factory()->create(['area_id' => $this->area->id]);

        $response = $this->deleteJson("/api/processes/{$process->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message'
                ])
                ->assertJsonPath('message', 'Processo removido com sucesso!');

        $this->assertDatabaseMissing('processes', [
            'id' => $process->id
        ]);
    }

    /** @test */
    public function cannot_delete_process_without_authentication()
    {
        $process = Process::factory()->create(['area_id' => $this->area->id]);

        $response = $this->deleteJson("/api/processes/{$process->id}");

        $response->assertStatus(401);
    }

    /** @test */
    public function delete_process_with_children_is_recursive()
    {
        Sanctum::actingAs($this->user);

        $parentProcess = Process::factory()->create(['area_id' => $this->area->id]);
        
        Process::factory()->create([
            'area_id' => $this->area->id,
            'parent_id' => $parentProcess->id
        ]);

        $response = $this->deleteJson("/api/processes/{$parentProcess->id}");

        $response->assertStatus(200)
                ->assertJsonPath('message', 'Processo removido com sucesso!');

        $this->assertDatabaseMissing('processes', [ 'id' => $parentProcess->id ]);
    }

    /** @test */
    public function cannot_delete_nonexistent_process()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson('/api/processes/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function can_filter_processes_by_status()
    {
        Process::factory()->create([
            'area_id' => $this->area->id,
            'status' => 'active'
        ]);

        Process::factory()->create([
            'area_id' => $this->area->id,
            'status' => 'inactive'
        ]);

        $response = $this->getJson('/api/processes?status=active');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function can_filter_processes_by_criticality()
    {
        Process::factory()->create([
            'area_id' => $this->area->id,
            'criticality' => 'high'
        ]);

        Process::factory()->create([
            'area_id' => $this->area->id,
            'criticality' => 'medium'
        ]);

        $response = $this->getJson('/api/processes?criticality=high');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function can_filter_processes_by_area()
    {
        $area2 = Area::factory()->create();

        Process::factory()->create(['area_id' => $this->area->id]);
        Process::factory()->create(['area_id' => $area2->id]);

        $response = $this->getJson("/api/processes?area_id={$this->area->id}");

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }
}
