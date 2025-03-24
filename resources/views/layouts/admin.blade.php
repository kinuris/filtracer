<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href=" https://printjs-4de6.kxcdn.com/print.min.css">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.svg') }}" type="image/x-icon">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    @vite('resources/css/app.css')
    <title>@yield('title')</title>
</head>

<body>
    <div class="flex w-screen">
        @if (request()->path() !== 'admin/chat')
        <a href="/admin/chat" class="fixed rounded-full bottom-0 z-50 right-0 mb-6 mr-6 shadow-lg bg-blue-600 transition-transform hover:scale-110">
            <div class="w-5 h-5 flex place-items-center justify-center bg-red-500 absolute -top-1 -left-1 rounded-full">
                <p class="text-white text-xs text-center font-semibold" id="chatAlerts"></p>
            </div>
            <img class="w-8 m-3" src="{{ asset('assets/chat.svg') }}" alt="Chat">
        </a>
        @endif
        <div class="bg-white border-r h-screen max-h-screen min-w-[max(20%,300px)] px-6 pb-2 flex flex-col">
            <img class="h-fit w-44" src="{{ asset('assets/filtracer_nolabel.svg') }}" alt="Filtracer Logo">

            <a href="/admin">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/dashboard.svg') }}" alt="Dashboard">
                    Dashboard
                </div>
            </a>

            <!-- <a href="/department">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/departments.svg') }}" alt="Dashboard">
                    Departments
                </div>
            </a> -->

            <a href="/account">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/accounts.svg') }}" alt="Dashboard">
                    Accounts
                </div>
            </a>

            <a href="/admin/chat">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/chat_gray.svg') }}" alt="Chat">
                    Chats
                </div>
            </a>

            <a href="/admin/post">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/posts.svg') }}" alt="Dashboard">
                    Posts
                </div>
            </a>

            <a href="/audit">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/audit.svg') }}" alt="Dashboard">
                    Audit Trail
                </div>
            </a>

            @if (Auth::user()->admin()->is_super)
            <a href="/backup">
                <div class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/backup.svg') }}" alt="Dashboard">
                    Backup
                </div>
            </a>
            @endif

            <div class="relative group">
                <a href="#" class="hover:bg-gray-100 font-medium tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-7 mr-4" src="{{ asset('assets/reports.svg') }}" alt="Dashboard">
                    Reports
                </a>
                <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg">
                    <a href="/report/graphical" class="block px-4 py-2 hover:bg-gray-100">Graphical Report</a>
                    <a href="/report/statistical" class="block px-4 py-2 hover:bg-gray-100">Statistical Report</a>
                </div>
            </div>

            <div class="flex-1"></div>

            <a href="/admin/settings">
                <div class="hover:bg-gray-100 tracking-wide hover:text-blue-500 rounded-lg p-3 flex place-items-center">
                    <img class="w-6 mr-4" src="{{ asset('assets/settings.svg') }}" alt="Dashboard">
                    Settings
                </div>
            </a>
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

                    <div class="absolute right-3 top-3 z-40 hidden border group-hover:block bg-white shadow-lg rounded-lg overflow-hidden min-w-80">
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
                        <a href="/admin/profile" class="block py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="menu-item-1">
                            <div class="py-1 flex place-items-center" role="none">
                                <img class="block h-4 mx-4" src="{{ asset('assets/profile.svg') }}" alt="Profile">
                                My Profile
                            </div>
                        </a>

                        <a href="/settings/account" class="block py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="menu-item-3">
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

                        @php($linkedAccounts = App\Models\BoundAccount::query()->where('admin_id', '=', Auth::user()->id)->get())
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
                                    <form action="/switch-account/{{ $account->alumni->id }}" method="POST" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">
                                            <div class="flex place-items-center">
                                                <img class="w-5 h-5 mr-3 rounded-full" src="{{ $account->alumni->image() }}" alt="{{ $account->alumni->name }}">
                                                {{ $account->alumni->name }}
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
            @if (session('message'))
            <div class="flex absolute bottom-20 w-64 -left-[282px] z-40 bg-white shadow-lg p-4 rounded-lg place-items-center">
                <img class="w-8 mr-3" src="{{ asset('assets/success.svg') }}" alt="Success">
                <h1 class="text-sm">{{ session('message') }}</h1>
            </div>
            @endif
            @yield('content')
        </div>
    </div>
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
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

            if (arr.length === 0) {
                const emptyState = document.createElement('div');
                emptyState.classList.add('text-center', 'py-4', 'text-gray-500');
                emptyState.textContent = 'No results found';
                namesContainer.appendChild(emptyState);
                return;
            }

            arr.forEach((element) => {
                const a = document.createElement('a');
                const li = document.createElement('div');
                const nameSpan = document.createElement('span');
                const iconSpan = document.createElement('span');

                a.href = '/user/view/' + element.value;
                a.classList.add('block', 'transition-colors');

                li.classList.add('px-3', 'py-2', 'rounded', 'hover:bg-gray-100', 'flex', 'justify-between', 'items-center');

                nameSpan.classList.add('font-medium');
                nameSpan.textContent = element.label;

                iconSpan.classList.add('material-symbols-outlined', 'text-gray-400', 'text-sm');
                iconSpan.textContent = 'arrow_forward';

                li.appendChild(nameSpan);
                li.appendChild(iconSpan);
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
                chatAlerts.textContent = await message.text();
            }

            repeat();

            setInterval(repeat, 1000);
        })()
    </script>
    @yield('script')
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
</body>

</html>