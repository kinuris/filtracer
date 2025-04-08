@extends('layouts.alumni')

@section('title', 'Dashboard')

@section('content')
<div class="bg-gray-100 w-full h-full p-8 flex flex-col max-h-[calc(100%-4rem)]">
    @php($user = auth()->user())
    <div class="flex flex-col max-h-full overflow-auto">
        <div class="flex items-center relative h-36">
            <img class="absolute h-32 border-8 left-0 border-gray-100 aspect-square object-cover rounded-full shadow-md hover:shadow-lg transition-shadow duration-300" src="{{ $user->image() }}" alt="Profile">
            <div class="ml-20 shadow-md hover:shadow-lg transition-shadow duration-300 w-full rounded-lg">
                <div class="pl-16 h-28 bg-white py-5 flex items-center px-6 border-b rounded-lg">
                    <div class="flex flex-col">
                        <h1 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h1>
                        @php($prof = $user->getProfessionalBio())
                        @if ($prof)
                        <p class="text-gray-600 mt-1">{{ $prof->employment_status }}</p>
                        @else
                        <p class="text-gray-500 italic mt-1">(No professional bio)</p>
                        @endif
                    </div>

                    <div class="flex-1"></div>

                    <a class="bg-blue-600 hover:bg-blue-700 text-white self-center py-2 px-4 rounded-lg transition-colors duration-300 font-medium" href="/alumni/profile">See Profile</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 my-8">
            <!-- Notifications Panel -->
            <div id="notificationsPanel" class="bg-white shadow-md hover:shadow-xl transition-all duration-300 rounded-lg overflow-hidden border border-gray-100">
                <div class="flex justify-between items-center p-5 bg-gradient-to-r from-blue-50 to-white border-b">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                        </svg>
                        <h3 class="font-bold text-gray-800 text-lg">Notifications</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 hover:underline cursor-pointer">Mark all as read</span>
                        <button class="p-1.5 rounded-full hover:bg-gray-100 transition-all">
                            <img src="{{ asset('assets/option.svg') }}" alt="Menu" class="h-4 w-4">
                        </button>
                    </div>
                </div>

                <div id="alertContainerBottom" class="p-4 max-h-[320px] overflow-y-auto"></div>
            </div>

            <!-- Messages Panel -->
            <div id="messagesPanel" class="bg-white shadow-md hover:shadow-xl transition-all duration-300 rounded-lg overflow-hidden border border-gray-100">
                <div class="flex justify-between items-center p-5 bg-gradient-to-r from-green-50 to-white border-b">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                            <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                        </svg>
                        <h3 class="font-bold text-gray-800 text-lg">Messages</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 hover:underline cursor-pointer">View all</span>
                        <button class="p-1.5 rounded-full hover:bg-gray-100 transition-all">
                            <img src="{{ asset('assets/option.svg') }}" alt="Menu" class="h-4 w-4">
                        </button>
                    </div>
                </div>

                <div id="messageContainerBottom" class="p-4 max-h-[320px] overflow-y-auto"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const alertContainerBottom = document.querySelector('#alertContainerBottom');
    const messageContainerBottom = document.querySelector('#messageContainerBottom');

    const notificationsPanel = document.querySelector('#notificationsPanel');
    const messagesPanel = document.querySelector('#messagesPanel');

    const initialNotifiesState = localStorage.getItem('display-notifies');
    const initialMessagesState = localStorage.getItem('display-messages');

    if (initialNotifiesState === null || initialNotifiesState === 'true') {
        notificationsPanel.style.display = 'block';
    } else {
        notificationsPanel.style.display = 'none';
    }

    if (initialMessagesState === null || initialMessagesState === 'true') {
        messagesPanel.style.display = 'block';
    } else {
        messagesPanel.style.display = 'none';
    }

    (function() {
        async function repeat() {
            try {
                const alerts = await fetch('/alert/gen');
                const text = await alerts.text();

                alertContainerBottom.innerHTML = text;
                messageContainerBottom.innerHTML = text;
            } catch (error) {
                console.error('Failed to fetch alerts:', error);
            }
        }

        repeat();
        setInterval(repeat, 1000);
    })();
</script>
@endsection