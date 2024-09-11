<div id="educationModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-lg font-bold mb-4">Add Education</h2>

        <form action="/alumni/profile/add/educational/{{ $user->id }}" method="POST">
            @csrf
            <div class="flex flex-col">
                <label for="school">School</label>
                <select class="text-gray-400 border rounded-lg p-2" name="school" id="school">
                    @foreach ($schools as $school)
                    <option value="{{ $school }}">{{ $school }}</option>
                    @endforeach
                </select>

                <label class="mt-3" for="location">Location</label>
                <input type="text" class="text-gray-400 border rounded-lg p-2" placeholder="Location" name="location" id="location">

                <label class="mt-3" for="type">Degree Type</label>
                <select class="text-gray-400 border rounded-lg p-2" name="type" id="type">
                    <option value="Bachelor">Bachelor</option>
                    <option value="Masteral">Masteral</option>
                    <option value="Doctoral">Doctoral</option>
                </select>

                <label class="mt-3" for="course">Course</label>
                <select class="text-gray-400 border rounded-lg p-2" name="course" id="course">
                    @php($courses = App\Models\Course::all())
                    @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>

                <label class="mt-3" for="major">Major</label>
                <select class="text-gray-400 border rounded-lg p-2" name="major" id="major">
                    @php($majors = App\Models\Major::all())
                    @foreach ($majors as $major)
                    <option value="{{ $major->id }}">{{ $major->name }}</option>
                    @endforeach
                </select>

                <div class="flex mt-3 gap-3">
                    <div class="flex-1 flex flex-col">
                        <label for="start">Start</label>
                        <input class="text-gray-400 border rounded-lg p-2" type="number" name="start" id="start" placeholder="Year">
                    </div>

                    <div class="flex-1 flex flex-col">
                        <label for="end">End</label>
                        <input class="text-gray-400 border rounded-lg p-2" type="number" name="end" id="end" placeholder="Year">
                    </div>
                </div>

                <div class="flex mt-4">
                    <button type="button" id="closeEducationModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>