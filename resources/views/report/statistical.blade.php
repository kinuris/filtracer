@extends('layouts.admin')

@section('title', 'Statistical Report')

@section('content')
<?php

$department = App\Models\Department::find(request('department') ?? -1);
$course = App\Models\Course::find(request('courses') ?? -1);
$category = request('category');
$selectedBatch = request('batch') ?? ''; // Get selected batch, default to empty string for 'All Batches'

// Helper function to add batch filter if selected
// Ensure the relationship name on the User model is correct (e.g., 'educationRecords')
function applyBatchFilter($query, $selectedBatch)
{
    if ($selectedBatch !== '') {
        // Use whereHas to filter users who have at least one education record
        // where the 'end' year matches the selected batch.
        $query = $query->whereHas('educationalRecord', function ($subQuery) use ($selectedBatch) {
            $subQuery->where('end', '=', $selectedBatch);
        });
    }
    // dd($selectedBatch, $query->get());
    return $query;
}

// Initialize $users to null or an empty paginator instance
$users = null;

// Determine the base query conditions based on department and course
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

// Determine admin query conditions based on department and course
$adminQueryConditions = function ($query) use ($department, $course) {
    $query->where('role', '=', 'Admin');
    if ($department) {
        $query->where('department_id', '=', $department->id); // Assuming admins belong to dept
    }
    if ($course) {
        // Adjust if admin course relation differs or isn't applicable
        $query->whereRelation('course', 'courses.id', '=', $course->id);
    }
};


// Apply filters based on category
switch ($category) {
    case "All Entities":
        $alumniQuery = App\Models\User::query()->where($baseQueryConditions);
        $alumniQuery = applyBatchFilter($alumniQuery, $selectedBatch); // Filter alumni part
        $alumniIds = $alumniQuery->pluck('id');

        $adminQuery = App\Models\User::query()->where($adminQueryConditions);
        $adminIds = $adminQuery->pluck('id'); // Admins not filtered by batch

        $users = App\Models\User::query()->whereIn('id', $alumniIds->merge($adminIds))->paginate(6);
        break;

    case "All Users":
        $query = App\Models\User::query()->where($baseQueryConditions);
        $query = applyBatchFilter($query, $selectedBatch); // Apply batch filter
        $users = $query->paginate(6);
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
            case "Self-Employed Alumni": $dbCategory = "Self-employed"; break;
            case "Student Alumni": $dbCategory = "Student"; break;
            case "Retired Alumni": $dbCategory = "Retired"; break;
            // Working Student maps directly
        }
        $query = App\Models\User::query()->where($baseQueryConditions); // Start with base conditions
        $query->whereRelation('professionalRecords', 'employment_status', '=', $dbCategory);
        $query = applyBatchFilter($query, $selectedBatch); // Apply batch filter
        $users = $query->paginate(6);
        break;

    case "Verified Alumni":
    case "Unverified Alumni":
        $query = App\Models\User::query()->where($baseQueryConditions); // Start with base conditions
        $query->whereRelation('personalRecords', 'status', '=', $category === "Verified Alumni" ? 1 : 0);
        $query = applyBatchFilter($query, $selectedBatch); // Apply batch filter
        $users = $query->paginate(6);
        break;

    case "Verified Admin":
    case "Unverified Admin":
        // Batch filter typically doesn't apply to Admins
        $query = App\Models\User::query()->where($adminQueryConditions); // Start with admin conditions
        $query->whereRelation('adminRelation', 'is_verified', '=', $category === "Verified Admin" ? 1 : 0)
              ->whereRelation('adminRelation', 'is_super', '=', 0);
        $users = $query->paginate(6);
        break;

    case "Verified User":
    case "Unverified User":
        // Complex case: Filter alumni by batch, admins likely not
        $alumniQuery = App\Models\User::query()
            ->where($baseQueryConditions) // Apply base scope (dept/course if selected)
            ->where(function ($q_inner) use ($category) {
                if ($category === "Verified User") {
                    $q_inner->where(function ($q_status) {
                            $q_status->whereHas('personalRecords', fn($pr) => $pr->where('status', 1))
                                ->orWhereHas('partialPersonal'); // Assuming partialPersonal implies verified enough
                        });
                } else { // Unverified User
                    $q_inner->where(function ($q_status) {
                            $q_status->whereDoesntHave('personalRecords', fn($pr) => $pr->where('status', 1))
                                ->whereDoesntHave('partialPersonal');
                        });
                }
            });

        $alumniQuery = applyBatchFilter($alumniQuery, $selectedBatch); // Filter alumni part by batch
        $alumniIds = $alumniQuery->pluck('id');

        $adminQuery = App\Models\User::query()
            ->where($adminQueryConditions) // Apply admin scope (dept/course if selected)
            ->whereRelation('adminRelation', 'is_verified', '=', $category === "Verified User" ? 1 : 0)
            ->whereRelation('adminRelation', 'is_super', '=', 0);
        $adminIds = $adminQuery->pluck('id'); // Admins not filtered by batch

        $users = App\Models\User::query()->whereIn('id', $alumniIds->merge($adminIds))->paginate(6);
        break;

    default: // Fallback if no category matches or category is null/empty, show all alumni filtered by batch
        $query = App\Models\User::query()->where($baseQueryConditions);
        $query = applyBatchFilter($query, $selectedBatch);
        $users = $query->paginate(6);
        break;
}

// Apply search filter if present, after category/batch/dept/course filters
if (request()->filled('search') && isset($users)) {
    $searchTerm = '%' . request('search') . '%';
    $userIds = $users->pluck('id'); // Get IDs from the already filtered & paginated set for this page *before* search

    // Re-query based on the IDs collected *before* pagination, then apply search
    // Note: This search logic might need refinement. Searching *after* pagination limits search to the current page.
    // A better approach might be to apply search *before* pagination.
    // However, keeping the original logic for now.
    $query = App\Models\User::whereIn('id', $userIds) // Query only within the pre-filtered set
        ->where(function ($q) use ($searchTerm) {
            // Search in User model fields (adjust if needed)
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
              // Search in Admin Relation
              ->orWhereHas('adminRelation', function ($ar) use ($searchTerm) {
                  $ar->where('first_name', 'like', $searchTerm)
                     ->orWhere('last_name', 'like', $searchTerm)
                     ->orWhere('position_id', 'like', $searchTerm) // Assuming position_id is searchable like student_id
                     ->orWhere('email_address', 'like', $searchTerm)
                     ->orWhere('phone_number', 'like', $searchTerm);
              });
        });
    // Re-paginate the search results, keeping other query params
    $users = $query->paginate(6)->withQueryString();
}


?>

<div class="bg-gray-100 w-full h-full p-4 md:p-8 max-h-[calc(100%-4rem)] overflow-auto">
    <h1 class="font-medium tracking-widest text-lg">Statistical Report</h1>
    <p class="text-gray-400 text-xs mb-4">Report / <span class="text-blue-500">Statistical</span></p>

    {{-- Filters Section --}}
    <div class="bg-white shadow rounded-lg mb-6">
        <form id="filterForm" action="{{ route('report.statistical') }}" method="GET">
            <div class="p-4 md:p-6 space-y-4"> {{-- Use space-y for vertical spacing --}}

                {{-- Filters Row --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    {{-- Category Select --}}
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select onchange="handleChangeCategory()" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" name="category" id="category">
                            <optgroup label="All">
                                @if (Auth::user()->admin()->is_super)
                                <option @if (request('category')=='All Entities' ) selected @endif value="All Entities">All Users</option>
                                @endif
                                <option @if (request('category', 'All Users' )=='All Users' ) selected @endif value="All Users">Alumni</option> {{-- Default to All Users --}}
                            </optgroup>
                            <optgroup label="Employment">
                                <option @if (request('category')=='Employed Alumni' ) selected @endif value="Employed Alumni">Employed Alumni</option>
                                <option @if (request('category')=='Working Student' ) selected @endif value="Working Student">Working Student</option>
                                <option @if (request('category')=='Unemployed Alumni' ) selected @endif value="Unemployed Alumni">Unemployed Alumni</option>
                                <option @if (request('category')=='Self-Employed Alumni' ) selected @endif value="Self-Employed Alumni">Self-Employed Alumni</option>
                                <option @if (request('category')=='Student Alumni' ) selected @endif value="Student Alumni">Student Alumni</option>
                                <option @if (request('category')=='Retired Alumni' ) selected @endif value="Retired Alumni">Retired Alumni</option>
                            </optgroup>
                            @if(Auth::user()->admin()->is_super)
                            <optgroup label="User Status">
                                <option @if (request('category')=='Verified Alumni' ) selected @endif value="Verified Alumni">Verified Alumni</option>
                                <option @if (request('category')=='Unverified Alumni' ) selected @endif value="Unverified Alumni">Unverified Alumni</option>
                                <option @if (request('category')=='Verified Admin' ) selected @endif value="Verified Admin">Verified Admin</option>
                                <option @if (request('category')=='Unverified Admin' ) selected @endif value="Unverified Admin">Unverified Admin</option>
                                <option @if (request('category')=='Verified User' ) selected @endif value="Verified User">Verified Users</option>
                                <option @if (request('category')=='Unverified User' ) selected @endif value="Unverified User">Unverified Users</option>
                            </optgroup>
                            @endif
                        </select>
                    </div>

                    {{-- Batch Dropdown --}}
                    <div>
                        <label for="batch" class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                        <select onchange="handleBatchChange()" name="batch" id="batch" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Batches</option>
                            @php
                            // Fetch distinct batch years efficiently
                            $batches = App\Models\EducationRecord::whereNotNull('end')
                            ->distinct()
                            ->orderBy('end', 'asc') // Sort ascending
                            ->pluck('end')
                            ->unique(); // Ensure uniqueness
                            @endphp
                            @foreach ($batches as $batchYear)
                            <option value="{{ $batchYear }}" {{ $selectedBatch == $batchYear ? 'selected' : '' }}>{{ $batchYear }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Department Select --}}
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select @if(!Auth::user()->admin()->is_super) disabled @endif onchange="handleDepartmentChange()" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 disabled:text-gray-500" name="department" id="department">
                            <option value="-1">All Departments</option>
                            @php
                            $depts = App\Models\Department::allValid()
                            @endphp
                            @foreach ($depts as $deptOption) {{-- Renamed loop var --}}
                            {{-- Logic to select based on request or user's non-super admin office --}}
                            <option value="{{ $deptOption->id }}"
                                @if(Auth::user()->admin()->is_super)
                                {{ request('department') == $deptOption->id ? 'selected' : '' }}
                                @else
                                {{ Auth::user()->admin()->office == $deptOption->id ? 'selected' : '' }}
                                @endif
                                >{{ $deptOption->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Add hidden input if non-super admin to ensure their dept is submitted --}}
                    @if(!Auth::user()->admin()->is_super)
                    <input type="hidden" name="department" value="{{ Auth::user()->admin()->office }}">
                    @endif

                    {{-- Course Select --}}
                    <div>
                        <label for="courses" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                        <select @if(request('locked') || (!Auth::user()->admin()->is_super && !request('department')) || !$department) disabled @endif onchange="handleCourseChange()" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 disabled:text-gray-500" name="courses" id="courses">
                            <option value="-1">All Courses</option>
                            @php
                            // Determine the department to fetch courses from
                            $deptIdForCourses = null;
                            if (!Auth::user()->admin()->is_super) {
                                $deptIdForCourses = Auth::user()->admin()->office;
                            } elseif (request('department') && request('department') != -1) {
                                $deptIdForCourses = request('department');
                            }
                            // Fetch courses only if a valid department ID is determined
                            $coursesList = $deptIdForCourses ? App\Models\Department::find($deptIdForCourses)?->getCourses() ?? collect() : collect();
                            @endphp
                            @foreach ($coursesList as $courseOption)
                            <option value="{{ $courseOption->id }}" {{ request('courses') == $courseOption->id ? 'selected' : '' }}>{{ $courseOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Search Row --}}
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input value="{{ request('search') ?? '' }}" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Search name, ID, email..." type="text" name="search" id="search">
                </div>

                {{-- Action Buttons Row --}}
                <div class="flex flex-col sm:flex-row gap-2 justify-end">
                    {{-- Filter Button --}}
                    <button class="w-full sm:w-auto rounded-md px-4 py-2 text-sm bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" type="submit">Filter</button>
                    {{-- Link to Generate Report View --}}
                    <a href="{{ route('report.statistical.generate', request()->query()) }}" class="w-full sm:w-auto rounded-md px-4 py-2 text-sm bg-green-600 text-white text-center hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" target="_blank">Generate Report</a>
                </div>

            </div>
        </form>
    </div>

    {{-- Results Section --}}
    <div class="shadow rounded-lg">
        {{-- Report Title Area --}}
        <div class="bg-white py-3 px-4 md:px-6 border-b rounded-t-lg">
            <p class="font-semibold text-sm md:text-base text-gray-800">
                @php
                $category_name = request('category', 'All Users'); // Default category
                // ... switch for $category_name ...
                switch ($category_name) {
                    case 'All Entities': $category_name = 'All Users'; break;
                    case 'All Users': $category_name = 'Alumni'; break;
                    case 'Employed Alumni': $category_name = 'Employed Alumni'; break;
                    case 'Working Student': $category_name = 'Working Student'; break;
                    case 'Unemployed Alumni': $category_name = 'Unemployed Alumni'; break;
                    case 'Self-Employed Alumni': $category_name = 'Self-Employed Alumni'; break;
                    case 'Student Alumni': $category_name = 'Student Alumni'; break;
                    case 'Retired Alumni': $category_name = 'Retired Alumni'; break;
                    case 'Verified Alumni': $category_name = 'Verified Alumni'; break;
                    case 'Unverified Alumni': $category_name = 'Unverified Alumni'; break;
                    case 'Verified Admin': $category_name = 'Verified Admin'; break;
                    case 'Unverified Admin': $category_name = 'Unverified Admin'; break;
                    case 'Verified User': $category_name = 'Verified Users'; break;
                    case 'Unverified User': $category_name = 'Unverified Users'; break;
                    // No default needed due to request default above
                }

                // Build title parts
                $titleParts = [$category_name];
                if ($selectedBatch != '') {
                    $titleParts[] = 'from Batch ' . e($selectedBatch); // Escape output
                } else {
                    $titleParts[] = 'from All Batches';
                }

                // Determine department name for title
                $deptNameForTitle = 'All Departments';
                if (!Auth::user()->admin()->is_super) {
                    $userDept = App\Models\Department::find(Auth::user()->admin()->office);
                    $deptNameForTitle = $userDept?->name ?? 'Their Department';
                } elseif ($department) {
                    $deptNameForTitle = $department->name;
                }
                $titleParts[] = 'from ' . e($deptNameForTitle);

                // Determine course name for title
                $courseNameForTitle = 'All Courses';
                if ($course) {
                    $courseNameForTitle = $course->name;
                }
                $titleParts[] = e($courseNameForTitle);

                $reportTitle = implode(' ', $titleParts);

                @endphp
                {{ $reportTitle }}
                @if(request()->filled('search'))
                    <span class="text-gray-500 font-normal"> (matching search "{{ e(request('search')) }}")</span>
                @endif
            </p>
        </div>
        <div class="overflow-x-auto"> {{-- Make table horizontally scrollable on small screens --}}
            <table class="w-full min-w-[640px]"> {{-- Ensure minimum width for table layout --}}
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider border-b">
                    <tr> {{-- Use <tr> for thead row --}}
                        <th class="py-3 px-3 text-left font-medium">ID</th> {{-- Padding and alignment --}}
                        <th class="py-3 px-3 text-left font-medium">Name</th>
                        <th class="py-3 px-3 text-left font-medium">Student ID / Position</th>
                        <th class="py-3 px-3 text-left font-medium">Email</th>
                        <th class="py-3 px-3 text-left font-medium">Contact Number</th>
                        <th class="py-3 px-3 text-center font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                    @if(isset($users) && $users->count() > 0)
                    @foreach ($users as $user)
                    <tr class="text-gray-700 hover:bg-gray-50"> {{-- Hover effect --}}
                        <td class="py-3 px-3 whitespace-nowrap">{{ $user->id }}</td> {{-- Padding --}}
                        <td class="py-3 px-3 whitespace-nowrap"> {{-- Padding --}}
                            @if($user->role == 'Admin')
                            {{ $user->adminRelation?->first_name ?? '' }} {{-- Use adminRelation --}}
                            {{ $user->adminRelation?->middle_name ? substr($user->adminRelation->middle_name, 0, 1) . '. ' : '' }}
                            {{ $user->adminRelation?->last_name ?? '' }}
                            @else
                            {{ ($user->personalBio ?? $user->partialPersonal)?->first_name ?? '' }}
                            {{ ($user->personalBio ?? $user->partialPersonal)?->middle_name ? substr(($user->personalBio ?? $user->partialPersonal)->middle_name, 0, 1) . '. ' : '' }}
                            {{ ($user->personalBio ?? $user->partialPersonal)?->last_name ?? '' }}
                            {{ ($user->personalBio ?? $user->partialPersonal)?->suffix ?? '' }}
                            @endif
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap"> {{-- Padding --}}
                            @if($user->role == 'Admin')
                            {{ $user->adminRelation?->position_id ?? 'N/A' }} {{-- Use adminRelation --}}
                            @else
                            {{ ($user->personalBio ?? $user->partialPersonal)?->student_id ?? 'N/A' }}
                            @endif
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap"> {{-- Padding --}}
                            @if($user->role == 'Admin')
                            {{ $user->adminRelation?->email_address ?? 'N/A' }} {{-- Use adminRelation --}}
                            @else
                            {{ ($user->personalBio ?? $user->partialPersonal)?->email_address ?? 'N/A' }}
                            @endif
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap"> {{-- Padding --}}
                            @if($user->role == 'Admin')
                            {{ $user->adminRelation?->phone_number ?? 'N/A' }} {{-- Use adminRelation --}}
                            @else
                            {{ ($user->personalBio ?? $user->partialPersonal)?->phone_number ?? 'N/A' }}
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center whitespace-nowrap"> {{-- Padding --}}
                            <a class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="/user/view/{{ $user->id }}">See Profile</a> {{-- Styled link --}}
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-500">No users found matching the criteria.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if(isset($users) && $users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages()) {{-- Check if $users is paginator and has pages --}}
        <div class="bg-white rounded-b-lg p-4 border-t">
            {{-- Ensure pagination links include all current query parameters --}}
            {{ $users->withQueryString()->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
    function handleBatchChange() {
        // Submit the main form when batch changes
        document.getElementById('filterForm').submit();
    }

    function handleDepartmentChange() {
        // Clear the course selection when department changes before submitting
        const courseSelect = document.getElementById('courses');
        if (courseSelect) {
            courseSelect.value = '-1'; // Reset to "All Courses"
        }
        document.getElementById('filterForm').submit();
    }

    function handleCourseChange() {
        // Submit the main form
        document.getElementById('filterForm').submit();
    }

    function handleChangeCategory() {
        // Submit the main form
        document.getElementById('filterForm').submit();
    }

    // Optional: Re-enable course dropdown if a valid department is selected on page load
    document.addEventListener('DOMContentLoaded', function() {
        const deptSelect = document.getElementById('department');
        const courseSelect = document.getElementById('courses');
        const isSuperAdmin = {{ Auth::user()->admin()->is_super ? 'true' : 'false' }};

        // Function to update course dropdown state
        function updateCourseState() {
            if (courseSelect) {
                const deptValue = deptSelect ? deptSelect.value : null;
                // Enable if super admin and a department is selected, OR if not super admin (dept is fixed)
                const shouldEnable = (isSuperAdmin && deptValue && deptValue !== '-1') || !isSuperAdmin;
                 // Also check if coursesList was populated server-side
                const hasCourses = {{ $coursesList->isNotEmpty() ? 'true' : 'false' }};
                courseSelect.disabled = !(shouldEnable && hasCourses);

                // Tailwind handles disabled styling via the 'disabled:' prefix in classes
            }
        }

        // Initial check on page load
        updateCourseState();

        // Add listener if department select exists (for super admins)
        // Note: The change itself triggers a form submit which reloads the page,
        // so this listener primarily ensures correct state on initial load.
        // The updateCourseState() call above handles the initial load state.
    });

</script>
@endsection
