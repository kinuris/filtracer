<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Course;
use App\Models\Department;
use App\Models\EducationRecord;
use App\Models\Major;
use App\Models\PersonalRecord;
use App\Models\ProfessionalRecord;
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
            'name' => 'Admins Assigned',
            'logo' => 'ccs.png',
        ]);

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
            'name' => 'College of Electronics Engineering',
            'logo' => 'coe.png',
        ]);

        foreach (Department::allValid() as $dept) {
            for ($i = 0; $i < fake()->numberBetween(3, 10); $i++) {
                Course::query()->create([
                    'department_id' => $dept->id,
                    'name' => fake()->firstName(),
                ]);
            }
        }

        foreach (Course::all() as $course) {
            for ($i = 0; $i < fake()->numberBetween(3, 10); $i++) {
                Major::query()->create([
                    'name' => fake()->firstName(),
                    'course_id' => $course->id,
                    'description' => fake()->sentence(),
                ]);
            }
        }

        User::factory(10)->create();
        User::query()->create([
            'name' => 'Chris D. Chan',
            'username' => 'superadmin',
            'password' => bcrypt('password'),
            'department_id' => Department::allValid()->random()->id,
            'role' => 'Admin',
        ]);

        User::query()->create([
            'name' => 'Admin N. Ai',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'department_id' => Department::allValid()->random()->id,
            'role' => 'Admin',
        ]);

        User::query()->create([
            'name' => 'Alum N. Ai',
            'username' => 'alumni',
            'password' => bcrypt('password'),
            'department_id' => Department::allValid()->random()->id,
            'role' => 'Alumni',
        ]);

        $admins = User::allAdmin();
        foreach ($admins as $admin) {
            Admin::query()->create([
                'user_id' => $admin->id,
                'first_name' => $admin->username === 'superadmin' ? 'Admin' : fake()->firstName(),
                'last_name' => fake()->lastName(),
                'position_id' => random_int(0, 9999),
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
                'is_verified' => $admin->username === 'admin',
                'is_super' => $admin->username === 'superadmin',
            ]);
        }


        foreach (User::noBio('educational')->get() as $user) {
            $major = Major::query()->whereRelation('departmentThroughCourse', 'departments.id', '=', $user->department_id)->get()->random();
            $year = fake()->year();

            EducationRecord::query()->create([
                'user_id' => $user->id,
                'school' => fake()->randomElement([
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
                'major_id' => $major->id,
                'course_id' => $major->course->id,
                'start' => $year,
                'end' => $year + 4,
            ]);
        }

        foreach (User::noBio('personal')->get() as $user) {
            if ($user->role === 'Admin') continue;

            PersonalRecord::query()->create([
                'user_id' => $user->id,
                'student_id' => $user->id,
                'first_name' => $user->username === 'alumni' ? 'Alumni' : fake()->firstName(),
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
        }

        foreach (User::noBio('professional')->get() as $user) {
            ProfessionalRecord::query()->create([
                'user_id' => $user->id,
                'employment_status' => fake()->randomElement([
                    'Employed',
                    'Unemployed',
                    'Self-employed',
                    'Student',
                    'Working Student',
                    'Retired'
                ]),
                'employment_type1' => fake()->randomElement([
                    'Private',
                    'Government',
                    'NGO/INGO'
                ]),
                'employment_type2' => fake()->randomElement([
                    'Full-Time',
                    'Part-Time',
                    'Traineeship',
                    'Internship',
                    'Contract'
                ]),
                'monthly_salary' => fake()->randomElement([
                    'no income',
                    'below 10,000',
                    '10,000-20,000',
                    '20,001-40,000',
                    '40,001-60,000',
                    '60,001-80,000',
                    '80,001-100,000',
                    'over 100,000'
                ]),
                'job_title' => fake()->word(),
                'company_name' => fake()->country(),
                'industry' => fake()->name(),
                'work_location' => fake()->address(),
            ]);
        }
    }
}
