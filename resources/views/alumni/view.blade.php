@extends('layouts.admin')

@section('content')
@php($query = request()->query('type') ?? 'personal')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg">Profile Details</h1>
    <p class="text-gray-400 text-xs mb-2">Department / {{ $dept->name }} / <span class="text-blue-500">{{ $user->name }}</span></p>

    <div class="shadow rounded-lg mt-6">
        <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
            <img class="w-32 h-32 rounded-full object-cover shadow-md mr-8" src="{{ $user->image() }}" alt="Profile">
            <div class="flex flex-col">
                <p class="text-lg">{{ $user->name }}</p>
                <p class="text-gray-400 text-sm">{{ $user->getEducationalBio()->getCourse()->name }}</p>
            </div>

            <div class="flex-1"></div>

            <a class="rounded-lg p-2 px-3 bg-blue-600 text-white" href="">Generate Report</a>
            <a class="rounded-lg p-2 px-3 bg-blue-600 text-white mx-3" href="">Manage Account</a>
            <a class="rounded-lg p-2 px-3 bg-blue-600 text-white" href="">Log History</a>
        </div>
    </div>

    <div class="shadow rounded-lg mt-4 box-border h-full max-h-full overflow-scroll">
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
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-scroll">{{ $personal->permanent_address }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Last Name</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->last_name }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700 line-clamp-1" title="Current Address">Current Addr.</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-scroll">{{ $personal->current_address }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Student ID</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->student_id }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700 line-clamp-1" title="Current Address">Email</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-scroll">{{ $personal->email_address }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Age</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->getAge() }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700 line-clamp-1" title="Current Address">Phone Number</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-scroll">{{ $personal->phone_number }}</p>
                    </div>
                </div>

                <div class="flex mt-2">
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700">Gender</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100">{{ $personal->gender }}</p>
                    </div>
                    <div class="flex justify-between place-items-center flex-1 mx-4 h-fit">
                        <p class="flex-1 tracking-wider text-slate-700 line-clamp-1" title="Current Address">Social Link</p>
                        <p class="flex-[2] border p-2 rounded-lg text-gray-400 bg-gray-100 line-clamp-1 overflow-scroll"><a class="underline" href="{{ $personal->social_link }}">{{ $personal->social_link }}</a></p>
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
            @elseif ($query === 'educational' && $user->getEducationalBio() === null)
            <div class="flex flex-col justify-center flex-1">
                <h1 class="text-center text-3xl text-gray-400">No Educational Info</h1>
            </div>
            @endif

            @if ($query === 'professional' && $user->getProfessionalBio() !== null)
            @elseif ($query === 'professional' && $user->getProfessionalBio() === null)
            <div class="flex flex-col justify-center flex-1">
                <h1 class="text-center text-3xl text-gray-400">No Professional Info</h1>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection