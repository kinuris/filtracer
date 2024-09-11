@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)] overflow-scroll">
    <div class="shadow rounded-lg mt-4">
        <div class="bg-white p-8 flex place-items-center justify-between rounded-lg">
            <div class="flex"> 
                <div class="flex flex-col mr-10">
                    <p class="text-3xl font-bold">102</p>
                    <p class="text-sm text-gray-400">Registered Users</p>
                </div>
                <img src="{{ asset('assets/registered.svg') }}" alt="Registered">
            </div>

            <div class="flex">
                <div class="flex flex-col mr-10">
                    <p class="text-3xl font-bold">102</p>
                    <p class="text-sm text-gray-400">Employed Users</p>
                </div>
                <img src="{{ asset('assets/employed.svg') }}" alt="Employed">
            </div>

            <div class="flex">
                <div class="flex flex-col mr-10">
                    <p class="text-3xl font-bold">102</p>
                    <p class="text-sm text-gray-400">Unemployed Users</p>
                </div>
                <img src="{{ asset('assets/unemployed.svg') }}" alt="Unemployed">
            </div>
        </div>
    </div>
</div>
@endsection