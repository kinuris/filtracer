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
    'Over 1 year'
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

@php $prof = $user->getProfessionalBio(); @endphp
@include('components.education-modal')
@include('components.reset-password-modal')
@include('components.view-attachments-modal')

<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg">Update Profile</h1>
    <p class="text-gray-400 text-xs">Profile / <span class="text-blue-500">Update Profile</span></p>

    <div class="flex max-h-[calc(100%-16px)]">
        <div class="shadow rounded-lg h-fit mt-6 flex-1 min-w-80">
            <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
                <img class="w-32 h-32 rounded-full object-cover shadow-md mr-8" src="{{ $user->image() }}" id="user-profile" alt="Profile">
                <p class="text-lg font-bold my-6">{{ $user->name }}</p>

                <div class="flex place-items-center">
                    <img class="h-4 mr-3" src="{{ asset('assets/alumni_job.svg') }}" alt="Job">
                    <p class="text-gray-400 text-sm">{{ $user->getProfessionalBio()?->employment_status ?? 'N/A' }}</p>
                </div>
                <div class="flex mt-1 ml-[1px] place-items-center">
                    <img class="h-4 mr-3" src="{{ asset('assets/alumni_location.svg') }}" alt="Job">
                    <p class="text-gray-400 text-sm">{{ $user->getProfessionalBio()?->work_location ?? 'N/A' }}</p>
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

        <div class="flex-[3] flex flex-col mt-6 mb-3 max-h-full overflow-auto">
            <div class="shadow rounded-lg">
                <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg" id="main-content-area">
                    <div class="flex mb-4">
                        <a class="text-gray-400 font-semibold @if($query === 'personal') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=personal">Personal Info</a>
                        <a class="text-gray-400 font-semibold mx-4 @if($query === 'educational') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=educational">Educational Info</a>
                        <a class="text-gray-400 font-semibold @if($query === 'professional') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=professional">Professional Info</a>
                    </div>

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

                            <div class="flex mt-4 justify-end">
                                <button type="button" class="text-white mr-2 bg-blue-600 p-2 rounded-lg flex place-items-center" id="openResetPasswordModal">
                                    <img class="w-4 mr-2" src="{{ asset('assets/pass_reset.svg') }}" alt="Reset">
                                    Reset Password
                                </button>
                                <button class="text-white bg-blue-600 py-2 px-6 rounded-lg" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
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

                    @foreach($user->primsecEducational as $primsec)
                    <form action="/alumni/profile/update/primsec/{{ $primsec->id }}/{{ $user->id }}" method="POST">
                        @csrf
                        <div class="flex flex-col">
                            <div class="flex">
                                <div class="flex flex-col flex-1 max-w-[50%]">
                                    <label for="type_{{ $loop->index }}">Education Level</label>
                                    {{-- Added sync-url-on-change class --}}
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
                                        {{-- Added sync-url-on-change class --}}
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
                                <button type="button" id="clearEducationalChangesBtn" class="text-white mr-2 bg-red-500 hover:bg-red-600 p-2 rounded-lg flex place-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                      </svg>
                                    Clear Changes
                                </button>
                                @endif
                                <button class="text-white bg-blue-600 py-2 px-6 rounded-lg" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                    @endforeach

                    @foreach ($user->educationalBios as $educ)
                    <form action="/alumni/profile/update/educational/{{ $educ->id }}/{{ $user->id }}" method="POST">
                        @csrf
                        <div class="flex flex-col">
                            <div class="flex">
                                <div class="flex flex-col flex-1 max-w-[50%]">
                                    <label for="school_{{ $loop->index }}">School</label>
                                    {{-- Added sync-url-on-change class --}}
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
                                    {{-- Added sync-url-on-change class --}}
                                    <select class="text-gray-400 border rounded-lg p-2 course-select sync-url-on-change" name="course" id="course_{{ $loop->index }}">
                                        @php $selectedCourseId = request()->query('course_'.$loop->index, $educ->course_id); @endphp
                                        @foreach (App\Models\Course::all() as $course)
                                        <option {{ $selectedCourseId == $course->id ? 'selected' : '' }} value="{{ $course->id }}">{{ $course->name }}</option>
                                        @endforeach
                                    </select>

                                    <label class="mt-3" for="degree_{{ $loop->index }}">Degree Type</label>
                                    {{-- Added sync-url-on-change class --}}
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
                            {{-- Added sync-url-on-change class --}}
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
                                <button type="button" id="clearEducationalChangesBtn" class="text-white mr-2 bg-red-500 hover:bg-red-600 p-2 rounded-lg flex place-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                      </svg>
                                    Clear Changes
                                </button>
                                @endif
                                @if ($loop->last) {{-- Show Add button only on the last education form --}}
                                <button class="text-white mr-2 bg-blue-600 p-2 rounded-lg flex place-items-center" type="button" id="openEducationModal">
                                    <img class="w-4 mr-2" src="{{ asset('assets/pass_reset.svg') }}" alt="Add">
                                    Add Education
                                </button>
                                @endif
                                <button class="text-white bg-blue-600 py-2 px-6 rounded-lg" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                    @endforeach

                    @elseif ($query === 'professional')
                    <form enctype="multipart/form-data" action="{{ $user->getProfessionalBio() ? '/profbio/update/' . $user->id : '/profbio/create/' . $user->id }}" method="POST">
                        @csrf
                        <div class="flex flex-col">
                            <div class="flex">
                                <div class="flex flex-col flex-1 max-w-[50%]">
                                    <label for="employment_status">Employment Status</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="employment_status" id="employment_status">
                                        @foreach ($statuses as $status)
                                        <option {{ $prof && $prof->employment_status === $status ? 'selected' : '' }} value="{{ $status}}">{{ $status }}</option>
                                        @endforeach
                                    </select>

                                    <label class="mt-3" for="type1">Employment Type 1</label>
                                    <select class="text-gray-400 border rounded-lg p-2" type="text" name="employment_type1">
                                        <option {{ $prof && $prof->employment_type1 === 'Private' ? 'selected' : '' }} value="Private">Private</option>
                                        <option {{ $prof && $prof->employment_type1 === 'Government' ? 'selected' : '' }} value="Government">Government</option>
                                        <option {{ $prof && $prof->employment_type1 === 'NGO/INGO' ? 'selected' : '' }} value="NGO/INGO">NGO/INGO</option>
                                    </select>

                                    <label class="mt-3" for="type2">Employment Type 2</label>
                                    <select class="text-gray-400 border rounded-lg p-2" type="number" name="employment_type2">
                                        <option {{ $prof && $prof->employment_type2 === 'Full-Time' ? 'selected' : '' }} value="Full-Time">Full-Time</option>
                                        <option {{ $prof && $prof->employment_type2 === 'Part-Time' ? 'selected' : '' }} value="Part-Time">Part-Time</option>
                                        <option {{ $prof && $prof->employment_type2 === 'Traineeship' ? 'selected' : '' }} value="Traineeship">Traineeship</option>
                                        <option {{ $prof && $prof->employment_type2 === 'Internship' ? 'selected' : '' }} value="Internship">Internship</option>
                                        <option {{ $prof && $prof->employment_type2 === 'Contract' ? 'selected' : '' }} value="Contract">Contract</option>
                                    </select>

                                    <label class="mt-3" for="industry">Industry</label>
                                    <input value="{{ $prof ? $prof->industry : '' }}" class="text-gray-400 border rounded-lg p-2" type="text" name="industry">
                                </div>

                                <div class="mx-2"></div>

                                <div class="flex flex-col flex-1">
                                    <label for="job-title">Current Job Title</label>
                                    <input value="{{ $prof ? $prof->job_title : '' }}" class="text-gray-400 border rounded-lg p-2" type="text" name="job_title">

                                    <label class="mt-3" for="company">Company / Employer</label>
                                    <input value="{{ $prof ? $prof->company_name : '' }}" class="text-gray-400 border rounded-lg p-2" type="text" name="company">

                                    @php
                                    $ranges = [
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
                                    <label class="mt-3" for="range">Monthly Salary Range</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="monthly_salary" id="range">
                                        @foreach ($ranges as $range)
                                        <option value="{{ $range }}" {{ $prof && $prof->monthly_salary === $range ? 'selected' : '' }}>{{ $range }}</option>
                                        @endforeach
                                    </select>

                                    <label class="mt-3" for="location">Location</label>
                                    <input value="{{ $prof ? $prof->work_location : '' }}" class="text-gray-400 border rounded-lg p-2" type="text" name="work_location">
                                </div>
                            </div>

                            <label class="mt-5" for="waiting">Waiting Time <span class="text-gray-400">(period to get a job after graduation)</span></label>
                            <select class="text-gray-400 border rounded-lg p-2" name="waiting_time" id="waiting">
                                @foreach ($times as $time)
                                <option value="{{ $time }}" {{ $prof && $prof->waiting_time === $time ? 'selected' : '' }}>{{ $time }}</option>
                                @endforeach
                            </select>

                            <label class="mt-5" for="methods">Job Search Methods <span class="text-gray-400">(used to find a job)</span></label>
                            <select multiple name="methods[]" id="methods">
                                {{-- Options will be populated by Choices.js --}}
                            </select>

                            <div class="flex">
                                <div class="flex-1 flex flex-col">
                                    <label class="mt-3" for="hard-skills">Hard Skill/s</label>
                                    <select multiple name="hard_skills[]" id="hard-skills">
                                        {{-- Options will be populated by Choices.js --}}
                                    </select>
                                </div>

                                <div class="mx-2"></div>

                                <div class="flex-1 flex flex-col">
                                    <label class="mt-3" for="soft-skills">Soft Skill/s</label>
                                    <select multiple name="soft_skills[]" id="soft-skills">
                                        {{-- Options will be populated by Choices.js --}}
                                    </select>
                                </div>
                            </div>

                            <label class="mt-3" for="certs">
                                <p>Certification & Licenses</p>
                                <div class="flex gap-2 mt-3">
                                    <div class="bg-gray-500 rounded-lg text-white p-2 w-fit cursor-pointer" onclick="document.getElementById('certs').click()">Upload</div>
                                    <button id="openViewAttachmentsModal" type="button" class="bg-blue-500 rounded-lg text-white p-2 w-fit">Existing</button>
                                </div>
                            </label>
                            <input class="mt-2" type="file" id="certs" accept="application/pdf" name="certs[]" multiple>

                            <div class="flex mt-4 justify-end">
                                <button class="text-white bg-blue-600 py-2 px-6 rounded-lg" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Save scroll position before page reload
    function saveScrollPosition() {
        sessionStorage.setItem('scrollPosition', mainContentArea.scrollTop);
    }

    // Add event listeners to all elements that trigger page reload
    document.querySelectorAll('.sync-url-on-change').forEach(el => {
        el.addEventListener('change', saveScrollPosition);
    });

    if (document.getElementById('clearEducationalChangesBtn')) {
        document.getElementById('clearEducationalChangesBtn').addEventListener('click', saveScrollPosition);
    }

    // Restore scroll position after page load
    document.addEventListener('DOMContentLoaded', () => {
        const savedPosition = sessionStorage.getItem('scrollPosition');
        if (savedPosition) {
            mainContentArea.scrollTop = parseInt(savedPosition);
            sessionStorage.removeItem('scrollPosition'); // Clear stored position
        }
    });
</script>
<script>
    const upload = document.getElementById('upload');
    const changeProfile = document.getElementById('change-profile');
    const userProfile = document.getElementById('user-profile');

    upload.addEventListener('change', (e) => {
        const file = e.target.files[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        reader.readAsDataURL(file);
        reader.addEventListener('load', () => {
            userProfile.src = reader.result;

            changeProfile.submit();
        });
    });
</script>

@if ($query === 'personal')
<script>
    const openResetPasswordModal = document.getElementById('openResetPasswordModal');
    const closeResetPasswordModal = document.getElementById('closeResetPasswordModal');
    const resetPasswordModal = document.getElementById('resetPasswordModal');

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
</script>
@endif

@if ($query === 'educational')
<script>
    // --- Primsec Strand Toggle ---
    function toggleStrand(index) {
        const typeSelect = document.getElementById('type_' + index);
        const strandContainer = document.getElementById('strand_container_' + index);

        if (typeSelect && strandContainer) { // Check if elements exist
            // Use the value from the query parameter if available, otherwise use the selected value
            const currentTypeValue = new URL(window.location.href).searchParams.get('type_' + index) || typeSelect.value;
            if (currentTypeValue === 'primary') {
                strandContainer.classList.add('hidden');
            } else {
                strandContainer.classList.remove('hidden');
            }
        }
    }

    // Initialize all primsec forms on page load based on current state (including query params)
    document.addEventListener('DOMContentLoaded', function() {
        const types = document.querySelectorAll('.education-type');
        types.forEach((select, index) => {
            // Get the index from the id (e.g., 'type_0' -> 0)
            const idIndex = select.id.split('_').pop();
            if (!isNaN(idIndex)) {
                toggleStrand(parseInt(idIndex, 10));
            }
        });
    });

    // --- Generic Select Change Handling for URL Sync ---
    const syncSelects = document.querySelectorAll('.sync-url-on-change');
    const mainContentArea = document.getElementById('main-content-area'); // Target the container

    syncSelects.forEach(select => {
        select.addEventListener('change', (e) => {
            // Get the index from the ID attribute, as name no longer has it
            const selectId = e.target.id;
            const indexMatch = selectId.match(/_(\d+)$/);
            if (!indexMatch) return; // Exit if ID doesn't contain index
            const index = indexMatch[1];

            // Construct the parameter name using the original base name and the index
            let paramNameBase = e.target.name; // e.g., 'type', 'school', 'course'
            // Handle specific base names if needed, otherwise assume simple structure
            if (selectId.startsWith('type_')) paramNameBase = 'type';
            else if (selectId.startsWith('strand_')) paramNameBase = 'strand';
            else if (selectId.startsWith('school_')) paramNameBase = 'school';
            else if (selectId.startsWith('course_')) paramNameBase = 'course';
            else if (selectId.startsWith('degree_')) paramNameBase = 'degree_type';
            else if (selectId.startsWith('major_')) paramNameBase = 'major';
            // Add more cases if other indexed fields exist

            const paramName = `${paramNameBase}_${index}`; // Reconstruct indexed name for URL param
            const selectValue = e.target.value;
            const currentUrl = new URL(window.location.href);

            // Add visual feedback
            if (mainContentArea) {
                mainContentArea.classList.add('is-reloading');
            }

            // Update the specific query parameter
            currentUrl.searchParams.set(paramName, selectValue);

            // Special handling for course change: reset major if course changes
            if (paramNameBase === 'course') {
                 currentUrl.searchParams.delete('major_' + index); // Remove major param
            }

            // Reload the page with the new URL
            window.location.href = currentUrl.toString();
        });
    });


    // --- Education Modal ---
    const openEducationModal = document.getElementById('openEducationModal');
    const closeEducationModalButtons = document.querySelectorAll('.closeEducationModal');
    const educationModal = document.getElementById('educationModal');

    if (openEducationModal && educationModal) { // Check if elements exist
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

            // Find all parameters matching the prefixes
            for (const key of currentUrl.searchParams.keys()) {
                for (const prefix of prefixesToRemove) {
                    if (key.startsWith(prefix)) {
                        paramsToRemove.push(key);
                        break; // Move to the next key once a match is found
                    }
                }
            }

            // Remove the identified parameters
            paramsToRemove.forEach(param => {
                currentUrl.searchParams.delete(param);
            });

            // Add visual feedback
            if (mainContentArea) {
                mainContentArea.classList.add('is-reloading');
            }

            // Reload the page with cleaned URL
            window.location.href = currentUrl.toString();
        });
    }
</script>
@endif

@if ($query === 'professional')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script>
    // --- View Attachments Modal ---
    const openViewAttachmentsModal = document.getElementById('openViewAttachmentsModal');
    const viewAttachmentsModal = document.getElementById('viewAttachmentsModal');
    const closeViewAttachmentsModal = document.getElementById('closeViewAttachmentsModal');

    if (openViewAttachmentsModal && viewAttachmentsModal && closeViewAttachmentsModal) { // Check if elements exist
        openViewAttachmentsModal.addEventListener('click', () => {
            viewAttachmentsModal.classList.remove('hidden');
        });

        closeViewAttachmentsModal.addEventListener('click', () => {
            viewAttachmentsModal.classList.add('hidden');
        });

        window.addEventListener('click', (e) => {
            if (e.target === viewAttachmentsModal) {
                viewAttachmentsModal.classList.add('hidden');
            }
        });
    }

    // --- Choices.js Initialization ---
    const methodsSelect = document.querySelector('#methods');
    const softSkillsSelect = document.querySelector('#soft-skills');
    const hardSkillsSelect = document.querySelector('#hard-skills');

    if (methodsSelect) {
        new Choices(methodsSelect, {
            removeItemButton: true,
            choices: [
                <?php
                if ($prof) {
                    $existingMethods = $prof->methods->pluck('method')->toArray();
                } else {
                    $existingMethods = [];
                }

                foreach ($methods as $method) {
                    $exists = in_array($method, $existingMethods) ? 'true' : 'false';
                    echo "{ label: '$method', value: '$method', selected: $exists },";
                }
                ?>
            ]
        });
    }

    if (softSkillsSelect) {
        new Choices(softSkillsSelect, {
            removeItemButton: true,
            choices: [
                <?php
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

                if ($prof) {
                    $existingSoftSkills = $prof->softSkills->pluck('skill')->toArray();
                } else {
                    $existingSoftSkills = [];
                }

                foreach ($softSkillsList as $skill) {
                    $exists = in_array($skill, $existingSoftSkills) ? 'true' : 'false';
                    echo "{ label: '$skill', value: '$skill', selected: $exists },";
                }
                ?>
            ]
        });
    }

    if (hardSkillsSelect) {
        new Choices(hardSkillsSelect, {
            removeItemButton: true,
            choices: [
                <?php
                $hardSkillsList = [
                    'Technical Skills',
                    'Engineering Skills',
                    'Business and Finance Skills',
                    'Marketing Skills',
                    'Cooking Skills'
                ];

                if ($prof) {
                    $existingHardSkills = $prof->hardSkills->pluck('skill')->toArray();
                } else {
                    $existingHardSkills = [];
                }

                foreach ($hardSkillsList as $skill) {
                    $exists = in_array($skill, $existingHardSkills) ? 'true' : 'false';
                    echo "{ label: '$skill', value: '$skill', selected: $exists },";
                }
                ?>
            ]
        });
    }
</script>
@endif
@endsection