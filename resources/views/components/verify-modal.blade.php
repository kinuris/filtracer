@php($id = request('verify_modal'))

@if($id)
@php($user = App\Models\User::find($id))
<div id="verifyModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3 min-w-[500px] max-h-[max(85%,600px)] overflow-auto">
        <h2 class="text-lg font-bold mb-4 flex place-items-center">
            {{ $user->role }} Details
        </h2>

        <img class="w-36 h-36 object-cover rounded-full mb-3 shadow mx-auto" src="{{ $user->image() }}" alt="">

        <!-- View Mode -->
        <div id="viewMode">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p>Full Name</p>
                    <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->name }}</p>
                </div>

                <div>
                    <p>Username</p>
                    <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->username }}</p>
                </div>

                @if ($user->role != 'Admin')
                <div>
                    <p>Email</p>
                    <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getPersonalBio()->email_address }}</p>
                </div>

                <div>
                    <p>Contact Number</p>
                    <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getPersonalBio()->phone_number }}</p>
                </div>
                @else
                <div>
                    <p>Email</p>
                    <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->admin()->email_address }}</p>
                </div>

                <div>
                    <p>Contact Number</p>
                    <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->admin()->phone_number }}</p>
                </div>
                @endif

                @if ($user->role != 'Admin')
                <div>
                    <p>Student ID</p>
                    <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getPersonalBio()->student_id }}</p>
                </div>

                <div>
                    <p>Course</p>
                    <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getEducationalBio()->getCourse()->name }}</p>
                </div>

                <div class="col-span-2">
                    <p>Batch</p>
                    <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->getEducationalBio()->start }} - {{ $user->getEducationalBio()->end }}</p>
                </div>
                @else
                <div>
                    <p>Position ID</p>
                    <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->admin()->position_id }}</p>
                </div>

                <div>
                    <p>Office</p>
                    <p class="bg-gray-100 p-2 border rounded text-gray-500">{{ $user->admin()->officeRelation->name }}</p>
                </div>
                @endif
            </div>

            <div class="mt-4 flex">
                <button type="button" id="closeVerifyModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                <a class="bg-blue-500 text-white px-4 py-2 rounded mr-2" href="/admin/useraccount/verify/{{ $user->id }}">Verify</a>
                @php($user = App\Models\User::query()->find($id))
                @if ($user->role !== 'Admin')
                <button type="button" id="editButton" class="bg-blue-500 text-white px-4 py-2 rounded cursor-pointer">
                    Edit
                </button>
                @endif
            </div>
        </div>

        <!-- Edit Mode -->
        <form id="editMode" class="hidden" action="/admin/useraccount/update/{{ $user->id }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                @if ($user->role != 'Admin')
                <div>
                    <label>First Name</label>
                    <input name="first_name" class="w-full p-2 border rounded" value="{{ $user->getPersonalBio()->first_name }}">
                </div>

                <div>
                    <label>Middle Name</label>
                    <input name="middle_name" class="w-full p-2 border rounded" value="{{ $user->getPersonalBio()->middle_name }}">
                </div>

                <div>
                    <label>Last Name</label>
                    <input name="last_name" class="w-full p-2 border rounded" value="{{ $user->getPersonalBio()->last_name }}">
                </div>

                <div>
                    <label>Suffix</label>
                    <input name="suffix" class="w-full p-2 border rounded" value="{{ $user->getPersonalBio()->suffix }}">
                </div>
                @else
                <div>
                    <label>First Name</label>
                    <input name="first_name" class="w-full p-2 border rounded" value="{{ $user->admin()->first_name }}">
                </div>

                <div>
                    <label>Middle Name</label>
                    <input name="middle_name" class="w-full p-2 border rounded" value="{{ $user->admin()->middle_name }}">
                </div>

                <div>
                    <label>Last Name</label>
                    <input name="last_name" class="w-full p-2 border rounded" value="{{ $user->admin()->last_name }}">
                </div>

                <div>
                    <label>Suffix</label>
                    <input name="suffix" class="w-full p-2 border rounded" value="{{ $user->admin()->suffix }}">
                </div>
                @endif

                <div>
                    <label>Username</label>
                    <input name="username" class="w-full p-2 border rounded" value="{{ $user->username }}">
                </div>

                @if ($user->role != 'Admin')
                <div>
                    <label>Email</label>
                    <input type="email" name="email_address" class="w-full p-2 border rounded" value="{{ $user->getPersonalBio()->email_address }}">
                </div>

                <div>
                    <label>Contact Number</label>
                    <input name="phone_number" class="w-full p-2 border rounded" value="{{ $user->getPersonalBio()->phone_number }}">
                </div>

                <div>
                    <label>Student ID</label>
                    <input name="student_id" class="w-full p-2 border rounded" value="{{ $user->getPersonalBio()->student_id }}">
                </div>

                <div>
                    <label>Course</label>
                    <select name="course_id" class="course-select w-full p-2 border rounded">
                        @foreach(App\Models\Course::all() as $course)
                        <option value="{{ $course->id }}" {{ $user->getEducationalBio()->course_id == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->department->name }})
                        </option>
                        @endforeach
                    </select>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Add search functionality to course dropdown
                            const courseSelect = document.querySelector('.course-select');

                            // Create search input
                            const searchWrapper = document.createElement('div');
                            searchWrapper.className = 'relative';

                            const searchInput = document.createElement('input');
                            searchInput.type = 'text';
                            searchInput.className = 'w-full p-2 border rounded mb-1';
                            searchInput.placeholder = 'Search courses...';

                            const optionsContainer = document.createElement('div');
                            optionsContainer.className = 'absolute z-10 bg-white border rounded max-h-48 w-full overflow-y-auto hidden';

                            // Get all options
                            const options = Array.from(courseSelect.options).map(opt => ({
                                value: opt.value,
                                text: opt.text,
                                selected: opt.selected
                            }));

                            // Replace select with custom implementation
                            courseSelect.parentNode.insertBefore(searchWrapper, courseSelect);
                            searchWrapper.appendChild(searchInput);
                            searchWrapper.appendChild(optionsContainer);
                            searchWrapper.appendChild(courseSelect);
                            courseSelect.style.display = 'none';

                            // Create display for selected option
                            const selectedDisplay = document.createElement('div');
                            selectedDisplay.className = 'p-2 border rounded bg-gray-100 cursor-pointer truncate';
                            const selectedOption = options.find(opt => opt.selected);
                            selectedDisplay.textContent = selectedOption ? selectedOption.text : 'Select a course';
                            searchWrapper.insertBefore(selectedDisplay, searchInput);
                            searchInput.style.display = 'none';

                            // Render options
                            function renderOptions(filterText = '') {
                                optionsContainer.innerHTML = '';
                                const filtered = filterText ?
                                    options.filter(opt => opt.text.toLowerCase().includes(filterText.toLowerCase())) :
                                    options;

                                filtered.forEach(opt => {
                                    const option = document.createElement('div');
                                    option.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                                    option.textContent = opt.text;
                                    option.dataset.value = opt.value;

                                    option.addEventListener('click', () => {
                                        courseSelect.value = opt.value;
                                        selectedDisplay.textContent = opt.text;
                                        toggleDropdown(false);
                                    });

                                    optionsContainer.appendChild(option);
                                });
                            }

                            // Toggle dropdown visibility
                            function toggleDropdown(show) {
                                if (show) {
                                    optionsContainer.classList.remove('hidden');
                                    searchInput.style.display = 'block';
                                    selectedDisplay.style.display = 'none';
                                    searchInput.focus();
                                    renderOptions();
                                } else {
                                    optionsContainer.classList.add('hidden');
                                    searchInput.style.display = 'none';
                                    selectedDisplay.style.display = 'block';
                                    searchInput.value = '';
                                }
                            }

                            // Event listeners
                            selectedDisplay.addEventListener('click', () => toggleDropdown(true));
                            searchInput.addEventListener('input', () => renderOptions(searchInput.value));

                            // Close dropdown on outside click
                            document.addEventListener('click', (e) => {
                                if (!searchWrapper.contains(e.target)) {
                                    toggleDropdown(false);
                                }
                            });
                        });
                    </script>
                </div>

                <div class="col-span-2">
                    <label>Batch</label>
                    <div class="flex space-x-2">
                        <input name="start" class="w-1/2 p-2 border rounded" value="{{ $user->getEducationalBio()->start }}">
                        <input name="end" class="w-1/2 p-2 border rounded" value="{{ $user->getEducationalBio()->end }}">
                    </div>
                </div>
                @else
                <div>
                    <label>Email</label>
                    <input type="email" name="email_address" class="w-full p-2 border rounded" value="{{ $user->admin()->email_address }}">
                </div>

                <div>
                    <label>Contact Number</label>
                    <input name="phone_number" class="w-full p-2 border rounded" value="{{ $user->admin()->phone_number }}">
                </div>

                <div>
                    <label>Position ID</label>
                    <input name="position_id" class="w-full p-2 border rounded" value="{{ $user->admin()->position_id }}">
                </div>

                <div>
                    <label>Office</label>
                    <input name="office" class="w-full p-2 border rounded" value="{{ $user->admin()->office }}">
                </div>
                @endif
            </div>

            <div class="mt-4 flex">
                <button type="button" id="cancelEdit" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>

            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButton = document.getElementById('editButton');
        const viewMode = document.getElementById('viewMode');
        const editMode = document.getElementById('editMode');
        const cancelEdit = document.getElementById('cancelEdit');

        editButton.addEventListener('click', function() {
            viewMode.classList.add('hidden');
            editMode.classList.remove('hidden');
        });

        cancelEdit.addEventListener('click', function() {
            editMode.classList.add('hidden');
            viewMode.classList.remove('hidden');
        });
    });
</script>
@endif