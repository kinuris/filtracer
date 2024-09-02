<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Course;
use App\Models\Department;
use App\Models\EducationalBio;
use App\Models\PersonalBio;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Department::query()->create([
            'name' => 'College of Computer Studies',
            'logo' => 'ccs.png',
        ]);

        Department::query()->create([
            'name' => 'College of Business and Accountancy',
            'logo' => 'cba.png',
        ]);

        Department::query()->create([
            'name' => 'College of Arts and Sciences',
            'logo' => 'cas.png',
        ]);

        Department::query()->create([
            'name' => 'College of Teacher Education',
            'logo' => 'cte.png',
        ]);

        Department::query()->create([
            'name' => 'College of Electronics and Telecommunications',
            'logo' => 'coe.png',
        ]);

        foreach (Department::all() as $dept) {
            for ($i = 0; $i < fake()->numberBetween(3, 10); $i++) {
                Course::query()->create([
                    'department_id' => $dept->id,
                    'name' => fake()->firstName(),
                ]);
            }
        }

        User::factory(50)->create();
        User::query()->create([
            'name' => 'Chris D. Chan',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'department_id' => Department::all()->random()->id,
            'role' => 'Admin',
        ]);

        $admins = User::allAdmin();
        foreach ($admins as $admin) {
            Admin::query()->create([
                'user_id' => $admin->id,
                'fullname' => $admin->name,
                'office' => fake()->randomElement([
                    'Alumni Office',
                    'CAS Faculty Office',
                    'CBA Faculty Office',
                    'CCS Faculty Office',
                    'CCJE Faculty Office',
                    'CHTM Faculty Office',
                    'CN Faculty Office',
                    'COE Faculty Office',
                    'CTE Faculty Office',
                    'Graduate School Faculty Office'
                ]),
                'email_address' => fake()->email(),
                'phone_number' => fake()->phoneNumber(),
                'profile_picture' => null,
            ]);
        }

        foreach (User::noBio('educational')->get() as $user) {
            $bio = EducationalBio::query()->create([
                'school_name' => fake()->randomElement([
                    'Filamer Christian University',
                    'University of the Philippines in the Visayas',
                    'Central Philippine University',
                    'John B. Lacson Foundation Maritime University',
                    'University of St. La Salle',
                    'West Visayas State University',
                    'University of Negros Occidental - Recoletos',
                    'University of Iloilo - PHINMA',
                    'Iloilo Science and Technology University',
                    'Aklan State University',
                    'University of San Agustin',
                    'Capiz State University',
                    'St. Paul University Iloilo',
                    'University of Antique',
                    'Central Philippine Adventist College',
                    'Western Institute of Technology',
                    'Guimaras State University',
                    'STI West Negros University'
                ]),
                'school_location' => fake()->address(),
                'degree_type' => fake()->randomElement([
                    'Bachelor',
                    'Masteral',
                    'Doctoral',
                ]),
                'course_id' => Course::all()->random()->id,
                'batch' => fake()->year(),
            ]);

            $user->update(['educational_bio_id' => $bio->id]);
        }

        foreach (User::noBio('personal')->get() as $user) {
            $bio = PersonalBio::query()->create([
                'student_id' => $user->id,
                'first_name' => fake()->firstName(),
                'middle_name' => fake()->optional()->lastName(),
                'last_name' => fake()->lastName(),
                'suffix' => null,
                'gender' => fake()->randomElement(['Male', 'Female']),
                'birthdate' => fake()->date(),
                'civil_status' => fake()->randomElement([
                    'Married',
                    'Single',
                    'Divorced',
                    'Widowed',
                    'Separated'
                ]),
                'permanent_address' => fake()->address(),
                'current_address' => fake()->address(),
                'email_address' => fake()->email(),
                'phone_number' => fake()->phoneNumber(),
                'social_link' => fake()->url(),
                'status' => fake()->randomElement([0, 1]),
                'profile_picture' => fake()->imageUrl(),
            ]);

            $user->update(['personal_bio_id' => $bio->id]);
        }
    }
}
