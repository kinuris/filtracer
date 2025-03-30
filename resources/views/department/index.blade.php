@extends('layouts.admin')

@section('title', 'Departments')

@section('content')
<div class="bg-gray-100 w-full max-h-screen overflow-auto p-8">
    <h1 class="font-medium tracking-widest text-lg mb-4">Departments</h1>
    @php($depts = App\Models\Department::allValid())
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($depts as $dept)
        <a href="/department/{{ $dept->id }}">
            <div class="bg-white h-full p-4 border shadow rounded-lg flex flex-col items-center justify-between">
                <img class="w-32 h-32 rounded-full object-cover" src="{{ asset('storage/departments/' . $dept->logo) }}" alt="{{ $dept->name }}">
                <p class="text-center">{{ $dept->name }}</p>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection