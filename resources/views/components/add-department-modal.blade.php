<div id="addDepartmentModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-lg font-bold mb-4">Add Department</h2>

        <form action="/settings/department/create" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="flex flex-col">
                <label for="name">Department Name</label>
                <input class="text-gray-400 border rounded-lg p-2 mt-1" placeholder="Department Name" type="text" name="name" id="name">
            </div>

            <div class="mt-3 flex flex-col">
                <label for="logo">
                    <div class="bg-gray-100 py-4 flex justify-center rounded-lg border border-dashed">
                        <img id="preview" class="hidden w-32 rounded-full aspect-square object-cover" alt="Preview">
                        <div id="nofile" class="flex justify-center place-items-center">
                            <div class="bg-blue-600 w-fit p-1.5 rounded-full mr-2">
                                <img src="{{ asset('assets/upload.svg') }}" alt="Upload">
                            </div>
                            <p class="text-gray-400">Upload Logo</p>
                        </div>
                    </div>
                </label>
                <input class="mt-2 hidden" type="file" name="logo" id="logo" accept="image/jpg,image/jpeg,image/png">
            </div>

            <div class="flex justify-end mt-6">
                <button type="button" id="closeAddDeparmentModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                <button type="submit" class="p-2 bg-blue-600 text-white rounded px-4">Save</button>
            </div>
        </form>
    </div>
</div>