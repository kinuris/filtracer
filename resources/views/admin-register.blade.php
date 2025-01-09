<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Officer Registration</title>
    <link rel="shortcut icon" href="{{ asset('assets/favicon.svg') }}" type="image/x-icon">
</head>

@php($offices = App\Models\Department::allValid())

<body>
    <div class="bg-gray-100 w-screen h-screen flex justify-center place-items-center">
        <form action="/register/admin" method="POST" id="officer-registration">
            @csrf
            <div class="flex h-full bg-white aspect-[7/4.2] shadow-lg rounded-2xl p-12 pt-8 pb-12 pr-0">
                <img class="max-w-96 border-r pr-12" src="{{ asset('assets/filtracer.svg') }}" alt="Filtracer Logo">

                <div class="flex flex-col min-w-[500px] max-w-[600px] px-12">
                    <h1 class="text-center text-xl tracking-widest">Alumni Officer Register Form</h1>
                    <p class="py-2 border rounded mt-6 text-center mx-8 text-gray-400 text-sm">By clicking Register and filling up your details, you agree to our <span class="text-blue-600">Website Policy</span> and our <span class="text-blue-600">Privacy Notice and Data Privacy Policy</span>.</p>

                    @if (!isset($step))
                    <input type="hidden" name="step" value="0">
                    @elseif ($step == 1)
                    <input type="hidden" name="step" value="1">
                    @endif

                    @if (!isset($step))
                    <!-- <input class="rounded-lg border border-gray-200 p-2 mb-3 mt-8" type="text" placeholder="Full Name" name="name" id="name"> -->
                    <div class="flex gap-2 mt-8">
                        <div class="flex flex-col flex-1 relative">
                            <label class="text-sm font-semibold mb-1" for="first_name">First Name</label>
                            <input class="rounded-lg border p-2 mb-1 {{ $errors->has('first_name') ? 'border-red-500' : 'border-gray-200' }}" type="text" name="first_name" placeholder="Enter first name" id="first_name" value="{{ old('first_name') }}">
                            @if ($errors->has('first_name'))
                            <span class="text-red-500 text-[10px] absolute -bottom-0.5">{{ $errors->first('first_name') }}</span>
                            @endif
                        </div>

                        <div class="flex flex-col flex-1 relative">
                            <label class="text-sm font-semibold mb-1" for="middle_name">Middle Name</label>
                            <input class="rounded-lg border border-gray-200 p-2 mb-3" type="text" name="middle_name" placeholder="Enter middle name" id="middle_name" value="{{ old('middle_name') }}">
                            @if ($errors->has('middle_name'))
                            <span class="text-red-500 text-[10px] absolute -bottom-0.5">{{ $errors->first('middle_name') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-2 mt-1">
                        <div class="flex flex-col flex-1 relative">
                            <label class="text-sm font-semibold mb-1" for="last_name">Last Name</label>
                            <input class="rounded-lg border p-2 mb-3 {{ $errors->has('last_name') ? 'border-red-500' : 'border-gray-200' }}" type="text" name="last_name" placeholder="Enter last name" id="last_name" value="{{ old('last_name') }}">
                            @if ($errors->has('last_name'))
                            <span class="text-red-500 text-[10px] absolute -bottom-0.5">{{ $errors->first('last_name') }}</span>
                            @endif
                        </div>

                        <div class="flex flex-col flex-1 relative">
                            <label class="text-sm font-semibold mb-1" for="suffix">Suffix</label>
                            <input class="rounded-lg border border-gray-200 p-2 mb-3" type="text" name="suffix" placeholder="Enter suffix (Jr., Sr. PhD, etc.)" id="suffix" value="{{ old('suffix') }}">
                            @if ($errors->has('suffix'))
                            <span class="text-red-500 text-[10px] absolute -bottom-0.5">{{ $errors->first('suffix') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- <input class="rounded-lg border border-gray-200 p-2 mb-3" type="text" placeholder="Username" name="username" id="username">

                    <div class="flex mb-3">
                        <input class="rounded-lg border border-gray-200 p-2 flex-1" type="password" placeholder="Password" name="password" id="password">
                        <div class="mx-1"></div>
                        <input class="rounded-lg border border-gray-200 p-2 flex-1" type="password" placeholder="Repeat Password" name="confirm_password" id="confirm_password">
                    </div>

                    <div class="flex mb-3">
                        <input class="rounded-lg border border-gray-200 p-2 flex-1" type="email" placeholder="Email" name="email_address" id="email">
                        <div class="mx-1"></div>
                        <input class="rounded-lg border border-gray-200 p-2 flex-1" type="tel" placeholder="Contact Number" name="phone_number" id="contact_number">
                    </div> -->

                    <div class="flex flex-col gap-1 mt-1">
                        <div class="flex flex-col flex-1 relative">
                            <label class="text-sm font-semibold mb-1" for="position">Position ID</label>
                            <input class="rounded-lg border p-2 mb-3 {{ $errors->has('position_id') ? 'border-red-500' : 'border-gray-200' }}" type="text" name="position_id" placeholder="Enter position ID" id="position" value="{{ old('position_id') }}">
                            @if ($errors->has('position_id'))
                            <span class="text-red-500 text-[10px] absolute -bottom-0.5">{{ $errors->first('position_id') }}</span>
                            @endif
                        </div>

                        <div class="flex flex-col relative">
                            <label class="text-sm font-semibold mb-1" for="office">Office</label>
                            <select class="rounded-lg border p-2 mb-3 {{ $errors->has('office') ? 'border-red-500' : 'border-gray-200' }}" name="office" id="office">
                                <option value="" disabled selected>Select Department</option>
                                @foreach ($offices as $office)
                                <option value="{{ $office->id }}" {{ old('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('office'))
                            <span class="text-red-500 text-[10px] absolute -bottom-0.5">{{ $errors->first('office') }}</span>
                            @endif
                        </div>
                    </div>
                    @else
                    @foreach ($validated as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="flex flex-col mt-8 relative">
                        <label class="text-sm font-semibold mb-1" for="email">Email</label>
                        <input class="rounded-lg border p-2 mb-3 {{ $errors->has('email_address') ? 'border-red-500' : 'border-gray-200' }}" type="email" name="email_address" placeholder="Enter email" id="email" value="{{ old('email_address') }}">
                        @if ($errors->has('email_address'))
                        <span class="text-red-500 text-[10px] absolute -bottom-0.5">{{ $errors->first('email_address') }}</span>
                        @endif
                    </div>

                    <div class="flex gap-2 mt-2">
                        <div class="flex flex-col flex-1 relative">
                            <label class="text-sm font-semibold mb-1" for="username">Username</label>
                            <input class="rounded-lg border p-2 mb-3 {{ $errors->has('username') ? 'border-red-500' : 'border-gray-200' }}" type="text" name="username" placeholder="Enter username" id="username" value="{{ old('username') }}">
                            @if ($errors->has('username'))
                            <span class="text-red-500 text-[10px] absolute -bottom-0.5">{{ $errors->first('username') }}</span>
                            @endif
                        </div>

                        <div class="flex flex-col flex-1 relative">
                            <label class="text-sm font-semibold mb-1" for="contact_number">Contact Number</label>
                            <input class="rounded-lg border p-2 mb-3 {{ $errors->has('phone_number') ? 'border-red-500' : 'border-gray-200' }}" type="tel" name="phone_number" placeholder="Enter contact number" id="contact_number" value="{{ old('phone_number') }}">
                            @if ($errors->has('phone_number'))
                            <span class="text-red-500 text-[10px] absolute -bottom-0.5">{{ $errors->first('phone_number') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-2 mt-2">
                        <div class="flex flex-col flex-1 relative">
                            <label class="text-sm font-semibold mb-1" for="password">Password</label>
                            <input class="rounded-lg border p-2 mb-3 {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }}" type="password" name="password" placeholder="Enter password" id="password">
                            @if ($errors->has('password'))
                            <span class="text-red-500 text-[10px] absolute -bottom-0.5">{{ $errors->first('password') }}</span>
                            @endif
                        </div>

                        <div class="flex flex-col flex-1 relative">
                            <label class="text-sm font-semibold mb-1" for="confirm_password">Confirm Password</label>
                            <input class="rounded-lg border p-2 mb-3 {{ $errors->has('confirm_password') ? 'border-red-500' : 'border-gray-200' }}" type="password" name="confirm_password" placeholder="Enter password again" id="confirm_password">
                            @if ($errors->has('confirm_password'))
                            <span class="text-red-500 text-[10px] absolute -bottom-0.5">{{ $errors->first('confirm_password') }}</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <p class="text-gray-300 text-center text-sm my-4">Protected by reCaptcha v3</p>

                    <div class="flex gap-1.5">
                        @if (isset($step))
                        {{ session()->flashInput($validated) }}
                        <a href="{{ url()->previous() }}" class="bg-[#147DC8] rounded-lg text-white flex-1 py-2 text-center">Back</a>
                        @endif
                        <button class="g-recaptcha bg-[#147DC8] rounded-lg text-white py-2 flex-1"
                            data-sitekey="6Lcd9TEqAAAAAHY7KfIvIqaRPwuyrg0uEeGd8zkb"
                            data-callback='onSubmit'
                            data-action='submit'>
                            @if(isset($step)) Register @else Next @endif
                        </button>
                    </div>

                    <p class="text-gray-400 text-xs text-center pt-3">Already have an account? <a class="text-blue-600" href="/login">Login</a></p>
                </div>
            </div>
        </form>
    </div>

    <!-- @if ($errors->any())
    <div class="flex absolute top-5 left-5 z-40 bg-white shadow-lg p-4 rounded-lg place-items-center">
        <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
    </div>
    @endif -->

    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        function onSubmit(token) {
            const form = document.getElementById('officer-registration');
            form.submit();
        }
    </script>
</body>

</html>