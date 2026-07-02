<?php

namespace Database\Factories;

use App\Models\CurriculumSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CurriculumSubject>
 */
class CurriculumSubjectFactory extends Factory
{
    protected $model = CurriculumSubject::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('??-####')),
            'title' => fake()->sentence(3),
            'units' => fake()->randomFloat(1, 1, 3),
            'category' => fake()->randomElement(['core', 'major', 'ge', 'elective', 'pe', 'nstp']),
            'year_level' => fake()->numberBetween(1, 4),
            'semester' => fake()->numberBetween(1, 2),
            'is_active' => true,
            'curriculum_version' => config('eteeap.curriculum.default_version'),
        ];
    }
}
