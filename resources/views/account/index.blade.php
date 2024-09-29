@extends('layouts.admin')

@section('content')
@include('components.verify-modal')
<div class="bg-gray-100 w-full h-full p-8">
    <h1 class="font-medium tracking-widest text-lg mb-6">User Accounts</h1>

    @php($status = request('user_status') ?? -1)
    <div class="shadow rounded-lg">
        <form action="">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-t-lg justify-between">
                <input value="{{ request('search') ?? '' }}" class="bg-gray-100 px-2 py-1 rounded border min-w-[max(33%,270px)]" placeholder="Search..." type="text" name="search" id="search">
                <select class="pr-4 font-thin text-gray-500" name="user_status" id="user_status">
                    <option {{ $status == -1 ? 'selected' : '' }} value="-1">All Users</option>
                    <option {{ $status == 0 ? 'selected' : '' }} value="0">Unverified</option>
                    <option {{ $status == 1 ? 'selected' : '' }} value="1">Verified</option>
                </select>
            </div>
        </form>
        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <th class="py-4 font-thin">ID</th>
                <th class="font-thin">Name</th>
                <th class="font-thin">Username</th>
                <th class="font-thin">Role</th>
                <th class="font-thin">Date Registered</th>
                <th class="font-thin">Action</th>
            </thead>
            <tbody class="bg-white text-center">
                @foreach ($users as $user)
                <tr class="border-b">
                    <td class="text-blue-900 py-4">{{ $user->id }}</td>
                    <td class="text-blue-900">{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ date_create($user->created_at)->format('Y-m-d') }}</td>
                    <td>
                        <div class="flex justify-start max-w-24 place-items-center">
                            @php ($personal = $user->getPersonalBio())
                            @if ($personal !== null && $personal->status == 1)
                            <a href="" class="w-6"><img class="w-5" src="{{ asset('/assets/verified_user.svg') }}" alt="Verified"></a>
                            @elseif ($personal !== null && $personal->status == 0)
                            <button data-user-id="{{ $user->id }}" class="openVerifyModal w-6"><img src="{{ asset('/assets/unverified_user.svg') }}" alt="Verified"></button>
                            @endif
                            @if ($user->role !== 'Admin')
                            <a class="mx-3" href="/user/view/{{ $user->id }}"><img class="w-6" src="{{ asset('assets/view.svg') }}" alt="View"></a>
                            @else
                            <a class="mx-3"><img class="w-6 opacity-0 pointer-events-none" src="{{ asset('assets/view.svg') }}" alt="View"></a>
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
    const closeVerifyModal = document.getElementById('closeVerifyModal');

    const params = new URLSearchParams(window.location.search);
    const user = params.get('verify_modal');

    if (user) {
        verifyModal.classList.remove('hidden');
    }

    closeVerifyModal.addEventListener('click', () => {
        verifyModal.classList.add('hidden');
    });

    window.addEventListener('click', (e) => {
        if (e.target === verifyModal) {
            verifyModal.classList.add('hidden');
        }
    })
</script>
<script>
    for (let button of document.querySelectorAll('.openVerifyModal')) {
        button.addEventListener('click', () => {
            const user = button.getAttribute('data-user-id');

            const params = new URLSearchParams(window.location.search);
            if (params.has('verify_modal')) {
                params.delete('verify_modal');
            }

            params.append('verify_modal', user);

            window.location.replace('/account?' + params.toString());
        });
    }
</script>
@endsection