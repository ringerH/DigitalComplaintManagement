<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'usertype' => $this->faker->randomElement(['student', 'hospital_patient']),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'college_id' => $this->faker->randomElement([1, 2, 3, 4]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}