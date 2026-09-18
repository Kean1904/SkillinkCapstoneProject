<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed Accounts
        $users = [
            [
                'name' => 'admin@Admin',
                'first_name' => 'Municipal',
                'last_name' => 'Administrator',
                'email' => 'admin.peso@magalang.gov.ph',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'role' => 'admin',
                'age' => 42,
                'gender' => 'Male',
                'address' => 'Magalang Municipal Hall Complex',
                'barangay' => 'San Nicolas 1st',
                'contact_number' => '09998887766',
                'is_verified' => true,
                'rating' => 5.00,
                'status' => 'active',
            ],
            [
                'name' => 'peso_officer@Staff',
                'first_name' => 'Grace',
                'last_name' => 'Manalo',
                'email' => 'peso.magalang@pampanga.gov.ph',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('staff123'),
                'role' => 'peso staff',
                'age' => 30,
                'gender' => 'Female',
                'address' => 'PESO Magalang Office',
                'barangay' => 'San Nicolas 1st',
                'contact_number' => '09451122334',
                'is_verified' => true,
                'rating' => 5.00,
                'status' => 'active',
            ],
            [
                'name' => 'Testing 1',
                'first_name' => 'Khane Hendrix',
                'last_name' => 'Torres',
                'email' => 'torres.khane@gmail.com',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'residential',
                'age' => 24,
                'gender' => 'Male',
                'address' => 'Purok 2',
                'barangay' => 'San Nicolas 1st',
                'contact_number' => '09123456789',
                'is_verified' => true,
                'rating' => 5.00,
                'status' => 'active',
            ],
            [
                'name' => 'maria_residential',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria.santos@gmail.com',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('client123'),
                'role' => 'residential',
                'age' => 28,
                'gender' => 'Female',
                'address' => 'Purok 3',
                'barangay' => 'San Nicolas 1st',
                'contact_number' => '09209876543',
                'is_verified' => true,
                'rating' => 4.90,
                'status' => 'active',
            ],
            [
                'name' => 'juan_plumber',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@gmail.com',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('worker123'),
                'role' => 'skilled worker',
                'age' => 35,
                'gender' => 'Male',
                'address' => 'Purok 1',
                'barangay' => 'San Nicolas 1st',
                'contact_number' => '09171234567',
                'skills' => 'Plumbing, Pipe Fitting, Water Line Repair',
                'certificate_proof' => 'TESDA NC II - Plumbing',
                'is_verified' => true,
                'rating' => 4.90,
                'status' => 'active',
            ],
            [
                'name' => 'pedro_electric',
                'first_name' => 'Pedro',
                'last_name' => 'Santos',
                'email' => 'pedro.electric@gmail.com',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('worker123'),
                'role' => 'skilled worker',
                'age' => 32,
                'gender' => 'Male',
                'address' => 'Purok 4',
                'barangay' => 'Dolores',
                'contact_number' => '09187654321',
                'skills' => 'Electrical, Wiring, Circuit Installation',
                'certificate_proof' => 'TESDA NC II - Electrical Installation',
                'is_verified' => true,
                'rating' => 4.80,
                'status' => 'active',
            ],
            [
                'name' => 'mario_carpenter',
                'first_name' => 'Mario',
                'last_name' => 'Reyes',
                'email' => 'mario.carpenter@gmail.com',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('worker123'),
                'role' => 'skilled worker',
                'age' => 40,
                'gender' => 'Male',
                'address' => 'Purok 2',
                'barangay' => 'San Agustin',
                'contact_number' => '09191122334',
                'skills' => 'Carpentry, Roofing, Furniture Repair',
                'certificate_proof' => 'TESDA NC II - Carpentry',
                'is_verified' => true,
                'rating' => 4.70,
                'status' => 'active',
            ],
        ];

        foreach ($users as $data) {
            $user = \App\Models\User::updateOrCreate(['name' => $data['name']], $data);
            if ($data['role'] === 'skilled worker') {
                \Illuminate\Support\Facades\DB::table('worker_profiles')->updateOrInsert(
                    ['user_id' => $user->user_id],
                    [
                        'skill_tags' => $data['skills'] ?? 'General Handyman',
                        'service_categories' => $data['skills'] ?? 'General Repair',
                        'biography' => 'Accredited skilled worker in Magalang, Pampanga.',
                        'average_rating' => $data['rating'] ?? 5.00,
                        'availability_status' => 'available',
                        'experience_years' => 5,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // 2. Seed Jobs
        $clientUser = \App\Models\User::where('name', 'Testing 1')->first();
        if ($clientUser) {
            \App\Models\JobPost::updateOrCreate(
                ['title' => 'Emergency Kitchen Pipe Leak Repair'],
                [
                    'client_id' => $clientUser->user_id,
                    'posted_by' => $clientUser->name,
                    'category' => 'Plumbing Repair',
                    'description' => 'The pipe under the kitchen sink is leaking heavily and needs urgent PVC replacement.',
                    'barangay' => 'San Nicolas 1st',
                    'location_tag' => 'San Nicolas 1st',
                    'preferred_schedule' => 'Immediate / Today',
                    'date_posted' => now()->toDateString(),
                    'status' => 'Applied',
                    'applicant_username' => 'juan_plumber',
                ]
            );

            \App\Models\JobPost::updateOrCreate(
                ['title' => 'Main Circuit Breaker Tripping Troubleshooting'],
                [
                    'client_id' => $clientUser->user_id,
                    'posted_by' => $clientUser->name,
                    'category' => 'Electrical Installation',
                    'description' => 'Breaker repeatedly trips when air conditioning unit is turned on. Need licensed electrician.',
                    'barangay' => 'Dolores',
                    'location_tag' => 'Dolores',
                    'preferred_schedule' => 'Tomorrow Morning',
                    'date_posted' => now()->toDateString(),
                    'status' => 'Pending',
                    'applicant_username' => null,
                ]
            );

            \App\Models\JobPost::updateOrCreate(
                ['title' => 'Roof Gutter & Ceiling Leak Repair'],
                [
                    'client_id' => $clientUser->user_id,
                    'posted_by' => $clientUser->name,
                    'category' => 'Carpentry & Roofing',
                    'description' => 'Rainwater entering ceiling during storms due to clogged and displaced galvanized gutters.',
                    'barangay' => 'San Agustin',
                    'location_tag' => 'San Agustin',
                    'preferred_schedule' => 'Weekend',
                    'date_posted' => now()->toDateString(),
                    'status' => 'Pending',
                    'applicant_username' => null,
                ]
            );
        }

        // 3. Seed Sample Booking with 4-stage stepper
        $workerUser = \App\Models\User::where('name', 'juan_plumber')->first();
        if ($clientUser && $workerUser) {
            $workerProfile = \Illuminate\Support\Facades\DB::table('worker_profiles')->where('user_id', $workerUser->user_id)->first();
            $firstJob = \App\Models\JobPost::first();
            if ($workerProfile && $firstJob) {
                \App\Models\Booking::updateOrCreate(
                    ['booking_reference' => 'BK-894102'],
                    [
                        'request_id' => $firstJob->request_id,
                        'worker_id' => $workerProfile->worker_id,
                        'client_username' => $clientUser->name,
                        'worker_username' => $workerUser->name,
                        'client_name' => $clientUser->full_name,
                        'worker_name' => $workerUser->full_name,
                        'service_category' => 'Plumbing Repair',
                        'task_description' => 'Kitchen water pipe fitting and sealant replacement.',
                        'service_address' => 'Purok 2, San Nicolas 1st, Magalang',
                        'barangay' => 'San Nicolas 1st',
                        'estimated_budget' => '₱650.00',
                        'scheduled_date' => now()->addDays(1)->format('M d, Y - 09:00 AM'),
                        'status' => 'ACCEPTED',
                    ]
                );
            }
        }
    }
}
