<?php

namespace Database\Seeders;

use App\Models\Intern;
use App\Models\InternAccount;
use App\Models\ManagersAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InternAccountSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, seed some managers if they don't exist
        $managers = ManagersAccount::first();
        
        if (!$managers) {
            ManagersAccount::create([
                'eti_id' => 'MGR001',
                'image' => 'default.jpg',
                'name' => 'Test Manager 1',
                'email' => 'manager1@test.com',
                'contact' => '03001234567',
                'join_date' => now()->format('Y-m-d'),
                'password' => Hash::make('password123'),
                'department' => 'HR',
                'status' => true,
                'emergency_contact' => 300111111,
            ]);

            ManagersAccount::create([
                'eti_id' => 'MGR002',
                'image' => 'default.jpg',
                'name' => 'Test Manager 2',
                'email' => 'manager2@test.com',
                'contact' => '03009876543',
                'join_date' => now()->format('Y-m-d'),
                'password' => Hash::make('password123'),
                'department' => 'Tech',
                'status' => true,
                'emergency_contact' => 300222222,
            ]);
        }

        // Get available managers
        $managers = ManagersAccount::limit(2)->get();

        // Seed intern table first
        $interns = Intern::count();
        
        if ($interns == 0) {
            $internData = [
                [
                    'name' => 'Ahmed Ali',
                    'email' => 'ahmed.ali@test.com',
                    'city' => 'Karachi',
                    'phone' => '03011234567',
                    'cnic' => '12345-1234567-1',
                    'gender' => 'Male',
                    'image' => 'default.jpg',
                    'join_date' => now()->format('Y-m-d'),
                    'birth_date' => '2000-01-15',
                    'university' => 'FAST',
                    'country' => 'Pakistan',
                    'interview_type' => 'online',
                    'technology' => 'Laravel',
                    'duration' => '3',
                    'status' => 'active',
                    'intern_type' => 'paid',
                    'interview_date' => now()->format('Y-m-d'),
                ],
                [
                    'name' => 'Fatima Khan',
                    'email' => 'fatima.khan@test.com',
                    'city' => 'Lahore',
                    'phone' => '03021234567',
                    'cnic' => '12345-1234567-2',
                    'gender' => 'Female',
                    'image' => 'default.jpg',
                    'join_date' => now()->format('Y-m-d'),
                    'birth_date' => '2001-03-22',
                    'university' => 'COMSATS',
                    'country' => 'Pakistan',
                    'interview_type' => 'online',
                    'technology' => 'React',
                    'duration' => '3',
                    'status' => 'active',
                    'intern_type' => 'paid',
                    'interview_date' => now()->format('Y-m-d'),
                ],
                [
                    'name' => 'Hassan Raza',
                    'email' => 'hassan.raza@test.com',
                    'city' => 'Islamabad',
                    'phone' => '03031234567',
                    'cnic' => '12345-1234567-3',
                    'gender' => 'Male',
                    'image' => 'default.jpg',
                    'join_date' => now()->format('Y-m-d'),
                    'birth_date' => '2000-06-10',
                    'university' => 'IIU',
                    'country' => 'Pakistan',
                    'interview_type' => 'online',
                    'technology' => 'Node.js',
                    'duration' => '3',
                    'status' => 'active',
                    'intern_type' => 'paid',
                    'interview_date' => now()->format('Y-m-d'),
                ],
                [
                    'name' => 'Ayesha Malik',
                    'email' => 'ayesha.malik@test.com',
                    'city' => 'Rawalpindi',
                    'phone' => '03041234567',
                    'cnic' => '12345-1234567-4',
                    'gender' => 'Female',
                    'image' => 'default.jpg',
                    'join_date' => now()->format('Y-m-d'),
                    'birth_date' => '2001-09-05',
                    'university' => 'Bahria',
                    'country' => 'Pakistan',
                    'interview_type' => 'online',
                    'technology' => 'Python',
                    'duration' => '3',
                    'status' => 'active',
                    'intern_type' => 'paid',
                    'interview_date' => now()->format('Y-m-d'),
                ],
                [
                    'name' => 'Muhammad Usman',
                    'email' => 'usman.muhammad@test.com',
                    'city' => 'Multan',
                    'phone' => '03051234567',
                    'cnic' => '12345-1234567-5',
                    'gender' => 'Male',
                    'image' => 'default.jpg',
                    'join_date' => now()->format('Y-m-d'),
                    'birth_date' => '2000-12-20',
                    'university' => 'Punjab University',
                    'country' => 'Pakistan',
                    'interview_type' => 'online',
                    'technology' => 'Angular',
                    'duration' => '3',
                    'status' => 'active',
                    'intern_type' => 'paid',
                    'interview_date' => now()->format('Y-m-d'),
                ],
            ];

            foreach ($internData as $data) {
                Intern::create($data);
            }
        }

        // Now seed intern accounts linked to these interns
        $interns = Intern::limit(5)->get();
        
        foreach ($interns as $internRecord) {
            // Skip if already has an account
            if (InternAccount::where('eti_id', $internRecord->id)->exists()) {
                continue;
            }

            // Assign a manager (round-robin)
            $manager = $managers[($internRecord->id - 1) % $managers->count()];

            InternAccount::create([
                'eti_id' => $internRecord->id,
                'name' => $internRecord->name,
                'email' => $internRecord->email,
                'phone' => $internRecord->phone,
                'password' => Hash::make('password123'),
                'int_technology' => $internRecord->technology,
                'start_date' => $internRecord->join_date,
                'int_status' => 'active',
                'portal_status' => 'active',
                'review' => 'Good',
                'supervisor_id' => $manager->manager_id,
                'manager_id' => $manager->manager_id,
                'city' => $internRecord->city,
                'university' => $internRecord->university,
                'bio' => "Intern studying " . $internRecord->technology,
            ]);
        }

        echo "✓ Successfully seeded " . InternAccount::count() . " intern accounts!\n";
    }
}
