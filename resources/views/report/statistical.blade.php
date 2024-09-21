@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 w-full h-full p-8">
    <h1 class="font-medium tracking-widest text-lg">Statistical Report</h1>
    <p class="text-gray-400 text-xs mb-2">Report / <span class="text-blue-500">Statistical</span></p>

    <div class="shadow rounded-lg mt-4">
        <form action="/report/statistical/generate">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
                <p class="tracking-wider font-semibold mr-4">All</p>
                <select class="g-gray-100 p-2 rounded border text-gray-400" name="category" id="category">
                    <option value="All Users">All Users</option>
                    <!-- <option value="Registered Users">Registered Users</option> -->
                    <option value="Employed Alumni">Employed Alumni</option>
                    <option value="Unemployed Alumni">Unemployed Alumni</option>
                    <option value="Self-Employed Alumni">Self-Employed Alumni</option>
                    <option value="Student Alumni">Student Alumni</option>
                    <option value="Retired Alumni">Retired Alumni</option>
                </select>

                <p class="tracking-wider font-semibold ml-10 mr-4">Of</p>
                <select onchange="handleDepartmentChange()" class="g-gray-100 p-2 max-w-56 rounded border text-gray-400 mr-4" name="department" id="department">
                    <option value="-1">All Departments</option>
                    @php($depts = App\Models\Department::allValid())
                    @foreach ($depts as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>

                <select class="g-gray-100 p-2 min-w-56 rounded border text-gray-400" name="courses" id="courses">
                    <option value="-1">All Courses</option>
                </select>

                <div class="flex-1"></div>
                <button class="rounded-lg p-2 px-3 bg-blue-600 text-white" type="submit">Generate Report</button>
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
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->personalBio->student_id }}</td>
                    <td>{{ $user->personalBio->email_address }}</td>
                    <td>{{ $user->personalBio->phone_number }}</td>
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
    const category = document.getElementById('courses');

    async function handleDepartmentChange() {
        const response = await fetch('/department/courses/' + document.getElementById('department').value);
        const {
            courses
        } = await response.json();

        const option = document.createElement('option');
        option.value = '-1';
        option.innerHTML = 'All Courses';

        category.innerHTML = ''; 
        category.appendChild(option);

        for (let course of courses) {
            const child = document.createElement('option');
            child.innerText = course.name;
            child.value = course.id;

            category.appendChild(child);
        }
    }
</script>
@endsection