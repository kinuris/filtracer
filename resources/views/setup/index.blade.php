@extends('layouts.setup')

@section('content')
@php($user = auth()->user())
<div class="flex place-items-start h-full justify-center">
    <div class="shadow-lg bg-white mt-16 w-[60%] p-10 rounded-lg flex flex-col">
        <img class="h-20" src="{{ asset('assets/setup_like.svg') }}" alt="Like">

        <h1 class="text-2xl font-semibold text-center mt-5 tracking-wider">Welcome to Filtracer, {{ explode(' ', $user->name)[0] }}!</h1>

        <p class="max-w-[50%] text-center self-center text-sm text-gray-400 leading-4 mt-4">Congratulations! You are now a verified user. Get started to complete your profile details and connect with others.</p>

        <a class="bg-blue-600 text-white p-2 rounded w-fit self-center mt-8" href="/alumni/setup/personal">Get Started</a>
    </div>
</div>
@endsection
