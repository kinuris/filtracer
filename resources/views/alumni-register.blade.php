<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Officer Registration</title>
</head>

<body>
    <div class="bg-gray-100 w-screen h-screen flex justify-center place-items-center">
        <form action="/register/alumni" method="POST" id="officer-registration">
            @csrf

            @if (!isset($step))
            <input type="hidden" name="step" value="0">
            @elseif ($step == 1)
            <input type="hidden" name="step" value="1">
            @endif

            <div class="flex h-full bg-white aspect-[8/5.1] shadow-lg rounded-2xl p-12 pt-8 pb-12 pr-0">
                <img class="max-w-96 border-r pr-12" src="{{ asset('assets/filtracer.svg') }}" alt="Filtracer Logo">

                <div class="flex flex-col min-w-[500px] max-w-[600px] px-12">
                    <h1 class="text-center text-xl tracking-widest">Alumni Register Form</h1>
                    <p class="py-2 border rounded mt-6 text-center mx-8 text-gray-400 text-sm">By clicking Register and filling up your details, you agree to our <span class="text-blue-600">Website Policy</span> and our <span class="text-blue-600">Privacy Notice and Data Privacy Policy</span>.</p>

                    @if (!isset($step))
                    <div class="flex mb-3 mt-6">
                        <div class="flex-1 relative">
                            <label for="first_name" class="block text-sm font-semibold text-gray-700">First Name</label>
                            <input class="rounded-lg border border-gray-200 p-2 w-full @error('first_name') border-red-500 @enderror" type="text" placeholder="First Name" name="first_name" id="first_name" value="{{ old('first_name') }}">
                            @error('first_name')
                            <span class="text-red-500 absolute left-0 -bottom-4 text-[11px]">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mx-1"></div>
                        <div class="flex-1 relative">
                            <label for="middle_name" class="block text-sm font-semibold text-gray-700">Middle Name</label>
                            <input class="rounded-lg border border-gray-200 p-2 w-full @error('middle_name') border-red-500 @enderror" type="text" placeholder="Middle Name" name="middle_name" id="middle_name" value="{{ old('middle_name') }}">
                            @error('middle_name')
                            <span class="text-red-500 absolute left-0 -bottom-4 text-[11px]">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex mb-3 mt-1">
                        <div class="flex-1 relative">
                            <label for="last_name" class="block text-sm font-semibold text-gray-700">Last Name</label>
                            <input class="rounded-lg border border-gray-200 p-2 w-full @error('last_name') border-red-500 @enderror" type="text" placeholder="Last Name" name="last_name" id="last_name" value="{{ old('last_name') }}">
                            @error('last_name')
                            <span class="text-red-500 absolute left-0 -bottom-4 text-[11px]">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mx-1"></div>
                        <div class="flex-1">
                            <label for="suffix" class="block text-sm font-semibold text-gray-700">Suffix</label>
                            <input class="rounded-lg border border-gray-200 p-2 w-full" type="text" placeholder="Suffix" name="suffix" id="suffix">
                        </div>
                    </div>

                    <div class="mb-3 mt-1 relative">
                        <label for="student_no" class="block text-sm font-semibold text-gray-700">Student No.</label>
                        <input class="rounded-lg border border-gray-200 p-2 w-full @error('student_id') border-red-500 @enderror" type="text" placeholder="Student No." name="student_id" id="student_no" value="{{ old('student_id') }}">
                        @error('student_id')
                        <span class="text-red-500 absolute left-0 -bottom-4 text-[11px]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3 mt-1">
                        <label for="department" class="block text-sm font-semibold text-gray-700">Department</label>
                        <select class="rounded-lg border border-gray-200 p-2 w-full" name="department" id="department">
                            @php($departments = App\Models\Department::allValid())
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    @foreach ($validated as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="mb-3 mt-8 relative">
                        <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                        <input class="rounded-lg border border-gray-200 p-2 w-full @error('email') border-red-500 @enderror" type="email" placeholder="Email" name="email" id="email" value="{{ old('email') }}">
                        @error('email')
                        <span class="text-red-500 absolute left-0 -bottom-4 text-[11px]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex mb-3 mt-3">
                        <div class="flex-1 relative">
                            <label for="contact_number" class="block text-sm font-semibold text-gray-700">Contact Number</label>
                            <input class="rounded-lg border border-gray-200 p-2 w-full @error('contact_number') border-red-500 @enderror" type="text" placeholder="Contact Number" name="contact_number" id="contact_number" value="{{ old('contact_number') }}">
                            @error('contact_number')
                            <span class="text-red-500 absolute left-0 -bottom-4 text-[11px]">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mx-1"></div>
                        <div class="flex-1 relative">
                            <label for="username" class="block text-sm font-semibold text-gray-700">Username</label>
                            <input class="rounded-lg border border-gray-200 p-2 w-full @error('username') border-red-500 @enderror" type="text" placeholder="Username" name="username" id="username" value="{{ old('username') }}">
                            @error('username')
                            <span class="text-red-500 absolute left-0 -bottom-4 text-[11px]">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex mb-3 mt-3">
                        <div class="flex-1 relative">
                            <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                            <input class="rounded-lg border border-gray-200 p-2 w-full @error('password') border-red-500 @enderror" type="password" placeholder="Password" name="password" id="password">
                            @error('password')
                            <span class="text-red-500 absolute left-0 -bottom-4 text-[11px]">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mx-1"></div>
                        <div class="flex-1 relative">
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Confirm Password</label>
                            <input class="rounded-lg border border-gray-200 p-2 w-full" type="password" placeholder="Confirm Password" name="password_confirmation" id="password_confirmation">
                        </div>
                    </div>
                    @endif

                    <div class="flex-1"></div>

                    <p class="text-gray-300 text-center text-sm my-3">Protected by reCaptcha v3</p>

                    <div class="flex mb-3 mt-3">
                        @if (isset($step))
                        {{ session()->flashInput($validated) }}
                        <a href="{{ url()->previous() }}" class="bg-[#147DC8] rounded-lg text-white flex-1 py-2 text-center mr-2">Back</a>
                        @endif
                        <button class="g-recaptcha bg-[#147DC8] rounded-lg text-white py-2 flex-1"
                            data-sitekey="6Lcd9TEqAAAAAHY7KfIvIqaRPwuyrg0uEeGd8zkb"
                            data-callback='onSubmit'
                            data-action='submit'>
                            Next
                        </button>
                    </div>

                    <p class="text-gray-400 text-xs text-center pt-3">Already have an account? <a class="text-blue-600" href="/login">Login</a></p>
                </div>
            </div>
        </form>
    </div>

    @if (session('message'))
    <div class="flex absolute top-5 left-5 z-40 bg-white shadow-lg p-4 rounded-lg place-items-center">
        <img class="w-8 mr-3" src="{{ asset('assets/success.svg') }}" alt="Success">
        <h1>{{ session('message') }}</h1>
    </div>
    @endif

    <!-- @if ($errors->any())
    <div class="flex absolute top-5 left-5 z-40 bg-white shadow-lg p-4 rounded-lg place-items-center">
        <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
    </div>
    @endif -->
    @yield('content')

    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        function onSubmit(token) {
            document.getElementById("officer-registration").submit();
        }
    </script>
</body>

</html>