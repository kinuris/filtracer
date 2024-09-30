@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 max-h-[calc(100%-4rem)] overflow-auto">
    <h1 class="font-medium tracking-widest text-lg mb-4">Audit Trail</h1>

    <div class="shadow rounded-lg">
        <form action="">
            <div class="bg-white py-4 flex place-items-center px-6 border-b rounded-t-lg justify-between">
                <input value="{{ request('search') ?? '' }}" class="bg-gray-100 px-2 py-1 rounded border min-w-[max(33%,270px)]" placeholder="Search..." type="text" name="search" id="search">
            </div>
        </form>

        <table class="w-full">
            <thead class="bg-white text-blue-900 border-b">
                <th class="py-4 pl-4 font-thin">ID</th>
                <th class="font-thin">Date and Time</th>
                <th class="font-thin">Initiator</th>
                <th class="font-thin">Activity</th>
                <th class="font-thin pr-6">Event</th>
            </thead>
            <tbody class="bg-white text-center">
                @foreach ($audits as $audit)
                <tr class="border-b">
                    <td class="py-4 pl-4">{{ $audit->id }}</td>
                    <td>{{ $audit->created_at->format('Y-m-d H:i:s') }}</td>
                    <td class="text-sm">{{ $audit->user->name }}</td>
                    @php($modified = $audit->getModified())
                    <td>
                        @foreach ($modified as $key => $value)
                        @php($old = isset($value['old']) ? $value['old'] : '(Creation)')
                        @php($new = $value['new'])

                        <p class="text-sm text-left max-w-96"><strong>{{ $key }}:</strong> <i>{{ $old }}</i> → <i>{{ $new }}</i></p>

                        @endforeach
                    </td>
                    <td class="pr-6">
                        <p>{{ ucwords($audit->event) }}</p>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="bg-white rounded-b-lg p-1">
            <div class="bg-white rounded-b-lg p-3">
                {{ $audits->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>
</div>
@endsection