<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    public function definition()
    {
        return [
            'category_id' => $this->faker->numberBetween(1, 5),
            'status' => $this->faker->randomElement(['open', 'in-progress', 'resolved']),
            'additional_data' => json_encode(['details' => $this->faker->sentence]),
            'complainant_name' => $this->faker->name,
            'complaint_text' => $this->faker->paragraph,
            'notes' => $this->faker->optional()->sentence,
            'college_id' => $this->faker->randomElement([1, 2, 3, 4]),
            'user_id' => $this->faker->randomElement(User::pluck('id')->toArray()),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}