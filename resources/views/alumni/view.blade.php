@extends('layouts.admin')

@section('title', 'Profile Details')

@section('content')
@php($query = request()->query('type') ?? 'personal')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg">Profile Details</h1>
    <p class="text-gray-400 text-xs mb-2">Department / {{ $dept->name }} / <span class="text-blue-500">{{ $user->name }}</span></p>

    <div class="shadow rounded-lg mt-6">
        <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
            <img class="w-32 h-32 rounded-full object-cover shadow-md mr-8" src="{{ $user->image() }}" alt="Profile">
            <div class="flex flex-col">
                <div class="flex place-items-center">
                    <p class="text-lg">{{ $user->name }}</p>
                    <a href="/admin/chat?initiate={{ $user->id }}">
                        <img class="w-5 ml-3" src="{{ asset('assets/chat_gray.svg') }}" alt="Chat">
                    </a>
                </div>
                <p class="text-gray-400 text-sm">{{ $user->getEducationalBio()->getCourse()->name }}</p>
            </div>

            <div class="flex-1"></div>

            <a class="rounded-lg p-2 px-3 bg-blue-600 text-white" href="/profile/report/{{ $user->id }}">Generate Report</a>
            <a class="rounded-lg p-2 px-3 bg-blue-600 text-white mx-3" href="">Manage Account</a>
            <a class="rounded-lg p-2 px-3 bg-blue-600 text-white" href="">Log History</a>
        </div>
    </div>

    <div class="shadow rounded-lg mt-4 box-border h-full max-h-full overflow-auto">
        <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg min-h-full">
            <div class="flex mb-4">
                <a class="text-gray-400 font-semibold @if($query === 'personal') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=personal">Personal Info</a>
                <a class="text-gray-400 font-semibold mx-4 @if($query === 'educational') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=educational">Educational Info</a>
                <a class="text-gray-400 font-semibold @if($query === 'professional') pb-1 border-b-2 border-blue-500 !text-blue-500 @endif" href="?type=professional">Professional Info</a>
            </div>
            @if ($query === 'personal' && $user->getPersonalBio() !== null)
            @php ($personal = $user->getPersonalBio())
            <div class="flex flex-col h-full">
                <div class="flex">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">First Name</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->first_name }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Civil Status</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->civil_status }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Middle Name</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->middle_name ?? '(None)' }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700 line-clamp-1" title="Permanent Address">Permanent Addr.</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $personal->permanent_address }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Last Name</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->last_name }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700 line-clamp-1" title="Current Address">Current Addr.</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $personal->current_address }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Student ID</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->student_id }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700 line-clamp-1" title="Current Address">Email</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $personal->email_address }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Age</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->getAge() }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700 line-clamp-1" title="Current Address">Phone Number</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $personal->phone_number }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Gender</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->gender }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700 line-clamp-1" title="Current Address">Social Link</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto"><a class="underline" href="{{ $personal->social_link }}">{{ $personal->social_link }}</a></p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Date of Birth</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->birthdate }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                    </div>
                </div>
            </div>

            @elseif ($query === 'personal' && $user->getPersonalBio() === null)
            <div class="flex flex-col justify-center flex-1">
                <h1 class="text-center text-3xl text-gray-400">No Personal Info</h1>
            </div>
            @endif

            @if ($query === 'educational' && $user->getEducationalBio() !== null)
            @php ($records = $user->educationalBios)
            <div class="flex flex-col h-full">
                @foreach($records as $educ)
                <div class="flex">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">School</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $educ->school }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Course</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $educ->getCourse()->name }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Location</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $educ->school_location }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Start Year</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $educ->start }}</p>
                    </div>
                </div>

                <div class="flex mt-2 border-b pb-3">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Degree Type</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $educ->degree_type }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">End Year</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $educ->end }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @elseif ($query === 'educational' && $user->getEducationalBio() === null)
            <div class="flex flex-col justify-center flex-1">
                <h1 class="text-center text-3xl text-gray-400">No Educational Info</h1>
            </div>
            @endif

            @if ($query === 'professional' && $user->getProfessionalBio() !== null)
            @php($prof = $user->getProfessionalBio())
            <div class="flex flex-col h-full">
                <div class="flex">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Employment Status</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $prof->employment_status }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Current Job Title</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $prof->job_title }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Employment Type 1</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $prof->employment_type1 }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Company / Employer</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $prof->company_name }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Employment Type 2</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $prof->employment_type2 }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Monthly Salary Range</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $prof->monthly_salary }} PHP</p>
                    </div>
                </div>

                <div class="flex mt-2 border-b pb-6 mb-4">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Industry</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $prof->industry }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Location</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $prof->work_location }} PHP</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Waiting Time</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-auto">{{ $prof->waiting_time }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Certification and Licenses</p>
                        <p class="flex-[2]"></p>
                    </div>
                </div>

                <div class="flex mt-6">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Job Search Method(s)</p>
                        <div class="flex-[2]">
                            <ul class="list-disc pl-6">
                                @php($hardSkills = $prof->methods)
                                @foreach ($hardSkills as $skill)
                                <li class="text-gray-400 text-lg">{{ $skill->method }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Attached File(s)</p>
                        <p class="flex flex-col flex-[2]">
                            @foreach ($prof->attachments as $attachment)
                            <a class="text-sm underline text-blue-400" href="{{ asset('storage/professional/attachments/' . $attachment->link) }}" target="_blank">{{ $attachment->name }}</a>
                            @endforeach
                        </p>
                    </div>
                </div>

                <div class="flex mt-6">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Hard Skill(s)</p>
                        <div class="flex-[2]">
                            <ul class="list-disc pl-6">
                                @php($hardSkills = $prof->hardSkills)
                                @foreach ($hardSkills as $skill)
                                <li class="text-gray-400 text-lg">{{ $skill->skill }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Link Posts</p>
                        <p class="flex-[2]"></p>
                    </div>
                </div>

                <div class="flex mt-6">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Soft Skill(s)</p>
                        <div class="flex-[2]">
                            <ul class="list-disc pl-6">
                                @php($softSkills = $prof->softSkills)
                                @foreach ($softSkills as $skill)
                                <li class="text-gray-400 text-lg">{{ $skill->skill }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700"></p>
                        <p class="flex-[2]"></p>
                    </div>
                </div>
            </div>
            @elseif ($query === 'professional' && $user->getProfessionalBio() === null)
            <div class="flex flex-col justify-center flex-1">
                <h1 class="text-center text-3xl text-gray-400">No Professional Info</h1>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection