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
    <h1 class="font-medium tracking-widest text-lg mb-4">Alumni Profile Backups</h1>

    <div class="bg-white shadow rounded-lg p-4 mb-4">
        <div class="flex gap-3 items-center">
            <span class="font-semibold">Last Backup:</span>
            @if (App\Models\DatabaseSnapshot::query()->latest()->first())
                <span>{{ App\Models\DatabaseSnapshot::query()->latest()->first()->created_at->format('F j, Y, g:i a') }}</span>
            @else
                <span>No backups available</span>
            @endif

            <div class="flex-1"></div>

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
                        <form action="/backup/download/{{ $backup->id }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="hover:bg-blue-700/20 text-white font-bold py-2 px-4 rounded">
                                <img src="{{ asset('assets/download.svg') }}" alt="Download">
                            </button>
                        </form>
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