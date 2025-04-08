<div id="resetPasswordModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-lg font-bold mb-4">Password Information</h2>

        <form action="/alumni/password/{{ Auth::user()->id }}/reset" method="POST">
            @csrf
            <div class="flex flex-col">
                <label for="current">Current Password</label>
                <input type="password" class="text-gray-400 border rounded-lg p-2" placeholder="Current Password" name="current" id="current">

                <label class="mt-3" for="new">New Password</label>
                <input type="password" class="text-gray-400 border rounded-lg p-2" placeholder="New Password" name="new" id="new">

                <label class="mt-3" for="confirm">Confirm Password</label>
                <input type="password" class="text-gray-400 border rounded-lg p-2" placeholder="Confirm Password" name="confirm" id="confirm">

                <p class="text-sm mt-4">Password requirements:</p>
                <p class="text-gray-400 mt-2 text-xs leading-none">Ensure that these character requirements are met:</p>
                <p class="text-gray-400 text-xs">At least 8 characters (and up to 100 characters)</p>

                <div class="flex mt-4 justify-end">
                    <button type="button" id="closeResetPasswordModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>