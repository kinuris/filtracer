@extends('layouts.alumni')

@section('content')

@section('title', 'Posts')

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
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)] overflow-auto">
    <h1 class="font-medium tracking-widest text-lg mb-4" id="title">{{ $title }}</h1>

    <div class="shadow rounded-lg mt-4">
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
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 gap-4">
        @foreach ($posts as $post)
        <div class="shadow-md rounded-lg mt-4 hover:shadow-lg transition-shadow duration-300">
            <div class="bg-white p-6 rounded-lg h-full flex flex-col">
            <div class="flex items-center mb-4">
                <img class="w-12 h-12 rounded-full object-cover border-2 border-gray-200" src="{{ $post->creator->image() }}" alt="Profile">
                <div class="ml-3">
                <p class="font-medium">{{ $post->creator->name }}
                    @if (request('category') == 'Your Posts')
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full font-medium
                    @if($post->status == 'Denied') bg-red-100 text-red-700 
                    @elseif($post->status == 'Approved') bg-green-100 text-green-700 
                    @elseif($post->status == 'Pending') bg-yellow-100 text-yellow-700 
                    @endif">
                    {{ $post->status }}
                    </span>
                    @endif
                </p>
                <div class="flex items-center text-xs text-gray-500">
                    <span>{{ $post->creator->role === 'Admin' ? ($post->creator->admin()->is_super ? 'Superadmin' : 'Admin' ) : 'Alumni' }}</span>
                    @if ($post->post_category === 'Event')
                    <img src="{{ asset('assets/calendar.svg') }}" class="ml-2 w-4 h-4" alt="Event">
                    @elseif ($post->post_category === 'Job Opening')
                    <img src="{{ asset('assets/job_opening.svg') }}" class="ml-2 w-4 h-4" alt="Job Opening">
                    @elseif ($post->post_category === 'Announcement')
                    <img src="{{ asset('assets/announcement.svg') }}" class="ml-2 w-4 h-4" alt="Announcement">
                    @endif
                    <span class="ml-2 text-gray-400">· {{ $post->created_at->diffForHumans() }}</span>
                </div>
                </div>

                <div class="group relative ml-auto">
                <button class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                    <img src="{{ asset('assets/option.svg') }}" alt="Options" class="w-5 h-5">
                </button>

                <div class="w-52 right-0 absolute hidden group-hover:block bg-white shadow-xl rounded-lg overflow-hidden z-10 border border-gray-100">
                    <a href="/post/pin/toggle/{{ $post->id }}" class="flex items-center p-3 hover:bg-gray-50 transition-colors">
                    <img class="w-5 h-5" src="{{ asset($post->isPinnedBy(auth()->user()) ? 'assets/post_unpin.svg' : 'assets/post_pin.svg') }}" alt="Pin">
                    <span class="ml-3">{{ $post->isPinnedBy(auth()->user()) ? 'Unpin Post' : 'Pin Post' }}</span>
                    </a>

                    <a href="/post/save/toggle/{{ $post->id }}" class="flex items-center p-3 hover:bg-gray-50 transition-colors">
                    <img class="w-5 h-5" src="{{ asset('assets/post_save.svg') }}" alt="Save">
                    <span class="ml-3">{{ $post->isSavedBy(auth()->user()) ? 'Unsave Post' : 'Save Post' }}</span>
                    </a>

                    @if ($post->creator->id === Auth::user()->id)
                    <hr class="border-gray-100">
                    <div class="flex items-center p-3 hover:bg-gray-50 transition-colors cursor-pointer post-edit-btn" data-post-id="{{ $post->id }}">
                    <img class="w-5 h-5" src="{{ asset('assets/post_edit.svg') }}" alt="Edit">
                    <span class="ml-3">Edit Post</span>
                    </div>
                    <a href="/post/delete/{{ $post->id }}" class="flex items-center p-3 hover:bg-gray-50 transition-colors text-red-600">
                    <img class="w-5 h-5" src="{{ asset('assets/post_delete.svg') }}" alt="Delete">
                    <span class="ml-3">Delete Post</span>
                    </a>
                    @endif
                </div>
                </div>
            </div>

            <h2 class="text-xl font-bold mb-2">{{ $post->title }}</h2>
            <p class="text-gray-700 mb-4 leading-relaxed">{{ $post->content }}</p>

            @if ($post->attached_image !== null)
            <div class="mb-4 rounded-lg overflow-hidden">
                <img class="w-full object-cover" src="{{ $post->image() }}" alt="Attachment">
            </div>
            @endif

            @if ($post->source !== null)
            <div class="mb-4 bg-gray-50 p-3 rounded-lg">
                <p class="text-sm text-gray-500">Source:</p>
                <a class="text-blue-600 hover:underline text-sm break-all" target="_blank" href="{{ $post->source }}">{{ $post->source }}</a>
            </div>
            @endif

            @if ($post->post_category !== 'Announcement')
            <p class="text-sm mb-2">Status: <span class="font-medium">{{ $post->post_status }}</span></p>
            @endif

            <p class="text-xs text-gray-400 mb-4">Posted on {{ $post->created_at->format('F j, Y \a\t g:i a') }}</p>
            
            <div class="mt-auto flex gap-2">
                <a href="/post/pin/toggle/{{ $post->id }}" class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors">
                <img class="w-4 h-4" src="{{ asset('assets/post_pin.svg') }}" alt="Pin">
                <span>{{ $post->isPinnedBy(auth()->user()) ? 'Unpin' : 'Pin' }}</span>
                </a>
                <a href="/post/save/toggle/{{ $post->id }}" class="flex-1 flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded-lg transition-colors">
                <img class="w-4 h-4" src="{{ asset('assets/post_save.svg') }}" alt="Save">
                <span>{{ $post->isSavedBy(auth()->user()) ? 'Unsave' : 'Save' }}</span>
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
            window.location = window.location.origin + '/alumni/post';
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

            window.location = window.location.origin + '/alumni/post?' + params.toString();
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

    <?php if (session('openModal') && session('openModal') === 1): ?>
        addPostModal.classList.remove('hidden');
    <?php endif ?>

    const creationCategory = document.getElementById('creation-category');
    const categoryStatus = document.getElementById('category-status');

    const eventStatuses = document.getElementById('event-statuses');
    const jobStatuses = document.getElementById('job-statuses');

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

        window.location = window.location.origin + '/alumni/post?' + params.toString();
    })
</script>
@endsection