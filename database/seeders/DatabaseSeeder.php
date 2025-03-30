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

        $ccs = Department::query()->create([
            'name' => 'College of Computer Studies',
            'logo' => 'ccs.png',
        ]);

        $cba = Department::query()->create([
            'name' => 'College of Business and Accountancy',
            'logo' => 'cba.png',
        ]);

        $cas = Department::query()->create([
            'name' => 'College of Arts and Sciences',
            'logo' => 'cas.png',
        ]);

        $cte = Department::query()->create([
            'name' => 'College of Teacher Education',
            'logo' => 'cte.png',
        ]);

        Department::query()->create([
            'name' => 'College of Electronics Engineering',
            'logo' => 'coe.png',
        ]);

        Department::query()->create([
            'name' => 'College of Criminal Justice Education',
            'logo' => 'ccje.png',
        ]);

        $chtm = Department::query()->create([
            'name' => 'College of Hospitality and Tourism Management',
            'logo' => 'chtm.png',
        ]);

        Department::query()->create([
            'name' => 'College of Nursing',
            'logo' => 'cn.png',
        ]);

        Department::query()->create([
            'name' => 'Graduate School',
            'logo' => 'gradsch.png',
        ]);

        Course::query()->create([
            'department_id' => $cas->id,
            'name' => 'Bachelor of Arts',
            'image_link' => 'cas/bachelor-of-arts.png',
        ]);

        Course::query()->create([
            'department_id' => $cas->id,
            'name' => 'Bachelor of Science in Biology',
            'image_link' => 'cas/biology.png',
        ]);

        Course::query()->create([
            'department_id' => $cas->id,
            'name' => 'Bachelor of Science in Psychology',
            'image_link' => 'cas/psychology.png',
        ]);

        Course::query()->create([
            'department_id' => $cas->id,
            'name' => 'Bachelor of Science in Social Work',
            'image_link' => 'cas/social-work.png',
        ]);

        Course::query()->create([
            'department_id' => $cba->id,
            'name' => 'Bachelor of Science in Accountancy',
            'image_link' => 'cba/accountancy.png',
        ]);

        Course::query()->create([
            'department_id' => $cba->id,
            'name' => 'Bachelor of Science in Business Administration',
            'image_link' => 'cba/business-administration.png',
        ]);

        Course::query()->create([
            'department_id' => $cba->id,
            'name' => 'Bachelor of Science in Entrepreneurship',
            'image_link' => 'cba/entrepreneurship.png',
        ]);

        Course::query()->create([
            'department_id' => $ccs->id,
            'name' => 'Bachelor of Science in Computer Science',
            'image_link' => 'ccs/computer-science.png',
        ]);

        Course::query()->create([
            'department_id' => $ccs->id,
            'name' => 'Bachelor of Science in Information Technology',
            'image_link' => 'ccs/information-technology.png',
        ]);

        Course::query()->create([
            'department_id' => $chtm->id,
            'name' => 'Bachelor of Science in Hospitality Management',
            'image_link' => 'chtm/hospitality-management.png',
        ]);

        Course::query()->create([
            'department_id' => $chtm->id,
            'name' => 'Bachelor of Science in Tourism Management',
            'image_link' => 'chtm/tourism-management.png',
        ]);

        Course::query()->create([
            'department_id' => $cte->id,
            'name' => 'Bachelor of Culture and Arts Education',
            'image_link' => 'cte/culture-and-arts.png',
        ]);

        Course::query()->create([
            'department_id' => $cte->id,
            'name' => 'Diploma in Early Childhood Education',
            'image_link' => 'cte/diplomate-in-early-childhood.png',
        ]);

        Course::query()->create([
            'department_id' => $cte->id,
            'name' => 'Bachelor of Early Childhood Education',
            'image_link' => 'cte/early-childhood.png',
        ]);

        Course::query()->create([
            'department_id' => $cte->id,
            'name' => 'Bachelor of Elementary Education',
            'image_link' => 'cte/elementary.png',
        ]);

        // Course::query()->create([
        //     'department_id' => $cte->id,
        //     'name' => 'Bachelor of Physical Education',
        //     'image_link' => 'cte/physical-education.png',
        // ]);

        Course::query()->create([
            'department_id' => $cte->id,
            'name' => 'Bachelor in Physical Education',
            'image_link' => 'cte/physical.png',
        ]);

        Course::query()->create([
            'department_id' => $cte->id,
            'name' => 'Bachelor of Secondary Education',
            'image_link' => 'cte/secondary.png',
        ]);

        Course::query()->create([
            'department_id' => $cte->id,
            'name' => 'Bachelor of Special Needs Education',
            'image_link' => 'cte/special-needs.png',
        ]);

        Course::query()->create([
            'department_id' => $cte->id,
            'name' => 'Teacher Education Certificate',
            'image_link' => 'cte/teacher-education-certificate.png',
        ]);

        // foreach (Department::allValid() as $dept) {
        //     for ($i = 0; $i < fake()->numberBetween(3, 10); $i++) {
        //         Course::query()->create([
        //             'department_id' => $dept->id,
        //             'name' => fake()->firstName(),
        //         ]);
        //     }
        // }

        // foreach (Course::all() as $course) {
        //     for ($i = 0; $i < fake()->numberBetween(3, 10); $i++) {
        //         Major::query()->create([
        //             'name' => fake()->firstName(),
        //             'course_id' => $course->id,
        //             'description' => fake()->sentence(),
        //         ]);
        //     }
        // }

        // User::factory(10)->create();
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

        // Create 5 sample users under the CTE department
        User::query()->create([
            'name' => 'Maria Santos',
            'username' => 'maria_santos',
            'password' => bcrypt('password'),
            'department_id' => $cte->id,
            'role' => 'Alumni',
        ]);

        User::query()->create([
            'name' => 'Juan Dela Cruz',
            'username' => 'juan_delacruz',
            'password' => bcrypt('password'),
            'department_id' => $cte->id,
            'role' => 'Alumni',
        ]);

        User::query()->create([
            'name' => 'Elena Reyes',
            'username' => 'elena_reyes',
            'password' => bcrypt('password'),
            'department_id' => $cte->id,
            'role' => 'Alumni',
        ]);

        User::query()->create([
            'name' => 'Pedro Lim',
            'username' => 'pedro_lim',
            'password' => bcrypt('password'),
            'department_id' => $cte->id,
            'role' => 'Alumni',
        ]);

        User::query()->create([
            'name' => 'Sofia Garcia',
            'username' => 'sofia_garcia',
            'password' => bcrypt('password'),
            'department_id' => $cte->id,
            'role' => 'Alumni',
        ]);

        $admins = User::allAdmin();
        foreach ($admins as $admin) {
            Admin::query()->create([
                'user_id' => $admin->id,
                'first_name' => $admin->username === 'superadmin' ? 'Admin' : fake()->firstName(),
                'last_name' => fake()->lastName(),
                'position_id' => random_int(0, 9999),
                'office' => Department::allValid()->random()->id,
                'email_address' => fake()->email(),
                'phone_number' => fake()->phoneNumber(),
                'profile_picture' => $admin->username === 'superadmin' ? 'superadmin-profile.png' : 'admin-profile.png',
                'is_verified' => $admin->username === 'admin',
                'is_super' => $admin->username === 'superadmin',
            ]);
        }


        foreach (User::noBio('educational')->get() as $user) {
            $majorCollection = Major::query()->whereRelation('departmentThroughCourse', 'departments.id', '=', $user->department_id)->get();
            $major = $majorCollection->isNotEmpty() ? $majorCollection->random() : Major::query()->inRandomOrder()->first();
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
                'major_id' => is_null($major) ? null :  $major->id,
                'course_id' => Course::where('department_id', '=', $user->department_id)->exists() 
                    ? Course::where('department_id', '=', $user->department_id)->inRandomOrder()->first()->id
                    : Course::inRandomOrder()->first()->id,
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
                'profile_picture' => 'alumni-profile.png',
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
