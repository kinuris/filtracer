@extends('layouts.admin')

@section('title', 'Backup')

@section('content')
<div id="startBackupModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-1/3">
        <div class="border-b px-4 py-2 flex justify-between items-center">
            <h3 class="font-semibold text-lg">Start Backup</h3>
            <!-- <button class="text-gray-600 hover:text-gray-800" onclick="closeStartBackupModal()">&times;</button> -->
        </div>
        <div class="p-4">
            <p class="text-center text-gray-700">Starting Backup...</p>
            <div class="flex justify-center mt-4">
                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </div>
        <div class="flex justify-end p-4 border-t">
            <!-- <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2" onclick="closeStartBackupModal()">Cancel</button> -->
            <!-- <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Start Backup</button> -->
        </div>
    </div>
</div>

<div id="zipFileErrorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-1/3">
        <div class="border-b px-4 py-2 flex justify-between items-center">
            <h3 class="font-semibold text-lg">Error</h3>
            <button class="text-gray-600 hover:text-gray-800" onclick="closeZipFileErrorModal()">&times;</button>
        </div>
        <div class="p-4">
            <p class="text-center text-gray-700">Creating zip file failed. Please try again.</p>
        </div>
        <div class="flex justify-end p-4 border-t">
            <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded" onclick="closeZipFileErrorModal()">Close</button>
        </div>
    </div>
</div>

<div id="backupSuccessModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-1/3">
        <div class="border-b px-4 py-2 flex justify-between items-center">
            <h3 class="font-semibold text-lg">Backup Successful</h3>
            <button class="text-gray-600 hover:text-gray-800" onclick="closeBackupSuccessModal()">&times;</button>
        </div>
        <div class="p-4">
            <p class="text-center text-gray-700">Backup has been created successfully.</p>
        </div>
        <div class="flex justify-end p-4 border-t">
            <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded" onclick="closeBackupSuccessModal()">Close</button>
        </div>
    </div>
</div>

<div class="bg-gray-100 w-full h-full p-8 overflow-auto max-h-[calc(100vh-64px)]">
    <h1 class="font-medium tracking-widest text-lg mb-4">Database Backups</h1>

    <div class="bg-white shadow rounded-lg p-4 mb-4">
        <div class="flex gap-3 items-center">
            <div class="flex flex-col">
                <span class="font-semibold">Last Backup:</span>
                @if (App\Models\DatabaseSnapshot::query()->latest()->first())
                <span>{{ App\Models\DatabaseSnapshot::query()->latest()->first()->created_at->format('F j, Y, g:i a') }}</span>
                @else
                <span>No backups available</span>
                @endif
            </div>

            <div class="flex-1"></div>

            <div id="uploadLoadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg shadow-lg w-1/3">
                    <div class="border-b px-4 py-2 flex justify-between items-center">
                        <h3 class="font-semibold text-lg">Uploading Backup</h3>
                    </div>
                    <div class="p-4">
                        <p class="text-center text-gray-700">Uploading and processing backup file...</p>
                        <div class="flex justify-center mt-4">
                            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div id="uploadSuccessModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg shadow-lg w-1/3">
                    <div class="border-b px-4 py-2 flex justify-between items-center">
                        <h3 class="font-semibold text-lg">Upload Successful</h3>
                        <button class="text-gray-600 hover:text-gray-800" onclick="closeUploadSuccessModal()">&times;</button>
                    </div>
                    <div class="p-4">
                        <p class="text-center text-gray-700">Backup file has been uploaded and restored successfully.</p>
                    </div>
                    <div class="flex justify-end p-4 border-t">
                        <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded" onclick="closeUploadSuccessModal()">Close</button>
                    </div>
                </div>
            </div>

            <div id="uploadErrorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg shadow-lg w-1/3">
                    <div class="border-b px-4 py-2 flex justify-between items-center">
                        <h3 class="font-semibold text-lg">Upload Error</h3>
                        <button class="text-gray-600 hover:text-gray-800" onclick="closeUploadErrorModal()">&times;</button>
                    </div>
                    <div class="p-4">
                        <p class="text-center text-gray-700" id="uploadErrorMessage">An error occurred while uploading the backup file.</p>
                    </div>
                    <div class="flex justify-end p-4 border-t">
                        <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded" onclick="closeUploadErrorModal()">Close</button>
                    </div>
                </div>
            </div>

            <form action="/backup/upload" method="POST" enctype="multipart/form-data" class="flex items-center" id="backupUploadForm">
                @csrf
                <label for="backup_file" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded cursor-pointer">
                    Restore From File
                </label>
                <input type="file" id="backup_file" name="backup_file" class="hidden" accept=".zip">
            </form>

            <script>
                function closeUploadSuccessModal() {
                    document.getElementById('uploadSuccessModal').classList.add('hidden');
                    window.location.reload();
                }

                function closeUploadErrorModal() {
                    document.getElementById('uploadErrorModal').classList.add('hidden');
                }

                document.getElementById('backup_file').addEventListener('change', async function() {
                    try {
                        // Show loading modal
                        document.getElementById('uploadLoadingModal').classList.remove('hidden');

                        const formData = new FormData(document.getElementById('backupUploadForm'));

                        const response = await fetch('/backup/upload', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        // Hide loading modal
                        document.getElementById('uploadLoadingModal').classList.add('hidden');

                        const data = await response.json();

                        if (data.status === 'success') {
                            document.getElementById('uploadSuccessModal').classList.remove('hidden');
                        } else {
                            document.getElementById('uploadErrorMessage').textContent = 'Error uploading backup: ' + data.message;
                            document.getElementById('uploadErrorModal').classList.remove('hidden');
                        }
                    } catch (error) {
                        // Hide loading modal on error
                        document.getElementById('uploadLoadingModal').classList.add('hidden');

                        console.error('Error:', error);
                        document.getElementById('uploadErrorMessage').textContent = 'An error occurred while uploading the backup file';
                        document.getElementById('uploadErrorModal').classList.remove('hidden');
                    }
                });
            </script>

            <button onclick="openStartBackupModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Backup Now
            </button>
        </div>
    </div>

    <div class="shadow rounded-lg">
        <div class="bg-white py-5 flex items-center px-4 border-b rounded-t-lg">
            <h2 class="font-semibold">Backup History</h2>
        </div>

        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <tr>
                    <th class="text-left py-2 px-4">ID</th>
                    <th class="text-left py-2 px-4">Date and Time</th>
                    <th class="text-left py-2 px-4">Backup Size (In Bytes)</th>
                    <th class="text-right py-2 px-4">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @foreach ($backups as $backup)
                <tr>
                    <td class="px-4 py-2 border-b">{{ $backup->id }}</td>
                    <td class="px-4 py-2 border-b">{{ $backup->created_at }}</td>
                    <td class="px-4 py-2 border-b">{{ number_format(($backup->getFileSize() + $backup->getSqlSize()) / (1000 * 1000), 2) }} MB</td>
                    <td class="px-4 py-2 border-b text-right">
                        <div class="flex justify-end gap-2">
                            <form action="/backup/download/{{ $backup->id }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="hover:bg-blue-700/20 text-white font-bold py-2 px-4 rounded">
                                    <img src="{{ asset('assets/download.svg') }}" alt="Download">
                                </button>
                            </form>

                            <button onclick="openRestoreConfirmationModal({{ $backup->id }})" class="hover:bg-green-700/20 text-white font-bold py-2 px-4 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div id="restoreConfirmModal-{{ $backup->id }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
                                <div class="bg-white rounded-lg shadow-lg w-1/3">
                                    <div class="border-b px-4 py-2 flex justify-between items-center">
                                        <h3 class="font-semibold text-lg">Confirm Restore</h3>
                                        <button class="text-gray-600 hover:text-gray-800" onclick="closeRestoreConfirmationModal({{ $backup->id }})">&times;</button>
                                    </div>
                                    <div class="p-4">
                                        <p class="text-center text-gray-700">Are you sure you want to restore this backup?</p>
                                        <p class="text-center text-gray-700 font-semibold">{{ $backup->created_at }}</p>
                                    </div>
                                    <div class="flex justify-end p-4 border-t">
                                        <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2" onclick="closeRestoreConfirmationModal({{ $backup->id }})">Cancel</button>
                                        <button onclick="startRestoreBackup({{ $backup->id }})" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Restore</button>
                                    </div>
                                </div>
                            </div>

                            <div id="restoreBackupModal-{{ $backup->id }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
                                <div class="bg-white rounded-lg shadow-lg w-1/3">
                                    <div class="border-b px-4 py-2 flex justify-between items-center">
                                        <h3 class="font-semibold text-lg">Restoring Backup</h3>
                                    </div>
                                    <div class="p-4">
                                        <p class="text-center text-gray-700">Restoring backup from {{ $backup->created_at }}...</p>
                                        <div class="flex justify-center mt-4">
                                            <svg class="animate-spin h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="restoreSuccessModal-{{ $backup->id }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
                                <div class="bg-white rounded-lg shadow-lg w-1/3">
                                    <div class="border-b px-4 py-2 flex justify-between items-center">
                                        <h3 class="font-semibold text-lg">Restore Successful</h3>
                                        <button class="text-gray-600 hover:text-gray-800" onclick="closeRestoreSuccessModal({{ $backup->id }})">&times;</button>
                                    </div>
                                    <div class="p-4">
                                        <p class="text-center text-gray-700">System has been successfully restored from backup created on {{ $backup->created_at->format('F j, Y, g:i a') }}.</p>
                                    </div>
                                    <div class="flex justify-end p-4 border-t">
                                        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="closeRestoreSuccessModal({{ $backup->id }})">Close</button>
                                    </div>
                                </div>
                            </div>

                            <button onclick="openDeleteConfirmationModal({{ $backup->id }})" class="hover:bg-red-700/20 text-white font-bold py-2 px-4 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div id="deleteConfirmModal-{{ $backup->id }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
                            <div class="bg-white rounded-lg shadow-lg w-1/3">
                                <div class="border-b px-4 py-2 flex justify-between items-center">
                                    <h3 class="font-semibold text-lg">Confirm Delete</h3>
                                    <button class="text-gray-600 hover:text-gray-800" onclick="closeDeleteConfirmationModal({{ $backup->id }})">&times;</button>
                                </div>
                                <div class="p-4">
                                    <p class="text-center text-gray-700">Are you sure you want to delete this backup?</p>
                                    <p class="text-center text-gray-700 font-semibold">{{ $backup->created_at }}</p>
                                </div>
                                <div class="flex justify-end p-4 border-t">
                                    <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2" onclick="closeDeleteConfirmationModal({{ $backup->id }})">Cancel</button>
                                    <form action="/backup/delete/{{ $backup->id }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="bg-white rounded-b-lg p-3">
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function openRestoreConfirmationModal(backupId) {
        const modal = document.getElementById(`restoreConfirmModal-${backupId}`);
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeRestoreConfirmationModal(backupId) {
        const modal = document.getElementById(`restoreConfirmModal-${backupId}`);
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>
<script>
    async function startRestoreBackup(backupId) {
        // Show the restore modal
        const modal = document.getElementById(`restoreBackupModal-${backupId}`);
        if (modal) {
            modal.classList.remove('hidden');
        }

        try {
            // Send restore request to the server
            const response = await fetch(`/backup/restore/${backupId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            // Hide the restore modal
            if (modal) {
                modal.classList.add('hidden');
            }

            // Show success message
            if (data.status === 'success') {
                const successModal = document.getElementById(`restoreSuccessModal-${backupId}`);
                if (successModal) {
                    successModal.classList.remove('hidden');
                }
            } else {
                alert('Failed to restore backup: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            if (modal) {
                modal.classList.add('hidden');
            }
            alert('An error occurred during the restore process');
        }
    }

    function closeRestoreSuccessModal(backupId) {
        const modal = document.getElementById(`restoreSuccessModal-${backupId}`);
        if (modal) {
            modal.classList.add('hidden');
        }
        window.location.reload();
    }
</script>

<script>
    function openDeleteConfirmationModal(backupId) {
        const modal = document.getElementById(`deleteConfirmModal-${backupId}`);
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeDeleteConfirmationModal(backupId) {
        const modal = document.getElementById(`deleteConfirmModal-${backupId}`);
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>
<script>
    function closeBackupSuccessModal() {
        const modal = document.getElementById('backupSuccessModal');
        if (modal) {
            modal.classList.add('hidden');
        }

        window.location.reload();
    }

    async function openStartBackupModal() {
        const modal = document.getElementById('startBackupModal');

        if (modal) {
            modal.classList.remove('hidden');
        }

        const response = await fetch('/backup/start', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (data.status === 'success') {
            const backupSuccessModal = document.getElementById('backupSuccessModal');
            if (backupSuccessModal) {
                backupSuccessModal.classList.remove('hidden');
            }
        }

        if (data.status === 'zipf') {
            const zipFileErrorModal = document.getElementById('zipFileErrorModal');
            if (zipFileErrorModal) {
                zipFileErrorModal.classList.remove('hidden');
            }
        }

        closeStartBackupModal();
    }

    function closeZipFileErrorModal() {
        const modal = document.getElementById('zipFileErrorModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function closeStartBackupModal() {
        const modal = document.getElementById('startBackupModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    document.querySelector('.bg-blue-600').addEventListener('click', openStartBackupModal);
</script>
@endsection