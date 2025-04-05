<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.svg') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    <title>Login</title>
</head>

<body>
    <div class="relative bg-gray-100 w-screen h-screen flex justify-center place-items-center">
        @if (session('reset_number'))
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm z-50 flex justify-center items-center transition-opacity duration-300">
            <div class="bg-white p-8 rounded-xl shadow-2xl max-w-md mx-auto border border-gray-100">
                <div class="flex items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-800">Password Reset Sent</h2>
                </div>
                <p class="mb-6 text-gray-600">Password reset instructions have been sent to your phone number ending in <span class="font-medium text-gray-800">{{ session('reset_number') }}</span>.</p>
                <div class="flex justify-end">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Close</button>
                </div>
            </div>
        </div>
        @endif

        <form action="/login" method="POST" id="login-form">
            @csrf
            <div class="flex flex-col h-full bg-white aspect-[5/6] shadow-lg rounded-2xl p-20 pt-8 pb-12">
                <img class="h-fit w-80" src="{{ asset('assets/filtracer.svg') }}" alt="Filtracer Logo">

                <div class="flex flex-col mb-3 mt-8">
                    <label for="username" class="mb-0.5 text-sm  text-gray-700">Username</label>
                    <input class="rounded-lg border border-gray-200 p-2" type="text" placeholder="Enter username" name="username" id="username">
                </div>

                <div class="flex flex-col mb-1">
                    <label for="password" class="mb-0.5 text-sm text-gray-700">Password</label>
                    <input class="rounded-lg border border-gray-200 p-2" type="password" placeholder="Enter password" name="password" id="password">
                </div>

                <div class="flex justify-end">
                    <button type="button" id="forgotPasswordBtn" class="text-blue-600 text-sm bg-transparent border-none cursor-pointer p-0 hover:text-blue-800 transition-colors">Forgot password?</button>
                </div>

                <!-- Password Reset Confirmation Modal -->
                <div id="passwordResetModal" class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-50 hidden flex justify-center items-center transition-all duration-300 opacity-0">
                    <div class="bg-white p-8 rounded-xl shadow-xl max-w-md w-full mx-4 border border-gray-100 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
                        <div class="flex items-center mb-6">
                            <div class="bg-blue-50 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800">Reset Password</h2>
                        </div>
                        <p class="mb-8 text-gray-600 leading-relaxed">Are you sure you want to reset your password? A verification code will be sent to your registered phone number.</p>
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancelResetBtn" class="px-5 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors duration-200 font-medium focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">Cancel</button>
                            <button type="button" id="confirmResetBtn" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center">
                                <span>Reset Password</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const modal = document.getElementById('passwordResetModal');
                        const modalContent = document.getElementById('modalContent');
                        const forgotBtn = document.getElementById('forgotPasswordBtn');
                        const cancelBtn = document.getElementById('cancelResetBtn');
                        const confirmBtn = document.getElementById('confirmResetBtn');
                        const form = document.getElementById('login-form');

                        forgotBtn.addEventListener('click', function() {
                            modal.classList.remove('hidden');
                            // Set display flex explicitly since we're toggling between hidden and flex
                            modal.style.display = 'flex';
                            setTimeout(() => {
                                modal.classList.add('opacity-100');
                                modalContent.classList.add('opacity-100', 'scale-100');
                                modalContent.classList.remove('scale-95', 'opacity-0');
                            }, 10);
                        });

                        function closeModal() {
                            modal.classList.remove('opacity-100');
                            modalContent.classList.remove('opacity-100', 'scale-100');
                            modalContent.classList.add('scale-95', 'opacity-0');
                            setTimeout(() => {
                                modal.classList.add('hidden');
                                modal.style.display = 'none';
                            }, 300);
                        }

                        cancelBtn.addEventListener('click', closeModal);

                        confirmBtn.addEventListener('click', function() {
                            form.action = '/forgot-password';
                            form.submit();
                        });

                        modal.addEventListener('click', function(e) {
                            if (e.target === modal) closeModal();
                        });
                    });
                </script>

                <p class="text-gray-300 text-center text-sm my-4">Protected by reCaptcha v3</p>
                <button class="g-recaptcha bg-[#147DC8] rounded-lg text-white py-2"
                    data-sitekey="6LcjGN0qAAAAAGYxpw-G6r_328og3MoP3NPrc8wS"
                    data-callback='onSubmit'
                    data-action='submit'>
                    Login
                </button>
                <hr class="my-5">
                <p class="text-gray-500 font-thin text-sm text-center">Don't have an account? <a class="text-blue-600 text-sm text-center font-medium" href="/register/alumni">Register</a></p>

                <div class="flex-1"></div>

                <!-- <div class="flex flex-col">
                    <a class="text-blue-600 text-sm text-center" href="/register/admin">Register as alumni officer</a>
                    <a class="text-blue-600 text-sm text-center" href="/register/alumni">Reigster as alumni</a>
                </div> -->
            </div>
        </form>
    </div>

    @if (session('message'))
    <div class="flex absolute top-5 left-5 w-80 z-40 bg-white shadow-md p-4 rounded-lg flex-col border-l-4 border-green-500 animate-fade-in">
        <div class="flex w-full place-items-center">
            <img class="w-6 mr-3" src="{{ asset('assets/success.svg') }}" alt="Success">
            <h1 class="text-sm font-semibold text-gray-800">{{ session('message') }}</h1>
        </div>
        @if(session('subtitle'))
        <p class="text-xs text-gray-600 mt-1.5 ml-9">{{ session('subtitle') }}</p>
        @endif
        <div class="absolute top-2 right-2">
            <button onclick="this.parentElement.parentElement.style.display='none'" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="w-full bg-gray-100 h-1 mt-3 rounded-full overflow-hidden">
            <div class="bg-green-500 h-full success-timer"></div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successTimer = document.querySelector('.success-timer');
            if (successTimer) {
                successTimer.style.width = '100%';
                successTimer.style.transition = 'width 5s linear';
                setTimeout(() => {
                    successTimer.style.width = '0%';
                }, 100);
                setTimeout(() => {
                    const alert = successTimer.closest('.animate-fade-in');
                    if (alert) alert.style.display = 'none';
                }, 5100);
            }
        });
    </script>
    @endif

    @if (session('failed_message'))
    <div class="flex absolute top-5 left-5 w-80 z-40 bg-white shadow-md p-4 rounded-lg flex-col border-l-4 border-red-500 animate-fade-in">
        <div class="flex w-full place-items-center">
            <img class="w-6 mr-3" src="{{ asset('assets/failed.svg') }}" alt="Failed">
            <h1 class="text-sm font-semibold text-gray-800">{{ session('failed_message') }}</h1>
        </div>
        @if(session('failed_subtitle'))
        <p class="text-xs text-gray-600 mt-1.5 ml-9">{{ session('failed_subtitle') }}</p>
        @endif
        <div class="absolute top-2 right-2">
            <button onclick="this.parentElement.parentElement.style.display='none'" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="w-full bg-gray-100 h-1 mt-3 rounded-full overflow-hidden">
            <div class="bg-red-500 h-full failed-timer"></div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const failedTimer = document.querySelector('.failed-timer');
            if (failedTimer) {
                failedTimer.style.width = '100%';
                failedTimer.style.transition = 'width 5s linear';
                setTimeout(() => {
                    failedTimer.style.width = '0%';
                }, 100);
                setTimeout(() => {
                    const alert = failedTimer.closest('.animate-fade-in');
                    if (alert) alert.style.display = 'none';
                }, 5100);
            }
        });
    </script>
    @endif

    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        function onSubmit(token) {
            document.getElementById("login-form").submit();
        }
    </script>
</body>

</html>