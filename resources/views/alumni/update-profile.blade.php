@extends('layouts.alumni')

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

@php($statuses = [
'Employed',
'Unemployed',
'Self-employed',
'Student',
'Working Student',
'Retired'
])
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg">Update Profile</h1>
    <p class="text-gray-400 text-xs">Profile / <span class="text-blue-500">Update Profile</span></p>

    <div class="flex max-h-[calc(100%-16px)]">
        <div class="shadow rounded-lg h-fit mt-6 flex-1 min-w-80">
            <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
                <img class="w-32 h-32 rounded-full object-cover shadow-md mr-8" src="{{ $user->image() }}" alt="Profile">
                <p class="text-lg font-bold my-6">{{ $user->name }}</p>

                <div class="flex place-items-center">
                    <img class="h-4 mr-3" src="{{ asset('assets/alumni_job.svg') }}" alt="Job">
                    <p class="text-gray-400 text-sm">Example Job</p>
                </div>
                <div class="flex mt-1 ml-[1px] place-items-center">
                    <img class="h-4 mr-3" src="{{ asset('assets/alumni_location.svg') }}" alt="Job">
                    <p class="text-gray-400 text-sm">Roxas City, Capiz</p>
                </div>

                <form action="">
                    <label for="change">
                        <p class="bg-blue-600 text-white w-fit p-2.5 rounded-lg mt-4 flex place-items-center">
                            <img class="w-4 mr-2" src="{{ asset('assets/upload.svg') }}" alt="Upload">
                            Change Picture
                        </p>
                    </label>
                    <input class="hidden" type="file" name="profile" id="change">
                </form>
            </div>
        </div>

        <div class="mx-2"></div>

        <div class="flex-[3] flex flex-col mt-6 mb-3 max-h-full overflow-scroll">
            <div class="shadow rounded-lg">
                <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
                    <div class="flex mb-4">
                        <a class="text-gray-400 font-semibold @if($query === 'personal') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=personal">Personal Info</a>
                        <a class="text-gray-400 font-semibold mx-4 @if($query === 'educational') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=educational">Educational Info</a>
                        <a class="text-gray-400 font-semibold @if($query === 'professional') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=professional">Professional Info</a>
                    </div>

                    @if ($query === 'personal')
                    <form action="" method="POST">
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
                                    <input value="{{ $user->getPersonalBio()->birthdate->format('Y-m-d') }}" class="text-gray-400 border rounded-lg p-2" type="date" name="student_id">

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
                                </div>
                            </div>

                            <div class="flex mt-4 justify-end">
                                <a class="text-white mr-2 bg-blue-600 p-2 rounded-lg flex place-items-center" href="">
                                    <img class="w-4 mr-2" src="{{ asset('assets/pass_reset.svg') }}" alt="Reset">
                                    Reset Password
                                </a>
                                <button class="text-white bg-blue-600 py-2 px-6 rounded-lg" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                    @elseif ($query === 'educational')

                    <form action="" method="POST">
                        <div class="flex flex-col">
                            <div class="flex">
                                <div class="flex flex-col flex-1 max-w-[50%]">
                                    <label for="school">School</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="school" id="school">
                                        @foreach ($schools as $school)
                                        <option {{ $user->getEducationalBio()->school_name === $school ? 'selected' : '' }} value="{{ $school }}">{{ $school }}</option>
                                        @endforeach
                                    </select>

                                    <label class="mt-3" for="location">Location</label>
                                    <input value="{{ $user->getEducationalBio()->school_location }}" class="text-gray-400 border rounded-lg p-2" type="text" name="location">

                                    <label class="mt-3" for="studentid">Year Started</label>
                                    <input value="{{ $user->getEducationalBio()->batch - 4 }}" class="text-gray-400 border rounded-lg p-2" type="number" name="started">
                                </div>

                                <div class="mx-2"></div>

                                <div class="flex flex-col flex-1">
                                    <label for="course">Course</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="course" id="course">
                                        @foreach (App\Models\Course::all() as $course)
                                        <option {{ $user->getEducationalBio()->course_id === $course->id ? 'selected' : '' }} value="{{ $course->id }}">{{ $course->name }}</option>
                                        @endforeach
                                    </select>

                                    <label class="mt-3" for="degree">Degree Type</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="degree_type" id="degree">
                                        <option {{ $user->getEducationalBio()->degree_type === 'Bachelor' ? 'selected' : '' }} value="Bachelor">Bachelor</option>
                                        <option {{ $user->getEducationalBio()->degree_type === 'Masteral' ? 'selected' : '' }} value="Masteral">Masteral</option>
                                        <option {{ $user->getEducationalBio()->degree_type === 'Doctoral' ? 'selected' : '' }} value="Doctoral">Doctoral</option>
                                    </select>

                                    <label class="mt-3" for="email">Year Graduated</label>
                                    <input value="{{ $user->getEducationalBio()->batch }}" class="text-gray-400 border rounded-lg p-2" type="number" name="graduated">
                                </div>
                            </div>

                            <div class="flex mt-4 justify-end">
                                <a class="text-white mr-2 bg-blue-600 p-2 rounded-lg flex place-items-center" href="">
                                    <img class="w-4 mr-2" src="{{ asset('assets/pass_reset.svg') }}" alt="Reset">
                                    Add Education
                                </a>
                                <button class="text-white bg-blue-600 py-2 px-6 rounded-lg" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                    @elseif ($query === 'professional')
                    <form action="" method="POST">
                        <div class="flex flex-col">
                            <div class="flex">
                                @php($prof = $user->getProfessionalBio())
                                <div class="flex flex-col flex-1 max-w-[50%]">
                                    <label for="school">Employment Status</label>
                                    <select class="text-gray-400 border rounded-lg p-2" name="school" id="school">
                                        @foreach ($statuses as $status)
                                        <option {{ $prof && $prof->employement_status === $status ? 'selected' : '' }} value="{{ $status}}">{{ $status }}</option>
                                        @endforeach
                                    </select>

                                    <label class="mt-3" for="type1">Employment Type 1</label>
                                    <select class="text-gray-400 border rounded-lg p-2" type="text" name="employment_type1">
                                        <option value="Private">Private</option>
                                        <option value="Government">Government</option>
                                        <option value="NGO/INGO">NGO/INGO</option>
                                    </select>

                                    <label class="mt-3" for="type2">Employement Type 2</label>
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
                                    <select class="text-gray-400 border rounded-lg p-2" name="montly_salary" id="range">
                                        @foreach ($ranges as $range)
                                        <option value="{{ $range }}" {{ $prof && $prof->monthly_salary === $range ? 'selected' : '' }}>{{ $range }}</option">
                                        </option>
                                        @endforeach
                                    </select>

                                    <label class="mt-3" for="location">Location</label>
                                    <input value="{{ $prof ? $prof->work_location : '' }}" class="text-gray-400 border rounded-lg p-2" type="text" name="work_location">
                                </div>
                            </div>

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
                            <label class="mt-5" for="waiting">Waiting Time <span class="text-gray-400">(period to get a job after graduation)</span></label>
                            <select class="text-gray-400 border rounded-lg p-2" name="waiting_time" id="waitign">
                                @foreach ($times as $time)
                                <option value="{{ $time }}" {{ $prof && $prof->waiting_time === $time ? 'selected' : '' }}>{{ $time }}</option>
                                @endforeach
                            </select>

                            <label class="mt-5" for="waiting">Job Search Methods <span class="text-gray-400">(used to find a job)</span></label>
                            <select multiple class="text-gray-400 border rounded-lg p-2" name="waiting_time" id="waitign">
                                @foreach ($methods as $method)
                                <option value="{{ $method }}">{{ $method}}</option>
                                @endforeach
                            </select>


                            <div class="flex">
                                <div class="flex-1 flex flex-col">
                                    @php($hard = [
                                    'Technical Skills',
                                    'Engineering Skills',
                                    'Business and Finance Skills',
                                    'Marketing Skills',
                                    'Cooking Skills'
                                    ])
                                    <label class="mt-5" for="waiting">Hard Skill/s</label>
                                    <select multiple class="text-gray-400 border rounded-lg p-2" name="waiting_time" id="waitign">
                                        @foreach ($hard as $skill)
                                        <option value="{{ $skill }}">{{ $skill }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mx-2"></div>

                                <div class="flex-1 flex flex-col">
                                    @php($soft = [
                                    'Communication Skills',
                                    'Teamwork and Collaboration',
                                    'Leadership Skills',
                                    'Adaptability and Problem-Solving',
                                    'Time Management',
                                    'Work Ethic',
                                    'Interpersonal Skills'
                                    ])
                                    <label class="mt-5" for="waiting">Soft Skill/s</label>
                                    <select multiple class="text-gray-400 border rounded-lg p-2" name="waiting_time" id="waitign">
                                        @foreach ($soft as $skill)
                                        <option value="{{ $skill }}">{{ $skill }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

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