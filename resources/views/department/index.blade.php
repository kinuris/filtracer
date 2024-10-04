@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 w-full h-full p-8">
    <h1 class="font-medium tracking-widest text-lg mb-4">Departments</h1>
    @php($depts = App\Models\Department::allValid())
    <div class="flex flex-wrap">
        @foreach ($depts as $dept)
        <a class="m-2" href="/department/{{ $dept->id }}">
            <div class="bg-white h-full  max-w-72 min-w-72 p-4 border shadow rounded-lg flex flex-col items-center justify-between">
                <img class="w-32 h-32 rounded-full" src="{{ asset('storage/departments/' . $dept->logo) }}" alt="{{ $dept->name }}">
                <p class="text-center">{{ $dept->name }}</p>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection