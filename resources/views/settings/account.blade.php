@extends('layouts.admin')

@section('content')
@php($user = Auth::user())
<div class="bg-gray-100 w-full h-full p-8 pb-0 flex flex-col max-h-[calc(100%-64px)]">
    <h1 class="font-medium tracking-widest text-lg">Account Settings</h1>
    <div class="flex h-full mt-4 max-h-[calc(100%-64px)]">
        <div class="shadow rounded-lg flex-[5] h-full">
            <form class="h-full" action="">
                <div class="bg-white overflow-scroll py-4 h-full border-b rounded-lg">
                    <div class="flex mx-6 mb-3 place-items-center">
                        <h1 class="font-semibold mr-6 tracking-wider">Personal Information</h1>
                        <img class="max-h-3.5" src="{{ asset('assets/edit.svg') }}" alt="Edit">
                    </div>
                    <hr>
                    <div class="mx-6 mt-3">
                        <div>
                            <p class="mb-1 font-thin">Full Name</p>
                            <input value="{{ $user->name }}" class="w-full text-gray-500 font-thin border rounded-md p-2 bg-gray-50" type="text" name="name" id="name">
                        </div>

                        <div class="mt-3">
                            <p class="mb-1 font-thin">Position</p>
                            <input value="{{ $user->role }}" class="w-full text-gray-500 font-thin border rounded-md p-2 bg-gray-50" type="text" readonly name="position" id="position">
                        </div>

                        <div class="mt-3">
                            <p class="mb-1 font-thin">Office</p>
                            <select class="w-full text-gray-500 font-thin border rounded-md p-2 bg-gray-50" name="department" id="department">
                                @php($depts = App\Models\Department::all())
                                @foreach ($depts as $dept)
                                <option {{ $user->department->name === $dept->name ? 'selected' : ''}} value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-3">
                            <p class="mb-1 font-thin">Email</p>
                            <input value="{{ $user->personalBio->email_address }}" class="w-full text-gray-500 font-thin border rounded-md p-2 bg-gray-50" type="email" name="email" id="email">
                        </div>

                        <div class="mt-3">
                            <p class="mb-1 font-thin">Phone Number</p>
                            <input value="{{ $user->personalBio->phone_number }}" class="w-full text-gray-500 font-thin border rounded-md p-2 bg-gray-50" type="tel" name="phone" id="phone">
                        </div>

                        <div class="mt-3">
                            <p class="mb-1 font-thin">Password</p>
                            <a class="text-white bg-blue-500 font-semibold p-2 rounded-lg flex w-fit" href="">
                                Reset Password
                                <img class="mx-2" src="{{ asset('assets/pass_reset.svg') }}" alt="Reset Password">
                            </a>
                        </div>

                        <div class="flex justify-end -mb-2 mt-1">
                            <a class="border-blue-500 p-2 rounded-lg border text-blue-500 mr-2" href="/admin/settings">Cancel</a>
                            <input type="submit" value="Save" class="border-blue-500 p-2 rounded-lg border bg-blue-500 text-white px-4">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="mx-2"></div>
        <div class="flex-[3] h-full max-w-[400px] flex items-start justify-center max-h-[calc(100%-64px)]">
            <div class="shadow rounded-lg w-full">
                <form class="w-full h-full" action="/profile/pic/edit" enctype="multipart/form-data" method="POST">
                    <div class="bg-white py-4 flex flex-col h-full border-b rounded-lg">
                        <h1 class="ml-6 mb-3 font-bold tracking-wide">Your Photo</h1>
                        <hr>
                        <div class="m-4">
                            <div class="flex place-items-center">
                                <img class="w-14 rounded-full shadow aspect-square object-cover" src="{{ $user->image() }}" alt="Profile Picture">
                                <div class="flex flex-col ml-5">
                                    <p class="font-thin">Edit your photo</p>
                                    <a class="text-blue-600" href="">Update</a>
                                </div>
                            </div>
                        </div>

                        <div class="mx-4">
                            <div class="m-4 aspect-[2] max-w-80 mx-auto w-full min-w-64 mt-0 border border-dashed border-blue-400 rounded-lg bg-blue-50 flex justify-center place-items-center">
                                <p class="text-blue-600 text-xs">Click to upload</p>
                            </div>
                        </div>

                        <div class="mx-4 flex justify-end">
                            <a class="border-blue-500 p-2 rounded-lg border text-blue-500 mr-2" href="">Cancel</a>
                            <input type="submit" value="Save" class="border-blue-500 p-2 rounded-lg border bg-blue-500 text-white px-4">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection