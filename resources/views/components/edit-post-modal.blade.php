@php($categories = [
'Event',
'Job Opening',
'Announcement',
])

@php ($post = App\Models\Post::query()->find(request('edit_post')))
<div id="editPostModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-1/3">
        <h2 class="border-b p-4 text-lg font-bold">Create Post</h2>

        <form class="p-6" action="/post/edit/{{ $post->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col">
                <label for="title">Title</label>
                <input value="{{ $post->title }}" class="text-gray-400 border rounded-lg p-2 mt-1" placeholder="Write Title" type="text" name="title" id="title">
            </div>

            <div class="flex flex-col mt-3">
                <label for="content">Content</label>
                <textarea class="max-h-32 min-h-24 text-gray-400 border rounded-lg p-2 mt-1" placeholder="Write Content" name="content" id="content">{{ $post->content }}</textarea>
            </div>

            <div class="flex flex-col mt-3">
                <label for="source">Source</label>
                <input value="{{ $post->source }}" type="url" class="text-gray-400 border rounded-lg p-2 mt-1" placeholder="Write Content" name="source" id="source">
            </div>

            <div class="flex mt-3">
                <div class="flex flex-col flex-1">
                    <label for="creation-category">Post Category</label>
                    <select class="text-gray-400 border rounded-lg p-2 mt-1" name="post_category" id="creation-category">
                        @foreach ($categories as $category)
                        <option {{ $post->post_category == $category ? 'selected' : '' }} value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mx-2"></div>

                <div class="flex flex-col flex-1">
                    <label for="category-status">Post Status</label>
                    <select class="text-gray-400 border rounded-lg p-2 mt-1" name="post_status" id="category-status">
                        <optgroup label="Event" id="event-statuses">
                            <option {{ $post->post_status == 'Incoming' ? 'selected' : '' }} value="Incoming">Incoming</option>
                            <option {{ $post->post_status == 'Ended' ? 'selected' : '' }} value="Ended">Ended</option>
                        </optgroup>
                        <optgroup class="hidden" label="Job Opening" id="job-statuses">
                            <option {{ $post->post_status == 'Open' ? 'selected' : '' }} value="Open">Open</option>
                            <option {{ $post->post_status == 'Closed' ? 'selected' : '' }} value="Closed">Closed</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="flex flex-col mt-3">
                <label for="attachment">Attach Image</label>
                <input class="text-gray-400 border rounded-lg p-2 mt-1" type="file" name="attachment" id="attachment">
            </div>

            <div class="flex justify-end mt-6">
                <button type="button" id="closeEditPostModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                <button type="submit" class="p-2 bg-blue-600 text-white rounded px-4">Save</button>
            </div>
        </form>
    </div>
</div>