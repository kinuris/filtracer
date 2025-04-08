<div id="educationModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-lg font-bold mb-4">Add Education</h2>

        <div class="mb-4">
            <label for="educationLevel" class="block mb-2">Education Level</label>
            <select class="w-full text-gray-400 border rounded-lg p-2" name="educationLevel" id="educationLevel" onchange="toggleEducationForm()">
                <option value="Tertiary">Tertiary</option>
                <option value="Secondary">Secondary</option>
                <option value="Primary">Primary</option>
            </select>
        </div>

        <!-- Tertiary Education Form -->
        <form id="tertiaryForm" action="/alumni/profile/add/educational/{{ $user->id }}" method="POST">
            @csrf
            <input type="hidden" name="level" value="Tertiary">
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

                <div class="flex mt-4 justify-end">
                    <button type="button" class="closeEducationModal mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Add</button>
                </div>
            </div>
        </form>

        <!-- Secondary Education Form -->
        <form id="secondaryForm" action="/alumni/profile/add/educational/{{ $user->id }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="level" value="Secondary">
            <div class="flex flex-col">
                <label for="secondary_school">School</label>
                <input type="text" class="text-gray-400 border rounded-lg p-2" name="school" id="secondary_school" placeholder="School Name">

                <label class="mt-3" for="secondary_location">Location</label>
                <input type="text" class="text-gray-400 border rounded-lg p-2" placeholder="Location" name="location" id="secondary_location">

                <label class="mt-3" for="secondary_track">Track/Strand</label>
                <select class="text-gray-400 border rounded-lg p-2" name="track" id="secondary_track">
                    <option value="STEM">STEM</option>
                    <option value="HUMSS">HUMSS</option>
                    <option value="ABM">ABM</option>
                    <option value="TVL">TVL</option>
                    <option value="GAS">GAS</option>
                    <option value="Arts and Design">Arts and Design</option>
                    <option value="Sports">Sports</option>
                    <option value="None">None</option>
                </select>

                <div class="flex mt-3 gap-3">
                    <div class="flex-1 flex flex-col">
                        <label for="secondary_start">Start</label>
                        <input class="text-gray-400 border rounded-lg p-2" type="number" name="start" id="secondary_start" placeholder="Year">
                    </div>

                    <div class="flex-1 flex flex-col">
                        <label for="secondary_end">End</label>
                        <input class="text-gray-400 border rounded-lg p-2" type="number" name="end" id="secondary_end" placeholder="Year">
                    </div>
                </div>

                <div class="flex mt-4 justify-end">
                    <button type="button" class="closeEducationModal mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Add</button>
                </div>
            </div>
        </form>

        <!-- Primary Education Form -->
        <form id="primaryForm" action="/alumni/profile/add/educational/{{ $user->id }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="level" value="Primary">
            <div class="flex flex-col">
                <label for="primary_school">School</label>
                <input type="text" class="text-gray-400 border rounded-lg p-2" name="school" id="primary_school" placeholder="School Name">

                <label class="mt-3" for="primary_location">Location</label>
                <input type="text" class="text-gray-400 border rounded-lg p-2" placeholder="Location" name="location" id="primary_location">

                <div class="flex mt-3 gap-3">
                    <div class="flex-1 flex flex-col">
                        <label for="primary_start">Start</label>
                        <input class="text-gray-400 border rounded-lg p-2" type="number" name="start" id="primary_start" placeholder="Year">
                    </div>

                    <div class="flex-1 flex flex-col">
                        <label for="primary_end">End</label>
                        <input class="text-gray-400 border rounded-lg p-2" type="number" name="end" id="primary_end" placeholder="Year">
                    </div>
                </div>

                <div class="flex mt-4 justify-end">
                    <button type="button" class="closeEducationModal mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Add</button>
                </div>
            </div>
        </form>

        <script>
            function toggleEducationForm() {
                const level = document.getElementById('educationLevel').value;
                document.getElementById('tertiaryForm').classList.add('hidden');
                document.getElementById('secondaryForm').classList.add('hidden');
                document.getElementById('primaryForm').classList.add('hidden');

                if (level === 'Tertiary') {
                    document.getElementById('tertiaryForm').classList.remove('hidden');
                } else if (level === 'Secondary') {
                    document.getElementById('secondaryForm').classList.remove('hidden');
                } else if (level === 'Primary') {
                    document.getElementById('primaryForm').classList.remove('hidden');
                }
            }

            // Initialize the form display
            document.addEventListener('DOMContentLoaded', function() {
                toggleEducationForm();
            });
        </script>
    </div>
</div>