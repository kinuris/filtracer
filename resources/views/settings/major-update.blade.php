@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)] overflow-auto">
    <h1 class="font-medium tracking-widest text-lg">Major Settings</h1>
    <div class="shadow rounded-lg mt-4">
        <div class="bg-white p-4 flex place-items-center rounded-lg">
            <form action="/settings/major/edit/{{ $major->id }}" method="POST" class="w-full">
                @csrf
                <div class="flex flex-col">
                    <label for="name">Major Name</label>
                    <input value="{{ $major->name }}" class="mt-1 p-2 rounded border" name="name" placeholder="Course Name" type="text">
                </div>

                <div class="flex flex-col mt-3">
                    <label for="department">Course Name</label>
                    <select class="mt-1 p-2 rounded border" name="course" id="department">
                        @foreach (App\Models\Course::all() as $course) 
                        <option value="{{ $course->id }}" {{ $major->course_id == $course->id ? 'selected' : '' }}>{{ $course->name }} ({{ $course->department->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end mt-6">
                    <a href="/settings/major" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</a>
                    <button type="submit" class="p-2 bg-blue-600 text-white rounded px-4">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection