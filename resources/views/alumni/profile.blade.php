@extends('layouts.alumni')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <div class="flex justify-between">
        <h1 class="font-medium tracking-widest text-lg">Profile Details</h1>
        <a class="text-white bg-blue-600 flex place-items-center rounded-lg p-2" href="/alumni/profile/update">Update Profile</a>
    </div>

    <div class="flex max-h-full">
        <div class="shadow rounded-lg h-fit mt-6 flex-1 min-w-80 overflow-auto">
            <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
                <img class="w-32 h-32 rounded-full object-cover shadow-md mr-8" src="{{ $user->image() }}" alt="Profile">
                <p class="text-lg font-bold my-6">{{ $user->name }}</p>

                <div class="flex place-items-center">
                    <img class="h-4 mr-3" src="{{ asset('assets/alumni_job.svg') }}" alt="Job">
                    <p class="text-gray-400 text-sm">{{ $user->getProfessionalBio()->employment_status }}</p>
                </div>
                <div class="flex mt-1 ml-[1px] place-items-center">
                    <img class="h-4 mr-3" src="{{ asset('assets/alumni_location.svg') }}" alt="Job">
                    <p class="text-gray-400 text-sm">{{ $user->getProfessionalBio()->work_location }}</p>
                </div>

                <p class="text-gray-400 mt-4">Email Address</p>
                <p>{{ $user->getPersonalBio()->email_address }}</p>

                <p class="text-gray-400 mt-4">Degree</p>
                <p>{{ $user->getEducationalBio()->degree_type }} in {{ $user->getEducationalBio()->getCourse()->name }}</p>

                <p class="text-gray-400 mt-4">Alumni Batch</p>
                @php($bio = $user->getEducationalBio())
                <p>{{ $bio->end }}</p>

                <p class="text-gray-400 mt-4">Date Joined</p>
                <p>{{ $user->created_at->format('M. d, Y') }}</p>
            </div>
        </div>

        <div class="mx-2"></div>

        <div class="flex-[3] flex flex-col mt-6 max-h-full overflow-auto">
            <!-- Personal Information Card -->
            <div class="shadow rounded-lg">
                <div class="bg-white py-5 flex flex-col px-6 border-b rounded-lg">
                    <p class="text-blue-600 text-lg font-bold pb-3 border-b border-gray-100">Personal Information</p>
                    @php ($personal = $user->getPersonalBio())
                    <div class="flex justify-between mt-3">
                        <div class="flex-1 flex-col pr-4">
                            <p class="text-gray-400 text-sm">First Name</p>
                            <p class="font-medium">{{ $personal->first_name }}</p>

                            <p class="text-gray-400 text-sm mt-4">Suffix</p>
                            <p class="font-medium">{{ is_null($personal->suffix) ? 'N/A' : $personal->suffix }}</p>

                            <p class="text-gray-400 text-sm mt-4">Gender</p>
                            <p class="font-medium">{{ $personal->gender }}</p>

                            <p class="text-gray-400 text-sm mt-4">Username</p>
                            <p class="font-medium">{{ $user->username }}</p>

                            <p class="text-gray-400 text-sm mt-4">Home Address</p>
                            <p class="font-medium mr-3">{{ $personal->permanent_address }}</p>
                        </div>

                        <div class="flex-1 flex-col pr-4">
                            <p class="text-gray-400 text-sm">Middle Name</p>
                            <p class="font-medium">{{ $personal->middle_name }}</p>

                            <p class="text-gray-400 text-sm mt-4">Student ID</p>
                            <p class="font-medium">{{ $personal->student_id }}</p>

                            <p class="text-gray-400 text-sm mt-4">Date of Birth</p>
                            <p class="font-medium">{{ $personal->birthdate->format('M. d, Y') }}</p>

                            <p class="text-gray-400 text-sm mt-4">Phone Number</p>
                            <p class="font-medium">{{ $personal->phone_number }}</p>

                            <p class="text-gray-400 text-sm mt-4">Current Address</p>
                            <p class="font-medium mr-3">{{ $personal->current_address }}</p>
                        </div>

                        <div class="flex-1 flex-col">
                            <p class="text-gray-400 text-sm">Last Name</p>
                            <p class="font-medium">{{ $personal->last_name }}</p>

                            <p class="text-gray-400 text-sm mt-4">Age</p>
                            <p class="font-medium">{{ $personal->getAge() }}</p>

                            <p class="text-gray-400 text-sm mt-4">Civil Status</p>
                            <p class="font-medium">{{ $personal->civil_status }}</p>

                            <p class="text-gray-400 text-sm mt-4">Email Address</p>
                            <p class="font-medium">{{ $personal->email_address }}</p>

                            <p class="text-gray-400 text-sm mt-4">Social Link/s</p>
                            <a class="text-blue-600 hover:underline" href="{{ $personal->social_link }}">{{ $personal->social_link }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Educational Information Card -->
            <div class="shadow rounded-lg mt-4">
                <div class="bg-white py-5 flex flex-col px-6 border-b rounded-lg">
                    <p class="text-blue-600 text-lg font-bold pb-3 border-b border-gray-100">Educational Information</p>
                    @php ($educationals = $user->educationalBios)
                    @foreach ($educationals as $educational)
                    <div class="flex justify-between mt-3">
                        <div class="flex-1 flex-col pr-4">
                            <p class="text-gray-400 text-sm">School</p>
                            <p class="font-medium">{{ $educational->school }}</p>

                            <p class="text-gray-400 text-sm mt-4">Location</p>
                            <p class="font-medium">{{ $educational->school_location }}</p>
                        </div>

                        <div class="flex-1 flex-col pr-4">
                            <p class="text-gray-400 text-sm">Course</p>
                            <p class="font-medium">{{ $educational->getCourse()->name }}</p>

                            <p class="text-gray-400 text-sm mt-4">Year Started</p>
                            <p class="font-medium">{{ $educational->start }}</p>
                        </div>

                        <div class="flex-1 flex-col">
                            <p class="text-gray-400 text-sm">Degree Type</p>
                            <p class="font-medium">{{ $educational->degree_type }}</p>

                            <p class="text-gray-400 text-sm mt-4">Year Ended</p>
                            <p class="font-medium">{{ $educational->end }}</p>
                        </div>
                    </div>
                    <hr class="mt-3">
                    @endforeach
                </div>
            </div>

            <!-- Professional Information Card -->
            <!-- Professional Information Card -->
            @php ($professionals = $user->professionalBios)
            @if ($professionals->isNotEmpty())
            @foreach ($professionals as $index => $professional)
            <div class="shadow rounded-lg {{ $index > 0 ? 'mt-4' : '' }}">
                <div class="bg-white py-5 flex flex-col px-6 border-b rounded-lg">
                    <p class="text-blue-600 text-lg font-bold pb-3 border-b border-gray-100">Professional Information #{{ $index + 1 }}</p>
                    <div class="flex justify-between mt-3">
                        <div class="flex-1 flex-col pr-4">
                            <p class="text-gray-400 text-sm">Employment Status</p>
                            <p class="font-medium">{{ $professional->employment_status }}</p>

                            <p class="text-gray-400 text-sm mt-4">Current Job Title</p>
                            <p class="font-medium">{{ $professional->job_title }}</p>

                            <p class="text-gray-400 text-sm mt-4">Monthly Salary Range</p>
                            <p class="font-medium">{{ $professional->monthly_salary }} PHP</p>
                        </div>

                        <div class="flex-1 flex-col pr-4">
                            <p class="text-gray-400 text-sm">Employment Type</p>
                            <p class="font-medium">{{ $professional->employment_type1 }}{{ $professional->employment_type2 ? '; ' . $professional->employment_type2 : '' }}</p>

                            <p class="text-gray-400 text-sm mt-4">Company / Employer</p>
                            <p class="font-medium">{{ $professional->company_name }}</p>

                            <p class="text-gray-400 text-sm mt-4">Waiting Time</p>
                            <p class="font-medium">{{ $professional->waiting_time }}</p>
                        </div>

                        <div class="flex-1 flex-col">
                            <p class="text-gray-400 text-sm">Industry</p>
                            <p class="font-medium">{{ $professional->industry }}</p>

                            <p class="text-gray-400 text-sm mt-4">Location</p>
                            <p class="font-medium">{{ $professional->work_location }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Search Methods Card -->
            @if ($professional->methods->isNotEmpty())
            <div class="shadow rounded-lg mt-4">
                <div class="bg-white py-5 flex flex-col px-6 border-b rounded-lg">
                    <p class="text-blue-600 text-lg font-bold pb-3 border-b border-gray-100">Job Search Methods (Record #{{ $index + 1 }})</p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach ($professional->methods as $method)
                        <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-full text-sm">{{ $method->method }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Skills Cards -->
            <div class="flex w-full gap-4 mt-4 {{ $loop->last ? 'mb-16' : '' }}">
                @if ($professional->softSkills->isNotEmpty())
                <div class="shadow rounded-lg flex-1">
                    <div class="bg-white py-5 flex flex-col px-6 border-b rounded-lg h-full">
                        <p class="text-blue-600 text-lg font-bold pb-3 border-b border-gray-100">Soft Skills (Record #{{ $index + 1 }})</p>
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach ($professional->softSkills as $skill)
                            <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-full text-sm">{{ $skill->skill }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @if ($professional->hardSkills->isNotEmpty())
                <div class="shadow rounded-lg flex-1">
                    <div class="bg-white py-5 flex flex-col px-6 border-b rounded-lg h-full">
                        <p class="text-blue-600 text-lg font-bold pb-3 border-b border-gray-100">Hard Skills (Record #{{ $index + 1 }})</p>
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach ($professional->hardSkills as $skill)
                            <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-full text-sm">{{ $skill->skill }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @if (!$loop->last)
            <hr class="my-4 border-gray-300">
            @endif
            @endforeach
            @else
            <div class="shadow rounded-lg mt-4">
                <div class="bg-white py-5 flex flex-col px-6 border-b rounded-lg">
                    <p class="text-blue-600 text-lg font-bold pb-3 border-b border-gray-100">Professional Information</p>
                    <p class="mt-3">(No professional bio records found)</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection