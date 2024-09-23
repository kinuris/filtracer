@extends('layouts.alumni')

@section('header')
<style>
    input::-webkit-file-upload-button {
        display: none;
    }
</style>
@endsection

@section('title', 'Update Profile')

@section('content')
@php($query = request()->query('type') ?? 'personal')
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

@php($times = [
'Below 3 months',
'3-5 months',
'6 months-1 year',
'Over 1 year'
])

@php($methods = [
'Career Center',
'Experimental Learning',
'Networking',
'Online Resources',
'Campus Resources'
])

@php($statuses = [
'Employed',
'Unemployed',
'Self-employed',
'Student',
'Working Student',
'Retired'
])

@php($prof = $user->getProfessionalBio())
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
                    <p class="text-gray-400 text-sm">{{ $user->getProfessionalBio()->job_title }}</p>
                </div>
                <div class="flex mt-1 ml-[1px] place-items-center">
                    <img class="h-4 mr-3" src="{{ asset('assets/alumni_location.svg') }}" alt="Job">
                    <p class="text-gray-400 text-sm">{{ $user->getProfessionalBio()->work_location }}</p>
                </div>

                <form action="/alumni/profile/upload/{{ $user->id }}" enctype="multipart/form-data" method="POST" id="change-profile">
                    @csrf
                    <label for="upload">
                        <p class="bg-blue-600 text-white w-fit p-2.5 rounded-lg mt-4 flex place-items-center">
                            <img class="w-4 mr-2" src="{{ asset('assets/upload.svg') }}" alt="Upload">
                            Change Picture
                        </p>
                    </label>
                    <input class="hidden" type="file" name="profile" id="upload">
                </form>
            </div>
        </div>

        <div class="mx-2"></div>

        <div class="flex-[3] flex flex-col mt-6 mb-3 max-h-full overflow-auto">
            <div class="shadow rounded-lg">
                <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
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
                    @foreach ($user->educationalBios as $educ)
                    <form action="/alumni/profile/update/educational/{{ $educ->id }}/{{ $user->id }}" method="POST">
                        @csrf
                        <div class="flex flex-col">
                            <div class="flex">
                                <div class="flex flex-col flex-1 max-w-[50%]">
                                    <label for="school">School</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="school" id="school">
                                        @foreach ($schools as $school)
                                        <option {{ $educ->school_name === $school ? 'selected' : '' }} value="{{ $school }}">{{ $school }}</option>
                                        @endforeach
                                    </select>

                                    <label class="mt-3" for="location">Location</label>
                                    <input value="{{ $educ->school_location }}" class="text-gray-400 border rounded-lg p-2" type="text" name="location">

                                    <label class="mt-3" for="start">Year Started</label>
                                    <input value="{{ $educ->start }}" class="text-gray-400 border rounded-lg p-2" type="number" name="start" id="start">
                                </div>

                                <div class="mx-2"></div>

                                <div class="flex flex-col flex-1">
                                    <label for="course">Course</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="course" id="course">
                                        @foreach (App\Models\Course::all() as $course)
                                        <option {{ $educ->course_id === $course->id ? 'selected' : '' }} value="{{ $course->id }}">{{ $course->name }}</option>
                                        @endforeach
                                    </select>

                                    <label class="mt-3" for="degree">Degree Type</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="degree_type" id="degree">
                                        <option {{ $educ->degree_type === 'Bachelor' ? 'selected' : '' }} value="Bachelor">Bachelor</option>
                                        <option {{ $educ->degree_type === 'Masteral' ? 'selected' : '' }} value="Masteral">Masteral</option>
                                        <option {{ $educ->degree_type === 'Doctoral' ? 'selected' : '' }} value="Doctoral">Doctoral</option>
                                    </select>

                                    <label class="mt-3" for="end">Year Graduated</label>
                                    <input value="{{ $educ->end }}" class="text-gray-400 border rounded-lg p-2" type="number" name="end">
                                </div>
                            </div>

                            <label class="mt-3" for="major">Major</label>
                            <select class="text-gray-400 border rounded-lg p-2" name="major" id="major">
                                @php($majors = App\Models\Major::all())
                                @foreach ($majors as $major)
                                    <option {{ $educ->major_id === $major->id ? 'selected' : '' }} value="{{ $major->id }}">{{ $major->name }}</option> 
                                @endforeach
                            </select>

                            <div class="flex mt-4 justify-end">
                                @if ($loop->index === count($user->educationalBios) - 1)
                                <button class="text-white mr-2 bg-blue-600 p-2 rounded-lg flex place-items-center" type="button" id="openEducationModal">
                                    <img class="w-4 mr-2" src="{{ asset('assets/pass_reset.svg') }}" alt="Reset">
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
                                        <option value="Full-Time">Full-Time</option>
                                        <option value="Part-Time">Part-Time</option>
                                        <option value="Traineeship">Traineeship</option>
                                        <option value="Internship">Internship</option>
                                        <option value="Contract">Contract</option>
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

                                    @php($ranges = [
                                    'No Income',
                                    'Below 10,000',
                                    '10,000-20,000',
                                    '20,001-40,000',
                                    '40,001-60,000',
                                    '60,001-80,000',
                                    '80,001-100,000',
                                    'Over 100,000'
                                    ])
                                    <label class="mt-3" for="range">Monthly Salary Range</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="monthly_salary" id="range">
                                        @foreach ($ranges as $range)
                                        <option value="{{ $range }}" {{ $prof && $prof->monthly_salary === $range ? 'selected' : '' }}>{{ $range }}</option">
                                        </option>
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

                            </select>

                            <div class="flex">
                                <div class="flex-1 flex flex-col">
                                    <label class="mt-3" for="hard-skills">Hard Skill/s</label>
                                    <select multiple name="hard_skills[]" id="hard-skills">
                                    </select>
                                </div>

                                <div class="mx-2"></div>

                                <div class="flex-1 flex flex-col">
                                    <label class="mt-3" for="soft-skills">Soft Skill/s</label>
                                    <select multiple name="soft_skills[]" id="soft-skills">
                                    </select>
                                </div>
                            </div>

                            <label class="mt-3" for="certs">
                                <p>Certification & Licenses</p>
                                <div class="flex gap-2 mt-3">
                                    <div class="bg-gray-500 rounded-lg text-white p-2 w-fit">Upload</div>
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
    const openEducationModal = document.getElementById('openEducationModal');
    const closeEducationModal = document.getElementById('closeEducationModal');
    const educationModal = document.getElementById('educationModal');

    openEducationModal.addEventListener('click', () => {
        educationModal.classList.remove('hidden');
    });

    closeEducationModal.addEventListener('click', () => {
        educationModal.classList.add('hidden');
    });

    window.addEventListener('click', (e) => {
        if (e.target === educationModal) {
            educationModal.classList.add('hidden');
        }
    });
</script>
@endif

@if ($query === 'professional')
<script>
    const openViewAttachmentsModal = document.getElementById('openViewAttachmentsModal');
    const viewAttachmentsModal = document.getElementById('viewAttachmentsModal');
    const closeViewAttachmentsModal = document.getElementById('closeViewAttachmentsModal');

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
</script>
<script>
    const waiting = document.querySelector('#methods');
    const softSkills = document.querySelector('#soft-skills');
    const hardSkills = document.querySelector('#hard-skills');

    new Choices(waiting, {
        removeItemButton: true,
        choices: [
            <?php
            if ($prof) {
                $existing = $prof->methods->pluck('method')->toArray();
            } else {
                $existing = [];
            }

            foreach ($methods as $method) {
                $exists = in_array($method, $existing) ? 'true' : 'false';

                echo "{ label: '$method', value: '$method', selected: $exists },";
            }
            ?>
        ]
    });

    new Choices(softSkills, {
        removeItemButton: true,
        choices: [
            <?php
            $softSkills = [
                'Communication Skills',
                'Teamwork and Collaboration',
                'Leadership Skills',
                'Adaptability and Problem-Solving',
                'Time Management',
                'Work Ethic',
                'Interpersonal Skills'
            ];

            if ($prof) {
                $existing = $prof->softSkills->pluck('skill')->toArray();
            } else {
                $existing = [];
            }

            foreach ($softSkills as $skill) {
                $exists = in_array($skill, $existing) ? 'true' : 'false';

                echo "{ label: '$skill', value: '$skill', selected: $exists },";
            }
            ?>
        ]
    });

    new Choices(hardSkills, {
        removeItemButton: true,
        choices: [
            <?php
            $hardSkills = [
                'Technical Skills',
                'Engineering Skills',
                'Business and Finance Skills',
                'Marketing Skills',
                'Cooking Skills'
            ];

            if ($prof) {
                $existing = $prof->hardSkills->pluck('skill')->toArray();
            } else {
                $existing = [];
            }

            foreach ($hardSkills as $skill) {
                $exists = in_array($skill, $existing) ? 'true' : 'false';

                echo "{ label: '$skill', value: '$skill', selected: $exists },";
            }
            ?>
        ]
    });
</script>
@endif
@endsection