@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 overflow-auto max-h-[calc(100vh-64px)]">
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-6">Course Listings</h1>

        @if(count($courses) > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-6">
                @foreach($courses as $course)
                <div class="bg-white border rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="relative h-40 bg-gray-200">
                        @if($course->image_link)
                        <div class="flex items-center justify-center h-full bg-white">
                            <img src="{{ asset('storage/courses/' . $course->image_link) }}" alt="{{ $course->name }}" class="w-32 h-32 object-cover bg-white">
                        </div>
                        @else
                        <div class="flex items-center justify-center h-full bg-gray-100">
                            <span class="text-gray-400">No image available</span>
                        </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">{{ $course->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $course->description }}</p>
                        <div class="mt-auto">
                            <a href="{{ route('profiles.courses', $course->id) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View Details →</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-white p-6 rounded-lg shadow">
            <p class="text-gray-500">No courses found.</p>
        </div>
        @endif
    </div>
</div>
@endsection