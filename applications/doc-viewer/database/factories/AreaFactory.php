<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Area>
 */
class AreaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Area::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the area is for Recursos Humanos.
     */
    public function recursosHumanos()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Recursos Humanos',
            ];
        });
    }

    /**
     * Indicate that the area is for Financeiro.
     */
    public function financeiro()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Financeiro',
            ];
        });
    }

    /**
     * Indicate that the area is for Recrutamento.
     */
    public function recrutamento()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Recrutamento',
            ];
        });
    }
}
