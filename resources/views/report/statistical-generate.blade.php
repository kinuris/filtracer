@extends('layouts.admin')

@section('title', 'Statistical Report')

<?php

$department = App\Models\Department::find(request('department') ?? -1);
$course = App\Models\Course::find(request('courses') ?? -1);
$category = request('category');

if ($department && $course) {
    $query = App\Models\User::partialSet()
        ->where('role', '!=', 'Admin')
        ->whereRelation('course', 'courses.id', '=', $course->id);

    switch ($category) {
        case "All Entities":
            // Merge all partialSet() (non-Admin) users with Admin users
            $users = App\Models\User::partialSet()
                ->where('role', '!=', 'Admin')
                ->get()
                ->merge(App\Models\User::where('role', '=', 'Admin')->get());
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
            $query->whereRelation('professionalRecords', 'employment_status', '=', str_replace(' ', '', $category));
            break;
        case "Verified Alumni":
        case "Unverified Alumni":
            $query->whereRelation('personalRecords', 'status', '=', $category === "Verified Alumni" ? 1 : 0);
            break;
    }

    $users = $query->get();
} else if ($department) {
    $query = App\Models\User::partialSet()->where('role', '!=', 'Admin')->whereRelation('department', 'departments.id', '=', $department->id);

    switch ($category) {
        case "All Entities":
            // Merge all partialSet() (non-Admin) users with Admin users
            $users = App\Models\User::partialSet()
                ->where('role', '!=', 'Admin')
                ->get()
                ->merge(App\Models\User::where('role', '=', 'Admin')->get());
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
            $query->whereRelation('professionalRecords', 'employment_status', '=', str_replace(' ', '', $category));
            break;
        case "Verified Alumni":
        case "Unverified Alumni":
            $query->whereRelation('personalRecords', 'status', '=', $category === "Verified Alumni" ? 1 : 0);
            break;
    }

    $users = $query->get();
} else {
    switch ($category) {
        case "All Entities":
            // Merge all partialSet() (non-Admin) users with Admin users
            $users = App\Models\User::partialSet()
                ->where('role', '!=', 'Admin')
                ->get()
                ->merge(App\Models\User::where('role', '=', 'Admin')->get());
            break;
        case "All Users":
            $users = App\Models\User::partialSet()
                ->where('role', '!=', 'Admin')
                ->get();
            break;
        case "Registered Users":
            break;
        case "Working Student":
        case "Employed Alumni":
        case "Unemployed Alumni":
        case "Self-Employed Alumni":
        case "Student Alumni":
        case "Retired Alumni":
            switch($category) {
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
                ->get();
            break;
        case "Verified Alumni":
        case "Unverified Alumni":
            $users = App\Models\User::partialSet()
                ->where('role', '!=', 'Admin')
                ->whereRelation('personalRecords', 'status', '=', $category === "Verified Alumni" ? 1 : 0)
                ->get();
            break;
        case "Verified Admin":
        case "Unverified Admin":
            $users = App\Models\User::query()
                ->where('role', '=', 'Admin')
                ->whereRelation('adminRelation', 'is_verified', '=', $category === "Verified Admin" ? 1 : 0)
                ->whereRelation('adminRelation', 'is_super', '=', 0)
                ->get();
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
                    <h1 class="font-light text-blue-500"><span class="mx-1 italic font-bold">{{ $category }}</span> <span class="text-xl">|</span> <span class="italic font-bold mx-1">{{ $department->name }}</span> <span class="text-xl">|</span> <span class="italic font-bold mx-1">{{ $course->name }}</span></h1>
                    @elseif ($department)
                    <h1 class="font-light text-blue-500"><span class="mx-1 italic font-bold">{{ $category }}</span> <span class="text-xl">|</span> <span class="italic font-bold mx-1">{{ $department->name }}</span> <span class="text-xl">|</span> <span class="italic font-bold mx-1">All Courses</span></h1>
                    @else
                    <h1 class="font-light text-blue-500"><span class="mx-1 italic font-bold">{{ $category }}</span> <span class="text-xl">|</span> <span class="italic font-bold mx-1">All Departments</span> <span class="text-xl">|</span> <span class="italic font-bold mx-1">All Courses</span></h1>
                    @endif

                    <div><i class="text-[10px]">Generated at:</i> {{ date_create()->format('Y-m-d H:i:s') }}</div>
                </div>

                <table class="w-full text-center mt-4">
                    <thead class="border-t border-b">
                        <th class="font-semibold py-3">ID</th>
                        <th class="font-semibold">Name</th>
                        <th class="font-semibold">Student / Company ID</th>
                        <th class="font-semibold">Email</th>
                        <th class="font-semibold">Contact Number</th>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td class="py-1">{{ $user->id }}</td>
                            @if ($user->role === 'Admin')
                            <td>{{ $user->admin()->getFullnameAttribute() }}</td>
                            @else
                            <td>{{ ($user->partialPersonal ?? $user->getPersonalBio())->getFullnameAttribute() }}</td>
                            @endif
                            @if ($user->role === 'Admin')
                            @php($admin = $user->admin())
                            <td>{{ $admin->position_id }}</td>
                            <td>{{ $admin->email_address }}</td>
                            <td>{{ $admin->phone_number }}</td>

                            @else
                            <td>{{ ($user->partialPersonal ?? $user->getPersonalBio())->student_id }}</td>
                            <td>{{ ($user->partialPersonal ?? $user->getPersonalBio())->email_address }}</td>
                            <td>{{ ($user->partialPersonal ?? $user->getPersonalBio())->phone_number }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection