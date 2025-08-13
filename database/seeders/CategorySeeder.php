<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Hospital Patient Categories
            'Dispensary Services',
            'Patient Care',
            'Medical Treatment',
            'Visitor Experience',
            'Hospital Security',
            // Student Categories
            'Transportation',
            'Staff Behavior',
            'Hygiene',
            'Payment',
            'Infrastructure',
            'Academic Support',
            'Security',
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}