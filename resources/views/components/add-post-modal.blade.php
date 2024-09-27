<div id="addPostModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-1/3">
        <h2 class="border-b p-4 text-lg font-bold">Create Post</h2>

        <form class="p-6" action="">
            <div class="flex flex-col">
                <label for="title">Title</label>
                <input class="text-gray-400 border rounded-lg p-2 mt-1" placeholder="Write Title" type="text" name="title" id="title">
            </div>

            <div class="flex flex-col mt-3">
                <label for="content">Content</label>
                <textarea class="text-gray-400 border rounded-lg p-2 mt-1" placeholder="Write Content" name="content" id="content"></textarea>
            </div>

            <div class="flex justify-end mt-6">
                <button type="button" id="closeAddPostModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                <button type="submit" class="p-2 bg-blue-600 text-white rounded px-4">Save</button>
            </div>
        </form>
    </div>
</div>