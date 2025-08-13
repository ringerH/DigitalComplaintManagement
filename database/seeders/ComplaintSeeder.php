<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    public function run()
    {
        User::factory()->count(200)->create();
        Complaint::factory()->count(5000)->create();
    }
}