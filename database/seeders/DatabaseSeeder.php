<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\College;
use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CategorySeeder::class,
        ]);
        // Seed Colleges
        $colleges = [
            ['name' => 'Guwahati Medical College'],
            ['name' => 'Assam Medical College, Dibrugarh'],
            ['name' => 'Silchar Medical College'],
            ['name' => 'Jorhat Medical College'],
        ];
        foreach ($colleges as $college) {
            College::create($college);
        }

        // Seed an Admin User (assuming you don’t have one yet)
        $admin = User::create([
            'name' => 'Admin User',
            'phone'=> '1234567891',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Seed Complaints
        $complaints = [
            [
                'complaint_id' => 'CMP-001',
                'complainant_name' => 'Rahul Sharma',
                'college_id' => 1, // Guwahati
                'complaint_text' => 'Water leakage in hostel room #305.',
                'status' => 'Pending',
                'submitted_at' => '2025-02-24 10:00:00',
            ],
            [
                'complaint_id' => 'CMP-002',
                'complainant_name' => 'Priya Das',
                'college_id' => 2, // Dibrugarh
                'complaint_text' => 'Lab equipment (microscope) not functioning.',
                'status' => 'In Progress',
                'submitted_at' => '2025-02-23 14:30:00',
            ],
            [
                'complaint_id' => 'CMP-003',
                'complainant_name' => 'Anita Borah',
                'college_id' => 3, // Silchar
                'complaint_text' => 'Cafeteria hygiene issues—dirty tables.',
                'status' => 'Resolved',
                'submitted_at' => '2025-02-22 09:15:00',
            ],
            [
                'complaint_id' => 'CMP-004',
                'complainant_name' => 'Kunal Gogoi',
                'college_id' => 1, // Guwahati
                'complaint_text' => 'Noisy construction near library during exams.',
                'status' => 'Pending',
                'submitted_at' => '2025-02-21 16:45:00',
            ],
            [
                'complaint_id' => 'CMP-005',
                'complainant_name' => 'Meena Saikia',
                'college_id' => 4, // Jorhat
                'complaint_text' => 'Broken chairs in lecture hall B-12.',
                'status' => 'In Progress',
                'submitted_at' => '2025-02-20 11:20:00',
            ],
        ];
        foreach ($complaints as $complaint) {
            Complaint::create($complaint);
        }

        // Seed Complaint Updates
        $updates = [
            [
                'complaint_id' => 2, // CMP-002
                'status' => 'In Progress',
                'notes' => 'Assigned to maintenance team for inspection.',
                'updated_by' => $admin->id,
                'created_at' => '2025-02-23 15:00:00',
            ],
            [
                'complaint_id' => 3, // CMP-003
                'status' => 'In Progress',
                'notes' => 'Cleaning staff notified.',
                'updated_by' => $admin->id,
                'created_at' => '2025-02-22 10:00:00',
            ],
            [
                'complaint_id' => 3, // CMP-003
                'status' => 'Resolved',
                'notes' => 'Cafeteria cleaned and inspected—issue resolved.',
                'updated_by' => $admin->id,
                'created_at' => '2025-02-22 12:30:00',
            ],
            [
                'complaint_id' => 5, // CMP-005
                'status' => 'In Progress',
                'notes' => 'Carpenter scheduled for repair.',
                'updated_by' => $admin->id,
                'created_at' => '2025-02-20 12:00:00',
            ],
        ];
        foreach ($updates as $update) {
            ComplaintUpdate::create($update);
        }
    }
}