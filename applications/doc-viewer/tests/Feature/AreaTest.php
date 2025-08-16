<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Area;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AreaTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário para autenticação
        $this->user = User::factory()->create();
    }

    /** @test */
    public function can_list_areas()
    {
        // Criar algumas áreas
        Area::factory()->count(3)->create();

        $response = $this->getJson('/api/areas');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'name',
                                'stats',
                                'processes_count',
                                'created_at',
                                'updated_at'
                            ]
                        ],
                        'links',
                        'meta'
                    ],
                    'timestamp'
                ]);
    }

    /** @test */
    public function can_list_areas_with_search()
    {
        Area::factory()->create(['name' => 'Recursos Humanos']);
        Area::factory()->create(['name' => 'Financeiro']);

        $response = $this->getJson('/api/areas?search=Recursos');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data.data');
    }

    /** @test */
    public function can_list_areas_with_pagination()
    {
        Area::factory()->count(20)->create();

        $response = $this->getJson('/api/areas?per_page=5');

        $response->assertStatus(200)
                ->assertJsonPath('data.meta.per_page', 5)
                ->assertJsonPath('data.meta.total', 20);
    }

    /** @test */
    public function can_get_specific_area()
    {
        $area = Area::factory()->create();

        $response = $this->getJson("/api/areas/{$area->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            'id',
                            'name',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'timestamp'
                ])
                ->assertJsonPath('data.data.id', $area->id);
    }

    /** @test */
    public function returns_404_for_nonexistent_area()
    {
        $response = $this->getJson('/api/areas/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function can_get_areas_tree()
    {
        Area::factory()->count(3)->create();

        $response = $this->getJson('/api/areas/tree');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'timestamp'
                ]);
    }

    /** @test */
    public function can_get_processes_tree_for_area()
    {
        $area = Area::factory()->create();

        $response = $this->getJson("/api/areas/{$area->id}/processes/tree");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'area' => [
                            'id',
                            'name'
                        ],
                        'processes'
                    ],
                    'timestamp'
                ]);
    }

    /** @test */
    public function can_create_area_when_authenticated()
    {
        Sanctum::actingAs($this->user);

        $areaData = [
            'name' => 'Nova Área de Teste'
        ];

        $response = $this->postJson('/api/areas', $areaData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertJsonPath('data.name', 'Nova Área de Teste');

        $this->assertDatabaseHas('areas', [
            'name' => 'Nova Área de Teste'
        ]);
    }

    /** @test */
    public function cannot_create_area_without_authentication()
    {
        $areaData = [
            'name' => 'Nova Área de Teste'
        ];

        $response = $this->postJson('/api/areas', $areaData);

        $response->assertStatus(401);
    }

    /** @test */
    public function cannot_create_area_without_name()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/areas', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function cannot_create_area_with_duplicate_name()
    {
        Sanctum::actingAs($this->user);

        Area::factory()->create(['name' => 'Área Duplicada']);

        $response = $this->postJson('/api/areas', [
            'name' => 'Área Duplicada'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function can_update_area_when_authenticated()
    {
        Sanctum::actingAs($this->user);

        $area = Area::factory()->create(['name' => 'Nome Antigo']);

        $response = $this->putJson("/api/areas/{$area->id}", [
            'name' => 'Nome Novo'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertJsonPath('data.name', 'Nome Novo');

        $this->assertDatabaseHas('areas', [
            'id' => $area->id,
            'name' => 'Nome Novo'
        ]);
    }

    /** @test */
    public function cannot_update_area_without_authentication()
    {
        $area = Area::factory()->create();

        $response = $this->putJson("/api/areas/{$area->id}", [
            'name' => 'Nome Novo'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function cannot_update_nonexistent_area()
    {
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/areas/999', [
            'name' => 'Nome Novo'
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function can_delete_area_when_authenticated()
    {
        Sanctum::actingAs($this->user);

        $area = Area::factory()->create();

        $response = $this->deleteJson("/api/areas/{$area->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message'
                ])
                ->assertJsonPath('message', 'Área removida com sucesso!');

        $this->assertDatabaseMissing('areas', [
            'id' => $area->id
        ]);
    }

    /** @test */
    public function cannot_delete_area_without_authentication()
    {
        $area = Area::factory()->create();

        $response = $this->deleteJson("/api/areas/{$area->id}");

        $response->assertStatus(401);
    }

    /** @test */
    public function cannot_delete_area_with_associated_processes()
    {
        Sanctum::actingAs($this->user);

        $area = Area::factory()->create();
        
        // Criar um processo associado à área
        $area->processes()->create([
            'name' => 'Processo Teste',
            'description' => 'Descrição do processo',
            'type' => 'internal',
            'criticality' => 'medium',
            'status' => 'active'
        ]);

        $response = $this->deleteJson("/api/areas/{$area->id}");

        $response->assertStatus(422)
                ->assertJsonPath('message', 'Não é possível remover uma área que possui processos associados.');

        $this->assertDatabaseHas('areas', [
            'id' => $area->id
        ]);
    }

    /** @test */
    public function cannot_delete_nonexistent_area()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson('/api/areas/999');

        $response->assertStatus(404);
    }
}
