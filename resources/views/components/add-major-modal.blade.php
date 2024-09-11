<div id="addMajorModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-lg font-bold mb-4">Add Major</h2>

        <form action="">
            <div class="flex flex-col">
                <label for="name">Major Name</label>
                <input class="text-gray-400 border rounded-lg p-2 mt-1" placeholder="Major Name" type="text" name="name" id="name">
            </div>

            @php($courses = \App\Models\Course::all())
            <div class="flex flex-col mt-3">
                <label for="name">Course</label>
                <select class="text-gray-400 border rounded-lg p-2 mt-1" name="name" id="name">
                    @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end mt-6">
                <button type="button" id="closeAddMajorModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                <button type="submit" class="p-2 bg-blue-600 text-white rounded px-4">Save</button>
            </div>
        </form>
    </div>
</div>