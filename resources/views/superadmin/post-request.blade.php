@extends('layouts.admin')

@section('title', 'Post Request')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)] overflow-auto">
    <h1 class="font-medium tracking-widest text-lg mb-4" id="title">Post Requests</h1>

    <div class="shadow rounded-lg mt-4">
        <form action="">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
                <p class="font-semibold">Request Status</p>
                <select name="request_status" class="ml-4 p-2 border rounded w-36" onchange="location = this.value;">
                    <option value="?status=pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="?status=approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="?status=denied" {{ request('status') == 'denied' ? 'selected' : '' }}>Denied</option>
                </select>

                <div class="flex-1"></div>

                <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded mr-2" onclick="approveAll()">Approve All</button>
                <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded" onclick="denyAll()">Deny All</button>
            </div>
        </form>
    </div>

    <div class="shadow rounded-lg mt-4">
        <form action="/fullurl/{{ base64_encode(urlencode(request()->fullUrl())) }}">
            <div class="bg-white py-4 flex items-center px-6 border-b rounded-t-lg">
                <input type="text" name="search" placeholder="Search..." class="p-2 border rounded w-64" value="{{ request('search') }}">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded ml-2">Filter</button>
            </div>
        </form>

        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <tr>
                    <th class="py-2 px-4 font-thin">ID</th>
                    <th class="py-2 px-4 font-thin">Title</th>
                    <th class="py-2 px-4 font-thin">Author</th>
                    <th class="py-2 px-4 font-thin">Role</th>
                    <th class="py-2 px-4 font-thin">Post Category</th>
                    <th class="py-2 px-4 font-thin">Status</th>
                    <th class="py-2 px-4 font-thin">Date Submitted</th>
                    <th class="py-2 px-4 font-thin">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white text-center">
                @foreach($posts as $post)
                <tr class="border-b">
                    <td class="py-2 px-4">{{ $post->id }}</td>
                    <td class="py-2 px-4">{{ $post->title }}</td>
                    <td class="py-2 px-4">{{ $post->creator->name }}</td>
                    <td class="py-2 px-4">{{ $post->creator->role }}</td>
                    <td class="py-2 px-4">{{ $post->post_category }}</td>
                    <td class="py-2 px-4">
                        <span class="{{ $post->status == 'Pending' ? 'text-yellow-500' : ($post->status == 'Denied' ? 'text-red-500' : 'text-green-500') }}">
                            {{ ucfirst($post->status) }}
                        </span>
                    </td>
                    <td class="py-2 px-4">{{ $post->created_at->format('Y-m-d') }}</td>
                    <td class="py-2 px-4">
                        <div class="flex justify-center items-center space-x-2.5">
                            <a href="/post/changestat/{{ $post->id }}?status=Approved">
                                <img src="{{ asset('assets/approve.svg') }}" alt="Approve" class="w-5 h-5 inline">
                            </a>
                            <a href="/post/changestat/{{ $post->id }}?status=Denied">
                                <img src="{{ asset('assets/deny.svg') }}" alt="Deny" class="w-5 h-5 inline">
                            </a>
                            <button class="bg-transparent border-none p-0">
                                <img src="{{ asset('assets/view.svg') }}" alt="View" class="w-6 h-6 inline">
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="bg-white rounded-b-lg p-3">
        </div>
    </div>
</div>
@endsection