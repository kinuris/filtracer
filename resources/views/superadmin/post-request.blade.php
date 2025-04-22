@extends('layouts.admin')

@section('title', 'Post Request')

@section('content')
@if(request('view_post_modal'))
@php
$post = $posts->where('id', request('view_post_modal'))->first();
@endphp
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-1/3 min-w-[400px] max-h-[90vh] overflow-auto">
        <div class="p-4 flex items-center">
            <h2 class="font-semibold">Post Details</h2>

            <div class="flex-1"></div>

            <p class="text-xs">{{ $post->created_at->format('Y-m-d') }}</p>
        </div>
        <hr>
        <div class="p-4 overflow-y-auto max-h-[calc(90vh - 6rem)]">
            <div class="mb-4">
                <label class="block font-medium text-sm">Title</label>
                <input type="text" value="{{ $post->title }}" class="bg-gray-100 border rounded p-2 w-full" readonly>
            </div>
            <div class="mb-4">
                <label class="block font-medium text-sm">Content</label>
                <textarea class="bg-gray-100 border rounded p-2 w-full" readonly>{{ $post->content }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block font-medium text-sm">Source</label>
                <input type="text" value="{{ $post->source }}" class="bg-gray-100 border rounded p-2 w-full" readonly>
            </div>
            <div class="mb-4 flex space-x-4">
                <div class="w-1/2">
                    <label class="block font-medium text-sm">Post Category</label>
                    <input type="text" value="{{ $post->post_category }}" class="bg-gray-100 border rounded p-2 w-full" readonly>
                </div>
                <div class="w-1/2">
                    <label class="block font-medium text-sm">Status</label>
                    <input type="text" value="{{ ucfirst($post->post_status) }}" class="bg-gray-100 border rounded p-2 w-full" readonly>
                </div>
            </div>

            @if($post->attached_image)
            <div class="mb-4">
                <label class="block font-medium text-sm">Image</label>
                <img src="{{ $post->image() }}" alt="Post Image" class="w-full h-auto rounded">
            </div>
            @endif

            <div class="flex justify-end gap-2 mt-8">
                <a href="{{ request()->fullUrlWithoutQuery('view_post_modal') }}" class="border border-blue-600 text-blue-600 bg-white px-4 py-2 rounded">Close</a>
                <div class="flex-1"></div>
                <!-- <a href="/post/changestat/{{ $post->id }}?status=Denied" class="border border-blue-600 text-blue-600 bg-white px-4 py-2 rounded">Deny</a>
                <a href="/post/changestat/{{ $post->id }}?status=Approved" class="bg-blue-600 text-white px-4 py-2 rounded">Approve</a> -->
            </div>
        </div>
    </div>
</div>
@endif

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
                            @if($post->status != 'Approved')
                            <a href="/post/changestat/{{ $post->id }}?status=Approved">
                                <img src="{{ asset('assets/approve.svg') }}" alt="Approve" class="w-5 h-5 inline">
                            </a>
                            @endif
                            <a href="/post/changestat/{{ $post->id }}?status=Denied">
                                <img src="{{ asset('assets/deny.svg') }}" alt="Deny" class="w-5 h-5 inline">
                            </a>
                            <a href="?status={{ request('status') }}&view_post_modal={{ $post->id }}">
                                <img src="{{ asset('assets/view.svg') }}" alt="View" class="w-6 h-6 inline">
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="bg-white rounded-b-lg p-3">
            {{ $posts->appends(request()->except(['view_post_mdoal']))->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection