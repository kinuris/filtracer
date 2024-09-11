@extends('layouts.admin')

@section('title', 'Course List')

@section('content')
@include('components.add-course-modal')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)] overflow-scroll">
    <h1 class="font-medium tracking-widest text-lg">Course List</h1>
    <p class="text-gray-400 text-xs mb-2">Settings / <span class="text-blue-500">Courses</span></p>

    <div class="shadow rounded-lg">
        <form action="">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-t-lg">
                <input value="{{ request('search') ?? '' }}" class="bg-gray-100 px-2 py-2 rounded border min-w-[max(33%,270px)]" placeholder="Search..." type="text" name="search" id="search">
                <button type="button" class="bg-blue-600 text-white p-2 rounded ml-3" id="openAddCourseModal">Add Course</button>
            </div>
        </form>
        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <th class="py-4 font-thin">ID</th>
                <th class="font-thin">Course Name</th>
                <th class="font-thin">Department</th>
                <th class="font-thin">Action</th>
            </thead>
            <tbody>
                @foreach ($courses as $course)
                <tr class="border-b bg-white text-center">
                    <td class="text-gray-500 py-3">{{ $course->id }}</td>
                    <td>{{ $course->name }}</td>
                    <td>{{ $course->department->name }}</td>
                    <td>
                        <div class="flex justify-center place-items-center">
                            <a class="mr-3" href="/settings/course/edit/{{ $course->id }}"><img src="{{ asset('assets/settings_blue.svg') }}" alt="View"></a>
                            <a href="/course/delete/{{ $course->id }}"><img src="{{ asset('assets/trash.svg') }}" alt="Trash"></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="bg-white rounded-b-lg p-3">
            {{ $courses->withQueryString()->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const openAddCourseModal = document.getElementById('openAddCourseModal');
    const addCourseModal = document.getElementById('addCourseModal');
    const closeAddCourseModal = document.getElementById('closeAddCourseModal');

    openAddCourseModal.addEventListener('click', () => {
        addCourseModal.classList.remove('hidden');
    });

    closeAddCourseModal.addEventListener('click', () => {
        addCourseModal.classList.add('hidden');
    });

    window.addEventListener('click', function(event) {
        if (event.target === addCourseModal) {
            addCourseModal.classList.add('hidden');
        }
    })
</script>
@endsection