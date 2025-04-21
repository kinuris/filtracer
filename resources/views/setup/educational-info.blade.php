@extends('layouts.setup')

@section('content')
@php
    $schools = [
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
    ];
@endphp

@php
    $partial = auth()->user()->partialPersonal;
@endphp
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
            {{-- Use Grid Layout --}}
            <div class="grid grid-cols-2 gap-x-4 gap-y-3 mt-4">
                {{-- Column 1 --}}
                <div class="flex flex-col">
                    <label for="school">School</label>
                    <select class="text-gray-400 border rounded-lg p-2" name="school" id="school">
                        @foreach ($schools as $school)
                        <option value="{{ $school }}" {{ old('school', $educationalRecord->school ?? '') == $school ? 'selected' : '' }}>{{ $school }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Column 2 --}}
                <div class="flex flex-col">
                    <label for="course">Course</label>
                    <select class="text-gray-400 border rounded-lg p-2" name="course" id="course">
                        @foreach (App\Models\Course::query()->where('department_id', '=', Auth::user()->department_id)->get() as $course)
                        <option value="{{ $course->id }}" {{ old('course', $educationalRecord->course_id ?? '') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Column 1 --}}
                <div class="flex flex-col">
                    <label for="location">Location</label>
                    <input class="text-gray-400 border rounded-lg p-2 @error('location') border-red-500 @enderror" type="text" name="location" value="{{ old('location', $educationalRecord->school_location ?? '') }}">
                    @error('location')
                    <span class="text-red-500 text-sm block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Column 2 --}}
                <div class="flex flex-col">
                    <label for="degree">Degree Type</label>
                    <select class="text-gray-400 border rounded-lg p-2" name="degree_type" id="degree">
                        <option value="Bachelor" {{ old('degree_type', $educationalRecord->degree_type ?? '') == 'Bachelor' ? 'selected' : '' }}>Bachelor</option>
                        <option value="Masteral" {{ old('degree_type', $educationalRecord->degree_type ?? '') == 'Masteral' ? 'selected' : '' }}>Masteral</option>
                        <option value="Doctoral" {{ old('degree_type', $educationalRecord->degree_type ?? '') == 'Doctoral' ? 'selected' : '' }}>Doctoral</option>
                    </select>
                </div>

                {{-- Column 1 --}}
                <div class="flex flex-col">
                    <label for="start">Year Started</label>
                    <input class="text-gray-400 border rounded-lg p-2 @error('start') border-red-500 @enderror" type="number" name="start" id="start" value="{{ old('start', $educationalRecord->start ?? '') }}">
                    @error('start')
                    <span class="text-red-500 text-sm block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Column 2 --}}
                <div class="flex flex-col">
                    <label for="end">Year Graduated</label>
                    <input class="text-gray-400 border rounded-lg p-2 @error('end') border-red-500 @enderror" type="number" name="end" value="{{ old('end', $educationalRecord->end ?? '') }}">
                    @error('end')
                    <span class="text-red-500 text-sm block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Major Selection - Conditionally Displayed - Spanning both columns --}}
                @php
                    // Use existing record's course ID or the request's course ID for fetching majors
                    $selectedCourseId = old('course', $educationalRecord->course_id ?? request('course'));
                    $majors = $selectedCourseId ? App\Models\Major::query()->where('course_id', $selectedCourseId)->get() : collect();
                @endphp

                @if($majors->isNotEmpty())
                    {{-- Display Major dropdown if majors exist for the selected course --}}
                    <div class="flex flex-col col-span-2"> {{-- Span 2 columns --}}
                        <label for="major">Major</label>
                        <select class="text-gray-400 border rounded-lg p-2 @error('major') border-red-500 @enderror" name="major" id="major">
                            @foreach ($majors as $major)
                                <option value="{{ $major->id }}" {{ old('major', $educationalRecord->major_id ?? request('major')) == $major->id ? 'selected' : '' }}>
                                    {{ $major->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('major')
                            <span class="text-red-500 text-sm block">{{ $message }}</span>
                        @enderror
                    </div>
                @elseif($selectedCourseId)
                     {{-- Display message only if a course is selected but has no majors --}}
                     <div class="col-span-2 pt-1"> {{-- Span 2 columns and add padding for alignment --}}
                        <span class="text-gray-500 text-sm italic">No majors available for the selected course.</span>
                     </div>
                @endif
                {{-- If no course is selected yet ($selectedCourseId is null), this section remains empty --}}

                {{-- Submit Button - Spanning both columns and aligned right --}}
                <div class="col-span-2 flex justify-end gap-2 mt-4">
                    <a href="/alumni/setup/personal" class="p-2 bg-gray-400 text-white rounded w-fit self-end">Back</a>
                    <button class="text-white bg-blue-600 p-2 rounded" type="submit">Save & Next</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Pre-select the options from the URL query params if available
    const params = new URLSearchParams(window.location.search);
    document.querySelectorAll('select').forEach(function(select) {
        // Use the select's id as the query parameter key
        if (params.has(select.id)) {
            select.value = params.get(select.id);
        }
        select.addEventListener('change', function () {
            // Update the query parameter with the new value and reload
            params.set(select.id, select.value);
            // Clear major param if course changes to avoid invalid combinations
            if (select.id === 'course') {
                params.delete('major');
            }
            window.location.search = params.toString();
        });
    });

    // Also pre-select inputs if needed (though less common for query params)
    document.querySelectorAll('input[type="text"], input[type="number"]').forEach(function(input) {
        if (params.has(input.name)) { // Use name for inputs as they might not have IDs matching params
            input.value = params.get(input.name);
        }
        // Add change listeners for inputs if you want them to trigger reloads too
        // input.addEventListener('change', function () { ... });
    });
});
</script>

@endsection