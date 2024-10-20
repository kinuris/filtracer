@extends('layouts.admin')

@section('title', 'Statistical Report')

<?php

$department = App\Models\Department::find(request('department') ?? -1);
$course = App\Models\Course::find(request('courses') ?? -1);
$category = request('category');

if ($department && $course) {
    switch ($category) {
        case "All Users":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('course', 'courses.id', '=', $course->id)
                ->get();
            break;
        case "Registered Users":
            throw new Exception('SHIT DONT WORK YET');
            break;
        case "Working Student":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Working Student')
                ->whereRelation('course', 'courses.id', '=', $course->id)
                ->get();
            break;
        case "Employed Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Employed')
                ->whereRelation('course', 'courses.id', '=', $course->id)
                ->get();
            break;
        case "Unemployed Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Unemployed')
                ->whereRelation('course', 'courses.id', '=', $course->id)
                ->get();
            break;
        case "Self-Employed Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Self-employed')
                ->whereRelation('course', 'courses.id', '=', $course->id)
                ->get();
            break;
        case "Student Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Student')
                ->whereRelation('course', 'courses.id', '=', $course->id)
                ->get();
            break;
        case "Retired Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Retired')
                ->whereRelation('course', 'courses.id', '=', $course->id)
                ->get();
            break;
    }
} else if ($department) {
    switch ($category) {
        case "All Users":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('department', 'departments.id', '=', $department->id)
                ->get();
            break;
        case "Registered Users":
            break;
        case "Working Student":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Working Student')
                ->whereRelation('department', 'departments.id', '=', $department->id)
                ->get();
            break;
        case "Employed Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Employed')
                ->whereRelation('department', 'departments.id', '=', $department->id)
                ->get();
            break;
        case "Unemployed Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Unemployed')
                ->whereRelation('department', 'departments.id', '=', $department->id)
                ->get();
            break;
        case "Self-Employed Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Self-employed')
                ->whereRelation('department', 'departments.id', '=', $department->id)
                ->get();
            break;
        case "Student Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Student')
                ->whereRelation('department', 'departments.id', '=', $department->id)
                ->get();
            break;
        case "Retired Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Retired')
                ->whereRelation('department', 'departments.id', '=', $department->id)
                ->get();
            break;
    }
} else {
    switch ($category) {
        case "All Users":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->get();
            break;
        case "Registered Users":
            break;
        case "Working Student":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Working Student')
                ->get();
            break;
        case "Employed Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Employed')
                ->get();
            break;
        case "Unemployed Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Unemployed')
                ->get();
            break;
        case "Self-Employed Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Self-employed')
                ->get();
            break;
        case "Student Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Student')
                ->get();
            break;
        case "Retired Alumni":
            $users = App\Models\User::query()
                ->where('role', '!=', 'Admin')
                ->whereRelation('professionalRecords', 'employment_status', '=', 'Retired')
                ->get();
            break;
    }
}
?>

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)] overflow-auto">
    <div class="flex">
        <h1 class="font-medium tracking-widest text-lg">Statistical Report</h1>
        <div class="flex-1"></div>

        @php ($tw = Vite::asset('resources/css/app.css'))
        <button class="text-white bg-blue-600 p-2 px-3 rounded" onclick="printJS({ printable: 'printableStat', type: 'html', css: '{{ $tw }}' })">Print</button>
    </div>
    <div class="shadow rounded-lg mt-4">
        <div class="bg-white p-4 flex place-items-center border-b rounded-lg">
            <div id="printableStat" class="border border-black w-full p-4 rounded-lg">
                <div class="flex justify-between">
                    @if ($department && $course)
                    <h1 class="font-light text-blue-500"><span class="mx-1 italic font-bold">{{ $category }}</span> of <span class="italic font-bold mx-1">{{ $department->name }}</span> in <span class="italic font-bold mx-1">{{ $course->name }}</span></h1>
                    @elseif ($department)
                    <h1 class="font-light text-blue-500"><span class="mx-1 italic font-bold">{{ $category }}</span> of <span class="italic font-bold mx-1">{{ $department->name }}</span> in <span class="italic font-bold mx-1">All Courses</span></h1>
                    @else
                    <h1 class="font-light text-blue-500"><span class="mx-1 italic font-bold">{{ $category }}</span> of <span class="italic font-bold mx-1">All Departments</span> in <span class="italic font-bold mx-1">All Courses</span></h1>
                    @endif

                    <div>{{ date_create()->format('Y-m-d H:i:s') }}</div>
                </div>
                <table class="w-full text-center mt-4">
                    <thead class="border-t border-b">
                        <th class="font-semibold py-3">ID</th>
                        <th class="font-semibold">Name</th>
                        <th class="font-semibold">Student ID</th>
                        <th class="font-semibold">Email</th>
                        <th class="font-semibold">Contact Number</th>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td class="py-1">{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->personalBio->student_id }}</td>
                            <td>{{ $user->personalBio->email_address }}</td>
                            <td>{{ $user->personalBio->phone_number }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection