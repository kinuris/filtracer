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
            <a href="/account" class="text-black font-semibold rounded p-2 mr-4 hover:bg-gray-200 hover:text-blue-600 {{ request()->is('account') && !request('mode') ? 'bg-gray-200 text-blue-600' : '' }}">User Accounts</a>
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

                <select class="pr-1 font-thin text-gray-500" name="user_role" id="user_role">
                    @php($role = request('user_role') ?? '')
                    <option {{ $role == 'Alumni' ? 'selected' : '' }} value="Alumni">Alumni</option>
                    <option {{ $role == 'Admin' ? 'selected' : '' }} value="Admin">Admin</option>
                </select>

                <button type="submit" class="bg-blue-600 text-white rounded p-2 ml-6">Filter</button>
            </div>
        </form>

        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <th class="font-thin py-3">ID</th>
                <th class="font-thin">Name</th>
                <th class="font-thin">Student/Company ID</th>
                <th class="font-thin">Role</th>
                <th class="font-thin">Date Registered</th>
                @if (request('mode') === 'generated')
                <th class="font-thin">Username</th>
                <th class="font-thin">Password</th>
                @endif
                <th class="font-thin">Action</th>
            </thead>
            <tbody class="bg-white text-center">
                @foreach ($users as $user)
                <tr class="border-b">
                    <td class="text-blue-900 py-4">{{ $user->id }}</td>
                    @if (request('mode') === 'generated')
                    @if ($user->role === 'Admin')
                    <td class="text-blue-900">{{ $user->adminRelation->fullname }}</td>
                    @else
                    <td class="text-blue-900">{{ $user->partialPersonal->fullname }}</td>
                    @endif
                    @else
                    <td class="text-blue-900">{{ $user->name }}</td>
                    @endif
                    @if ($user->role === 'Admin')
                    <td>{{ $user->admin()->position_id }}</td>
                    @else
                    @if (request('mode') === 'generated')
                    <td>{{ $user->partialPersonal->student_id }}</td>
                    @else
                    <td>{{ $user->personalBio->student_id }}</td>
                    @endif
                    @endif
                    <td>{{ $user->role }}</td>
                    <td>{{ date_create($user->created_at)->format('Y-m-d') }}</td>
                    @if (request('mode') === 'generated')
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->adminGenerated->default_password }}</td>
                    @endif
                    <td>
                        <div class="flex justify-center w-full place-items-center">
                            @php ($personal = $user->getPersonalBio())
                            @if ($personal !== null && $personal->status == 1)
                            <button data-user-id="{{ $user->id }}" class="openUnverifyModal w-6"><img class="w-5" src="{{ asset('/assets/verified_user.svg') }}" alt="Verified"></button>
                            @elseif ($personal !== null && $personal->status == 0)
                            <button data-user-id="{{ $user->id }}" class="openVerifyModal w-6"><img class="w-5" src="{{ asset('/assets/unverified_user.svg') }}" alt="Verified"></button>
                            @elseif ($user->role === 'Admin' && !$user->admin()->is_verified)
                            <button data-user-id="{{ $user->id }}" class="openVerifyModal w-6"><img class="w-5" src="{{ asset('/assets/unverified_user.svg') }}" alt="Verified"></button>
                            @elseif ($user->role === 'Admin' && $user->admin()->is_verified)
                            <button data-user-id="{{ $user->id }}" class="openUnverifyModal w-6"><img class="w-5" src="{{ asset('/assets/verified_user.svg') }}" alt="Verified"></button>
                            @endif
                            @if (request('mode') !== 'generated')
                            @if ($user->role !== 'Admin')
                            <a class="mx-3" href="/user/view/{{ $user->id }}"><img class="w-6" src="{{ asset('assets/view.svg') }}" alt="View"></a>
                            @else
                            <a class="mx-3"><img class="w-6 opacity-0 pointer-events-none" src="{{ asset('assets/view.svg') }}" alt="View"></a>
                            @endif
                            @endif
                            <a href="/user/delete/{{ $user->id }}"><img src="{{ asset('assets/trash.svg') }}" alt="Trash"></a>
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