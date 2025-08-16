<?php

namespace Database\Factories;

use App\Models\Process;
use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Process>
 */
class ProcessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Process::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'area_id' => Area::factory(),
            'parent_id' => null,
            'name' => $this->faker->unique()->sentence(3),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['internal', 'external']),
            'criticality' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'tools' => $this->faker->optional()->sentence(),
            'responsible' => $this->faker->optional()->name(),
            'documentation' => $this->faker->optional()->url(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the process is a root process (no parent).
     */
    public function root()
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => null,
            ];
        });
    }

    /**
     * Indicate that the process is a subprocess.
     */
    public function subprocess()
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => Process::factory()->root(),
            ];
        });
    }

    /**
     * Indicate that the process is active.
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    /**
     * Indicate that the process is inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'inactive',
            ];
        });
    }

    /**
     * Indicate that the process has high criticality.
     */
    public function highCriticality()
    {
        return $this->state(function (array $attributes) {
            return [
                'criticality' => 'high',
            ];
        });
    }

    /**
     * Indicate that the process is internal.
     */
    public function internal()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'internal',
            ];
        });
    }

    /**
     * Indicate that the process is external.
     */
    public function external()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'external',
            ];
        });
    }
}
