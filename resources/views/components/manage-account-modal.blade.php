<div id="manageAccountModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-lg font-bold mb-4">Manage Account</h2>

        <form action="/manage/account/update/{{ $user->id }}" method="POST">
            @csrf
            @php($personalBio = $user->personalBio)
            @php($educational = $user->getEducationalBio())
            <div class="flex gap-4">
                <div class="flex-1">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" value="{{ old('last_name', $personalBio->last_name) }}" name="last_name" id="last_name" class="bg-gray-100 border block w-full border-gray-300 rounded-md p-1.5">
                    @error('last_name')
                        <span class="text-red-500 text-xs absolute">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex-1">
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" value="{{ old('first_name', $personalBio->first_name) }}" name="first_name" id="first_name" class="bg-gray-100 border block w-full border-gray-300 rounded-md p-1.5">
                    @error('first_name')
                        <span class="text-red-500 text-xs absolute">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <div class="flex-1">
                    <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                    <input type="text" value="{{ $personalBio->middle_name }}" name="middle_name" id="middle_name" class="bg-gray-100 border block w-full border-gray-300 rounded-md p-1.5">
                </div>
                <div class="flex-1">
                    <label for="suffix" class="block text-sm font-medium text-gray-700">Suffix</label>
                    <input type="text" value="{{ $personalBio->suffix }}" name="suffix" id="suffix" class="bg-gray-100 border block w-full border-gray-300 rounded-md p-1.5">
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <div class="flex-1">
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                    <input type="text" value="{{ old('student_id', $personalBio->student_id) }}" name="student_id" id="student_id" class="bg-gray-100 border block w-full border-gray-300 rounded-md p-1.5">
                    @error('student_id')
                        <span class="text-red-500 text-xs absolute">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex-1">
                    <label for="batch" class="block text-sm font-medium text-gray-700">Batch</label>
                    <input readonly type="text" value="S.Y. {{ $educational->start }}-{{ $educational->end }}" name="batch" id="batch" class="bg-gray-200 border block w-full border-gray-400 rounded-md p-1.5">
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <div class="flex-1">
                    <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
                    <input readonly type="text" value="{{ $educational->course->name }}" name="course" id="course" class="bg-gray-200 border block w-full border-gray-400 rounded-md p-1.5">
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <div class="flex-1">
                    <label for="major" class="block text-sm font-medium text-gray-700">Major</label>
                    <input readonly type="text" value="{{ $educational->major->name }}" name="major" id="major" class="bg-gray-200 border block w-full border-gray-400 rounded-md p-1.5">
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <div class="flex-1">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" value="{{ old('email', $personalBio->email_address) }}" name="email" id="email" class="bg-gray-100 border block w-full border-gray-300 rounded-md p-1.5">
                    @error('email')
                        <span class="text-red-500 text-xs absolute">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <div class="flex-1">
                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" value="{{ old('contact_number', $personalBio->phone_number) }}" name="contact_number" id="contact_number" class="bg-gray-100 border block w-full border-gray-300 rounded-md p-1.5">
                    @error('contact_number')
                        <span class="text-red-500 text-xs absolute">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex-1">
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" value="{{ old('username', $user->username) }}" name="username" id="username" class="bg-gray-100 border block w-full border-gray-300 rounded-md p-1.5">
                    @error('username')
                        <span class="text-red-500 text-xs absolute">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="button" id="closeManageAccountModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                <button type="submit" formaction="/manage/account/delete/{{ $user->id }}" class="mr-2 p-2 bg-blue-600 text-white rounded px-4">Delete</button>
                <button type="submit" class="p-2 bg-blue-600 text-white rounded px-4">Save</button>
            </div>
        </form>
    </div>
</div>