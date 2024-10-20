@extends('layouts.alumni')

@section('title', 'Dashboard')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    @php($user = auth()->user())
    <div class="flex flex-col max-h-full overflow-auto">
        <div class="flex items-center relative h-32">
            <img class="absolute h-32 border-8 left-0 border-gray-100 aspect-square object-cover rounded-full" src="{{ $user->image() }}" alt="Profile">
            <div class="ml-20 shadow w-full rounded-lg">
                <div class="pl-16 h-24 bg-white py-4 flex px-6 border-b rounded-lg">
                    <div class="flex flex-col">
                        <h1 class="text-xl font-bold">{{ $user->name }}</h1>
                        @php($prof = $user->getProfessionalBio())
                        @if ($prof)
                        <p>{{ $prof->employment_status  }}</p>
                        @else
                        <p>(No professional bio)</p>
                        @endif
                    </div>

                    <div class="flex-1"></div>

                    <a class="bg-blue-600 text-white self-center p-2 rounded-lg" href="/alumni/profile">See Profile</a>
                </div>
            </div>
        </div>

        <div class="flex my-3">
            <div class="flex flex-col bg-white shadow rounded-lg flex-1 max-h-36 overflow-auto">
                <div class="flex justify-between border-b p-4 sticky top-0 bg-white/80 backdrop-blur-[2px]">
                    <p class="font-bold">Notifications</p>
                    <img src="{{ asset('assets/option.svg') }}" alt="Menu">
                </div>

                <div id="alertContainerBottom"></div>
            </div>

            <div class="mx-3"></div>

            <div class="flex flex-col bg-white shadow rounded-lg flex-1 max-h-36 overflow-auto relative">
                <div class="flex justify-between border-b p-4 sticky top-0 bg-white/80 backdrop-blur-[2px]">
                    <p class="font-bold">Messages</p>
                    <img src="{{ asset('assets/option.svg') }}" alt="Menu">
                </div>

                <div id="messageContainerBottom"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const alertContainerBottom = document.querySelector('#alertContainerBottom');
    const messageContainerBottom = document.querySelector('#messageContainerBottom');

    (function() {
        async function repeat() {
            const alerts = await fetch('/alert/gen');

            const text = await alerts.text();

            alertContainerBottom.innerHTML = text;
            messageContainerBottom.innerHTML = text;
        }

        repeat();

        setInterval(repeat, 1000);
    })()
</script>
@endsection