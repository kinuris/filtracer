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
                <p>S.Y. {{ $bio->start }} - {{ $bio->end }}</p>

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
                    @php ($educational = $user->getEducationalBio())
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
                </div>
            </div>

            <!-- Professional Information Card -->
            <div class="shadow rounded-lg mt-4">
                <div class="bg-white py-5 flex flex-col px-6 border-b rounded-lg">
                    <p class="text-blue-600 text-lg font-bold pb-3 border-b border-gray-100">Professional Information</p>
                    @php ($prof = $user->getProfessionalBio())
                    <div class="flex justify-between mt-3">
                        <div class="flex-1 flex-col pr-4">
                            <p class="text-gray-400 text-sm">Employment Status</p>
                            <p class="font-medium">{{ $prof->employment_status }}</p>

                            <p class="text-gray-400 text-sm mt-4">Current Job Title</p>
                            <p class="font-medium">{{ $prof->job_title }}</p>

                            <p class="text-gray-400 text-sm mt-4">Monthly Salary Range</p>
                            <p class="font-medium">{{ $prof->monthly_salary }} PHP</p>
                        </div>

                        <div class="flex-1 flex-col pr-4">
                            <p class="text-gray-400 text-sm">Employment Type</p>
                            <p class="font-medium">{{ $prof->employment_type1 }}; {{ $prof->employment_type2 }}</p>

                            <p class="text-gray-400 text-sm mt-4">Company / Employer</p>
                            <p class="font-medium">{{ $prof->company_name }}</p>

                            <p class="text-gray-400 text-sm mt-4">Waiting Time</p>
                            <p class="font-medium">{{ $prof->waiting_time }}</p>
                        </div>

                        <div class="flex-1 flex-col">
                            <p class="text-gray-400 text-sm">Industry</p>
                            <p class="font-medium">{{ $prof->industry }}</p>

                            <p class="text-gray-400 text-sm mt-4">Location</p>
                            <p class="font-medium">{{ $prof->work_location }}</p>
                        </div>
                    </div>
                    @if ($prof)
                    @else
                    <p>(No professional bio)</p>
                    @endif
                </div>
            </div>

            <!-- Job Search Methods Card -->
            <div class="shadow rounded-lg mt-4">
                <div class="bg-white py-5 flex flex-col px-6 border-b rounded-lg">
                    <p class="text-blue-600 text-lg font-bold pb-3 border-b border-gray-100">Job Search Methods</p>
                    @php ($methods = $user->getProfessionalBio()->methods)
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach ($methods as $method)
                        <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-full text-sm">{{ $method->method }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Skills Cards -->
            <div class="flex w-full gap-4 mt-4 mb-16">
                <div class="shadow rounded-lg flex-1">
                    <div class="bg-white py-5 flex flex-col px-6 border-b rounded-lg h-full">
                        <p class="text-blue-600 text-lg font-bold pb-3 border-b border-gray-100">Soft Skills</p>
                        @php ($methods = $user->getProfessionalBio()->softSkills)
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach ($methods as $method)
                            <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-full text-sm">{{ $method->skill }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="shadow rounded-lg flex-1">
                    <div class="bg-white py-5 flex flex-col px-6 border-b rounded-lg h-full">
                        <p class="text-blue-600 text-lg font-bold pb-3 border-b border-gray-100">Hard Skills</p>
                        @php ($methods = $user->getProfessionalBio()->hardSkills)
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach ($methods as $method)
                            <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-full text-sm">{{ $method->skill }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection