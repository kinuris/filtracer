@extends('layouts.admin')

@section('title', 'Statistical Report')

@section('content')
<?php

$department = App\Models\Department::find(request('department') ?? -1);
$course = App\Models\Course::find(request('courses') ?? -1);
$category = request('category');

if ($department && $course) {
    $query = App\Models\User::query()
        ->whereIn(
            'id',
            App\Models\User::compSet()
                ->orWhereHas('partialPersonal')
                ->get()
                ->pluck('id')
        )
        ->where('role', '!=', 'Admin')
        ->where('department_id', '=', $department->id)
        ->whereRelation('course', 'courses.id', '=', $course->id);

    switch ($category) {
        case "All Entities":
            $users = App\Models\User::query()->whereIn(
                'id',
                App\Models\User::compSet()
                    ->orWhereHas('partialPersonal')
                    ->where('role', '!=', 'Admin')
                    ->get()
                    ->merge(App\Models\User::where('role', '=', 'Admin')->get())
                    ->map(function ($user) use ($course) {
                        return $user->id;
                    }),
            )->paginate(6);
            break;
        case "All Users":
            break;
        case "Registered Users":
            throw new Exception('SHIT DONT WORK YET');
            break;
        case "Working Student":
        case "Employed Alumni":
        case "Unemployed Alumni":
        case "Self-Employed Alumni":
        case "Student Alumni":
        case "Retired Alumni":
            switch ($category) {
                case "Working Student":
                    $category = "Working Student";
                    break;
                case "Employed Alumni":
                    $category = "Employed";
                    break;
                case "Unemployed Alumni":
                    $category = "Unemployed";
                    break;
                case "Self-Employed Alumni":
                    $category = "Self-employed";
                    break;
                case "Student Alumni":
                    $category = "Student";
                    break;
                case "Retired Alumni":
                    $category = "Retired";
                    break;
            }

            $query->whereRelation('professionalRecords', 'employment_status', '=', $category);
            break;
        case "Verified Alumni":
        case "Unverified Alumni":
            $query->whereRelation('personalRecords', 'status', '=', $category === "Verified Alumni" ? 1 : 0);
            break;
    }

    $users = $query->paginate(6);
} else if ($department) {
    $query = App\Models\User::query()
        ->whereIn(
            'id',
            App\Models\User::compSet()
                ->orWhereHas('partialPersonal')
                ->get()
                ->pluck('id')
        )
        ->where('role', '!=', 'Admin')
        ->where('department_id', '=', $department->id)
        ->whereRelation('department', 'departments.id', '=', $department->id);

    switch ($category) {
        case "All Entities":
            $users = App\Models\User::query()->whereIn(
                'id',
                App\Models\User::compSet()
                    ->orWhereHas('partialPersonal')
                    ->where('role', '!=', 'Admin')
                    ->get()
                    ->merge(App\Models\User::where('role', '=', 'Admin')->get())
                    ->map(function ($user) use ($course) {
                        return $user->id;
                    }),
            )->paginate(6);
            break;
        case "All Users":
            break;
        case "Registered Users":
            break;
        case "Working Student":
        case "Employed Alumni":
        case "Unemployed Alumni":
        case "Self-Employed Alumni":
        case "Student Alumni":
        case "Retired Alumni":
            switch ($category) {
                case "Working Student":
                    $category = "Working Student";
                    break;
                case "Employed Alumni":
                    $category = "Employed";
                    break;
                case "Unemployed Alumni":
                    $category = "Unemployed";
                    break;
                case "Self-Employed Alumni":
                    $category = "Self-employed";
                    break;
                case "Student Alumni":
                    $category = "Student";
                    break;
                case "Retired Alumni":
                    $category = "Retired";
                    break;
            }

            $query->whereRelation('professionalRecords', 'employment_status', '=', $category);
            break;
        case "Verified Alumni":
        case "Unverified Alumni":
            $query->whereRelation('personalRecords', 'status', '=', $category === "Verified Alumni" ? 1 : 0);
            break;
    }

    $users = $query->paginate(6);
} else {
    switch ($category) {
        case "All Entities":
            $users = App\Models\User::query()->whereIn(
                'id',
                App\Models\User::compSet()
                    ->orWhereHas('partialPersonal')
                    ->where('role', '!=', 'Admin')
                    ->get()
                    ->merge(App\Models\User::where('role', '=', 'Admin')->get())
                    ->map(function ($user) use ($course) {
                        return $user->id;
                    }),
            )->paginate(6);
            break;
        case "All Users":
            $users = App\Models\User::compSet()
                ->orWhereHas('partialPersonal')
                ->where('role', '!=', 'Admin')
                ->paginate(6);
            break;
        case "Registered Users":
            break;
        case "Working Student":
        case "Employed Alumni":
        case "Unemployed Alumni":
        case "Self-Employed Alumni":
        case "Student Alumni":
        case "Retired Alumni":
            switch ($category) {
                case "Working Student":
                    $category = "Working Student";
                    break;
                case "Employed Alumni":
                    $category = "Employed";
                    break;
                case "Unemployed Alumni":
                    $category = "Unemployed";
                    break;
                case "Self-Employed Alumni":
                    $category = "Self-employed";
                    break;
                case "Student Alumni":
                    $category = "Student";
                    break;
                case "Retired Alumni":
                    $category = "Retired";
                    break;
            }

            $users = App\Models\User::compSet()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', $category)
                ->paginate(6);
            break;
        case "Verified Alumni":
        case "Unverified Alumni":
            $users = App\Models\User::partialSet()
                ->where('role', '!=', 'Admin')
                ->whereRelation('personalRecords', 'status', '=', $category === "Verified Alumni" ? 1 : 0)
                ->paginate(6);
            break;
        case "Verified Admin":
        case "Unverified Admin":
            $users = App\Models\User::query()
                ->where('role', '=', 'Admin')
                ->whereRelation('adminRelation', 'is_verified', '=', $category === "Verified Admin" ? 1 : 0)
                ->whereRelation('adminRelation', 'is_super', '=', 0)
                ->paginate(6);
            break;
        case "Verified User":
        case "Unverified User":
            $users = App\Models\User::query()
                ->where(function ($query) use ($category) {
                    if ($category === "Verified User") {
                        $query->where('role', '!=', 'Admin')
                            ->whereRelation('personalRecords', 'status', '=', 1);
                    } else {
                        $query->where('role', '!=', 'Admin')
                            ->where(function ($query) {
                                $query->whereDoesntHave('personalRecords')
                                    ->orWhereRelation('personalRecords', 'status', '=', 0);
                            });
                    }
                })
                ->orWhere(function ($query) use ($category) {
                    $query->where('role', '=', 'Admin')
                        ->whereRelation('adminRelation', 'is_verified', '=', $category === "Verified User" ? 1 : 0)
                        ->whereRelation('adminRelation', 'is_super', '=', 0);
                })
                ->paginate(6);
            break;
    }
}
?>

<div class="bg-gray-100 w-full h-full p-8 max-h-[calc(100%-4rem)] overflow-auto">
    <h1 class="font-medium tracking-widest text-lg">Statistical Report</h1>
    <p class="text-gray-400 text-xs mb-2">Report / <span class="text-blue-500">Statistical</span></p>

    <div class="shadow rounded-lg mt-4">
        <form action="/report/statistical/generate">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
                <p class="tracking-wider font-semibold mr-4">All</p>
                <select onchange="handleChangeCategory()" class="g-gray-100 p-2 rounded border text-gray-400" name="category" id="category">
                    <optgroup label="All">
                        @if (Auth::user()->admin()->is_super)
                        <option @if (request('category')=='All Entities' ) selected @endif value="All Entities">All Users</option>
                        @endif
                        <option @if (request('category')=='All Users' ) selected @endif value="All Users">Alumni</option>
                    </optgroup>
                    <optgroup label="Employment">
                        <!-- <option value="Registered Users">Registered Users</option> -->
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

                <p class="tracking-wider font-semibold ml-4 mr-4">From</p>
                <select @if(!Auth::user()->admin()->is_super) disabled @endif onchange="handleDepartmentChange()" class="g-gray-100 p-2 max-w-56 rounded border text-gray-400 mr-4" name="department" id="department">
                    <option value="-1">All Departments</option>
                    @php
                        $depts = App\Models\Department::allValid()
                    @endphp
                    @foreach ($depts as $dept)
                    <option @if(Auth::user()->admin()->is_super) @if (request('department')==$dept->id) selected @endif value="{{ $dept->id }}" @else @if(Auth::user()->admin()->office == $dept->id && !Auth::user()->admin()->is_super) selected @endif value="{{ $dept->id }}" @endif>{{ $dept->name }}</option>
                    @endforeach
                </select>

                <input type="hidden" name="department" value="{{ request('department') ?? '' }}">

                <select @if(request('locked')) disabled @endif onchange="handleCourseChange()" class="g-gray-100 p-2 min-w-56 rounded border text-gray-400" name="courses" id="courses">
                    <option value="-1">All Courses</option>
                    @php
                        $dept = $adminDept ?? App\Models\Department::find(request('department'))
                    @endphp
                    @if (isset($dept))
                    @foreach ($dept->getCourses() as $course)
                    <option @if (request('courses')==$course->id) selected @endif value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                    @endif
                </select>

                <div class="flex-1"></div>
                <button class="rounded p-2 ml-1 text-sm bg-blue-600 text-white" type="submit">Generate Report</button>
            </div>
        </form>
    </div>

    <div class="shadow rounded-lg mt-4">
        <form action="">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-t-lg justify-between">
                <p class="font-bold">
                    @php
                    $category_name = request('category');
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
                        default:
                            $category_name = 'All Users';
                            break;
                    }
                    @endphp
                    {{ $category_name }}
                    @if(!Auth::user()->admin()->is_super)
                    from {{ $dept->name }}
                    @elseif ($dept == null)
                    from All Departments
                    @elseif ($dept != null)
                    from {{ $dept->name }}
                    @endif
                    @php
                        $course = App\Models\Course::find(request('courses'));
                    @endphp
                    @if($course == null)
                    from All Courses
                    @elseif ($course != null)
                    {{ $course->name }}
                    @endif
                </p>
                <input value="{{ request('search') ?? '' }}" class="bg-gray-100 p-2 rounded border min-w-[max(33%,270px)]" placeholder="Search..." type="text" name="search" id="search">
            </div>
        </form>
        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <th class="py-4 font-thin">ID</th>
                <th class="font-thin">Name</th>
                <th class="font-thin">Student ID</th>
                <th class="font-thin">Email</th>
                <th class="font-thin">Contact Number</th>
                <th class="font-thin">Action</th>
            </thead>
            <tbody class="bg-white text-center">
                @foreach ($users as $user)
                <tr class="border-b text-gray-500">
                    <td class="text-blue-900 py-3 px-8 font-thin">{{ $user->id }}</td>
                    <td>
                        @if($user->role == 'Admin')
                        {{ $user->admin()->first_name }}
                        {{ $user->admin()->middle_name ? substr($user->admin()->middle_name, 0, 1) . '. ' : '' }}
                        {{ $user->admin()->last_name }}
                        @else
                        {{ ($user->personalBio ? $user->personalBio->first_name : $user->partialPersonal->first_name) }}
                        {{ ($user->personalBio ? ($user->personalBio->middle_name ? substr($user->personalBio->middle_name, 0, 1) . '. ' : '') : ($user->partialPersonal->middle_name ? substr($user->partialPersonal->middle_name, 0, 1) . '. ' : '')) }}
                        {{ ($user->personalBio ? $user->personalBio->last_name : $user->partialPersonal->last_name) }}
                        {{ ($user->personalBio ? ($user->personalBio->suffix ? $user->personalBio->suffix : '') : ($user->partialPersonal->suffix ? $user->partialPersonal->suffix : '')) }}
                        @endif
                    </td>
                    <td>
                        @if($user->role == 'Admin')
                        {{ $user->admin()->position_id }}
                        @else
                        {{ $user->personalBio ? $user->personalBio->student_id : $user->partialPersonal->student_id }}
                        @endif
                    </td>
                    <td>
                        @if($user->role == 'Admin')
                        {{ $user->admin()->email_address }}
                        @else
                        {{ $user->personalBio ? $user->personalBio->email_address : $user->partialPersonal->email_address }}
                        @endif
                    </td>
                    <td>
                        @if($user->role == 'Admin')
                        {{ $user->admin()->phone_number }}
                        @else
                        {{ $user->personalBio ? $user->personalBio->phone_number : $user->partialPersonal->phone_number }}
                        @endif
                    </td>
                    <td>
                        <a class="bg-blue-600 text-white p-2 rounded-md" href="/user/view/{{ $user->id }}">See Profile</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="bg-white rounded-b-lg p-3">
            {{ $users->withQueryString()->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function handleDepartmentChange() {
        const department = document.getElementById('department').value;

        const params = new URLSearchParams(window.location.search);

        params.delete('courses');
        params.set('department', department);

        window.location.href = '/report/statistical?' + params.toString();
    }

    function handleCourseChange() {
        const course = document.getElementById('courses').value;

        const params = new URLSearchParams(window.location.search);
        params.set('courses', course);

        window.location.href = '/report/statistical?' + params.toString();
    }

    function handleChangeCategory() {
        const category = document.getElementById('category').value;

        const params = new URLSearchParams(window.location.search);
        if (category === "Verified Admin" || category === "Unverified Admin" || category === "Verified User" || category === "Unverified User") {
            params.set('locked', true);
        } else {
            params.delete('locked');
        }

        params.set('category', category);

        window.location.href = '/report/statistical?' + params.toString();
    }
</script>
@endsection