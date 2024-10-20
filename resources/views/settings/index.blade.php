@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col">
    <h1 class="font-medium tracking-widest text-lg">Settings</h1>

    <div class="flex">
        <div class="shadow rounded-lg mt-6 flex-1">
            <a href="/settings/department">
                <div class="bg-white h-24 p-7 flex place-items-center border-b rounded-lg">
                    <img class="h-8 mr-6" src="{{ asset('assets/department.svg') }}" alt="Departments">
                    <div class="flex flex-col">
                        <p class="tracking-wider">Departments</p>
                        <p class="text-sm text-gray-400">Manage Department Settings</p>
                    </div>
                    <div class="flex-1"></div>
                    <img class="w-6" src="{{ asset('assets/larrow.svg') }}" alt="To Department Settings">
                </div>
            </a>
        </div>

        <div class="mx-2"></div>

        <div class="shadow rounded-lg mt-6 flex-1">
            <a href="/settings/course">
                <div class="bg-white h-24 p-7 flex place-items-center border-b rounded-lg">
                    <img class="h-9 mr-6" src="{{ asset('assets/department.svg') }}" alt="Accounts">
                    <div class="flex flex-col">
                        <p class="tracking-wider">Courses</p>
                        <p class="text-sm text-gray-400">Manage Course Settings</p>
                    </div>
                    <div class="flex-1"></div>
                    <img class="w-6" src="{{ asset('assets/larrow.svg') }}" alt="To Department Settings">
                </div>
            </a>
        </div>
    </div>

    <div class="flex">
        <div class="shadow rounded-lg mt-4 flex-1">
            <a href="/settings/major">
                <div class="bg-white h-24 p-7 flex place-items-center border-b rounded-lg">
                    <img class="h-8 mr-6" src="{{ asset('assets/department.svg') }}" alt="Departments">
                    <div class="flex flex-col">
                        <p class="tracking-wider">Majors</p>
                        <p class="text-sm text-gray-400">Manage Major Settings</p>
                    </div>
                    <div class="flex-1"></div>
                    <img class="w-6" src="{{ asset('assets/larrow.svg') }}" alt="To Department Settings">
                </div>
            </a>
        </div>

        <div class="mx-2"></div>

        <div class="shadow rounded-lg mt-4 flex-1">
            <a href="/settings/account">
                <div class="bg-white h-24 p-7 flex place-items-center border-b rounded-lg">
                    <img class="h-9 mr-6" src="{{ asset('assets/account_settings.svg') }}" alt="Accounts">
                    <div class="flex flex-col">
                        <p class="tracking-wider">Account</p>
                        <p class="text-sm text-gray-400">Manage your Account Settings</p>
                    </div>
                    <div class="flex-1"></div>
                    <img class="w-6" src="{{ asset('assets/larrow.svg') }}" alt="To Department Settings">
                </div>
            </a>
        </div>
    </div>
</div>
@endsection