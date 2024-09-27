@extends('layouts.admin')

@section('content')

@include('components.add-post-modal')

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
    <h1 class="font-medium tracking-widest text-lg mb-4" id="title">{{ $title }}</h1>

    <div class="shadow rounded-lg mt-4">
        <form action="">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-lg">
                <label class="font-medium" for="category">Category</label>
                <select class="p-2 border rounded-lg ml-4" name="category" id="category">
                    @foreach ($categories as $category)
                    <option {{ request('category') == $category ? 'selected' : '' }} value="{{ $category }}">{{ $category }}</option>
                    @endforeach 
                </select>

                <div class="flex-1"></div>

                <button type="button" class="rounded-lg p-2 px-3 bg-blue-600 text-white" id="openAddPostModal">Add New Post</button>
                <a class="rounded-lg p-2 px-3 bg-blue-600 text-white ml-3">Your Profile</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    const openAddPostModal = document.getElementById('openAddPostModal');
    const addPostModal = document.getElementById('addPostModal');
    const closeAddPostModal = document.getElementById('closeAddPostModal');

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