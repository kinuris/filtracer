@extends('layouts.alumni')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <div class="flex justify-between">
        <h1 class="font-medium tracking-widest text-lg">Profile Details</h1>
        <a class="text-white bg-blue-600 text-sm flex place-items-center rounded-lg px-2" href="/alumni/profile/update">Update Profile</a>
    </div>

    <div class="flex max-h-full">
        <div class="shadow rounded-lg h-fit mt-6 flex-1 min-w-80">
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
            <div class="shadow rounded-lg">
                <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
                    <p class="text-lg font-bold pb-2">Personal Information</p>
                    @php ($personal = $user->getPersonalBio())
                    <div class="flex justify-between">
                        <div class="flex-1 flex-col">
                            <p class="text-gray-400">First Name</p>
                            <p>{{ $personal->first_name }}</p>

                            <p class="text-gray-400 mt-4">Suffix</p>
                            <p>{{ is_null($personal->suffix) ? 'N/A' : $personal->suffix }}</p>

                            <p class="text-gray-400 mt-4">Gender</p>
                            <p>{{ $personal->gender }}</p>

                            <p class="text-gray-400 mt-4">Username</p>
                            <p>{{ $user->username }}</p>

                            <p class="text-gray-400 mt-4">Home Address</p>
                            <p class="mr-3">{{ $personal->permanent_address }}</p>
                        </div>

                        <div class="flex-1 flex-col">
                            <p class="text-gray-400">Middle Name</p>
                            <p>{{ $personal->middle_name }}</p>

                            <p class="text-gray-400 mt-4">Student ID</p>
                            <p>{{ $personal->student_id }}</p>

                            <p class="text-gray-400 mt-4">Date of Birth</p>
                            <p>{{ $personal->birthdate->format('M. d, Y') }}</p>

                            <p class="text-gray-400 mt-4">Phone Number</p>
                            <p>{{ $personal->phone_number }}</p>

                            <p class="text-gray-400 mt-4">Current Address</p>
                            <p class="mr-3">{{ $personal->current_address }}</p>
                        </div>

                        <div class="flex-1 flex-col">
                            <p class="text-gray-400">Last Name</p>
                            <p>{{ $personal->last_name }}</p>

                            <p class="text-gray-400 mt-4">Age</p>
                            <p>{{ $personal->getAge() }}</p>

                            <p class="text-gray-400 mt-4">Civil Status</p>
                            <p>{{ $personal->civil_status }}</p>

                            <p class="text-gray-400 mt-4">Email Address</p>
                            <p>{{ $personal->email_address }}</p>

                            <p class="text-gray-400 mt-4">Social Link/s</p>
                            <a class="text-blue-500" href="{{ $personal->social_link }}">{{ $personal->social_link }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="shadow rounded-lg mt-3">
                <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
                    <p class="text-lg font-bold pb-2">Educational Information</p>
                    @php ($educational = $user->getEducationalBio())
                    <div class="flex justify-between">
                        <div class="flex-1 flex-col">
                            <p class="text-gray-400">School</p>
                            <p>{{ $educational->school }}</p>

                            <p class="text-gray-400 mt-4">Location</p>
                            <p>{{ $educational->school_location }}</p>
                        </div>

                        <div class="flex-1 flex-col">
                            <p class="text-gray-400">Course</p>
                            <p>{{ $educational->getCourse()->name }}</p>

                            <p class="text-gray-400 mt-4">Year Started</p>
                            <p>{{ $educational->start }}</p>
                        </div>

                        <div class="flex-1 flex-col">
                            <p class="text-gray-400">Degree Type</p>
                            <p>{{ $educational->degree_type }}</p>

                            <p class="text-gray-400 mt-4">Year Ended</p>
                            <p>{{ $educational->end }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="shadow rounded-lg mt-3">
                <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
                    <p class="text-lg font-bold pb-2">Professional Information</p>
                    @php ($prof = $user->getProfessionalBio())
                    <div class="flex justify-between">
                        <div class="flex-1 flex-col">
                            <p class="text-gray-400">Employment Status</p>
                            <p>{{ $prof->employment_status }}</p>

                            <p class="text-gray-400 mt-4">Current Job Title</p>
                            <p>{{ $prof->job_title }}</p>

                            <p class="text-gray-400 mt-4">Monthly Salary Range</p>
                            <p>{{ $prof->monthly_salary }} PHP</p>
                        </div>

                        <div class="flex-1 flex-col">
                            <p class="text-gray-400">Employment Type</p>
                            <p>{{ $prof->employment_type1 }}; {{ $prof->employment_type2 }}</p>

                            <p class="text-gray-400 mt-4">Company / Employer</p>
                            <p>{{ $prof->company_name }}</p>

                            <p class="text-gray-400 mt-4">Waiting Time</p>
                            <p>{{ $prof->waiting_time }}</p>
                        </div>

                        <div class="flex-1 flex-col">
                            <p class="text-gray-400">Industry</p>
                            <p>{{ $prof->industry }}</p>

                            <p class="text-gray-400 mt-4">Location</p>
                            <p>{{ $prof->work_location }}</p>
                        </div>
                    </div>
                    @if ($prof)

                    @else
                    <p>(No professional bio)</p>
                    @endif
                </div>
            </div>

            <div class="shadow rounded-lg mt-3">
                <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
                    <p class="text-lg font-bold pb-2">Job Search Methods</p>
                    @php ($methods = $user->getProfessionalBio()->methods)
                    <div class="flex gap-2">
                        @foreach ($methods as $method)
                        <p class="p-2 bg-gray-200 rounded">{{ $method->method}}</p>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex w-full gap-3 mt-3 mb-16">
                <div class="shadow rounded-lg flex-1 w-1/2">
                    <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg h-full">
                        <p class="text-lg font-bold pb-2">Soft Skills</p>
                        @php ($methods = $user->getProfessionalBio()->softSkills)
                        <div class="flex gap-2 flex-wrap">
                            @foreach ($methods as $method)
                            <p class="p-2 bg-gray-200 rounded">{{ $method->skill }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="shadow rounded-lg flex-1 w-1/2">
                    <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg">
                        <p class="text-lg font-bold pb-2">Hard Skills</p>
                        @php ($methods = $user->getProfessionalBio()->hardSkills)
                        <div class="flex gap-2 flex-wrap">
                            @foreach ($methods as $method)
                            <p class="p-2 bg-gray-200 rounded">{{ $method->skill }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection