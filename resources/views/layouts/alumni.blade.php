<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    @yield('header')
    @vite('resources/css/app.css')

    <title>@yield('title')</title>
</head>

<body>
    <div class="flex w-screen">
        @if (request()->path() !== 'alumni/chat')
        <a href="/alumni/chat" class="fixed rounded-full bottom-0 right-0 mb-6 mr-6 shadow-lg bg-blue-600 transition-transform hover:scale-110">
            <img class="w-8 m-3" src="{{ asset('assets/chat.svg') }}" alt="Chat">
        </a>
        @endif
        <div class="bg-white border-r h-screen max-h-screen min-w-[max(20%,300px)] px-6 pb-2 flex flex-col">
            <img class="h-fit w-44" src="{{ asset('assets/filtracer_nolabel.svg') }}" alt="Filtracer Logo">

            <a href="/alumni">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/dashboard.svg') }}" alt="Dashboard">
                    Home
                </div>
            </a>

            <a href="/alumni/profile">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/profile_outlined.svg') }}" alt="Profile">
                    Profile
                </div>
            </a>

            <a href="/alumni/chat">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/chat_gray.svg') }}" alt="Chat">
                    Chats
                </div>
            </a>

            <a href="/alumni/post">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/posts.svg') }}" alt="Jobs">
                    Posts
                </div>
            </a>

            <a href="/event">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/events.svg') }}" alt="Jobs">
                    Events
                </div>
            </a>

            <a href="/settings">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/settings.svg') }}" alt="Jobs">
                    Settings
                </div>
            </a>
        </div>

        <div class="flex flex-col w-full max-h-screen">
            <nav class="bg-white w-full max-h-16 min-h-16 h-16 border-b flex place-items-center px-6">
                <img class="w-6 mr-4" src="{{ asset('assets/search.svg') }}" alt="Dashboard">
                <input class="p-2 min-w-96" placeholder="Search" type="text">

                <div class="flex-1"></div>

                <div class="group relative">
                    <img class="w-5 mr-4" src="{{ asset('assets/notification.svg') }}" alt="Dashboard">

                    <div class="absolute right-3 top-3 z-40 hidden border group-hover:block bg-white shadow-lg rounded-lg overflow-hidden min-w-80 ">
                        <p class="border-b p-3 font-bold">Notifications</p>
                        <span class="block max-h-96 overflow-auto" id="alertContainer">

                        </span>
                    </div>
                </div>

                <div class="border-r h-[calc(100%-1rem)] my-2 mr-3"></div>

                <div class="border mr-4 rounded-full shadow">
                    <img class="w-10 rounded-full aspect-square object-cover" src="{{ auth()->user()->image() }}" alt="Profile">
                </div>

                <p class="mx-3">{{ auth()->user()->name }}</p>

                <div class="relative inline-block text-left">
                    <div>
                        <button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white py-2 text-sm font-semibold text-gray-900" id="menu-button" aria-expanded="true" aria-haspopup="true">
                            <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div id="options" class="hidden absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <a href="/alumni/profile" class="block py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="menu-item-1">
                            <div class="py-1 flex place-items-center" role="none">
                                <img class="block h-4 mx-4" src="{{ asset('assets/profile.svg') }}" alt="Profile">
                                My Profile
                            </div>
                        </a>



                        <a href="#" class="block py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="menu-item-3">
                            <div class="py-1 flex place-items-center" role="none">
                                <img class="block h-4 mx-4" src="{{ asset('assets/settings.svg') }}" alt="Profile">
                                Account Settings
                            </div>
                        </a>

                        <a href="/logout" class="block py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="menu-item-6">
                            <div class="py-1 flex place-items-center" role="none">
                                <img class="block w-4 mx-4" src="{{ asset('assets/logout.svg') }}" alt="Profile">
                                Logout
                            </div>
                        </a>
                    </div>
                </div>

            </nav>
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        const alertContainer = document.querySelector('#alertContainer');

        (function() {
            async function repeat() {
                const alerts = await fetch('/alert/gen');

                alertContainer.innerHTML = await alerts.text();
            }

            repeat();

            setInterval(repeat, 1000);
        })()
    </script>
    <script>
        const menuBtn = document.querySelector('#menu-button');
        const options = document.querySelector('#options');

        menuBtn.addEventListener('click', () => {
            options.classList.toggle('hidden');
        })

        window.addEventListener('click', (e) => {
            if (!menuBtn.contains(e.target) && !options.contains(e.target)) {
                options.classList.add('hidden');
            }
        })
    </script>

    @yield('script')
</body>

</html>