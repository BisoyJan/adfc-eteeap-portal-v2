<?php

namespace Database\Factories;

use App\Models\Rubric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rubric>
 */
class RubricFactory extends Factory
{
    protected $model = Rubric::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'criteria' => [
                ['name' => 'Criterion 1', 'weight' => 50, 'levels' => [
                    ['score' => 100, 'description' => 'Excellent'],
                    ['score' => 50, 'description' => 'Average'],
                    ['score' => 0, 'description' => 'Poor'],
                ]],
            ],
            'max_score' => 100,
            'is_template' => false,
        ];
    }
}
