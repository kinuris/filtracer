@extends('layouts.admin')

@section('title', 'Posts')

@section('content')
@include('components.add-post-modal')

@if (request('edit_post'))
@include('components.edit-post-modal')
@endif

@php($title = request('category', 'All Posts'))
@php($categories = [
'All Posts',
'Events',
'Job Openings',
'Announcements',
'Pinned Posts',
'Saved Posts',
'Your Posts',
])
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    <h1 class="font-medium tracking-widest text-lg" id="title">{{ $title }}</h1>

    <div class="shadow rounded-lg my-4">
        <form action="">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
                <label class="font-medium" for="category">Category</label>
                <select class="p-2 border rounded-lg ml-4" name="category" id="category">
                    @foreach ($categories as $category)
                    <option {{ request('category') == $category ? 'selected' : '' }} value="{{ $category }}">{{ $category === 'Your Posts' ? 'My Posts' : $category }}</option>
                    @endforeach
                </select>

                <div class="flex-1"></div>

                <button type="button" class="rounded-lg p-2 px-3 bg-blue-600 text-white" id="openAddPostModal">Add New Post</button>
                <a href="?category=Your+Posts" class="rounded-lg p-2 px-3 bg-blue-600 text-white ml-3">View My Posts</a>
                @if (Auth::user()->role === 'Admin' && Auth::user()->admin()->is_super)
                <a href="/post/request" class="rounded-lg p-2 px-3 bg-blue-600 text-white ml-3">Post Requests</a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 gap-4 overflow-auto">
        @foreach ($posts as $post)
        <div class="shadow rounded-lg mb-4">
            <div class="bg-white py-4 flex flex-col px-6 border-b rounded-lg h-full">
                <div class="flex">
                    <img class="w-10 h-10 rounded-full object-cover" src="{{ $post->creator->image() }}" alt="Poster Profile">
                    <div class="flex flex-col ml-3">
                        @if (request('category') == 'Your Posts')
                        <p>{{ $post->creator->name }} - 
                            <span class="text-gray-400" style="color: 
                                @if($post->status == 'Denied') red 
                                @elseif($post->status == 'Approved') green 
                                @elseif($post->status == 'Pending') yellow 
                                @endif">
                                {{ $post->status }}
                            </span>
                        </p>
                        @else
                        <p>{{ $post->creator->name }}</p>
                        @endif
                        <div class="flex place-items-center">
                            <p class="text-xs text-gray-400">{{ $post->creator->role === 'Admin' ? ($post->creator->admin()->is_super ? 'Superadmin' : 'Admin' ) : 'Alumni'  }}</p>
                            @if ($post->post_category === 'Event')
                            <img src="{{ asset('assets/calendar.svg') }}" class="inline w-3 h-3 ml-1" alt="Event Post">
                            @elseif ($post->post_category === 'Job Opening')
                            <img src="{{ asset('assets/job_opening.svg') }}" class="inline w-3 h-3 ml-1" alt="Job Opening Post">
                            @elseif ($post->post_category === 'Announcement')
                            <img src="{{ asset('assets/announcement.svg') }}" class="inline w-3 h-3 ml-1" alt="Announcement Post">
                            @endif
                        </div>
                    </div>

                    <div class="group relative ml-auto my-auto">
                        <img src="{{ asset('assets/option.svg') }}" alt="Option">

                        <div class="w-48 right-0 absolute hidden group-hover:block font-light text-sm bg-white shadow-lg rounded-lg overflow-hidden">
                            <a href="/post/pin/toggle/{{ $post->id }}" class="pl-4 flex p-2 group hover:bg-gray-100 cursor-pointer">
                                @if ($post->isPinnedBy(auth()->user()))
                                <img class="w-5" src="{{ asset('assets/post_unpin.svg') }}" alt="Pin Post">
                                <button class="text-left w-full block px-4 py-2 hover:bg-gray-100">Unpin Post</button>
                                @else
                                <img class="w-5" src="{{ asset('assets/post_pin.svg') }}" alt="Pin Post">
                                <button class="text-left w-full block px-4 py-2 hover:bg-gray-100">Pin Post</button>
                                @endif
                            </a>

                            <a href="/post/save/toggle/{{ $post->id }}" class="pl-4 flex place-items-center p-2 group hover:bg-gray-100 cursor-pointer">
                                <img class="h-4 w-5" src="{{ asset('assets/post_save.svg') }}" alt="Save Post">
                                @if ($post->isSavedBy(auth()->user()))
                                <button class="text-left w-full block px-4 py-2 hover:bg-gray-100">Unsave Post</button>
                                @else
                                <button class="text-left w-full block px-4 py-2 hover:bg-gray-100">Save Post</button>
                                @endif
                            </a>

                            @if (Auth::user()->role === 'Admin' && Auth::user()->admin()->is_super)
                            <form action="/post/delete/{{ $post->id }}" method="POST" class="pl-4 flex p-2 group place-items-center hover:bg-gray-100 cursor-pointer">
                                @csrf
                                <img class="h-4 w-5" src="{{ asset('assets/post_delete.svg') }}" alt="Delete Post">
                                <button type="submit" class="text-left w-full block px-4 py-2 text-red-600">(Superadmin) Delete Post</button>
                            </form>
                            @endif

                            @if ($post->creator->id === Auth::user()->id)
                            <div class="pl-4 flex p-2 group place-items-center hover:bg-gray-100 cursor-pointer post-edit-btn" data-post-id="{{ $post->id }}">
                                <img class="h-4 w-5" src="{{ asset('assets/post_edit.svg') }}" alt="Edit Post">
                                <button class="text-left w-full block px-4 py-2 hover:bg-gray-100">Edit Post</button>
                            </div>

                                @if (! (Auth::user()->role === 'Admin' && Auth::user()->admin()->is_super && $post->user_id === Auth::user()->id))
                                <form action="/post/delete/{{ $post->id }}" method="POST" class="pl-4 flex p-2 group place-items-center hover:bg-gray-100 cursor-pointer">
                                    @csrf
                                    <img class="h-4 w-5" src="{{ asset('assets/post_delete.svg') }}" alt="Delete Post">
                                    <button type="submit" class="text-left w-full block px-4 py-2 text-red-600">Delete Post</button>
                                </form>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>

                @if ($post->attached_image !== null)
                <img class="mt-3" src="{{ $post->image() }}" alt="Attachment">
                @endif

                <p class="text-lg font-bold mt-3">{{ $post->title }}</p>
                <p class="text-sm text-gray-400 break-words">{{ $post->content }}</p>

                @if ($post->source !== null)
                <p class="text-sm mt-3">Source:</p>
                <a class="text-sm underline text-blue-500" target="_blank" href="{{ $post->source }}">{{ $post->source }}</a>
                @endif

                @if ($post->post_category !== 'Announcement')
                <p class="text-sm mt-3">Status: <span class="text-gray-400 font-light">{{ $post->post_status }}</span></p>
                @endif

                <p class="text-sm mt-3">Posted on: <span class="text-gray-400 font-light">{{ $post->created_at->format('F j, Y \a\t g:i a') }}</span></p>
                <div class="flex-1"></div>

                <div class="flex mt-3">
                    <a href="/post/pin/toggle/{{ $post->id }}" class="flex-1 pl-4 flex group bg-blue-600 cursor-pointer rounded-lg">
                        @if ($post->isPinnedBy(auth()->user()))
                        <img class="w-5" src="{{ asset('assets/post_unpin.svg') }}" alt="Pin Post">
                        <button class="text-left w-full block px-4 py-2 text-white">Unpin Post</button>
                        @else
                        <img class="w-5" src="{{ asset('assets/post_pin.svg') }}" alt="Pin Post">
                        <button class="text-left w-full block px-4 py-2 text-white">Pin Post</button>
                        @endif
                    </a>
                    <div class="mx-1"></div>
                    <a href="/post/save/toggle/{{ $post->id }}" class="flex-1 pl-4 flex group bg-blue-600 cursor-pointer rounded-lg place-items-center">
                        <img class="h-4 w-5" src="{{ asset('assets/post_save.svg') }}" alt="Save Post">
                        @if ($post->isSavedBy(auth()->user()))
                        <button class="text-left w-full block px-4 py-2 text-white">Unsave Post</button>
                        @else
                        <button class="text-left w-full block px-4 py-2 text-white">Save Post</button>
                        @endif
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('script')
<script>
    const postEditBtn = document.querySelectorAll('.post-edit-btn');
    const readParams = new URLSearchParams(window.location.search);

    const editPostModal = document.getElementById('editPostModal');
    const closeEditPostModal = document.getElementById('closeEditPostModal');

    if (readParams.has('edit_post')) {
        editPostModal.classList.remove('hidden');

        closeEditPostModal.addEventListener('click', () => {
            window.location = window.location.origin + '/admin/post';
        })
    }

    postEditBtn.forEach((btn) => {
        btn.addEventListener('click', () => {
            const postId = btn.getAttribute('data-post-id');
            const params = new URLSearchParams(window.location.search);
            if (params.has('edit_post')) {
                params.delete('edit_post');
            }

            params.append('edit_post', postId);

            window.location = window.location.origin + '/admin/post?' + params.toString();
        })
    })
</script>
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
    }
</script>
<script>
    const openAddPostModal = document.getElementById('openAddPostModal');
    const addPostModal = document.getElementById('addPostModal');
    const closeAddPostModal = document.getElementById('closeAddPostModal');

    const creationCategory = document.getElementById('creation-category');
    const categoryStatus = document.getElementById('category-status');

    const eventStatuses = document.getElementById('event-statuses');
    const jobStatuses = document.getElementById('job-statuses');

    <?php if (session('openModal') && session('openModal') === 1): ?>
        addPostModal.classList.remove('hidden');
    <?php endif ?>

    creationCategory.addEventListener('change', (e) => {
        if (e.target.value === 'Event') {
            eventStatuses.classList.remove('hidden');
            jobStatuses.classList.add('hidden');
        } else if (e.target.value === 'Job Opening') {
            eventStatuses.classList.add('hidden');
            jobStatuses.classList.remove('hidden');
        } else {
            eventStatuses.classList.add('hidden');
            jobStatuses.classList.add('hidden');
            categoryStatus.removeAttribute('required');            
        }

        categoryStatus.value = null;
    })

    openAddPostModal.addEventListener('click', () => {
        addPostModal.classList.remove('hidden');
    })

    closeAddPostModal.addEventListener('click', () => {
        addPostModal.classList.add('hidden');
    })

    addPostModal.addEventListener('click', (e) => {
        if (e.target === addPostModal) {
            addPostModal.classList.add('hidden');
        }
    })
</script>
<script>
    const title = document.getElementById('title');
    const category = document.getElementById('category');
    const params = new URLSearchParams(window.location.search);

    category.addEventListener('change', (e) => {
        if (params.has('category')) {
            params.delete('category');
        }

        params.append('category', e.target.value);

        window.location = window.location.origin + '/admin/post?' + params.toString();
    })
</script>
@endsection