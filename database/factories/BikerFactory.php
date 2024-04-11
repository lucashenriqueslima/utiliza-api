<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Biker>
 */
class BikerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'locavibe_biker_id' => $this->faker->randomElement(['657b355b9dab07e97c6061ae', '657b355b9dab07e97c60635a', '657b355b9dab07e97c6062ea']),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'cpf' => $this->faker->cpf(),
            'cnh' => $this->faker->cnh(),
            'status' => $this->faker->randomElement(['available', 'not_avaible', 'busy']),
            'firebase_token' => 'fpxHmfK_QHWpFfOvKqBY7i:APA91bEDA1fhcfUeEAK3YJsWrOsSJW_F3c0g9dU5LRGvxMnjBqvYB37P_cOlj4JEOEVTzlYU22A38V2dCgQOvBij6ZnMpXyCHKNs5CB-LoywadIctSQrvupWaAEofHctAupvLxYvfMVI',
        ];
    }
}
