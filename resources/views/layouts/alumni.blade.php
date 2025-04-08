<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="shortcut icon" href="{{ asset('assets/favicon.svg') }}" type="image/x-icon">

    @yield('header')
    @vite('resources/css/app.css')

    <title>@yield('title')</title>
</head>

<body>
    <div class="flex w-screen">
        @if (request()->path() !== 'alumni/chat')
        <a href="/alumni/chat" class="fixed rounded-full bottom-0 z-50 right-0 mb-6 mr-6 shadow-lg bg-blue-600 transition-transform hover:scale-110">
            <div class="w-5 h-5 flex place-items-center justify-center bg-red-500 absolute -top-1 -left-1 rounded-full">
                <p class="text-white text-xs text-center font-semibold" id="chatAlerts"></p>
            </div>
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

            <a href="/settings">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/settings.svg') }}" alt="Jobs">
                    Settings
                </div>
            </a>

            @php($bindings = App\Models\BindingRequest::query()
            ->where('alumni_id', '=', Auth::user()->id)
            ->where('is_denied', '=', false)
            ->get())
            @foreach($bindings as $binding)
            <div class="mt-2 mb-3 p-4 border rounded-lg shadow-sm bg-white">
                <div class="flex flex-col">
                    <p class="font-medium text-gray-700 mb-2">Binding Account Request</p>
                    <p class="text-sm text-gray-600 mb-3">From: <span class="font-semibold">{{ $binding->admin->admin()->fullname }}</span></p>
                    <div class="flex space-x-2 mt-1">
                        <a href="/binding/accept/{{ $binding->id }}" class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">Accept</a>
                        <a href="/binding/deny/{{ $binding->id }}" class="px-4 py-2 text-sm bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">Decline</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex flex-col w-full max-h-screen relative">
            <nav class="bg-white w-full max-h-16 min-h-16 h-16 border-b flex place-items-center px-6">
                <img class="w-6 mr-4" src="{{ asset('assets/search.svg') }}" alt="Dashboard">
                <div class="group relative">
                    <input class="p-2 min-w-96 focus:outline-none" placeholder="Search" type="text" id="search">

                    <div class="hidden overflow-auto w-full max-h-96 z-50 p-3 bg-white shadow-lg rounded-b-lg absolute group-focus-within:block" id="namesContainer">

                    </div>
                </div>
                <div class="flex-1"></div>

                <div class="group relative">
                    <img class="w-5 mr-4" src="{{ asset('assets/notification.svg') }}" alt="Dashboard">

                    @php($alertCount = auth()->user()->alerts->where('is_read', '=', false)->count())
                    @if ($alertCount > 0)
                    <div class="w-3.5 h-3.5 flex place-items-center justify-center bg-red-500 absolute -top-0.5 -left-0.5 rounded-full">
                        <p class="text-white text-[8px] text-center font-semibold">{{ $alertCount }}</p>
                    </div>
                    @endif

                    <div class="absolute right-3 top-3 z-40 hidden border group-hover:block bg-white shadow-lg rounded-lg overflow-hidden min-w-80 ">
                        <div class="flex border-b p-3 place-items-center justify-between">
                            <p class="font-bold">Notifications</p>
                            <a class="flex place-items-center" href="/alert/seenall">
                                <span class="material-symbols-outlined" title="Mark all as read">
                                    mark_email_read
                                </span>
                            </a>
                        </div>

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

                        <a href="/settings" class="block py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="menu-item-3">
                            <div class="py-1 flex place-items-center" role="none">
                                <img class="block h-4 mx-4" src="{{ asset('assets/settings.svg') }}" alt="Profile">
                                Settings
                            </div>
                        </a>

                        <!-- Logout Link -->
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logoutModal').classList.remove('hidden');" class="block py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="menu-item-6">
                            <div class="py-1 flex place-items-center" role="none">
                                <img class="block w-4 mx-4" src="{{ asset('assets/logout.svg') }}" alt="Profile">
                                Logout
                            </div>
                        </a>

                        <!-- Logout Confirmation Modal -->
                        <div id="logoutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                <div class="mt-3 text-center">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Confirm Logout</h3>
                                    <div class="mt-2 px-7 py-3">
                                        <p class="text-sm text-gray-500">Are you sure you want to logout?</p>
                                    </div>
                                    <div class="flex justify-center gap-4 mt-3">
                                        <a href="/logout" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                                            Logout
                                        </a>
                                        <button onclick="document.getElementById('logoutModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php($linkedAccounts = App\Models\BoundAccount::query()->where('alumni_id', '=', Auth::user()->id)->get())
                        <div class="relative group">
                            <a href="/link-account" class="block py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="menu-item-7">
                                <div class="py-1 flex place-items-center justify-between" role="none">
                                    <div class="flex items-center">
                                        <img class="block h-4 mx-4" src="{{ asset('assets/switch_account.png') }}" alt="Link Account">
                                        Link Account
                                    </div>
                                    @if($linkedAccounts->count() !== 0)
                                    <span class="material-symbols-outlined text-gray-400 mr-2">chevron_right</span>
                                    @endif
                                </div>
                            </a>
                            @if($linkedAccounts->count() !== 0)
                            <div class="absolute left-0 -translate-x-full top-0 hidden group-hover:block bg-white shadow-lg rounded-md w-48 z-20">
                                <div class="py-1" role="none">
                                    <p class="px-4 py-2 text-xs font-medium text-gray-500">Linked Accounts</p>
                                    @foreach($linkedAccounts as $account)
                                    <form action="/switch-account/{{ $account->admin->id }}" method="POST" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">
                                            <div class="flex place-items-center">
                                                <img class="w-5 h-5 mr-3 rounded-full" src="{{ $account->admin->image() }}" alt="{{ $account->admin->name }}">
                                                {{ $account->admin->name }}
                                            </div>
                                        </button>
                                    </form>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>
            @yield('content')
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
                        setTimeout(() => { successTimer.style.width = '0%'; }, 100);
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
                        setTimeout(() => { failedTimer.style.width = '0%'; }, 100);
                        setTimeout(() => { 
                            const alert = failedTimer.closest('.animate-fade-in');
                            if (alert) alert.style.display = 'none';
                        }, 5100);
                    }
                });
            </script>
            @endif

            @if (session('error'))
            <div class="flex absolute top-20 left-2 z-40 bg-red-50 border-l-4 border-red-500 text-red-700 shadow-lg p-4 rounded-lg place-items-center">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 11c-.55 0-1-.45-1-1V8c0-.55.45-1 1-1s1 .45 1 1v4c0 .55-.45 1-1 1zm1 4h-2v-2h2v2z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h1 class="font-medium">{{ session('error') }}</h1>
                </div>
            </div>
            @endif
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        const search = document.querySelector('#search');
        const namesContainer = document.querySelector('#namesContainer');

        const users = [
            <?php

            use App\Models\User;
            use Illuminate\Support\Facades\Auth;

            foreach (User::all() as $user) {
                if ($user->id == Auth::user()->id) {
                    continue;
                }

                echo "{value: `" . $user->id . "`, label: `" . $user->name . "`},";
            }
            ?>
        ];

        function putIntoNames(arr) {
            namesContainer.innerHTML = '';

            arr.forEach((element) => {
                const a = document.createElement('a');
                const li = document.createElement('li');

                a.setAttribute('href', <?php echo (Auth::user()->role == 'Admin' ? '"/admin/chat"' : '"/alumni/chat"') ?> + '?initiate=' + element.value);
                li.classList.add('list-none', 'px-2', 'py-4', 'hover:bg-gray-100');

                li.innerHTML = element.label;
                a.appendChild(li);
                namesContainer.appendChild(a);
            });
        }

        putIntoNames(users);

        search.addEventListener('input', e => {
            const arr = users.filter((element) => {
                return element.label.toLowerCase().includes(e.target.value.toLowerCase());
            });

            putIntoNames(arr);
        });
    </script>
    <script>
        const alertContainer = document.querySelector('#alertContainer');
        const chatAlerts = document.querySelector('#chatAlerts');

        (function() {
            async function repeat() {
                const [alerts, message] = await Promise.all([fetch('/alert/gen'), fetch('/alert/messages')]);

                alertContainer.innerHTML = await alerts.text();

                if (!window.location.pathname.startsWith('/alumni/chat')) {
                    chatAlerts.textContent = await message.text();
                }
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