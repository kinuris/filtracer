@extends('layouts.admin')

@section('title', 'Create Account')
@php($offices = App\Models\Department::allValid())

@php($mode = request('mode', 'alumni'))
@section('content')
<div class="h-[calc(100%-4rem)]">
    <div class="bg-gray-100 w-full h-full p-8 flex flex-col overflow-auto max-h-[calc(100%-0.01px)]">
        <h1 class="font-medium tracking-widest text-lg">Create Individual Account</h1>

        <div class="shadow rounded-lg p-4 mb-4 bg-white mt-8 flex-col">
            <div class="flex">
                <a href="{{ request()->fullUrlWithQuery(array_merge(request()->query(), ['mode' => 'alumni'])) }}" class="pb-3 font-semibold hover:text-blue-500 text-lg border-b hover:border-blue-600 {{ $mode == 'alumni' ? 'text-blue-500 border-blue-600' : 'text-gray-700' }}">Alumni Registration Form</a>
                <div class="px-4 border-b"></div>
                <a href="{{ request()->fullUrlWithQuery(array_merge(request()->query(), ['mode' => 'admin'])) }}" class="pb-3 font-semibold hover:text-blue-500 text-lg border-b hover:border-blue-600 {{ $mode == 'admin' ? 'text-blue-500 border-blue-600' : 'text-gray-700' }}">Admin Registration Form</a>
                <div class="border-b flex-1"></div>
            </div>

            <form method="POST" class="mt-4">
                @csrf
                @if ($mode == 'alumni')
                <div class="flex flex-wrap -mx-2 mb-4 mt-8">
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4 md:mb-0">
                        <label for="first_name" class="block text-gray-700">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="w-full px-3 py-2 border rounded-lg @error('first_name') border-red-500 @enderror" placeholder="Enter your first name" value="{{ old('first_name') }}">
                        @error('first_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4 md:mb-0">
                        <label for="middle_name" class="block text-gray-700">Middle Name</label>
                        <input type="text" name="middle_name" id="middle_name" class="w-full px-3 py-2 border rounded-lg" placeholder="Enter your middle name" value="{{ old('middle_name') }}">
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4 md:mb-0">
                        <label for="last_name" class="block text-gray-700">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="w-full px-3 py-2 border rounded-lg @error('last_name') border-red-500 @enderror" placeholder="Enter your last name" value="{{ old('last_name') }}">
                        @error('last_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2">
                        <label for="suffix" class="block text-gray-700">Suffix</label>
                        <input type="text" name="suffix" id="suffix" class="w-full px-3 py-2 border rounded-lg" placeholder="Enter your suffix" value="{{ old('suffix') }}">
                    </div>
                </div>

                <div class="flex flex-wrap -mx-2 mt-8 mb-4">
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4 md:mb-0">
                        <label for="student_id" class="block text-gray-700">Student ID</label>
                        <input type="text" name="student_id" id="student_id" class="w-full px-3 py-2 border rounded-lg @error('student_id') border-red-500 @enderror" placeholder="Enter your student ID" value="{{ old('student_id') }}">
                        @error('student_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4 md:mb-0">
                        <label for="email" class="block text-gray-700">Email</label>
                        <input type="email" name="email" id="email" class="w-full px-3 py-2 border rounded-lg @error('email') border-red-500 @enderror" placeholder="Enter your email" value="{{ old('email') }}">
                        @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4 md:mb-0">
                        <label for="contact_number" class="block text-gray-700">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" class="w-full px-3 py-2 border rounded-lg @error('contact_number') border-red-500 @enderror" placeholder="Enter your contact number" value="{{ old('contact_number') }}">
                        @error('contact_number')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4 md:mb-0">
                        <label for="username" class="block text-gray-700">Username</label>
                        <input type="text" name="username" id="username" class="w-full px-3 py-2 border rounded-lg @error('username') border-red-500 @enderror" placeholder="Enter your username" value="{{ old('username') }}">
                        @error('username')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap -mx-2 mt-6 mb-4">
                    <div class="w-full md:w-1/2 lg:w-1/2 px-2">
                        <label for="password" class="block text-gray-700">Password</label>
                        <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-lg @error('password') border-red-500 @enderror" placeholder="Enter your password">
                        @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/2 px-2">
                        <label for="department" class="block text-gray-700">Department</label>
                        <select name="department" id="department" class="w-full px-3 py-2 border rounded-lg @error('department') border-red-500 @enderror" required>
                            <option value="" disabled {{ old('department') ? '' : 'selected' }}>Select your department</option>
                            @foreach (App\Models\Department::allValid() as $department)
                            <option value="{{ $department->id }}" {{ old('department') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="window.location.href='/account'" class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2">Back</button>
                    <div class="flex-1"></div>
                    <button type="button" onclick="clearFields()" class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2">Clear Fields</button>
                    <button formaction="/account/create-individual" type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Create Account</button>
                </div>
                @else
                <div class="flex flex-wrap -mx-2 mb-4 mt-6">
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4 md:mb-0">
                        <label for="first_name" class="block text-gray-700">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="w-full px-3 py-2 border rounded-lg @error('first_name') border-red-500 @enderror" placeholder="Enter your first name" value="{{ old('first_name') }}">
                        @error('first_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4 md:mb-0">
                        <label for="middle_name" class="block text-gray-700">Middle Name</label>
                        <input type="text" name="middle_name" id="middle_name" class="w-full px-3 py-2 border rounded-lg" placeholder="Enter your middle name" value="{{ old('middle_name') }}">
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4 md:mb-0">
                        <label for="last_name" class="block text-gray-700">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="w-full px-3 py-2 border rounded-lg @error('last_name') border-red-500 @enderror" placeholder="Enter your last name" value="{{ old('last_name') }}">
                        @error('last_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2">
                        <label for="suffix" class="block text-gray-700">Suffix</label>
                        <input type="text" name="suffix" id="suffix" class="w-full px-3 py-2 border rounded-lg" placeholder="Enter your suffix">
                    </div>
                </div>

                <div class="flex flex-wrap -mx-2 mt-6 mb-4">
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4 md:mb-0">
                        <label for="employee_id" class="block text-gray-700">Employee ID</label>
                        <input type="text" name="employee_id" id="employee_id" class="w-full px-3 py-2 border rounded-lg @error('employee_id') border-red-500 @enderror" placeholder="Enter your employee ID" value="{{ old('employee_id') }}">
                        @error('employee_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="w-full md:w-1/2 lg:w-3/4 px-2 mb-4 md:mb-0">
                        <label for="office" class="block text-gray-700">Office</label>
                        <select name="office" id="office" class="w-full px-3 py-2 border rounded-lg" required>
                            <option value="" disabled {{ old('office') ? '' : 'selected' }}>Select your office</option>
                            @foreach ($offices as $office)
                            <option value="{{ $office->id }}" {{ old('office') == $office ? 'selected' : '' }}>{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap -mx-2 mt-6 mb-4">
                    <div class="w-full md:w-1/2 px-2 mb-4">
                        <label for="email" class="block text-gray-700">Email</label>
                        <input type="email" name="email" id="email" class="w-full px-3 py-2 border rounded-lg @error('email') border-red-500 @enderror" placeholder="Enter your email" value="{{ old('email') }}">
                        @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-full md:w-1/2 px-2 mb-4">
                        <label for="contact_number" class="block text-gray-700">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" class="w-full px-3 py-2 border rounded-lg @error('contact_number') border-red-500 @enderror" placeholder="Enter your contact number" value="{{ old('contact_number') }}">
                        @error('contact_number')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap -mx-2 mb-2">
                    <div class="w-full md:w-1/2 px-2 mb-4">
                        <label for="username" class="block text-gray-700">Username</label>
                        <input type="text" name="username" id="username" class="w-full px-3 py-2 border rounded-lg @error('username') border-red-500 @enderror" placeholder="Enter your username" value="{{ old('username') }}">
                        @error('username')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-full md:w-1/2 px-2 mb-4">
                        <label for="password" class="block text-gray-700">Password</label>
                        <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-lg @error('password') border-red-500 @enderror" placeholder="Enter your password">
                        @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="window.location.href='/account'" class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2">Back</button>
                    <div class="flex-1"></div>
                    <button type="button" onclick="clearFields()" class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2">Clear Fields</button>
                    <button formaction="/account/create-admin" type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Create Account</button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function clearFields() {
        document.querySelectorAll('input').forEach(input => input.value = '');
        document.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    }
</script>
@endsection