@extends('layouts.alumni')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col">
    <h1 class="font-medium tracking-widest text-lg">Password Settings</h1>

    <div class="w-full mx-auto p-6 bg-white shadow-md rounded-lg mt-8">
        <h2 class="text-2xl font-semibold mb-4">Reset Password</h2>
        <p class="text-gray-600 text-sm mb-4">
            <strong>Password requirements:</strong> <br>
            Ensure that these requirements are met: <br>
            At least 8 characters (and up to 100 characters)
        </p>

        @if ($errors->any())
        <div class="mb-4">
            <ul class="list-disc list-inside text-red-600">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="/settings/alumni/password/{{ Auth::user()->id }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Current Password</label>
                <input type="password" name="current_password" placeholder="Enter current password"
                    class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-1">New Password</label>
                <input type="password" name="new_password" placeholder="Enter new password"
                    class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="confirm_new_password" placeholder="Confirm password"
                    class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300">
            </div>

            <div class="flex justify-end gap-2">
                <button type="reset"
                    class="px-4 py-2 text-gray-600 border border-gray-600 rounded-lg hover:bg-gray-100 transition">
                    Clear
                </button>
                <a
                    href="/settings"
                    class="px-4 py-2 text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-100 transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
@endsection