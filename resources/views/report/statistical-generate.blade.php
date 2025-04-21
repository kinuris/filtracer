@extends('layouts.admin')

@section('title', 'Statistical Report')

<?php

use App\Models\Department;
use App\Models\Course;
use App\Models\User;
use App\Models\EducationalRecord; // Keep this if used elsewhere, though not directly in batch filter now
use App\Models\ProfessionalRecord; // Add this for 'With' filter

$department = Department::find(request('department') ?? -1);
$course = Course::find(request('courses') ?? -1);
$category = request('category');
$selectedBatch = request('batch') ?? '';
$withField = request('with_field') ?? ''; // Get selected 'With' field
$withValue = request('with_value') ?? ''; // Get selected 'With' value

// Define mappable fields for the 'With' filter (Copied from statistical.blade.php)
$withFieldsMap = [
    'job_title' => 'Job', // Added
    'waiting_time' => 'Waiting Time',
    'industry' => 'Industry',
    'employment_type1' => 'Employment Type 1',
    'employment_type2' => 'Employment Type 2',
    'monthly_salary' => 'Monthly Salary',
    'job_search_method' => 'Job Search Method', // Added
];

// Helper function to add batch filter if selected (Copied from statistical.blade.php)
function applyBatchFilter($query, $selectedBatch) {
    if ($selectedBatch !== '') {
        $query = $query->whereHas('educationalRecord', function ($subQuery) use ($selectedBatch) {
            $subQuery->where('end', '=', $selectedBatch);
        });
    }
    return $query;
}

// Helper function to add 'With' filter if selected (Copied from statistical.blade.php)
function applyWithFilter($query, $withField, $withValue)
{
    if ($withField && $withValue && $withValue !== '') {
        if ($withField === 'job_search_method') {
            $query->whereHas('professionalRecords.methods', function ($subQuery) use ($withValue) {
                $subQuery->where('method', '=', $withValue);
            });
        } else {
            $query->whereHas('professionalRecords', function ($subQuery) use ($withField, $withValue) {
                $subQuery->where($withField, '=', $withValue);
            });
        }
    }
    return $query;
}

// Determine the base query conditions based on department and course (Copied from statistical.blade.php)
$baseQueryConditions = function ($query) use ($department, $course) {
    $query->whereIn(
        'id',
        App\Models\User::compSet()
            ->orWhereHas('partialPersonal')
            ->get()
            ->pluck('id')
    )
    ->where('role', '!=', 'Admin');

    if ($department) {
        $query->where('department_id', '=', $department->id);
    }
    if ($course) {
        $query->whereRelation('course', 'courses.id', '=', $course->id);
    }
};

// Determine admin query conditions based on department and course (Copied from statistical.blade.php)
$adminQueryConditions = function ($query) use ($department, $course) {
    $query->where('role', '=', 'Admin');
    if ($department) {
        $query->where('department_id', '=', $department->id);
    }
    if ($course) {
        $query->whereRelation('course', 'courses.id', '=', $course->id);
    }
};

$users = collect(); // Initialize as empty collection

// Refactored logic using the closures

if ($department && $course) {
    $query = User::query();
    $baseQueryConditions($query); // Apply base conditions
    $query = applyBatchFilter($query, $selectedBatch); // Apply batch filter
    $query = applyWithFilter($query, $withField, $withValue); // Apply 'With' filter

    switch ($category) {
        case "All Entities":
            $alumniQuery = User::query();
            $baseQueryConditions($alumniQuery); // Apply base conditions
            $alumniQuery = applyBatchFilter($alumniQuery, $selectedBatch); // Apply batch filter
            $alumniQuery = applyWithFilter($alumniQuery, $withField, $withValue); // Apply 'With' filter
            $alumniIds = $alumniQuery->pluck('id');

            $adminQuery = User::query();
            $adminQueryConditions($adminQuery); // Apply admin conditions (no batch or 'With' filter for admins)
            $adminIds = $adminQuery->pluck('id');

            $users = User::query()->whereIn('id', $alumniIds->merge($adminIds))->get();
            break;
        case "All Users":
            break; // Will use $query->get() later
        case "Working Student":
        case "Employed Alumni":
        case "Unemployed Alumni":
        case "Self-Employed Alumni":
        case "Student Alumni":
        case "Retired Alumni":
            $dbCategory = $category;
            switch ($category) {
                case "Employed Alumni": $dbCategory = "Employed"; break;
                case "Unemployed Alumni": $dbCategory = "Unemployed"; break;
                case "Self-Employed Alumni": $dbCategory = "Self-Employed"; break;
                case "Student Alumni": $dbCategory = "Student"; break;
                case "Retired Alumni": $dbCategory = "Retired"; break;
            }
            $query->whereRelation('professionalRecords', 'employment_status', '=', $dbCategory);
            break; // Will use $query->get() later
        case "Verified Alumni":
        case "Unverified Alumni":
            $query->whereRelation('personalRecords', 'status', '=', $category === "Verified Alumni" ? 1 : 0);
            break; // Will use $query->get() later
        case "Verified Admin":
        case "Unverified Admin":
            $adminQuery = User::query();
            $adminQueryConditions($adminQuery); // Apply admin conditions
            $adminQuery->whereRelation('adminRelation', 'is_verified', '=', $category === "Verified Admin" ? 1 : 0)
                       ->whereRelation('adminRelation', 'is_super', '=', 0);
            $users = $adminQuery->get();
            break;
        case "Verified User":
        case "Unverified User":
            $alumniQuery = User::query();
            $baseQueryConditions($alumniQuery); // Apply base conditions
            $alumniQuery = applyBatchFilter($alumniQuery, $selectedBatch); // Apply batch filter
            $alumniQuery = applyWithFilter($alumniQuery, $withField, $withValue); // Apply 'With' filter
            $alumniQuery->where(function ($q_inner) use ($category) {
                if ($category === "Verified User") {
                    $q_inner->where(function ($q_status) {
                        $q_status->whereHas('personalRecords', fn($pr) => $pr->where('status', 1))
                                 ->orWhereHas('partialPersonal');
                    });
                } else {
                    $q_inner->where(function ($q_status) {
                        $q_status->whereDoesntHave('personalRecords', fn($pr) => $pr->where('status', 1))
                                 ->whereDoesntHave('partialPersonal');
                    });
                }
            });
            $alumniIds = $alumniQuery->pluck('id');

            $adminQuery = User::query();
            $adminQueryConditions($adminQuery); // Apply admin conditions
            $adminQuery->whereRelation('adminRelation', 'is_verified', '=', $category === "Verified User" ? 1 : 0)
                       ->whereRelation('adminRelation', 'is_super', '=', 0);
            $adminIds = $adminQuery->pluck('id');

            $users = User::query()->whereIn('id', $alumniIds->merge($adminIds))->get();
            break;
        default:
            break;
    }

    if ($users->isEmpty() && isset($query)) {
        $users = $query->get();
    }

} else if ($department) {
    $query = User::query();
    $baseQueryConditions($query); // Apply base conditions (dept only)
    $query = applyBatchFilter($query, $selectedBatch); // Apply batch filter
    $query = applyWithFilter($query, $withField, $withValue); // Apply 'With' filter

    switch ($category) {
        case "All Entities":
            $alumniQuery = User::query();
            $baseQueryConditions($alumniQuery); // Apply base conditions (dept only)
            $alumniQuery = applyBatchFilter($alumniQuery, $selectedBatch); // Apply batch filter
            $alumniQuery = applyWithFilter($alumniQuery, $withField, $withValue); // Apply 'With' filter
            $alumniIds = $alumniQuery->pluck('id');

            $adminQuery = User::query();
            $adminQueryConditions($adminQuery); // Apply admin conditions (dept only)
            $adminIds = $adminQuery->pluck('id');

            $users = User::query()->whereIn('id', $alumniIds->merge($adminIds))->get();
            break;
        case "All Users":
            break; // Uses base query ->get()
        case "Working Student":
        case "Employed Alumni":
        case "Unemployed Alumni":
        case "Self-Employed Alumni":
        case "Student Alumni":
        case "Retired Alumni":
            $dbCategory = $category;
            switch ($category) {
                case "Employed Alumni": $dbCategory = "Employed"; break;
                case "Unemployed Alumni": $dbCategory = "Unemployed"; break;
                case "Self-Employed Alumni": $dbCategory = "Self-Employed"; break;
                case "Student Alumni": $dbCategory = "Student"; break;
                case "Retired Alumni": $dbCategory = "Retired"; break;
            }
            $query->whereRelation('professionalRecords', 'employment_status', '=', $dbCategory);
            break; // Uses base query ->get()
        case "Verified Alumni":
        case "Unverified Alumni":
            $query->whereRelation('personalRecords', 'status', '=', $category === "Verified Alumni" ? 1 : 0);
            break; // Uses base query ->get()
        case "Verified Admin":
        case "Unverified Admin":
            $adminQuery = User::query();
            $adminQueryConditions($adminQuery); // Apply admin conditions (dept only)
            $adminQuery->whereRelation('adminRelation', 'is_verified', '=', $category === "Verified Admin" ? 1 : 0)
                       ->whereRelation('adminRelation', 'is_super', '=', 0);
            $users = $adminQuery->get();
            break;
        case "Verified User":
        case "Unverified User":
            $alumniQuery = User::query();
            $baseQueryConditions($alumniQuery); // Apply base conditions (dept only)
            $alumniQuery = applyBatchFilter($alumniQuery, $selectedBatch); // Apply batch filter
            $alumniQuery = applyWithFilter($alumniQuery, $withField, $withValue); // Apply 'With' filter
            $alumniQuery->where(function ($q_inner) use ($category) {
                if ($category === "Verified User") {
                    $q_inner->where(function ($q_status) {
                        $q_status->whereHas('personalRecords', fn($pr) => $pr->where('status', 1))
                                 ->orWhereHas('partialPersonal');
                    });
                } else {
                    $q_inner->where(function ($q_status) {
                        $q_status->whereDoesntHave('personalRecords', fn($pr) => $pr->where('status', 1))
                                 ->whereDoesntHave('partialPersonal');
                    });
                }
            });
            $alumniIds = $alumniQuery->pluck('id');

            $adminQuery = User::query();
            $adminQueryConditions($adminQuery); // Apply admin conditions (dept only)
            $adminQuery->whereRelation('adminRelation', 'is_verified', '=', $category === "Verified User" ? 1 : 0)
                       ->whereRelation('adminRelation', 'is_super', '=', 0);
            $adminIds = $adminQuery->pluck('id');

            $users = User::query()->whereIn('id', $alumniIds->merge($adminIds))->get();
            break;
        default:
            break; // Uses base query ->get()
    }

    if ($users->isEmpty() && isset($query)) {
        $users = $query->get();
    }

} else {
    switch ($category) {
        case "All Entities":
            $alumniQuery = User::query();
            $baseQueryConditions($alumniQuery); // Apply base conditions (no dept/course)
            $alumniQuery = applyBatchFilter($alumniQuery, $selectedBatch); // Apply batch filter
            $alumniQuery = applyWithFilter($alumniQuery, $withField, $withValue); // Apply 'With' filter
            $alumniIds = $alumniQuery->pluck('id');

            $adminQuery = User::query();
            $adminQueryConditions($adminQuery); // Apply admin conditions (no dept/course)
            $adminIds = $adminQuery->pluck('id');

            $users = User::query()->whereIn('id', $alumniIds->merge($adminIds))->get();
            break;
        case "All Users":
            $query = User::query();
            $baseQueryConditions($query); // Apply base conditions (no dept/course)
            $query = applyBatchFilter($query, $selectedBatch); // Apply batch filter
            $query = applyWithFilter($query, $withField, $withValue); // Apply 'With' filter
            $users = $query->get();
            break;
        case "Working Student":
        case "Employed Alumni":
        case "Unemployed Alumni":
        case "Self-Employed Alumni":
        case "Student Alumni":
        case "Retired Alumni":
            $dbCategory = $category;
            switch ($category) {
                case "Employed Alumni": $dbCategory = "Employed"; break;
                case "Unemployed Alumni": $dbCategory = "Unemployed"; break;
                case "Self-Employed Alumni": $dbCategory = "Self-Employed"; break;
                case "Student Alumni": $dbCategory = "Student"; break;
                case "Retired Alumni": $dbCategory = "Retired"; break;
            }
            $query = User::query();
            $baseQueryConditions($query); // Apply base conditions (no dept/course)
            $query = applyBatchFilter($query, $selectedBatch); // Apply batch filter
            $query = applyWithFilter($query, $withField, $withValue); // Apply 'With' filter
            $query->whereRelation('professionalRecords', 'employment_status', '=', $dbCategory);
            $users = $query->get();
            break;
        case "Verified Alumni":
        case "Unverified Alumni":
            $query = User::query();
            $baseQueryConditions($query); // Apply base conditions (no dept/course)
            $query = applyBatchFilter($query, $selectedBatch); // Apply batch filter
            $query = applyWithFilter($query, $withField, $withValue); // Apply 'With' filter
            $query->whereRelation('personalRecords', 'status', '=', $category === "Verified Alumni" ? 1 : 0);
            $users = $query->get();
            break;
        case "Verified Admin":
        case "Unverified Admin":
            $adminQuery = User::query();
            $adminQueryConditions($adminQuery); // Apply admin conditions (no dept/course)
            $adminQuery->whereRelation('adminRelation', 'is_verified', '=', $category === "Verified Admin" ? 1 : 0)
                       ->whereRelation('adminRelation', 'is_super', '=', 0);
            $users = $adminQuery->get();
            break;
        case "Verified User":
        case "Unverified User":
            $alumniQuery = User::query();
            $baseQueryConditions($alumniQuery); // Apply base conditions (no dept/course)
            $alumniQuery = applyBatchFilter($alumniQuery, $selectedBatch); // Apply batch filter
            $alumniQuery = applyWithFilter($alumniQuery, $withField, $withValue); // Apply 'With' filter
            $alumniQuery->where(function ($q_inner) use ($category) {
                if ($category === "Verified User") {
                    $q_inner->where(function ($q_status) {
                        $q_status->whereHas('personalRecords', fn($pr) => $pr->where('status', 1))
                                 ->orWhereHas('partialPersonal');
                    });
                } else {
                    $q_inner->where(function ($q_status) {
                        $q_status->whereDoesntHave('personalRecords', fn($pr) => $pr->where('status', 1))
                                 ->whereDoesntHave('partialPersonal');
                    });
                }
            });
            $alumniIds = $alumniQuery->pluck('id');

            $adminQuery = User::query();
            $adminQueryConditions($adminQuery); // Apply admin conditions (no dept/course)
            $adminQuery->whereRelation('adminRelation', 'is_verified', '=', $category === "Verified User" ? 1 : 0)
                       ->whereRelation('adminRelation', 'is_super', '=', 0);
            $adminIds = $adminQuery->pluck('id');

            $users = User::query()->whereIn('id', $alumniIds->merge($adminIds))->get();
            break;
        default:
            $query = User::query();
            $baseQueryConditions($query); // Apply base conditions (no dept/course)
            $query = applyBatchFilter($query, $selectedBatch); // Apply batch filter
            $query = applyWithFilter($query, $withField, $withValue); // Apply 'With' filter
            $users = $query->get();
            break;
    }
}

// Apply search filter if present, after all other filters
if (request()->filled('search') && isset($users) && $users->isNotEmpty()) {
    $searchTerm = '%' . request('search') . '%';
    $userIds = $users->pluck('id');

    $query = App\Models\User::whereIn('id', $userIds)
        ->where(function ($q) use ($searchTerm) {
            $q->whereHas('personalBio', function ($pb) use ($searchTerm) {
                  $pb->where('first_name', 'like', $searchTerm)
                     ->orWhere('last_name', 'like', $searchTerm)
                     ->orWhere('student_id', 'like', $searchTerm)
                     ->orWhere('email_address', 'like', $searchTerm)
                     ->orWhere('phone_number', 'like', $searchTerm);
              })
              ->orWhereHas('partialPersonal', function ($pp) use ($searchTerm) {
                  $pp->where('first_name', 'like', $searchTerm)
                     ->orWhere('last_name', 'like', $searchTerm)
                     ->orWhere('student_id', 'like', $searchTerm)
                     ->orWhere('email_address', 'like', $searchTerm)
                     ->orWhere('phone_number', 'like', $searchTerm);
              })
              ->orWhereHas('adminRelation', function ($ar) use ($searchTerm) {
                  $ar->where('first_name', 'like', $searchTerm)
                     ->orWhere('last_name', 'like', $searchTerm)
                     ->orWhere('position_id', 'like', $searchTerm)
                     ->orWhere('email_address', 'like', $searchTerm)
                     ->orWhere('phone_number', 'like', $searchTerm);
              });
        });
    $users = $query->get();
}

?>

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)] overflow-auto">
    <div class="flex">
        <h1 class="font-medium tracking-widest text-lg">Statistical Report</h1>
        <div class="flex-1"></div>

        @php
            try {
                $tw = Vite::asset('resources/css/app.css');
            } catch (\Exception $e) {
                $tw = asset('build/assets/app.css');
            }
        @endphp
        <button class="text-white bg-blue-600 p-2 px-3 rounded" onclick="printJS({
            printable: 'printableStat',
            type: 'html',
            css: '{{ $tw }}',
            style: '#printableStat { font-size: 10px; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid black; padding: 4px; text-align: center; } thead { background-color: #f2f2f2; }'
        })">Print</button>
    </div>
    <div class="shadow rounded-lg mt-4">
        <div class="bg-white p-4 flex place-items-center border-b rounded-lg">
            <div id="printableStat" class="border border-black w-full p-4 rounded-lg">
                <div class="flex justify-between mb-4">

                @php
                    $category_name = request('category', 'All Users');
                    switch ($category_name) {
                        case 'All Entities':
                            $category_name = 'All Users';
                            break;
                        case 'All Users':
                            $category_name = 'Alumni';
                            break;
                        case 'Employed Alumni':
                            $category_name = 'Employed Alumni';
                            break;
                        case 'Working Student':
                            $category_name = 'Working Student';
                            break;
                        case 'Unemployed Alumni':
                            $category_name = 'Unemployed Alumni';
                            break;
                        case 'Self-Employed Alumni':
                            $category_name = 'Self-Employed Alumni';
                            break;
                        case 'Student Alumni':
                            $category_name = 'Student Alumni';
                            break;
                        case 'Retired Alumni':
                            $category_name = 'Retired Alumni';
                            break;
                        case 'Verified Alumni':
                            $category_name = 'Verified Alumni';
                            break;
                        case 'Unverified Alumni':
                            $category_name = 'Unverified Alumni';
                            break;
                        case 'Verified Admin':
                            $category_name = 'Verified Admin';
                            break;
                        case 'Unverified Admin':
                            $category_name = 'Unverified Admin';
                            break;
                        case 'Verified User':
                            $category_name = 'Verified Users';
                            break;
                        case 'Unverified User':
                            $category_name = 'Unverified Users';
                            break;
                    }

                    $titleParts = [$category_name];
                    if ($selectedBatch != '') {
                        $titleParts[] = 'from Batch ' . e($selectedBatch);
                    } else {
                        $titleParts[] = 'from All Batches';
                    }

                    $deptNameForTitle = 'All Departments';
                    if (!Auth::user()->admin()->is_super) {
                        $userDept = App\Models\Department::find(Auth::user()->admin()->office);
                        $deptNameForTitle = $userDept?->name ?? 'Their Department';
                    } elseif ($department) {
                        $deptNameForTitle = $department->name;
                    }
                    $titleParts[] = 'from ' . e($deptNameForTitle);

                    $courseNameForTitle = 'All Courses';
                    if ($course) {
                        $courseNameForTitle = $course->name;
                    }
                    $titleParts[] = e($courseNameForTitle);

                    if ($withField && $withValue && array_key_exists($withField, $withFieldsMap)) {
                        $titleParts[] = 'with ' . e($withFieldsMap[$withField]) . ' = "' . e($withValue) . '"';
                    }

                    $reportTitle = implode(' ', $titleParts);

                    @endphp
                    <h1 class="font-light text-blue-500 text-base">{{ $reportTitle }}</h1>

                    <div><i class="text-[10px]">Generated at:</i> {{ date_create()->format('Y-m-d H:i:s') }}</div>
                </div>

                <table class="w-full text-center mt-4 border-collapse border border-black">
                    <thead class="border-t border-b border-black bg-gray-200">
                        <th class="font-semibold py-3 border border-black">ID</th>
                        <th class="font-semibold border border-black">Name</th>
                        <th class="font-semibold border border-black">Username</th>
                        <th class="font-semibold border border-black">Student / Company ID</th>
                        <th class="font-semibold border border-black">Email</th>
                        <th class="font-semibold border border-black">Contact Number</th>
                    </thead>
                    <tbody>
                        @if(isset($users) && $users->count() > 0)
                            @foreach ($users as $user)
                            <tr class="border border-black">
                                <td class="py-1 border border-black">{{ $user->id }}</td>
                                @if ($user->role === 'Admin')
                                <td class="border border-black">{{ $user->adminRelation?->getFullnameAttribute() ?? 'N/A' }}</td>
                                @else
                                <td class="border border-black">{{ ($user->partialPersonal ?? $user->getPersonalBio())?->getFullnameAttribute() ?? 'N/A' }}</td>
                                @endif
                                <td class="border border-black">{{ $user->username }}</td>
                                @if ($user->role === 'Admin')
                                @php($admin = $user->adminRelation)
                                <td class="border border-black">{{ $admin?->position_id ?? 'N/A' }}</td>
                                <td class="border border-black">{{ $admin?->email_address ?? 'N/A' }}</td>
                                <td class="border border-black">{{ $admin?->phone_number ?? 'N/A' }}</td>
                                @else
                                <td class="border border-black">{{ ($user->partialPersonal ?? $user->getPersonalBio())?->student_id ?? 'N/A' }}</td>
                                <td class="border border-black">{{ ($user->partialPersonal ?? $user->getPersonalBio())?->email_address ?? 'N/A' }}</td>
                                <td class="border border-black">{{ ($user->partialPersonal ?? $user->getPersonalBio())?->phone_number ?? 'N/A' }}</td>
                                @endif
                            </tr>
                            @endforeach
                        @else
                             <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500 border border-black">No users found matching the criteria.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection