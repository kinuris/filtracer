@extends('layouts.alumni')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col">
    <h1 class="font-medium tracking-widest text-lg">Settings</h1>

    <div class="flex">
        <div class="shadow rounded-lg mt-4 flex-1">
            <a href="/settings/alumni/password">
                <div class="bg-white h-24 p-7 flex place-items-center border-b rounded-lg">
                    <img class="h-8 mr-6" src="{{ asset('assets/department.svg') }}" alt="Departments">
                    <div class="flex flex-col">
                        <p class="tracking-wider">Password Settings</p>
                        <p class="text-sm text-gray-400">Manage account password</p>
                    </div>
                    <div class="flex-1"></div>
                    <img class="w-6" src="{{ asset('assets/larrow.svg') }}" alt="To Department Settings">
                </div>
            </a>
        </div>

        <div class="mx-2"></div>

        <div class="shadow rounded-lg mt-4 flex-1">
            <a href="/settings/alumni/display">
                <div class="bg-white h-24 p-7 flex place-items-center border-b rounded-lg">
                    <img class="h-9 mr-6" src="{{ asset('assets/account_settings.svg') }}" alt="Accounts">
                    <div class="flex flex-col">
                        <p class="tracking-wider">Display Settings</p>
                        <p class="text-sm text-gray-400">Manage display settings</p>
                    </div>
                    <div class="flex-1"></div>
                    <img class="w-6" src="{{ asset('assets/larrow.svg') }}" alt="To Department Settings">
                </div>
            </a>
        </div>
    </div>
</div>
@endsection