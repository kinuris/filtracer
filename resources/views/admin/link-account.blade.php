@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 overflow-auto max-h-[calc(100vh-64px)]">
    <h1 class="font-medium tracking-widest text-lg mb-4">Link Accounts</h1>

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
                @php($bindingRequest = $user->hasActiveBindingRequestWith(Auth::user()))
                @php($isAlumniLinked = $user->isAlumniLinkedWith(Auth::user()))
                @php($otherLinks = App\Models\BoundAccount::query()->where('alumni_id', '=', $user->id)->get())
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
                    <td>{{ ($user->personalBio ?? $user->partialPersonal)->student_id }}</td>
                    @endif
                    @endif
                    <td>{{ $user->role }}</td>
                    <td>{{ date_create($user->created_at)->format('Y-m-d') }}</td>
                    @if (request('mode') === 'generated')
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->adminGenerated->default_password }}</td>
                    @endif
                    <td class="py-2">
                        @if ($otherLinks->count() > 0)
                        <div class="tooltip relative group inline-block">
                            <span class="inline-flex items-center px-2 py-1 bg-orange-50 text-orange-700 text-xs font-medium rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Linked ({{ $otherLinks->count() }})
                            </span>
                            <div class="tooltip-content invisible group-hover:visible absolute z-10 w-48 bg-white rounded-md shadow-lg p-2 text-xs text-left mt-1">
                                <p class="font-semibold mb-1">Linked to:</p>
                                @foreach($otherLinks as $link)
                                    <p class="text-gray-700">{{ $link->admin->name ?? 'Admin' }} (ID: {{ $link->admin_id }})</p>
                                @endforeach
                            </div>
                        </div>
                        @elseif ($bindingRequest->count() > 0)
                        <form action="{{ route('link.delete', $user->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-150 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Unlink
                            </button>
                        </form>
                        @elseif ($isAlumniLinked->count() < 1)
                        <form action="{{ route('link.create') }}" method="POST" class="inline">
                            <input type="hidden" name="alumni_id" value="{{ $user->id }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-150 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                Link
                            </button>
                        </form>
                        @else
                        <span class="inline-flex items-center px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Linked
                        </span>
                        @endif
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