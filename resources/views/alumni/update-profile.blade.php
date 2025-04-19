@extends('layouts.alumni')

@section('header')
<style>
    input::-webkit-file-upload-button {
        display: none;
    }

    /* Add some visual feedback during reload */
    .is-reloading {
        opacity: 0.5;
        pointer-events: none;
    }

    /* Style for Choices.js */
    .choices__inner {
        background-color: white;
        border: 1px solid #d1d5db; /* gray-300 */
        border-radius: 0.5rem; /* rounded-lg */
        padding: 0.5rem; /* p-2 equivalent */
        color: #6b7280; /* text-gray-500 */
    }
    .choices__list--multiple .choices__item {
        background-color: #3b82f6; /* bg-blue-500 */
        border-color: #2563eb; /* border-blue-600 */
    }
    .choices[data-type*="select-multiple"] .choices__button, .choices[data-type*="text"] .choices__button {
        border-left: 1px solid #1d4ed8; /* darker blue */
        margin-left: 5px;
    }
</style>
@endsection

@section('title', 'Update Profile')

@section('content')
@php $query = request()->query('type') ?? 'personal'; @endphp
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
$times = [
'Below 3 months',
'3-5 months',
'6 months-1 year',
'Over 1 year',
'Job not secured' // Added this option
];
@endphp

@php
$methods = [
'Career Center',
'Experimental Learning',
'Networking',
'Online Resources',
'Campus Resources'
];
@endphp

@php
$statuses = [
'Employed',
'Unemployed',
'Self-employed',
'Student',
'Working Student',
'Retired'
];
@endphp

@php
$softSkillsList = [
    'Communication Skills',
    'Teamwork and Collaboration',
    'Leadership Skills',
    'Marketing Skills',
    'Adaptability and Problem-Solving',
    'Time Management',
    'Work Ethic',
    'Interpersonal Skills'
];
@endphp

@php
$hardSkillsList = [
    'Technical Skills',
    'Engineering Skills',
    'Business and Finance Skills',
    'Marketing Skills',
    'Cooking Skills'
];
@endphp

@php
$salaryRanges = [
    'No Income',
    'Below 10,000',
    '10,000-20,000',
    '20,001-40,000',
    '40,001-60,000',
    '60,001-80,000',
    '80,001-100,000',
    'Over 100,000'
];
@endphp

@php
$industries = [
    'Education',
    'Healthcare and Medical Services',
    'IT and Software Development',
    'BPO / Call Center',
    'Engineering and Construction',
    'Manufacturing',
    'Banking and Financial Services',
    'Government and Public Administration',
    'Retail and Wholesale Trade',
    'Hospitality and Tourism',
    'Transportation and Logistics',
    'Media and Communications',
    'Legal Services',
    'Agriculture, Forestry, and Fisheries',
    'Real Estate',
    'Utilities',
    'Non-Profit',
    'Arts, Culture, and Entertainment',
    'Automotive',
    'Freelancing / Entrepreneurship',
];
@endphp

@include('components.education-modal')
@include('components.reset-password-modal')
@include('components.view-attachments-modal') {{-- Assuming attachments are global for now --}}

<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg">Update Profile</h1>
    <p class="text-gray-400 text-xs">Profile / <span class="text-blue-500">Update Profile</span></p>

    <div class="flex max-h-[calc(100%-16px)]">
        {{-- Left Panel (Profile Pic, etc.) --}}
        <div class="shadow rounded-lg h-fit mt-6 flex-1 min-w-80">
            <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
                <img class="w-32 h-32 rounded-full object-cover shadow-md mr-8" src="{{ $user->image() }}" id="user-profile" alt="Profile">
                <p class="text-lg font-bold my-6">{{ $user->name }}</p>

                @php $currentProf = $user->getProfessionalBio(); @endphp
                <div class="flex place-items-center">
                    <img class="h-4 mr-3" src="{{ asset('assets/alumni_job.svg') }}" alt="Job">
                    <p class="text-gray-400 text-sm">{{ $currentProf?->employment_status ?? 'N/A' }}</p>
                </div>
                <div class="flex mt-1 ml-[1px] place-items-center">
                    <img class="h-4 mr-3" src="{{ asset('assets/alumni_location.svg') }}" alt="Job">
                    <p class="text-gray-400 text-sm">{{ $currentProf?->work_location ?? 'N/A' }}</p>
                </div>

                <form action="/alumni/profile/upload/{{ $user->id }}" enctype="multipart/form-data" method="POST" id="change-profile">
                    @csrf
                    <label for="upload">
                        <p class="bg-blue-600 text-white w-fit p-2.5 rounded-lg mt-4 flex place-items-center cursor-pointer">
                            <img class="w-4 mr-2" src="{{ asset('assets/upload.svg') }}" alt="Upload">
                            Change Picture
                        </p>
                    </label>
                    <input class="hidden" type="file" name="profile" id="upload" accept="image/*">
                </form>
            </div>
        </div>

        <div class="mx-2"></div>

        {{-- Right Panel (Tabs and Forms) --}}
        <div class="flex-[3] flex flex-col mt-6 mb-3 max-h-full overflow-auto">
            <div class="shadow rounded-lg">
                <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg" id="main-content-area">
                    {{-- Tabs --}}
                    <div class="flex mb-4 sticky top-0 bg-white py-4 border-b z-[5]">
                        <a class="text-gray-400 font-semibold @if($query === 'personal') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=personal">Personal Info</a>
                        <a class="text-gray-400 font-semibold mx-4 @if($query === 'educational') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=educational">Educational Info</a>
                        <a class="text-gray-400 font-semibold @if($query === 'professional') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=professional">Professional Info</a>
                    </div>

                    {{-- Personal Info Form --}}
                    @if ($query === 'personal')
                    <form action="/alumni/profile/update/personal/{{ $user->id }}" method="POST">
                        @csrf
                        <div class="flex flex-col">
                            <div class="flex">
                                <div class="flex flex-col flex-1">
                                    <label for="firstname">First Name</label>
                                    <input value="{{ $user->getPersonalBio()->first_name }}" class="text-gray-400 border rounded-lg p-2" type="text" name="first_name">

                                    <label class="mt-3" for="middlename">Middle Name</label>
                                    <input value="{{ $user->getPersonalBio()->middle_name }}" class="text-gray-400 border rounded-lg p-2" type="text" name="middle_name">

                                    <label class="mt-3" for="lastname">Last Name</label>
                                    <input value="{{ $user->getPersonalBio()->last_name }}" class="text-gray-400 border rounded-lg p-2" type="text" name="last_name">

                                    <label class="mt-3" for="studentid">Student ID</label>
                                    <input value="{{ $user->getPersonalBio()->student_id }}" class="text-gray-400 border rounded-lg p-2" type="text" name="student_id">

                                    <label class="mt-3" for="gender">Gender</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="gender" id="gender">
                                        <option {{ $user->getPersonalBio()->gender === 'Male' ? 'selected' : '' }} value="Male">Male</option>
                                        <option {{ $user->getPersonalBio()->gender === 'Female' ? 'selected' : '' }} value="Female">Female</option>
                                    </select>

                                    <label class="mt-3" for="birthdate">Date of Birth</label>
                                    <input value="{{ $user->getPersonalBio()->birthdate->format('Y-m-d') }}" class="text-gray-400 border rounded-lg p-2" type="date" name="birthdate">

                                    <label class="mt-3" for="status">Civil Status</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="civil_status" id="status">
                                        <option {{ $user->getPersonalBio()->civil_status === 'Single' ? 'selected' : '' }} value="Single">Single</option>
                                        <option {{ $user->getPersonalBio()->civil_status === 'Married' ? 'selected' : '' }} value="Married">Married</option>
                                        <option {{ $user->getPersonalBio()->civil_status === 'Divorced' ? 'selected' : '' }} value="Divorced">Divorced</option>
                                        <option {{ $user->getPersonalBio()->civil_status === 'Widowed' ? 'selected' : '' }} value="Widowed">Widowed</option>
                                        <option {{ $user->getPersonalBio()->civil_status === 'Separated' ? 'selected' : '' }} value="Separated">Separated</option>
                                    </select>
                                </div>

                                <div class="mx-2"></div>

                                <div class="flex flex-col flex-1">
                                    <label for="permanent">Permanent Address</label>
                                    <input value="{{ $user->getPersonalBio()->permanent_address }}" class="text-gray-400 border rounded-lg p-2" type="text" name="permanent_address">

                                    <label class="mt-3" for="current">Current Address</label>
                                    <input value="{{ $user->getPersonalBio()->current_address }}" class="text-gray-400 border rounded-lg p-2" type="text" name="current_address">

                                    <label class="mt-3" for="email">Email</label>
                                    <input value="{{ $user->getPersonalBio()->email_address }}" class="text-gray-400 border rounded-lg p-2" type="email" name="email_address">

                                    <label class="mt-3" for="username">Username</label>
                                    <input value="{{ $user->username }}" class="text-gray-400 border rounded-lg p-2" type="text" name="username">

                                    <label class="mt-3" for="phone">Phone Number</label>
                                    <input value="{{ $user->getPersonalBio()->phone_number }}" class="text-gray-400 border rounded-lg p-2" type="tel" name="phone_number">

                                    <label class="mt-3" for="social">Website / Social</label>
                                    <input type="url" value="{{ $user->getPersonalBio()->social_link }}" class="text-gray-400 border rounded-lg p-2" name="social_media">
                                </div>
                            </div>

                            <div class="flex mt-4 justify-end space-x-2">
                                <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium" id="openResetPasswordModal">
                                    <img class="w-4 h-4 mr-2" src="{{ asset('assets/pass_reset.svg') }}" alt="Reset">
                                    Reset Password
                                </button>
                                <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Educational Info Forms --}}
                    @elseif ($query === 'educational')
                        @php
                        $hasEducationalChanges = false;
                        $educationalParamPrefixes = ['type_', 'strand_', 'school_', 'course_', 'degree_type_', 'major_'];
                        foreach (request()->query() as $key => $value) {
                            foreach ($educationalParamPrefixes as $prefix) {
                                if (str_starts_with($key, $prefix)) {
                                    $hasEducationalChanges = true;
                                    break 2; // Break both loops
                                }
                            }
                        }
                        @endphp

                        {{-- Primary/Secondary Forms --}}
                        @foreach($user->primsecEducational as $primsec)
                        <form action="/alumni/profile/update/primsec/{{ $primsec->id }}/{{ $user->id }}" method="POST" class="mb-6 border-b pb-4 last:border-b-0 last:pb-0">
                            @csrf
                            <h3 class="text-md font-semibold mb-2 text-gray-600">Primary/Secondary Record #{{ $loop->iteration }}</h3>
                            <div class="flex flex-col">
                                <div class="flex">
                                    <div class="flex flex-col flex-1 max-w-[50%]">
                                        <label for="type_{{ $loop->index }}">Education Level</label>
                                        <select class="text-gray-400 border rounded-lg p-2 education-type sync-url-on-change" name="type" id="type_{{ $loop->index }}" onchange="toggleStrand({{ $loop->index }})">
                                            <option {{ request()->query('type_'.$loop->index, $primsec->type) === 'primary' ? 'selected' : '' }} value="primary">Primary Education</option>
                                            <option {{ request()->query('type_'.$loop->index, $primsec->type) === 'secondary' ? 'selected' : '' }} value="secondary">Secondary Education</option>
                                        </select>

                                        <label class="mt-3" for="school_name_{{ $loop->index }}">School Name</label>
                                        <input value="{{ $primsec->school_name }}" class="text-gray-400 border rounded-lg p-2" type="text" name="school_name" id="school_name_{{ $loop->index }}">

                                        <label class="mt-3" for="start_{{ $loop->index }}">Year Started</label>
                                        <input value="{{ $primsec->start }}" class="text-gray-400 border rounded-lg p-2" type="number" name="start" id="start_{{ $loop->index }}">
                                    </div>

                                    <div class="mx-2"></div>

                                    <div class="flex flex-col flex-1">
                                        <div id="strand_container_{{ $loop->index }}" class="{{ request()->query('type_'.$loop->index, $primsec->type) === 'primary' ? 'hidden' : '' }} mb-3 flex flex-col">
                                            <label for="strand_{{ $loop->index }}">Strand (for Senior High)</label>
                                            <select class="text-gray-400 border rounded-lg p-2 sync-url-on-change" name="strand" id="strand_{{ $loop->index }}">
                                                @php $selectedStrand = request()->query('strand_'.$loop->index, $primsec->strand); @endphp
                                                <option {{ $selectedStrand === 'STEM' ? 'selected' : '' }} value="STEM">STEM</option>
                                                <option {{ $selectedStrand === 'HUMSS' ? 'selected' : '' }} value="HUMSS">HUMSS</option>
                                                <option {{ $selectedStrand === 'ABM' ? 'selected' : '' }} value="ABM">ABM</option>
                                                <option {{ $selectedStrand === 'GAS' ? 'selected' : '' }} value="GAS">GAS</option>
                                                <option {{ $selectedStrand === 'Home Economics' ? 'selected' : '' }} value="Home Economics">Home Economics</option>
                                                <option {{ $selectedStrand === 'ICT' ? 'selected' : '' }} value="ICT">ICT</option>
                                                <option {{ $selectedStrand === 'Industrial Arts' ? 'selected' : '' }} value="Industrial Arts">Industrial Arts</option>
                                                <option {{ $selectedStrand === 'Agri-Fishery Arts' ? 'selected' : '' }} value="Agri-Fishery Arts">Agri-Fishery Arts</option>
                                                <option {{ $selectedStrand === 'Sports Track' ? 'selected' : '' }} value="Sports Track">Sports Track</option>
                                                <option {{ $selectedStrand === 'Arts and Design Track' ? 'selected' : '' }} value="Arts and Design Track">Arts and Design Track</option>
                                            </select>
                                        </div>

                                        <label for="location_{{ $loop->index }}">Location</label>
                                        <input value="{{ $primsec->location }}" class="text-gray-400 border rounded-lg p-2" type="text" name="location" id="location_{{ $loop->index }}">

                                        <label class="mt-3" for="end_{{ $loop->index }}">Year Graduated</label>
                                        <input value="{{ $primsec->end }}" class="text-gray-400 border rounded-lg p-2" type="number" name="end" id="end_{{ $loop->index }}">
                                    </div>
                                </div>

                                <div class="flex mt-4 justify-end">
                                    @if ($hasEducationalChanges && $loop->last && $user->educationalBios->isEmpty()) {{-- Show Clear button only on last primsec if no tertiary exists --}}
                                    <button type="button" id="clearEducationalChangesBtn" class="text-white mr-2 bg-red-500 hover:bg-red-600 p-2 rounded-lg flex place-items-center text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                        Clear Changes
                                    </button>
                                    @endif
                                    <button class="text-white bg-blue-600 py-2 px-6 rounded-lg text-sm" type="submit">Save Record #{{ $loop->iteration }}</button>
                                </div>
                            </div>
                        </form>
                        @endforeach

                        {{-- Tertiary Forms --}}
                        @foreach ($user->educationalBios as $educ)
                        <form action="/alumni/profile/update/educational/{{ $educ->id }}/{{ $user->id }}" method="POST" class="mb-6 border-b pb-4 last:border-b-0 last:pb-0">
                            @csrf
                             <h3 class="text-md font-semibold mb-2 text-gray-600">Tertiary Record #{{ $loop->iteration }}</h3>
                            <div class="flex flex-col">
                                <div class="flex">
                                    <div class="flex flex-col flex-1 max-w-[50%]">
                                        <label for="school_{{ $loop->index }}">School</label>
                                        <select class="text-gray-400 border rounded-lg p-2 sync-url-on-change" name="school" id="school_{{ $loop->index }}">
                                            @php $selectedSchool = request()->query('school_'.$loop->index, $educ->school); @endphp
                                            @foreach ($schools as $school)
                                            <option {{ $selectedSchool === $school ? 'selected' : '' }} value="{{ $school }}">{{ $school }}</option>
                                            @endforeach
                                        </select>

                                        <label class="mt-3" for="location_educ_{{ $loop->index }}">Location</label>
                                        <input value="{{ $educ->school_location }}" class="text-gray-400 border rounded-lg p-2" type="text" name="location" id="location_educ_{{ $loop->index }}">

                                        <label class="mt-3" for="start_educ_{{ $loop->index }}">Year Started</label>
                                        <input value="{{ $educ->start }}" class="text-gray-400 border rounded-lg p-2" type="number" name="start" id="start_educ_{{ $loop->index }}">
                                    </div>

                                    <div class="mx-2"></div>

                                    <div class="flex flex-col flex-1">
                                        <label for="course_{{ $loop->index }}">Course</label>
                                        <select class="text-gray-400 border rounded-lg p-2 course-select sync-url-on-change" name="course" id="course_{{ $loop->index }}">
                                            @php $selectedCourseId = request()->query('course_'.$loop->index, $educ->course_id); @endphp
                                            @foreach (App\Models\Course::all() as $course)
                                            <option {{ $selectedCourseId == $course->id ? 'selected' : '' }} value="{{ $course->id }}">{{ $course->name }}</option>
                                            @endforeach
                                        </select>

                                        <label class="mt-3" for="degree_{{ $loop->index }}">Degree Type</label>
                                        <select class="text-gray-400 border rounded-lg p-2 sync-url-on-change" name="degree_type" id="degree_{{ $loop->index }}">
                                            @php $selectedDegree = request()->query('degree_type_'.$loop->index, $educ->degree_type); @endphp
                                            <option {{ $selectedDegree === 'Bachelor' ? 'selected' : '' }} value="Bachelor">Bachelor</option>
                                            <option {{ $selectedDegree === 'Masteral' ? 'selected' : '' }} value="Masteral">Masteral</option>
                                            <option {{ $selectedDegree === 'Doctoral' ? 'selected' : '' }} value="Doctoral">Doctoral</option>
                                        </select>

                                        <label class="mt-3" for="end_educ_{{ $loop->index }}">Year Graduated</label>
                                        <input value="{{ $educ->end }}" class="text-gray-400 border rounded-lg p-2" type="number" name="end" id="end_educ_{{ $loop->index }}">
                                    </div>
                                </div>

                                <label class="mt-3" for="major_{{ $loop->index }}">Major</label>
                                <select class="text-gray-400 border rounded-lg p-2 sync-url-on-change" name="major" id="major_{{ $loop->index }}">
                                    @php
                                    // Use the course ID from the query parameter if available, otherwise use the one from the database
                                    $courseIdForMajors = request()->query('course_'.$loop->index, $educ->course_id);
                                    $majors = App\Models\Major::where('course_id', $courseIdForMajors)->get();
                                    $selectedMajorId = request()->query('major_'.$loop->index, $educ->major_id);
                                    @endphp
                                    @forelse ($majors as $major)
                                    <option {{ $selectedMajorId == $major->id ? 'selected' : '' }} value="{{ $major->id }}">{{ $major->name }}</option>
                                    @empty
                                    <option value="">No majors available for this course</option>
                                    @endforelse
                                </select>

                                <div class="flex mt-4 justify-end">
                                    @if ($hasEducationalChanges && $loop->last) {{-- Show Clear button only on the last education form --}}
                                    <button type="button" id="clearEducationalChangesBtn" class="text-white mr-2 bg-red-500 hover:bg-red-600 p-2 rounded-lg flex place-items-center text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                        Clear Changes
                                    </button>
                                    @endif
                                    @if ($loop->last) {{-- Show Add button only on the last education form --}}
                                    <button class="text-white mr-2 bg-green-600 hover:bg-green-700 p-2 rounded-lg flex place-items-center text-sm" type="button" id="openEducationModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Add Education
                                    </button>
                                    @endif
                                    <button class="text-white bg-blue-600 py-2 px-6 rounded-lg text-sm" type="submit">Save Record #{{ $loop->iteration }}</button>
                                </div>
                            </div>
                        </form>
                        @endforeach

                        {{-- Show Add button if no tertiary records exist yet --}}
                        @if($user->educationalBios->isEmpty())
                            <div class="flex mt-4 justify-end">
                                <button class="text-white mr-2 bg-green-600 hover:bg-green-700 p-2 rounded-lg flex place-items-center text-sm" type="button" id="openEducationModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Add Education
                                </button>
                            </div>
                        @endif
                    {{-- Professional Info Forms --}}
                    @elseif ($query === 'professional')
                        @forelse ($user->professionalBios as $profBio)
                        <form enctype="multipart/form-data" action="/profbio/update/{{ $profBio->id }}/{{ $user->id }}" method="POST" class="mb-4 border-b pb-6 last:border-b-0 last:pb-0">
                            @csrf
                            <h3 class="text-md font-semibold mb-1 text-gray-600">Professional Record #{{ $loop->iteration }}</h3>
                            <p class="text-xs text-gray-400 mb-3">Created: {{ $profBio->created_at->format('M d, Y H:i') }}</p>

                            {{-- Use Grid for layout --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">

                                {{-- Employment Status --}}
                                <div class="flex flex-col">
                                    <label for="employment_status_{{ $loop->index }}">Employment Status</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="employment_status" id="employment_status_{{ $loop->index }}">
                                        @foreach ($statuses as $status)
                                        <option {{ $profBio->employment_status === $status ? 'selected' : '' }} value="{{ $status}}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Employment Type 1 --}}
                                <div class="flex flex-col">
                                    <label for="employment_type1_{{ $loop->index }}">Employment Type 1</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="employment_type1" id="employment_type1_{{ $loop->index }}">
                                        <option {{ $profBio->employment_type1 === 'Private' ? 'selected' : '' }} value="Private">Private</option>
                                        <option {{ $profBio->employment_type1 === 'Government' ? 'selected' : '' }} value="Government">Government</option>
                                        <option {{ $profBio->employment_type1 === 'NGO/INGO' ? 'selected' : '' }} value="NGO/INGO">NGO/INGO</option>
                                    </select>
                                </div>

                                {{-- Employment Type 2 --}}
                                <div class="flex flex-col">
                                    <label for="employment_type2_{{ $loop->index }}">Employment Type 2</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="employment_type2" id="employment_type2_{{ $loop->index }}">
                                        <option {{ $profBio->employment_type2 === 'Full-Time' ? 'selected' : '' }} value="Full-Time">Full-Time</option>
                                        <option {{ $profBio->employment_type2 === 'Part-Time' ? 'selected' : '' }} value="Part-Time">Part-Time</option>
                                        <option {{ $profBio->employment_type2 === 'Traineeship' ? 'selected' : '' }} value="Traineeship">Traineeship</option>
                                        <option {{ $profBio->employment_type2 === 'Internship' ? 'selected' : '' }} value="Internship">Internship</option>
                                        <option {{ $profBio->employment_type2 === 'Contract' ? 'selected' : '' }} value="Contract">Contract</option>
                                    </select>
                                </div>

                                {{-- Industry --}}
                                <div class="flex flex-col">
                                    <label for="industry_{{ $loop->index }}">Industry</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="industry" id="industry_{{ $loop->index }}">
                                        <option value="">Select Industry...</option>
                                        @foreach ($industries as $industry)
                                        <option value="{{ $industry }}" {{ $profBio->industry === $industry ? 'selected' : '' }}>{{ $industry }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Job Title --}}
                                <div class="flex flex-col">
                                    <label for="job-title_{{ $loop->index }}">Current Job Title</label>
                                    <input value="{{ $profBio->job_title ?? '' }}" class="text-gray-400 border rounded-lg p-2" type="text" name="job_title" id="job-title_{{ $loop->index }}">
                                </div>

                                {{-- Company / Employer --}}
                                <div class="flex flex-col">
                                    <label for="company_{{ $loop->index }}">Company / Employer</label>
                                    <input value="{{ $profBio->company_name ?? '' }}" class="text-gray-400 border rounded-lg p-2" type="text" name="company_name" id="company_{{ $loop->index }}">
                                </div>

                                {{-- Monthly Salary Range --}}
                                <div class="flex flex-col">
                                    <label for="range_{{ $loop->index }}">Monthly Salary Range</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="monthly_salary" id="range_{{ $loop->index }}">
                                        @foreach ($salaryRanges as $range)
                                        <option value="{{ $range }}" {{ $profBio->monthly_salary === $range ? 'selected' : '' }}>{{ $range }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Location --}}
                                <div class="flex flex-col">
                                    <label for="location_{{ $loop->index }}">Location</label>
                                    <input value="{{ $profBio->work_location ?? '' }}" class="text-gray-400 border rounded-lg p-2" type="text" name="work_location" id="location_{{ $loop->index }}">
                                </div>

                                {{-- Waiting Time (Full Width) --}}
                                <div class="flex flex-col md:col-span-2">
                                    <label for="waiting_{{ $loop->index }}">Waiting Time <span class="text-gray-400">(period to get a job after graduation)</span></label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="waiting_time" id="waiting_{{ $loop->index }}">
                                        @foreach ($times as $time)
                                        <option value="{{ $time }}" {{ $profBio->waiting_time === $time ? 'selected' : '' }}>{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Job Search Methods (Full Width) --}}
                                <div class="flex flex-col md:col-span-2">
                                    <label for="methods_{{ $loop->index }}">Job Search Methods <span class="text-gray-400">(used to find a job)</span></label>
                                    <select multiple name="methods[]" id="methods_{{ $loop->index }}" class="methods-choices"
                                            data-selected-methods="{{ json_encode($profBio->methods->pluck('method')->toArray()) }}">
                                        {{-- Options populated by Choices.js --}}
                                    </select>
                                </div>

                                {{-- Hard Skills --}}
                                <div class="flex flex-col">
                                    <label for="hard-skills_{{ $loop->index }}">Hard Skill/s</label>
                                    <select multiple name="hard_skills[]" id="hard-skills_{{ $loop->index }}" class="hard-skills-choices"
                                            data-selected-hard-skills="{{ json_encode($profBio->hardSkills->pluck('skill')->toArray()) }}">
                                        {{-- Options populated by Choices.js --}}
                                    </select>
                                </div>

                                {{-- Soft Skills --}}
                                <div class="flex flex-col">
                                    <label for="soft-skills_{{ $loop->index }}">Soft Skill/s</label>
                                    <select multiple name="soft_skills[]" id="soft-skills_{{ $loop->index }}" class="soft-skills-choices"
                                            data-selected-soft-skills="{{ json_encode($profBio->softSkills->pluck('skill')->toArray()) }}">
                                        {{-- Options populated by Choices.js --}}
                                    </select>
                                </div>

                                {{-- Attachments (Full Width) --}}
                                <div class="flex flex-col md:col-span-2">
                                    <label>Certification & Licenses</label>
                                    <div class="flex gap-2 mt-1">
                                        {{-- Upload Button Wrapper --}}
                                        <label class="bg-gray-500 rounded-lg text-white p-2 w-fit cursor-pointer text-sm inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 inline mr-1">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                            </svg>
                                            Upload New PDF(s)
                                            <input class="hidden" type="file" id="certs_{{ $loop->index }}" accept="application/pdf" name="certs[]" multiple>
                                        </label>

                                        {{-- View Existing Button --}}
                                        <button type="button"
                                                class="open-view-attachments-modal bg-blue-500 rounded-lg text-white p-2 w-fit text-sm inline-flex items-center"
                                                data-modal-target="#viewAttachmentsModal_{{ $profBio->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 inline mr-1">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            View Existing
                                        </button>

                                        {{-- Modal for this specific record's attachments --}}
                                        <div id="viewAttachmentsModal_{{ $profBio->id }}" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
                                            <div class="bg-white p-6 rounded-lg shadow-lg w-1/3 max-w-lg">
                                                <div class="flex justify-between items-center mb-4">
                                                    <h2 class="text-lg font-bold">Existing Attachments (Record #{{ $loop->iteration }})</h2>
                                                    <button type="button" class="close-view-attachments-modal text-gray-400 hover:text-gray-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="gap-2 p-2 flex flex-col max-h-80 overflow-y-auto border rounded">
                                                    @forelse ($profBio->attachments as $attachment)
                                                    <div class="bg-gray-50 border p-3 rounded-lg">
                                                        <div class="flex justify-between items-start">
                                                            <p class="text-sm font-semibold text-slate-700 break-all mr-2" title="{{ $attachment->name }}">{{ $attachment->name }}</p>
                                                            <a href="/alumni/delete/attachment/{{ $attachment->id }}"
                                                               onclick="return confirm('Are you sure you want to delete this attachment?');"
                                                               class="text-red-500 hover:text-red-700 flex items-center flex-shrink-0"
                                                               title="Delete Attachment">
                                                                <span class="material-symbols-outlined text-lg">delete</span>
                                                            </a>
                                                        </div>
                                                        <a class="text-sm w-fit font-semibold text-blue-600 hover:underline flex items-center mt-2"
                                                           href="{{ asset('storage/professional/attachments/' . $attachment->link) }}" target="_blank" title="Open File">
                                                            <span class="material-symbols-outlined text-lg mr-1">file_open</span>
                                                            Open File
                                                        </a>
                                                    </div>
                                                    @empty
                                                    <p class="text-gray-500 text-center py-4">No attachments found for this record.</p>
                                                    @endforelse
                                                </div>
                                                <div class="flex justify-end mt-4">
                                                    <button type="button" class="close-view-attachments-modal px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div> {{-- End Grid --}}

                            {{-- Action Buttons (Full Width) --}}
                            <div class="flex mt-4 justify-end space-x-2">
                                {{-- Delete Button - Only show if more than 1 record exists --}}
                                @if (count($user->professionalBios) > 1)
                                <a href="/profbio/delete/{{ $profBio->id }}"
                                   onclick="return confirm('Are you sure you want to delete this professional record? This action cannot be undone.');"
                                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    Delete
                                </a>
                                @endif
                                {{-- Save Button --}}
                                <button class="text-white bg-blue-600 py-2 px-6 rounded-lg text-sm" type="submit">Save Record #{{ $loop->iteration }}</button>
                            </div>
                        </form>
                        @empty
                            <p class="text-gray-500 text-center py-4">No professional records found.</p>
                        @endforelse

                        <div class="flex justify-end pt-4">
                             <a href="{{ route('alumni.professional.create', ['alumni' => Auth::user()->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add New Professional Record
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // --- View Attachments Modal (Per Record) ---
    document.addEventListener('DOMContentLoaded', () => {
        const openButtons = document.querySelectorAll('.open-view-attachments-modal');
        const allModals = document.querySelectorAll('[id^="viewAttachmentsModal_"]'); // Get all modals

        openButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modalId = button.getAttribute('data-modal-target');
                const modal = document.querySelector(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                }
            });
        });

        allModals.forEach(modal => {
            const closeButtons = modal.querySelectorAll('.close-view-attachments-modal');

            closeButtons.forEach(closeButton => {
                closeButton.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });
            });

            // Close modal if clicking outside the content area
            modal.addEventListener('click', (e) => {
                if (e.target === modal) { // Check if the click is directly on the backdrop
                    modal.classList.add('hidden');
                }
            });
        });
    });
</script>
<script>
    const mainContentArea = document.getElementById('main-content-area');

    // Save scroll position before page reload
    function saveScrollPosition() {
        if (mainContentArea) {
            sessionStorage.setItem('scrollPosition', mainContentArea.scrollTop);
        }
    }

    // Add event listeners to elements that trigger page reload
    document.querySelectorAll('.sync-url-on-change').forEach(el => {
        el.addEventListener('change', saveScrollPosition);
    });

    if (document.getElementById('clearEducationalChangesBtn')) {
        document.getElementById('clearEducationalChangesBtn').addEventListener('click', saveScrollPosition);
    }

    // Restore scroll position after page load
    document.addEventListener('DOMContentLoaded', () => {
        if (mainContentArea) {
            const savedPosition = sessionStorage.getItem('scrollPosition');
            if (savedPosition) {
                mainContentArea.scrollTop = parseInt(savedPosition);
                sessionStorage.removeItem('scrollPosition'); // Clear stored position
            }
        }
    });
</script>
<script>
    // Profile Picture Upload
    const upload = document.getElementById('upload');
    const changeProfile = document.getElementById('change-profile');
    const userProfile = document.getElementById('user-profile');

    if (upload && changeProfile && userProfile) {
        upload.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.addEventListener('load', () => {
                userProfile.src = reader.result;
                changeProfile.submit();
            });
        });
    }
</script>

@if ($query === 'personal')
<script>
    // Reset Password Modal
    const openResetPasswordModal = document.getElementById('openResetPasswordModal');
    const closeResetPasswordModal = document.getElementById('closeResetPasswordModal');
    const resetPasswordModal = document.getElementById('resetPasswordModal');

    if (openResetPasswordModal && closeResetPasswordModal && resetPasswordModal) {
        openResetPasswordModal.addEventListener('click', () => {
            resetPasswordModal.classList.remove('hidden');
        });
        closeResetPasswordModal.addEventListener('click', () => {
            resetPasswordModal.classList.add('hidden');
        });
        window.addEventListener('click', (e) => {
            if (e.target === resetPasswordModal) {
                resetPasswordModal.classList.add('hidden');
            }
        });
    }
</script>
@endif

@if ($query === 'educational')
<script>
    // --- Primsec Strand Toggle ---
    function toggleStrand(index) {
        const typeSelect = document.getElementById('type_' + index);
        const strandContainer = document.getElementById('strand_container_' + index);

        if (typeSelect && strandContainer) {
            const currentTypeValue = new URL(window.location.href).searchParams.get('type_' + index) || typeSelect.value;
            if (currentTypeValue === 'primary') {
                strandContainer.classList.add('hidden');
            } else {
                strandContainer.classList.remove('hidden');
            }
        }
    }

    // Initialize all primsec forms on page load
    document.addEventListener('DOMContentLoaded', function() {
        const types = document.querySelectorAll('.education-type');
        types.forEach((select) => {
            const idIndex = select.id.split('_').pop();
            if (!isNaN(idIndex)) {
                toggleStrand(parseInt(idIndex, 10));
            }
        });
    });

    // --- Generic Select Change Handling for URL Sync ---
    const syncSelects = document.querySelectorAll('.sync-url-on-change');

    syncSelects.forEach(select => {
        select.addEventListener('change', (e) => {
            const selectId = e.target.id;
            const indexMatch = selectId.match(/_(\d+)$/);
            if (!indexMatch) return;
            const index = indexMatch[1];

            let paramNameBase = e.target.name;
            if (selectId.startsWith('type_')) paramNameBase = 'type';
            else if (selectId.startsWith('strand_')) paramNameBase = 'strand';
            else if (selectId.startsWith('school_')) paramNameBase = 'school';
            else if (selectId.startsWith('course_')) paramNameBase = 'course';
            else if (selectId.startsWith('degree_')) paramNameBase = 'degree_type';
            else if (selectId.startsWith('major_')) paramNameBase = 'major';

            const paramName = `${paramNameBase}_${index}`;
            const selectValue = e.target.value;
            const currentUrl = new URL(window.location.href);

            if (mainContentArea) {
                mainContentArea.classList.add('is-reloading');
            }

            currentUrl.searchParams.set(paramName, selectValue);

            if (paramNameBase === 'course') {
                currentUrl.searchParams.delete('major_' + index);
            }

            window.location.href = currentUrl.toString();
        });
    });


    // --- Education Modal ---
    const openEducationModal = document.getElementById('openEducationModal');
    const closeEducationModalButtons = document.querySelectorAll('.closeEducationModal');
    const educationModal = document.getElementById('educationModal');

    if (openEducationModal && educationModal) {
        openEducationModal.addEventListener('click', () => {
            educationModal.classList.remove('hidden');
        });

        closeEducationModalButtons.forEach(button => {
            button.addEventListener('click', () => {
                educationModal.classList.add('hidden');
            });
        });

        window.addEventListener('click', (e) => {
            if (e.target === educationModal) {
                educationModal.classList.add('hidden');
            }
        });
    }

    // --- Clear Changes Button ---
    const clearChangesBtn = document.getElementById('clearEducationalChangesBtn');
    if (clearChangesBtn) {
        clearChangesBtn.addEventListener('click', () => {
            const currentUrl = new URL(window.location.href);
            const paramsToRemove = [];
            const prefixesToRemove = ['type_', 'strand_', 'school_', 'course_', 'degree_type_', 'major_'];

            for (const key of currentUrl.searchParams.keys()) {
                for (const prefix of prefixesToRemove) {
                    if (key.startsWith(prefix)) {
                        paramsToRemove.push(key);
                        break;
                    }
                }
            }

            paramsToRemove.forEach(param => {
                currentUrl.searchParams.delete(param);
            });

            if (mainContentArea) {
                mainContentArea.classList.add('is-reloading');
            }
            window.location.href = currentUrl.toString();
        });
    }
</script>
@endif

@if ($query === 'professional')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
{{-- Link is already in @section('header'), no need to repeat --}}
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" /> --}}
<script>
    // --- View Attachments Modal (Assuming Global) ---
    // const openViewAttachmentsModal = document.getElementById('openViewAttachmentsModal');
    // const viewAttachmentsModal = document.getElementById('viewAttachmentsModal');
    // const closeViewAttachmentsModal = document.getElementById('closeViewAttachmentsModal');

    // if (openViewAttachmentsModal && viewAttachmentsModal && closeViewAttachmentsModal) {
    //     openViewAttachmentsModal.addEventListener('click', () => {
    //         viewAttachmentsModal.classList.remove('hidden');
    //     });

    //     closeViewAttachmentsModal.addEventListener('click', () => {
    //         viewAttachmentsModal.classList.add('hidden');
    //     });

    //     window.addEventListener('click', (e) => {
    //         if (e.target === viewAttachmentsModal) {
    //             viewAttachmentsModal.classList.add('hidden');
    //         }
    //     });
    // }

    // --- Choices.js Initialization ---
    document.addEventListener('DOMContentLoaded', () => {
        const allMethods = @json($methods);
        const allSoftSkills = @json($softSkillsList);
        const allHardSkills = @json($hardSkillsList);

        // Initialize Methods selects
        document.querySelectorAll('.methods-choices').forEach(selectEl => {
            try {
                const selectedValues = JSON.parse(selectEl.dataset.selectedMethods || '[]');
                const choices = allMethods.map(method => ({
                    label: method, value: method, selected: selectedValues.includes(method)
                }));
                new Choices(selectEl, { removeItemButton: true, choices: choices });
            } catch (e) {
                console.error("Error initializing Choices.js for methods:", e, selectEl);
            }
        });

        // Initialize Soft Skills selects
        document.querySelectorAll('.soft-skills-choices').forEach(selectEl => {
             try {
                const selectedValues = JSON.parse(selectEl.dataset.selectedSoftSkills || '[]');
                const choices = allSoftSkills.map(skill => ({
                    label: skill, value: skill, selected: selectedValues.includes(skill)
                }));
                new Choices(selectEl, { removeItemButton: true, choices: choices });
            } catch (e) {
                console.error("Error initializing Choices.js for soft skills:", e, selectEl);
            }
        });

        // Initialize Hard Skills selects
        document.querySelectorAll('.hard-skills-choices').forEach(selectEl => {
             try {
                const selectedValues = JSON.parse(selectEl.dataset.selectedHardSkills || '[]');
                const choices = allHardSkills.map(skill => ({
                    label: skill, value: skill, selected: selectedValues.includes(skill)
                }));
                new Choices(selectEl, { removeItemButton: true, choices: choices });
            } catch (e) {
                console.error("Error initializing Choices.js for hard skills:", e, selectEl);
            }
        });
    });

</script>
@endif
@endsection