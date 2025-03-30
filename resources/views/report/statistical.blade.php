@extends('layouts.admin')

@section('title', 'Statistical Report')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 max-h-[calc(100%-4rem)] overflow-auto">
    <h1 class="font-medium tracking-widest text-lg">Statistical Report</h1>
    <p class="text-gray-400 text-xs mb-2">Report / <span class="text-blue-500">Statistical</span></p>

    <div class="shadow rounded-lg mt-4">
        <form action="/report/statistical/generate">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
                <p class="tracking-wider font-semibold mr-4">All</p>
                <select onchange="handleChangeCategory()" class="g-gray-100 p-2 rounded border text-gray-400" name="category" id="category">
                    <optgroup label="All">
                        <option @if (request('category')=='All Entities' ) selected @endif value="All Entities">All Users</option>
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
                    <optgroup label="User Status">
                        <option @if (request('category')=='Verified Alumni' ) selected @endif value="Verified Alumni">Verified Alumni</option>
                        <option @if (request('category')=='Unverified Alumni' ) selected @endif value="Unverified Alumni">Unverified Alumni</option>
                        <option @if (request('category')=='Verified Admin' ) selected @endif value="Verified Admin">Verified Admin</option>
                        <option @if (request('category')=='Unverified Admin' ) selected @endif value="Unverified Admin">Unverified Admin</option>
                        <option @if (request('category')=='Verified User' ) selected @endif value="Verified User">Verified Users</option>
                        <option @if (request('category')=='Unverified User' ) selected @endif value="Unverified User">Unverified Users</option>
                    </optgroup>
                </select>

                <p class="tracking-wider font-semibold ml-4 mr-4">From</p>
                <select @if(request('locked')) disabled @endif onchange="handleDepartmentChange()" class="g-gray-100 p-2 max-w-56 rounded border text-gray-400 mr-4" name="department" id="department">
                    <option value="-1">All Departments</option>
                    @php($depts = App\Models\Department::allValid())
                    @foreach ($depts as $dept)
                    <option @if (request('department')==$dept->id) selected @endif value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>

                <select @if(request('locked')) disabled @endif onchange="handleCourseChange()" class="g-gray-100 p-2 min-w-56 rounded border text-gray-400" name="courses" id="courses">
                    <option value="-1">All Courses</option>
                    @php($dept = App\Models\Department::find(request('department')))
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
                <p class="font-bold">All Users from All Departments from All Courses</p>
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
                        {{ ($user->personalBio ? $user->personalBio->first_name : $user->partialPersonal->first_name) }}
                        {{ ($user->personalBio ? ($user->personalBio->middle_name ? substr($user->personalBio->middle_name, 0, 1) . '. ' : '') : ($user->partialPersonal->middle_name ? substr($user->partialPersonal->middle_name, 0, 1) . '. ' : '')) }}
                        {{ ($user->personalBio ? $user->personalBio->last_name : $user->partialPersonal->last_name) }}
                        {{ ($user->personalBio ? ($user->personalBio->suffix ? $user->personalBio->suffix : '') : ($user->partialPersonal->suffix ? $user->partialPersonal->suffix : '')) }}
                    </td>
                    <td>{{ $user->personalBio ? $user->personalBio->student_id : $user->partialPersonal->student_id }}</td>
                    <td>{{ $user->personalBio ? $user->personalBio->email_address : $user->partialPersonal->email_address }}</td>
                    <td>{{ $user->personalBio ? $user->personalBio->phone_number : $user->partialPersonal->phone_number }}</td>
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

    // const category = document.getElementById('courses');

    // async function handleDepartmentChange() {
    //     const response = await fetch('/department/courses/' + document.getElementById('department').value);
    //     const {
    //         courses
    //     } = await response.json();

    //     const option = document.createElement('option');
    //     option.value = '-1';
    //     option.innerHTML = 'All Courses';

    //     category.innerHTML = '';
    //     category.appendChild(option);

    //     for (let course of courses) {
    //         const child = document.createElement('option');
    //         child.innerText = course.name;
    //         child.value = course.id;

    //         category.appendChild(child);
    //     }
    // }
</script>
@endsection