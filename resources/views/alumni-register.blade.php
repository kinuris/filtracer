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
        <form action="/register/admin" method="POST" id="officer-registration">
            @csrf
            <div class="flex h-full bg-white aspect-[8/5] shadow-lg rounded-2xl p-12 pt-8 pb-12 pr-0">
                <img class="max-w-96 border-r pr-12" src="{{ asset('assets/filtracer.svg') }}" alt="Filtracer Logo">

                <div class="flex flex-col min-w-[500px] max-w-[600px] px-12">
                    <h1 class="text-center text-xl tracking-widest">Alumni Register Form</h1>
                    <p class="py-2 border rounded mt-6 text-center mx-8 text-gray-400 text-sm">By clicking Register and filling up your details, you agree to our <span class="text-blue-600">Website Policy</span> and our <span class="text-blue-600">Privacy Notice and Data Privacy Policy</span>.</p>

                    <input class="rounded-lg border border-gray-200 p-2 mb-3 mt-8" type="text" placeholder="Full Name" name="username" id="username">
                    <input class="rounded-lg border border-gray-200 p-2 mb-3" type="text" placeholder="Username" name="username" id="username">

                    <div class="flex mb-3">
                        <input class="rounded-lg border border-gray-200 p-2 flex-1" type="password" placeholder="Password" name="password" id="password">
                        <div class="mx-1"></div>
                        <input class="rounded-lg border border-gray-200 p-2 flex-1" type="password" placeholder="Repeat Password">
                    </div>

                    <div class="flex mb-3">
                        <input class="rounded-lg border border-gray-200 p-2 flex-1" type="email" placeholder="Email" name="email" id="email">
                        <div class="mx-1"></div>
                        <input class="rounded-lg border border-gray-200 p-2 flex-1" type="tel" placeholder="Contact Number" name="contact_number" id="contact_number">
                    </div>

                    <input class="rounded-lg border border-gray-200 p-2 mb-3" type="text" placeholder="Student ID">

                    <div class="flex mb-3">
                        <input class="rounded-lg border border-gray-200 p-2 flex-1" type="email" placeholder="Course" name="course" id="course">
                        <div class="mx-1"></div>
                        <input class="rounded-lg border border-gray-200 p-2 flex-1" type="tel" placeholder="Batch" name="batch" id="batch">
                    </div>

                    <p class="text-gray-300 text-center text-sm my-3">Protected by reCaptcha v3</p>

                    <button class="g-recaptcha bg-[#147DC8] rounded-lg text-white py-2"
                        data-sitekey="6Lcd9TEqAAAAAHY7KfIvIqaRPwuyrg0uEeGd8zkb"
                        data-callback='onSubmit'
                        data-action='submit'>
                        Register Account
                    </button>

                    <p class="text-gray-400 text-xs text-center pt-3">Already have an account? <a class="text-blue-600" href="/login">Login</a></p>
                </div>
            </div>
        </form>
    </div>

    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        function onSubmit(token) {
            document.getElementById("officer-registration").submit();
        }
    </script>
</body>

</html>