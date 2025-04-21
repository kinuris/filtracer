@extends('layouts.admin')

@section('title', 'User Accounts')

@section('content')
@include('components.verify-modal')
@include('components.unverify-modal')
<div class="bg-gray-100 w-full h-full p-8 overflow-auto max-h-[calc(100vh-64px)]">
    <h1 class="font-medium tracking-widest text-lg mb-4">User Accounts</h1>

    @if (Auth::user()->admin()->is_super)
    <div class="shadow rounded-lg p-4 mb-4 bg-white">
        <div class="flex">
            <a href="/account" class="text-black font-semibold rounded p-2 mr-4 hover:bg-gray-200 hover:text-blue-600 {{ request()->is('account') && !request('mode') ? 'bg-gray-200 text-blue-600' : '' }}">All Accounts</a>
            <a href="{{ url()->current() . '?mode=generated' }}" class="text-black font-semibold rounded p-2 mr-4 hover:bg-gray-200 hover:text-blue-600 {{ request('mode') == 'generated' ? 'bg-gray-200 text-blue-600' : '' }}">Generated Accounts</a>
            <!-- <a href="/account/verify" class="text-black font-semibold rounded p-2 hover:bg-gray-200 hover:text-blue-600">Verify Account</a> -->

            <div class="flex-1"></div>

            <a href="/account/create-individual" class="bg-blue-600 text-white rounded p-2 mr-4 hover:bg-blue-700">Create Individual Account</a>
            <a href="/account/create-bulk" class="bg-blue-600 text-white rounded p-2 hover:bg-blue-700">Create Bulk Accounts</a>
        </div>
    </div>
    @endif

    @php($status = request('user_status') ?? -1)
    <div class="shadow rounded-lg">
        <form action="/fullurl/{{ base64_encode(urlencode(request()->fullUrl())) }}">
            <div class="bg-white py-4 flex items-center px-6 border-b rounded-t-lg">
                <input value="{{ request('search') ?? '' }}" class="bg-gray-100 px-2 py-1 rounded border min-w-[max(33%,270px)]" placeholder="Search..." type="text" name="search" id="search">

                <div class="flex-1"></div>

                <select class="pr-1 mr-8 font-thin text-gray-500" name="user_status" id="user_status">
                    <option {{ $status == -1 ? 'selected' : '' }} value="-1">All Accounts</option>
                    <option {{ $status == 0 ? 'selected' : '' }} value="0">Unverified</option>
                    <option {{ $status == 1 ? 'selected' : '' }} value="1">Verified</option>
                </select>

                @if (Auth::user()->admin()->is_super)
                <select class="pr-1 font-thin text-gray-500" name="user_role" id="user_role">
                    @php($role = request('user_role') ?? '')
                    <option {{ $role == 'Alumni' ? 'selected' : '' }} value="Alumni">Alumni</option>
                    <option {{ $role == 'Admin' ? 'selected' : '' }} value="Admin">Admin</option>
                </select>
                @endif

                <button type="submit" class="bg-blue-600 text-white rounded p-2 ml-6">Filter</button>
            </div>
        </form>

        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <th class="font-thin py-3">ID</th>
                <th class="font-thin">Name</th>
                <th class="font-thin">Student/Employee No.</th>
                <th class="font-thin">Role</th>
                <th class="font-thin">Date Registered</th>
                @if (request('mode') === 'generated')
                <th class="font-thin">Password</th>
                <th class="font-thin">Username</th>
                @endif
                <th class="font-thin">Action</th>
            </thead>
            <tbody class="bg-white text-center">
                @foreach ($users as $user)
                <tr class="border-b">
                    <td class="text-blue-900 py-4">{{ $user->id }}</td>
                    @if (request('mode') === 'generated')
                    @if ($user->role === 'Admin')
                    <td class="text-blue-900">{{ $user->adminRelation?->fullname }}</td>
                    @else
                    <td class="text-blue-900">{{ $user->partialPersonal?->fullname }}</td>
                    @endif
                    @else
                    <td class="text-blue-900">{{ $user->name }}</td>
                    @endif
                    @if ($user->role === 'Admin')
                    <td>{{ $user->admin()?->position_id }}</td>
                    @else
                    @if (request('mode') === 'generated')
                    <td>{{ $user->partialPersonal?->student_id }}</td>
                    @else
                    <td>{{ ($user->personalBio ?? $user->partialPersonal)?->student_id }}</td>
                    @endif
                    @endif
                    <td>{{ $user->role }}</td>
                    <td>{{ date_create($user->created_at)->format('Y-m-d') }}</td>
                    @if (request('mode') === 'generated')
                    <td>
                        <span class="blur-sm hover:blur-none transition-all duration-200 cursor-pointer"
                            onclick="copyToClipboard('{{ $user->adminGenerated?->default_password }}', this)">
                            {{ $user->adminGenerated?->default_password }}
                        </span>
                    </td>
                    <td>{{ $user->username }}</td>
                    @endif
                    <td>
                        <div class="flex justify-center w-full place-items-center">
                            @php ($personal = $user->getPersonalBio())
                            @if ($personal !== null && $personal->status == 1)
                            <button data-user-id="{{ $user->id }}" class="openUnverifyModal w-6"><img class="w-5" src="{{ asset('/assets/verified_user.svg') }}" alt="Verified"></button>
                            @elseif ($personal !== null && $personal->status == 0)
                            <button data-user-id="{{ $user->id }}" class="openVerifyModal w-6"><img class="w-5" src="{{ asset('/assets/unverified_user.svg') }}" alt="Verified"></button>
                            @elseif ($user->role === 'Admin' && !$user->admin()?->is_verified)
                            <button data-user-id="{{ $user->id }}" class="openVerifyModal w-6"><img class="w-5" src="{{ asset('/assets/unverified_user.svg') }}" alt="Verified"></button>
                            @elseif ($user->role === 'Admin' && $user->admin()?->is_verified)
                            <button data-user-id="{{ $user->id }}" class="openUnverifyModal w-6"><img class="w-5" src="{{ asset('/assets/verified_user.svg') }}" alt="Verified"></button>
                            @else
                            <div class="w-5"></div>
                            @endif
                            @if (request('mode') !== 'generated')
                            @if ($user->role !== 'Admin')
                            <a class="mx-3" href="/user/view/{{ $user->id }}"><img class="w-6" src="{{ asset('assets/view.svg') }}" alt="View"></a>
                            @else
                            <a class="mx-3"><img class="w-6 opacity-0 pointer-events-none" src="{{ asset('assets/view.svg') }}" alt="View"></a>
                            @endif
                            @endif
                            @if (request('mode') === 'generated')
                            <form method="POST" action="{{ route('sendsms.individual', $user->id) }}" class="inline">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <button type="submit" class="relative group mx-3 mt-1.5 border-0 bg-transparent cursor-pointer p-0">
                                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span class="absolute bottom-full -left-20 transform -translate-x-1/2 bg-black text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                                        Click this button to send <br>the credentials of the user via SMS<br> Number: {{ $user->role === 'Admin' ? $user->admin()?->phone_number : $user->partialPersonal?->phone_number }}
                                    </span>
                                </button>
                            </form>
                            @endif
                            <button type="button" onclick="document.getElementById('deleteModal{{ $user->id }}').classList.remove('hidden')" class="border-0 bg-transparent cursor-pointer p-0">
                                <img src="{{ asset('assets/trash.svg') }}" alt="Trash">
                            </button>

                            <!-- Delete Confirmation Modal -->
                            <div id="deleteModal{{ $user->id }}" class="hidden fixed inset-0 z-50">
                                <div class="absolute inset-0 bg-black opacity-60 transition-opacity"></div>
                                <div class="absolute inset-0 flex items-center justify-center p-4">
                                    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all">
                                        <div class="border-b px-6 py-4 flex items-center">
                                            <div class="bg-red-100 p-2 rounded-full mr-3">
                                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-semibold text-gray-900">Confirm Deletion</h3>
                                        </div>
                                        <div class="px-6 py-4">
                                            <p class="text-gray-600">Are you sure you want to delete this alumni record? This action cannot be undone.</p>
                                        </div>
                                        <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                                            <button onclick="document.getElementById('deleteModal{{ $user->id }}').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm">
                                                Cancel
                                            </button>
                                            <a href="/user/delete/{{ $user->id }}" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm">
                                                Delete Record
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="bg-white rounded-b-lg p-3">
            {{ $users->appends(request()->except(['verify_modal']))->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function copyToClipboard(text, element) {
        navigator.clipboard.writeText(text)
            .then(() => {
                // Provide user feedback (e.g., change text, add a tooltip)
                element.innerText = 'Copied!';
                setTimeout(() => {
                    element.innerText = text; // Revert after a delay
                }, 700);
            })
            .catch(err => {
                console.error('Failed to copy text: ', err);
                // Optionally, provide user feedback for failure
                element.innerText = 'Copy Failed';
            });
    }
</script>
<script>
    const verifyModal = document.getElementById('verifyModal');
    const unverifyModal = document.getElementById('unverifyModal');

    const closeVerifyModal = document.getElementById('closeVerifyModal');
    const closeUnverifyModal = document.getElementById('closeUnverifyModal');

    const params = new URLSearchParams(window.location.search);

    const user = params.get('verify_modal');
    const userUnverify = params.get('unverify_modal');

    if (user) {
        verifyModal.classList.remove('hidden');
    }

    if (userUnverify) {
        unverifyModal.classList.remove('hidden');
    }

    if (closeVerifyModal) {
        closeVerifyModal.addEventListener('click', () => {
            verifyModal.classList.add('hidden');
        });
    }

    if (closeUnverifyModal) {
        closeUnverifyModal.addEventListener('click', () => {
            unverifyModal.classList.add('hidden');
        });
    }

    window.addEventListener('click', (e) => {
        if (e.target === verifyModal) {
            verifyModal.classList.add('hidden');
        }

        if (e.target === unverifyModal) {
            unverifyModal.classList.add('hidden');
        }
    })
</script>
<script>
    for (let button of document.querySelectorAll('.openVerifyModal')) {
        button.addEventListener('click', () => {
            const user = button.getAttribute('data-user-id');

            const params = new URLSearchParams(window.location.search);
            if (params.has('unverify_modal')) {
                params.delete('unverify_modal');
            }

            if (params.has('verify_modal')) {
                params.delete('verify_modal');
            }

            params.append('verify_modal', user);

            window.location.replace('/account?' + params.toString());
        });
    }

    for (let button of document.querySelectorAll('.openUnverifyModal')) {
        button.addEventListener('click', () => {
            const user = button.getAttribute('data-user-id');

            const params = new URLSearchParams(window.location.search);
            if (params.has('verify_modal')) {
                params.delete('verify_modal');
            }

            if (params.has('unverify_modal')) {
                params.delete('unverify_modal');
            }

            params.append('unverify_modal', user);

            window.location.replace('/account?' + params.toString());
        });
    }
</script>
@endsection