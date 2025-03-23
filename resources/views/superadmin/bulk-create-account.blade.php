@extends('layouts.admin')

@section('title', 'Bulk Create Account')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 overflow-auto max-h-[calc(100vh-64px)] relative">
    <div id="loading-modal" class="fixed inset-0 z-50 bg-gray-800 bg-opacity-75 flex items-center hidden justify-center">
        <div class="spinner-border animate-spin inline-block w-32 h-32 border-4 border-t-transparent border-white rounded-full text-white" role="status">
            <span class="visually-hidden"></span>
        </div>
    </div>

    <div id="wrong-file-structure-modal" class="fixed inset-0 z-50 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-8 text-center">
            <h2 class="text-xl font-semibold mb-4">Wrong File Structure</h2>
            <p class="mb-6">The uploaded file does not have the correct structure. Please ensure the file is a valid CSV with the required columns.</p>
            <button onclick="closeWrongFileStructureModal()" class="bg-blue-600 text-white rounded py-2 px-4">Close</button>
        </div>
    </div>

    <div id="import-success-modal" class="fixed inset-0 z-50 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-8 text-center">
            <h2 class="text-xl font-semibold mb-4">Import Successful</h2>
            <p class="mb-6">The data has been successfully imported.</p>
            <button onclick="closeImportSuccessModal()" class="bg-blue-600 text-white rounded py-2 px-4">Close</button>
        </div>
    </div>

    <div id="department-not-found-modal" class="fixed inset-0 z-50 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-8 text-center">
            <h2 class="text-xl font-semibold mb-4">Department Not Found</h2>
            <p class="mb-6">The department specified in the CSV file was not found. Please check the file and try again.</p>
            <button onclick="closeDepartmentNotFoundModal()" class="bg-blue-600 text-white rounded py-2 px-4">Close</button>
        </div>
    </div>

    <div id="database-error-modal" class="fixed inset-0 z-50 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-8 text-center">
            <h2 class="text-xl font-semibold mb-4">Database Error</h2>
            <p class="mb-6 w-[max(50vw,600px)]" id="message"></p>
            <button onclick="closeDatabaseErrorModal()" class="bg-blue-600 text-white rounded py-2 px-4">Close</button>
        </div>
    </div>

    <div id="file-exists-modal" class="fixed inset-0 z-50 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-8 text-center">
            <h2 class="text-xl font-semibold mb-4">File Already Exists</h2>
            <p class="mb-6">The file you are trying to upload already exists. Please rename the file or upload a different file.</p>
            <button onclick="closeFileExistsModal()" class="bg-blue-600 text-white rounded py-2 px-4">Close</button>
        </div>
    </div>

    <div class="flex items-center">
        <h1 class="font-medium tracking-widest text-lg">Create Bulk Accounts</h1>

        <div class="flex-1"></div>

        <form action="/account/import" method="POST" enctype="multipart/form-data">
            <label for="file-upload" class="bg-blue-600 text-white rounded py-2.5 px-4 cursor-pointer">
                Import Data
            </label>
            <input id="file-upload" type="file" name="file" accept=".csv" class="hidden" onchange="handleFileSelect(event)">
        </form>

        <a href="/account/imports" class="bg-blue-600 text-white rounded ml-3 py-2 px-4">See Imports</a>
    </div>

    <div class="shadow rounded-lg mt-8">
        <div class="flex items-center justify-start mt-4 bg-white rounded-lg p-4">
            <form action="/account/send-sms-credentials" method="POST" class="flex items-center">
                @csrf
                <select name="import_history_id" class="border rounded py-2 px-4 w-64" onchange="window.location.href = window.location.pathname + '?send_sms_history=' + this.value">
                    <option value="">Select Import Batch</option>
                    @foreach(App\Models\ImportHistory::all() as $history)
                    <option value="{{ $history->id }}" {{ request('send_sms_history') == $history->id ? 'selected' : '' }}>{{ $history->filename }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded py-2 px-4 ml-4 {{ request('send_sms_history') ? 'bg-blue-600 text-white' : 'bg-gray-400 text-gray-700 cursor-not-allowed' }}" {{ request('send_sms_history') ? '' : 'disabled' }}>
                    Send Credentials via SMS
                </button>
            </form>
        </div>
    </div>

    <div class="shadow rounded-lg mt-4">
        <form>
            <div class="bg-white py-4 flex items-center px-6 border-b rounded-t-lg">
                @if(request()->has('send_sms_history'))
                <input type="hidden" name="send_sms_history" value="{{ request('send_sms_history') }}">
                @endif
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" class="border rounded py-2 px-4 w-full max-w-64">
                <select name="import_history" class="border rounded py-2 px-4 ml-4">
                    <option value="-1">All</option>
                    @foreach(App\Models\ImportHistory::all() as $history)
                    <option value="{{ $history->id }}" {{ request('import_history') == $history->id ? 'selected' : '' }}>{{ $history->filename }}</option>
                    @endforeach
                </select>
                <div class="flex-1"></div>
                <!-- <select name="account_type" class="border rounded py-2 px-4 ml-4">
                    <option value="alumni">Alumni Accounts</option>
                    <option value="admin">Admin Accounts</option>
                </select> -->
                <input type="submit" value="Filter" class="bg-blue-600 text-white rounded py-2 px-4 ml-4">
            </div>
        </form>

        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <th class="font-thin py-3">ID</th>
                <th class="font-thin">Name</th>
                <th class="font-thin">Student/Company ID</th>
                <th class="font-thin">Username</th>
                <th class="font-thin">Password</th>
                <th class="font-thin">Date Created</th>
                <th class="font-thin">Actions</th>
            </thead>
            <tbody class="bg-white text-center">
                @foreach ($users as $user)
                <tr class="border-b">
                    @php($partialPersonal = $user->partialPersonal)
                    <td class="py-3">{{ $user->id }}</td>
                    <td>{{ $partialPersonal->fullname }}</td>
                    <td>{{ $partialPersonal->student_id }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->importGenerated->default_password }}</td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="flex justify-center gap-4 place-items-center">
                            <a href="/user/view/{{ $user->id }}"><img class="w-6" src="{{ asset('assets/view.svg') }}" alt="View"></a>
                            <button type="button" onclick="document.getElementById('deleteModal{{ $user->id }}').classList.remove('hidden')" class="border-0 bg-transparent cursor-pointer p-0">
                                <img src="{{ asset('assets/trash.svg') }}" alt="Trash">
                            </button>

                            <!-- Delete Confirmation Modal -->
                            <div id="deleteModal{{ $user->id }}" class="hidden fixed inset-0 z-50">
                                <div class="absolute inset-0 bg-black opacity-60 transition-opacity"></div>
                                <div class="absolute inset-0 flex items-center justify-center p-4">
                                    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all">
                                        <div class="border-b px-6 py-4 flex items-center">
                                            <div class="bg-red-100 p-2 rounded-full mr-3">
                                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-semibold text-gray-900">Confirm Deletion</h3>
                                        </div>
                                        <div class="px-6 py-4">
                                            <p class="text-gray-600">Are you sure you want to delete this alumni record? This action cannot be undone.</p>
                                        </div>
                                        <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                                            <button onclick="document.getElementById('deleteModal{{ $user->id }}').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm">
                                                Cancel
                                            </button>
                                            <a href="/user/delete/{{ $user->id }}" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm">
                                                Delete Record
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="bg-white rounded-b-lg p-3">
            {{ $users->appends(request()->except(['verify_modal']))->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function closeFileExistsModal() {
        document.getElementById('file-exists-modal').classList.add('hidden');
    }

    function closeWrongFileStructureModal() {
        document.getElementById('wrong-file-structure-modal').classList.add('hidden');
    }

    function closeImportSuccessModal() {
        document.getElementById('import-success-modal').classList.add('hidden');

        window.location.reload();
    }

    function closeDatabaseErrorModal() {
        document.getElementById('database-error-modal').classList.add('hidden');
    }

    function closeDepartmentNotFoundModal() {
        document.getElementById('department-not-found-modal').classList.add('hidden');
    }

    async function tryImportCsv(content, filename) {
        document.getElementById('loading-modal').classList.remove('hidden');

        const response = await fetch('/account/import', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                content,
                filename,
            })
        });

        document.getElementById('loading-modal').classList.add('hidden');

        if (!response.ok) {
            const data = await response.json();
            if (data.status === 'wrongfs') {
                document.getElementById('wrong-file-structure-modal').classList.remove('hidden');
            }

            if (data.status === 'deptnf') {
                document.getElementById('department-not-found-modal').classList.remove('hidden');
            }

            if (data.status === 'insertf') {
                document.getElementById('database-error-modal').classList.remove('hidden');
                document.getElementById('message').innerText = data.message;
            }

            if (data.status === 'fexist') {
                document.getElementById('file-exists-modal').classList.remove('hidden');
            }

            return;
        }

        document.getElementById('import-success-modal').classList.remove('hidden');
    }

    function handleFileSelect(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = async function(e) {
                const contents = e.target.result;
                const filename = file.name;

                await tryImportCsv(contents, filename);

                fileInput.value = '';
            };

            reader.readAsText(file);
        }
    }
</script>
@endsection