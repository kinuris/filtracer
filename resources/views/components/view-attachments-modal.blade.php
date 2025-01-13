@php($prof = auth()->user()->getProfessionalBio())

@if ($prof !== null)
<div id="viewAttachmentsModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-lg font-bold mb-4">Existing Attachments</h2>

        <div class="gap-2 p-2 flex flex-col">
            @foreach ($prof->attachments as $attachment)
            <div class="bg-gray-100 border p-2 rounded-lg">
                <div class="flex justify-between">
                    <p class="text-sm font-semibold text-slate-600">{{ $attachment->name }}</p>
                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this attachment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm w-fit font-semibold text-red-600 flex place-items-center">
                            <span class="material-symbols-outlined text-red-600">
                                delete
                            </span>
                        </button>
                    </form>
                </div>
                <a class="text-sm w-fit font-semibold text-slate-600 flex place-items-center mt-4" href="{{ asset('storage/professional/attachments/' . $attachment->link) }}" target="_blank">
                    <span class="material-symbols-outlined text-gray-400">
                        file_open
                    </span>
                    <p class="underline ml-2 text-gray-400">Open File</p>
                </a>
            </div>
            @endforeach
        </div>
        <button type="button" id="closeViewAttachmentsModal" class="mr-2 mt-3 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">Cancel</button>
    </div>
</div>
@else
<div id="viewAttachmentsModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h1 class="text-lg font-bold">(No Attachments)</h1>
    </div>
</div>
@endif