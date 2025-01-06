@extends('layouts.setup')

@section('content')

@php($schools = [
'Filamer Christian University',
'University of the Philippines in the Visayas',
'Central Philippine University',
'John B. Lacson Foundation Maritime University',
'University of St. La Salle',
'West Visayas State University',
'University of Negros Occidental - Recoletos',
'University of Iloilo - PHINMA',
'Iloilo Science and Technology University',
'Aklan State University',
'University of San Agustin',
'Capiz State University',
'St. Paul University Iloilo',
'University of Antique',
'Central Philippine Adventist College',
'Western Institute of Technology',
'Guimaras State University',
'STI West Negros University'
])

@php($partial = auth()->user()->partialPersonal)
<div class="flex place-items-start h-full justify-center max-h-[calc(100vh-5rem)] pb-10 overflow-auto">
    <div class="shadow-lg bg-white mt-12 w-[60%] p-2 rounded-lg flex flex-col">
        <div class="flex gap-2">
            <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
            <div class="flex-1 border p-1 rounded-full bg-blue-600"></div>
            <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
            <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
        </div>

        <h1 class="text-2xl font-semibold tracking-wider text-center mt-5">Complete your Profile</h1>
        <p class="text-blue-600 font-bold mt-2 text-xl text-center">Educational Info</p>

        <form class="mx-8" action="/alumni/setup/educational/{{ auth()->user()->id }}" method="POST">
            @csrf
            <div class="flex flex-col">
                <div class="flex">
                    <div class="flex flex-col flex-1 max-w-[50%]">
                        <label for="school">School</label>
                        <select class="text-gray-400 border rounded-lg p-2" name="school" id="school">
                            @foreach ($schools as $school)
                            <option value="{{ $school }}">{{ $school }}</option>
                            @endforeach
                        </select>

                        <label class="mt-3" for="location">Location</label>
                        <input class="text-gray-400 border rounded-lg p-2 @error('location') border-red-500 @enderror" type="text" name="location" value="{{ old('location') }}">
                        @error('location')
                        <span class="text-red-500 text-sm block">{{ $message }}</span>
                        @enderror

                        <label class="mt-3" for="start">Year Started</label>
                        <input class="text-gray-400 border rounded-lg p-2 @error('start') border-red-500 @enderror" type="number" name="start" id="start" value="{{ old('start') }}">
                        @error('start')
                        <span class="text-red-500 text-sm block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mx-2"></div>

                    <div class="flex flex-col flex-1">
                        <label for="course">Course</label>
                        <select class="text-gray-400 border rounded-lg p-2" name="course" id="course">
                            @foreach (App\Models\Course::all() as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>

                        <label class="mt-3" for="degree">Degree Type</label>
                        <select class="text-gray-400 border rounded-lg p-2" name="degree_type" id="degree">
                            <option value="Bachelor">Bachelor</option>
                            <option value="Masteral">Masteral</option>
                            <option value="Doctoral">Doctoral</option>
                        </select>

                        <label class="mt-3" for="end">Year Graduated</label>
                        <input class="text-gray-400 border rounded-lg p-2 @error('end') border-red-500 @enderror" type="number" name="end" value="{{ old('end') }}">
                        @error('end')
                        <span class="text-red-500 text-sm block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <label class="mt-3" for="major">Major</label>
                <select class="text-gray-400 border rounded-lg p-2" name="major" id="major">
                    @php($majors = App\Models\Major::all())
                    @foreach ($majors as $major)
                    <option value="{{ $major->id }}">{{ $major->name }}</option>
                    @endforeach
                </select>

                <div class="flex mt-4 justify-end">
                    <button class="text-white bg-blue-600 p-2 rounded" type="submit">Save & Next</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection