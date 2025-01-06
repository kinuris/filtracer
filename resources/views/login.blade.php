<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Login</title>
</head>

<body>
    <div class="bg-gray-100 w-screen h-screen flex justify-center place-items-center">
        <form action="/login" method="POST" id="login-form">
            @csrf
            <div class="flex flex-col h-full bg-white aspect-[5/6] shadow-lg rounded-2xl p-20 pt-8 pb-12">
                <img class="h-fit w-80" src="{{ asset('assets/filtracer.svg') }}" alt="Filtracer Logo">

                <div class="flex flex-col mb-3 mt-8">
                    <label for="username" class="mb-0.5 text-sm  text-gray-700">Username</label>
                    <input class="rounded-lg border border-gray-200 p-2" type="text" placeholder="Username" name="username" id="username">
                </div>

                <div class="flex flex-col mb-3">
                    <label for="password" class="mb-0.5 text-sm text-gray-700">Password</label>
                    <input class="rounded-lg border border-gray-200 p-2" type="password" placeholder="Password" name="password" id="password">
                </div>

                <p class="text-gray-300 text-center text-sm my-4">Protected by reCaptcha v3</p>
                <button class="g-recaptcha bg-[#147DC8] rounded-lg text-white py-2"
                    data-sitekey="6Lcd9TEqAAAAAHY7KfIvIqaRPwuyrg0uEeGd8zkb"
                    data-callback='onSubmit'
                    data-action='submit'>
                    Login
                </button>
                <hr class="my-5">
                <p class="text-gray-500 font-thin text-sm text-center">Don't have an account?</p>

                <div class="flex-1"></div>

                <div class="flex flex-col">
                    <a class="text-blue-600 text-sm text-center" href="/register/admin">Register as alumni officer</a>
                    <a class="text-blue-600 text-sm text-center" href="/register/alumni">Reigster as alumni</a>
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

    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        function onSubmit(token) {
            document.getElementById("login-form").submit();
        }
    </script>
</body>

</html>