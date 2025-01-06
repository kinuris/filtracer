@extends('layouts.setup')

@section('content')
@php($partial = auth()->user()->partialPersonal)
<div>
    <form method="POST" class="flex place-items-start h-full justify-center max-h-[calc(100vh-5rem)] pb-10 overflow-auto" action="/alumni/setup/personal/{{ auth()->user()->id }}">
        @csrf
        <div class="shadow-lg bg-white mt-12 w-[60%] p-2 rounded-lg flex flex-col">
            <div class="flex gap-2">
                <div class="flex-1 border p-1 rounded-full bg-blue-600"></div>
                <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
                <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
                <div class="flex-1 border p-1 rounded-full bg-gray-300"></div>
            </div>

            <h1 class="text-2xl font-semibold tracking-wider text-center mt-5">Complete your Profile</h1>
            <p class="text-blue-600 font-bold mt-2 text-xl text-center">Personal Info</p>

            <div class="flex px-8 gap-8 mt-4">
                <div class="flex flex-col flex-1 relative">
                    <label for="first_name">First Name</label>
                    <input class="p-2 border rounded @error('first_name') border-red-500 @enderror" placeholder="Enter First Name" value="{{ $partial->first_name }}" type="text" name="first_name" id="first_name">
                    @error('first_name')
                        <span class="text-red-500 text-[11px] absolute -bottom-4 left-0">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col flex-1">
                    <label for="middle_name">Middle Name</label>
                    <input class="p-2 border rounded" placeholder="Enter Middle Name" value="{{ $partial->middle_name }}" type="text" name="middle_name" id="middle_name">
                </div>
            </div>

            <div class="flex px-8 gap-8 mt-3">
                <div class="flex flex-col flex-1 relative">
                    <label for="last_name">Last Name</label>
                    <input class="p-2 border rounded @error('last_name') border-red-500 @enderror" placeholder="Enter Last Name" value="{{ $partial->last_name }}" type="text" name="last_name" id="last_name">
                    @error('last_name')
                        <span class="text-red-500 text-[11px] absolute -bottom-4 left-0">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col flex-1">
                    <label for="suffix">Suffix</label>
                    <input class="p-2 border rounded" value="{{ $partial->suffix }}" placeholder="Enter Suffix" value="{{ $partial->suffix }}" type="text" name="suffix" id="suffix">
                </div>

                <div class="flex flex-col flex-1 relative">
                    <label for="student_id">Student ID</label>
                    <input value="{{ $partial ? $partial->student_id : '' }}" class="p-2 border rounded @error('student_id') border-red-500 @enderror" type="text" placeholder="Enter Student ID" name="student_id" id="student_id">
                    @error('student_id')
                        <span class="text-red-500 text-[11px] absolute -bottom-4 left-0">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex px-8 gap-8 mt-3">
                <div class="flex flex-col flex-1 relative">
                    <label for="birthdate">Date of Birth</label>
                    <input class="p-2 border rounded @error('birthdate') border-red-500 @enderror" type="date" name="birthdate" id="birthdate" value="{{ old('birthdate') }}">
                    @error('birthdate')
                        <span class="text-red-500 text-[11px] absolute -bottom-4 left-0">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col flex-1">
                    <label for="gender">Gender</label>
                    <select class="p-2 border rounded" name="gender" id="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>

            @php($statuses = [
            'Married',
            'Single',
            'Divorced',
            'Widowed',
            'Separated'
            ])
            <div class="flex px-8 gap-8 mt-3">
                <div class="flex flex-col flex-1 relative">
                    <label for="permanent_address">Permanent Address</label>
                    <input class="p-2 border rounded @error('permanent_address') border-red-500 @enderror" type="text" placeholder="Enter Permanent Address" name="permanent_address" id="permanent_address">
                    @error('permanent_address')
                        <span class="text-red-500 text-[11px] absolute -bottom-4 left-0">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col flex-1">
                    <label for="status">Civil Status</label>
                    <select class="p-2 border rounded" name="civil_status" id="status">
                        @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex px-8 gap-8 mt-3">
                <div class="flex flex-col flex-1 relative">
                    <label for="current_address">Current Address</label>
                    <input class="p-2 border rounded @error('current_address') border-red-500 @enderror" type="text" placeholder="Enter Current Address" name="current_address" id="current_address">
                    @error('current_address')
                        <span class="text-red-500 text-[11px] absolute -bottom-4 left-0">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col flex-1 relative">
                    <label for="email">Email</label>
                    <input value="{{ $partial ? $partial->email_address : '' }}" class="p-2 border rounded @error('email') border-red-500 @enderror" type="email" placeholder="Enter Email" name="email" id="email">
                    @error('email')
                        <span class="text-red-500 text-[11px] absolute -bottom-4 left-0">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex px-8 gap-8 mt-3">
                <div class="flex flex-col flex-1 relative">
                    <label for="phone">Phone Number</label>
                    <input value="{{ $partial ? $partial->phone_number : '' }}" class="p-2 border rounded @error('phone_number') border-red-500 @enderror" type="tel" placeholder="Enter Phone Number" name="phone_number" id="phone">
                    @error('phone_number')
                        <span class="text-red-500 text-[11px] absolute -bottom-4 left-0">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col flex-1">
                    <label for="link">Social Link</label>
                    <input class="p-2 border rounded" type="url" placeholder="Enter Social Link" name="social_link" id="link">
                </div>
            </div>

            <button class="p-2 mr-8 mb-4 mt-16 bg-blue-600 text-white rounded w-fit self-end" type="submit">Save & Next</button>
        </div>
    </form>
</div>
@endsection