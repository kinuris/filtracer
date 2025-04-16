@extends('layouts.admin')

@section('title', 'Profile Report' . ' - ' . $alumni->name)

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col overflow-auto max-h-[calc(100%-4rem)]">
    <div class="flex justify-between">
        <h1 class="font-medium tracking-widest text-lg">Profile Report</h1>
        @php ($tw = Vite::asset('resources/css/app.css'))
        <button class="p-2 bg-blue-600 text-white rounded" onclick="printJS({ printable: 'printableProfile', type: 'html', css: '{{ $tw }}', style: '#printableProfile { height: 2000px, width: 1000px }' })">Print</button>
    </div>

    <div class="flex">
        <div class="shadow rounded-lg h-fit mb-4 mt-6 flex-1 min-w-80">
            <div id="printableProfile" class="bg-white py-4 flex flex-col px-6 border-b rounded-lg text-xs">
                <div class="flex place-items-center">
                    <img class="w-24 h-24 object-cover rounded-full shadow-lg" src="{{ $alumni->image() }}" alt="">
                    <div class="flex flex-col ml-5">
                        @php($educ = $alumni->educationalBios()->first())
                        <p class="text-lg font-bold">{{ $alumni->personalBio->getFullname() }}</p>
                        <p class="font-light text-sm">{{ $educ->course->name }}</p>
                        <p class="font-light text-sm">Alumni Batch {{ $educ->end }}</p>
                    </div>
                    <div class="flex-1"></div>
                    <div class="flex flex-col text-gray-700 text-right self-start">
                        <p class="text-xl font-light">#{{ $alumni->id }}</p>
                        <p>{{ date_create()->format('M. d, Y') }}</p>
                    </div>
                </div>

                <p class="mt-6 font-bold tracking-wider text-lg text-blue-600">Personal Information</p>
                <div class="flex border-t border-b font-semibold py-1.5 mt-4 border-gray-400">
                    <div class="flex-1 text-center">First Name</div>
                    <div class="flex-1 text-center">Middle Name</div>
                    <div class="flex-1 text-center">Last Name</div>
                </div>
                <div class="flex mt-2 font-gray-600">
                    <div class="flex-1 text-center text-sm">{{ $alumni->personalBio->first_name }}</div>
                    <div class="flex-1 text-center text-sm">{{ $alumni->personalBio->middle_name }}</div>
                    <div class="flex-1 text-center text-sm">{{ $alumni->personalBio->last_name }}</div>
                </div>

                <div class="flex mt-4 border-b pb-4">
                    <div class="flex-1 flex">
                        @php($personal = $alumni->personalBio)
                        <div class="flex flex-col mr-8 font-semibold gap-3">
                            <p>Student ID</p>
                            <p>Age</p>
                            <p>Gender</p>
                            <p>Date of Birth</p>
                            <p>Civil Status</p>
                        </div>
                        <div class="flex flex-col text-gray-700 gap-3">
                            <p>{{ $personal->student_id }}</p>
                            <p>{{ $personal->getAge() }}</p>
                            <p>{{ $personal->gender }}</p>
                            <p>{{ $personal->birthdate->format('M. d, Y') }}</p>
                            <p>{{ $personal->civil_status }}</p>
                        </div>
                    </div>
                    <div class="flex-[2] flex">
                        <div class="flex flex-1 flex-col mr-8 font-semibold gap-3">
                            <p class="line-clamp-1">Permanent Address</p>
                            <p class="line-clamp-1">Current Address</p>
                            <p class="line-clamp-1">Email</p>
                            <p class="line-clamp-1">Phone Number</p>
                            <p class="line-clamp-1">Social Link</p>
                        </div>
                        <div class="flex flex-[3] flex-col text-gray-700 gap-3">
                            <p>{{ $personal->permanent_address }}</p>
                            <p>{{ $personal->current_address }}</p>
                            <p>{{ $personal->email_address }}</p>
                            <p>{{ $personal->phone_number }}</p>
                            <p>{{ $personal->social_link }}</p>
                        </div>
                    </div>
                </div>

                <p class="mt-6 font-bold tracking-wider text-lg text-blue-600">Educational Information</p>
                <div class="grid grid-cols-2 gap-8 mt-4 border-b pb-4">
                    @foreach($alumni->educationalBios()->get() as $educ)
                    <div class="flex p-2 @if($loop->first) border-l-4 border-blue-300 @endif @if($loop->last) border-r-4 border-green-300 @endif">
                        <div class="flex flex-col mr-8 font-semibold gap-3">
                            <p>Degree Type</p>
                            <p>Course</p>
                            <p>Year Range</p>
                            <p>School</p>
                            <p>Location</p>
                        </div>
                        <div class="flex flex-col text-gray-700 gap-3">
                            <p>{{ $educ->degree_type }}</p>
                            <p>{{ $educ->course->name }}</p>
                            <p>{{ $educ->start }} - {{ $educ->end }}</p>
                            <p class="line-clamp-1">{{ $educ->school }}</p>
                            <p class="line-clamp-1">{{ $educ->school_location }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <p class="mt-6 font-bold tracking-wider text-lg text-blue-600">Professional Information</p>
                @php($prof = $alumni->getProfessionalBio())
                <div class="flex mt-4 border-b pb-4">
                    <div class="flex mr-16">
                        <div class="flex flex-col mr-8 font-semibold gap-3">
                            <p>Current Employment Status</p>
                            <p>Employment Time</p>
                            <p>Industry</p>
                            <p>Current Job Title</p>
                        </div>
                        <div class="flex flex-col text-gray-700 gap-3">
                            <p>{{ $prof->employment_status }}</p>
                            <p>{{ $prof->employment_type1 }}; {{ $prof->employment_type2 }}</p>
                            <p>{{ $prof->industry }}</p>
                            <p class="line-clamp-1">{{ $prof->job_title }}</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex flex-col mr-8 font-semibold gap-3">
                            <p>Current Company / Employer</p>
                            <p>Montly Salary Range</p>
                            <p>Location</p>
                        </div>
                        <div class="flex flex-col text-gray-700 gap-3">
                            <p>{{ $prof->company_name }}</p>
                            <p>{{ $prof->monthly_salary }}</p>
                            <p>{{ $prof->work_location }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex mt-4 mb-8">
                    <div class="flex flex-col gap-4 mr-12 flex-wrap">
                        <div class="flex gap-20">
                            <p class="font-semibold">Waiting Time</p>
                            <p>{{ $prof->waiting_time }}</p>
                        </div>

                        <div class="flex gap-8">
                            <p class="font-semibold">Job Search Method/s</p>
                            <ul>
                                @foreach($prof->methods()->get() as $method)
                                <li class="list-disc">{{ $method->method }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="flex gap-[5.6rem]">
                            <p class="font-semibold">Hard Skill/s</p>
                            <ul>
                                @foreach($prof->hardSkills()->get() as $skill)
                                <li class="list-disc">{{ $skill->skill }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 mr-8 flex-wrap">
                        <div class="flex gap-36">
                            <p class="font-semibold">Soft Skill/s</p>
                            <ul>
                                @foreach($prof->softSkills()->get() as $skill)
                                <li class="list-disc">{{ $skill->skill }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="flex gap-8">
                            <p class="font-semibold">Certifications and Licenses</p>
                            <ul>
                                
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection