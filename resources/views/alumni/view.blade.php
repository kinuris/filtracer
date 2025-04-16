@extends('layouts.admin')

@section('title', 'Profile Details')

@section('content')
@if($user->isCompSet())
@include('components.manage-account-modal')
@endif
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
                    <!-- <a href="/admin/chat?initiate={{ $user->id }}">
                        <img class="w-5 ml-3" src="{{ asset('assets/chat_gray.svg') }}" alt="Chat">
                    </a> -->
                </div>
                @if ($user->isCompSet())
                <p class="text-gray-400 text-sm">{{ $user->getEducationalBio()->getCourse()->name }}</p>
                @else
                <div class="flex gap-3">
                    <p class="text-gray-400 text-sm">Incomplete Setup (Cannot Verify/Unverify)</p>
                    <!-- <button type="button" onclick="document.getElementById('deleteModal{{ $user->id }}').classList.remove('hidden')" class="border-0 bg-transparent cursor-pointer p-0">
                        <img src="{{ asset('assets/trash.svg') }}" alt="Trash">
                    </button> -->

                    <!-- Delete Confirmation Modal -->
                    <div id="deleteModal{{ $user->id }}" class="hidden fixed inset-0 z-50">
                        <div class="absolute inset-0 bg-black opacity-60 transition-opacity"></div>
                        <div class="absolute inset-0 flex items-center justify-center p-4">
                            <div class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all">
                                <div class="border-b px-6 py-4 flex items-center">
                                    <div class="bg-red-100 p-2 rounded-full mr-3">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900">Confirm Deletion</h3>
                                </div>
                                <div class="px-6 py-4">
                                    <p class="text-gray-600">Are you sure you want to delete this alumni record? This action cannot be undone.</p>
                                </div>
                                <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                                    <button onclick="document.getElementById('deleteModal{{ $user->id }}').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm">
                                        Cancel
                                    </button>
                                    <a href="/user/delete/{{ $user->id }}" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm">
                                        Delete Record
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="flex-1"></div>

            @if($user->isCompSet())<a class="rounded-lg p-2 px-3 bg-blue-600 text-white" href="/profile/report/{{ $user->id }}">Generate Report</a>@endif
            @if (Auth::user()->admin()->is_super)
            <!-- <button class="rounded-lg p-2 px-3 bg-blue-600 text-white mx-3" id="openManageAccountModal">Manage Account</button> -->
            @else
            <div class="mx-1.5"></div>
            @endif

            @if ($user->isCompSet())
            <a class="rounded-lg p-2 ml-2 px-3 bg-blue-600 text-white" href="/admin/chat?initiate={{ $user->id }}&override=1">Message</a>
            @endif
        </div>
    </div>

    <div class="shadow rounded-lg mt-4 box-border h-full max-h-full overflow-auto">
        <div class="bg-white py-5 flex flex-col px-7 border-b rounded-lg min-h-full shadow-sm">
            <div class="flex mb-5 border-b">
                <a class="text-gray-700 font-semibold px-3 py-2 transition-colors duration-200 @if($query === 'personal') pb-2 border-b-2 border-blue-600 !text-blue-600 @endif" href="?type=personal">Basic Info</a>
                <a class="text-gray-700 font-semibold px-3 py-2 transition-colors duration-200 @if($query === 'educational') pb-2 border-b-2 border-blue-600 !text-blue-600 @endif" href="?type=educational">Educational Info</a>
                <a class="text-gray-700 font-semibold px-3 py-2 transition-colors duration-200 @if($query === 'professional') pb-2 border-b-2 border-blue-600 !text-blue-600 @endif" href="?type=professional">Professional Info</a>
            </div>
            @if ($query === 'personal' && ($user->getPersonalBio() !== null || $user->partialPersonal !== null))
            @php ($personal = $user->getPersonalBio() ?? $user->partialPersonal)
            <div class="grid grid-cols-4 gap-4 h-full">
                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">First Name</p>
                    <p class="border p-2 rounded-md {{ empty($personal->first_name) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($personal->first_name) ? $personal->first_name : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Middle Name</p>
                    <p class="border p-2 rounded-md {{ empty($personal->middle_name) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($personal->middle_name) ? $personal->middle_name : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Last Name</p>
                    <p class="border p-2 rounded-md {{ empty($personal->last_name) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($personal->last_name) ? $personal->last_name : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Suffix</p>
                    <p class="border p-2 rounded-md {{ empty($personal->suffix) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($personal->suffix) ? $personal->suffix : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Age</p>
                    <p class="border p-2 rounded-md {{ ($user->getPersonalBio() === null || empty($personal->getAge())) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ ($user->getPersonalBio() !== null && !empty($personal->getAge())) ? $personal->getAge() : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Student ID</p>
                    <p class="border p-2 rounded-md {{ empty($personal->student_id) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($personal->student_id) ? $personal->student_id : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Username</p>
                    <p class="border p-2 rounded-md {{ empty($user->username) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($user->username) ? $user->username : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Gender</p>
                    <p class="border p-2 rounded-md {{ empty($personal->gender) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($personal->gender) ? $personal->gender : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Date of Birth</p>
                    <p class="border p-2 rounded-md {{ empty($personal->birthdate) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($personal->birthdate) ? $personal->birthdate : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Civil Status</p>
                    <p class="border p-2 rounded-md {{ empty($personal->civil_status) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($personal->civil_status) ? $personal->civil_status : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Phone Number</p>
                    <p class="border p-2 rounded-md {{ empty($personal->phone_number) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner line-clamp-1 overflow-auto">{{ !empty($personal->phone_number) ? $personal->phone_number : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Email</p>
                    <p class="border p-2 rounded-md {{ empty($personal->email_address) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner line-clamp-1 overflow-auto">{{ !empty($personal->email_address) ? $personal->email_address : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit col-span-2">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Home Address</p>
                    <p class="border p-2 rounded-md {{ empty($personal->permanent_address) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner line-clamp-1 overflow-auto">{{ !empty($personal->permanent_address) ? $personal->permanent_address : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit col-span-2">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Current Address</p>
                    <p class="border p-2 rounded-md {{ empty($personal->current_address) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner line-clamp-1 overflow-auto">{{ !empty($personal->current_address) ? $personal->current_address : 'None' }}</p>
                </div>

                <div class="flex flex-col h-fit col-span-4">
                    <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Social Link</p>
                    <p class="border p-2 rounded-md {{ empty($personal->social_link) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner line-clamp-1 overflow-auto">
                        @if(!empty($personal->social_link))
                        <a class="underline text-blue-600 hover:text-blue-800" href="{{ $personal->social_link }}">{{ $personal->social_link }}</a>
                        @else
                        None
                        @endif
                    </p>
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
            <div class="mb-6 border-b pb-6 last:border-b-0 last:pb-0 last:mb-0">
                <div class="flex mb-3 justify-between items-center">
                    <h4 class="text-sm font-medium text-gray-700">{{ $educ->school }}</h4>
                    <span class="text-xs text-blue-600 bg-blue-50 rounded-full px-2 py-0.5 border border-blue-100">{{ $educ->degree_type }}</span>
                </div>

                <div class="flex">
                    <div class="flex flex-col justify-between flex-1 mx-2 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Course</p>
                        <p class="border p-2 rounded-md text-gray-800 bg-gray-50 shadow-inner">{{ $educ->getCourse()->name }}</p>
                    </div>
                    <div class="flex flex-col justify-between flex-1 mx-2 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Location</p>
                        <p class="border p-2 rounded-md text-gray-800 bg-gray-50 shadow-inner">{{ $educ->school_location }}</p>
                    </div>
                </div>

                <div class="flex mt-3">
                    <div class="flex flex-col justify-between flex-1 mx-2 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Start Year</p>
                        <p class="border p-2 rounded-md text-gray-800 bg-gray-50 shadow-inner">{{ $educ->start }}</p>
                    </div>
                    <div class="flex flex-col justify-between flex-1 mx-2 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">End Year</p>
                        <p class="border p-2 rounded-md text-gray-800 bg-gray-50 shadow-inner">{{ is_null($educ->end) ? 'Present' : $educ->end }}</p>
                    </div>
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
        @php ($records = $user->professionalBios)
        <div class="flex flex-col h-full">
            @foreach($records as $prof)
            <div class="mb-6 border-b pb-6 last:border-b-0 last:pb-0 last:mb-0">
                <div class="flex gap-4">
                    <div class="flex flex-col justify-between flex-1 mx-1 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Employment Status</p>
                        <p class="border p-2 rounded-md {{ empty($prof->employment_status) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($prof->employment_status) ? $prof->employment_status : 'None' }}</p>
                    </div>
                    <div class="flex flex-col justify-between flex-1 mx-1 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Current Job Title</p>
                        <p class="border p-2 rounded-md {{ empty($prof->job_title) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($prof->job_title) ? $prof->job_title : 'None' }}</p>
                    </div>
                </div>

                <div class="flex mt-4 gap-4">
                    <div class="flex flex-col justify-between flex-1 mx-1 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Employment Type 1</p>
                        <p class="border p-2 rounded-md {{ empty($prof->employment_type1) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($prof->employment_type1) ? $prof->employment_type1 : 'None' }}</p>
                    </div>
                    <div class="flex flex-col justify-between flex-1 mx-1 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Company / Employer</p>
                        <p class="border p-2 rounded-md {{ empty($prof->company_name) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($prof->company_name) ? $prof->company_name : 'None' }}</p>
                    </div>
                </div>

                <div class="flex mt-4 gap-4">
                    <div class="flex flex-col justify-between flex-1 mx-1 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Employment Type 2</p>
                        <p class="border p-2 rounded-md {{ empty($prof->employment_type2) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($prof->employment_type2) ? $prof->employment_type2 : 'None' }}</p>
                    </div>
                    <div class="flex flex-col justify-between flex-1 mx-1 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Monthly Salary Range</p>
                        <p class="border p-2 rounded-md {{ empty($prof->monthly_salary) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($prof->monthly_salary) ? $prof->monthly_salary . ' PHP' : 'None' }}</p>
                    </div>
                </div>

                <div class="flex mt-4 gap-4">
                    <div class="flex flex-col justify-between flex-1 mx-1 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Industry</p>
                        <p class="border p-2 rounded-md {{ empty($prof->industry) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($prof->industry) ? $prof->industry : 'None' }}</p>
                    </div>
                    <div class="flex flex-col justify-between flex-1 mx-1 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Location</p>
                        <p class="border p-2 rounded-md {{ empty($prof->work_location) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($prof->work_location) ? $prof->work_location : 'None' }}</p>
                    </div>
                </div>

                <div class="flex mt-4 gap-4">
                    <div class="flex flex-col justify-between flex-1 mx-1 h-fit">
                        <p class="text-xs font-semibold mb-1.5 tracking-wider text-gray-700">Waiting Time</p>
                        <p class="border p-2 rounded-md {{ empty($prof->waiting_time) ? 'bg-gray-100 text-gray-400 italic' : 'bg-gray-50 text-gray-800' }} shadow-inner">{{ !empty($prof->waiting_time) ? $prof->waiting_time : 'None' }}</p>
                    </div>
                    <div class="flex flex-col flex-1 mx-1"></div>
                </div>

                <div class="grid grid-cols-2 gap-8 mt-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Job Search Method(s)</h3>
                        @if($prof->methods->isNotEmpty())
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($prof->methods as $method)
                            <li class="text-gray-700">{{ $method->method }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-500 italic">None</p>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Attachment(s)</h3>
                        @if($prof->attachments->isNotEmpty())
                        <div class="space-y-2">
                            @foreach ($prof->attachments as $attachment)
                            <a class="text-blue-600 hover:text-blue-800 underline block" href="{{ asset('storage/professional/attachments/' . $attachment->link) }}" target="_blank">
                                {{ $attachment->name }}
                            </a>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 italic">None</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8 mt-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Hard Skill(s)</h3>
                        @if($prof->hardSkills->isNotEmpty())
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($prof->hardSkills as $skill)
                            <li class="text-gray-700">{{ $skill->skill }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-500 italic">None</p>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Soft Skill(s)</h3>
                        @if($prof->softSkills->isNotEmpty())
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($prof->softSkills as $skill)
                            <li class="text-gray-700">{{ $skill->skill }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-500 italic">None</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @elseif ($query === 'professional' && $user->getProfessionalBio() === null)
        <div class="flex flex-col justify-center flex-1">
            <h1 class="text-center text-3xl text-gray-400">No Professional Info</h1>
        </div>
        @endif
    </div>
</div>
@endsection
@section('script')
<script>
    const manageAccountsModal = document.getElementById('manageAccountModal');
    const openManageAccountModal = document.getElementById('openManageAccountModal');
    const closeManageAccountModal = document.getElementById('closeManageAccountModal');

    <?php if (session('openModal') && session('openModal') === 1): ?>
        manageAccountsModal.classList.remove('hidden');
    <?php endif ?>

    openManageAccountModal.addEventListener('click', () => {
        manageAccountsModal.classList.remove('hidden');
    });

    closeManageAccountModal.addEventListener('click', () => {
        manageAccountsModal.classList.add('hidden');
    });
</script>
@endsection