@extends('layouts.admin')

@section('title', 'My Profile')

@section('content')
@php($user = auth()->user())
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg">My Profile</h1>

    <div class="shadow rounded-lg mt-6">
        <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
            <img class="w-32 h-32 rounded-full object-cover shadow-md mr-8" src="{{ $user->image() }}" alt="Profile">
            <div class="flex flex-col">
                <p class="text-lg">{{ $user->name }}</p>
                <p class="text-gray-400 text-sm">{{ $user->department->name }} / {{ $user->admin()->is_super ? 'Superadmin' : 'Admin' }}</p>
            </div>

            <div class="flex-1"></div>

            <a class="rounded-lg p-2 px-3 bg-blue-600 text-white mr-3" href="/settings/account">Go to Settings</a>
            <!-- <a class="rounded-lg p-2 px-3 bg-blue-600 text-white" href="">Log History</a> -->
        </div>
    </div>

    @php ($personal = $user->admin())
    <div class="shadow rounded-lg mt-4 box-border h-full max-h-full overflow-auto">
        <div class="bg-white py-4 flex flex-col border-b rounded-lg min-h-full">
            <h1 class="px-6 pb-4 font-medium tracking-widest text-md">Personal Information</h1>
            <hr>
            <div class="px-6 pt-4 flex flex-col">
                <div class="flex">
                    <div class="flex flex-1">
                        <p class="flex-[2] text-gray-500">Full Name</p>
                        <p class="flex-[5] font-bold">{{ $user->name }}</p>
                    </div>
                    <div class="flex flex-1">
                        <p class="flex-[2] text-gray-500">Username</p>
                        <p class="flex-[5] font-bold">{{ $user->username }}</p>
                    </div>
                </div>

                <div class="flex mt-4">
                    <div class="flex flex-1">
                        <p class="flex-[2] text-gray-500">Company ID</p>
                        <p class="flex-[5] font-bold">{{ $user->admin()->position_id }}</p>
                    </div>
                    <div class="flex flex-1">
                        <p class="flex-[2] text-gray-500">Email</p>
                        <p class="flex-[5] font-bold">{{ $personal ? $personal->email_address : "(No personal record)" }}</p>
                    </div>
                </div>

                <div class="flex mt-4">
                    @if (!$user->admin()->is_super)
                    <div class="flex flex-1">
                        <p class="flex-[2] text-gray-500">Office</p>
                        <p class="flex-[5] font-bold">{{ $user->department->name }}</p>
                    </div>
                    @endif
                    <div class="flex @if($user->admin()->is_super) w-1/2 @else flex-1 @endif">
                        <p class="flex-[2] text-gray-500">Phone Number</p>
                        <p class="flex-[5] font-bold">{{ $personal ? $personal->phone_number : "(No personal record)" }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection