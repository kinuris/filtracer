@extends('layouts.alumni')

@section('content')
<div class="bg-gray-50 w-full min-h-screen p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Display Settings</h1>
        
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="text-lg font-medium text-gray-800">Visibility Preferences</h2>
                <p class="text-sm text-gray-500">Control what appears in your dashboard</p>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-start py-3 border-b border-gray-100">
                    <label class="switch mr-4">
                        <input type="checkbox" id="toggle-notifies">
                        <span class="slider round"></span>
                    </label>
                    <div>
                        <h3 class="font-medium text-gray-800">Notifications</h3>
                        <p class="text-sm text-gray-500">Show notification alerts in your dashboard</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-start py-3">
                    <label class="switch mr-4">
                        <input type="checkbox" id="toggle-messages">
                        <span class="slider round"></span>
                    </label>
                    <div>
                        <h3 class="font-medium text-gray-800">Messages</h3>
                        <p class="text-sm text-gray-500">Display incoming messages in your dashboard</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <p class="text-xs text-gray-500">Changes are saved automatically</p>
            </div>
        </div>
    </div>
</div>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 26px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #e2e8f0;
        transition: .2s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .2s;
    }

    input:checked + .slider {
        background-color: #3b82f6;
    }

    input:focus + .slider {
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 26px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleNotifies = document.getElementById('toggle-notifies');
        const toggleMessages = document.getElementById('toggle-messages');

        // Load initial state from localStorage, default to true if not set
        const initialNotifiesState = localStorage.getItem('display-notifies') === null ? true : localStorage.getItem('display-notifies') === 'true';
        const initialMessagesState = localStorage.getItem('display-messages') === null ? true : localStorage.getItem('display-messages') === 'true';

        toggleNotifies.checked = initialNotifiesState;
        toggleMessages.checked = initialMessagesState;

        // Function to update localStorage and apply changes
        function updateDisplay(key, value) {
            localStorage.setItem(key, value);
            // Add your logic here to hide/show elements based on the value
            console.log(`Setting ${key} to ${value}`);
        }

        toggleNotifies.addEventListener('change', function () {
            updateDisplay('display-notifies', this.checked);
        });

        toggleMessages.addEventListener('change', function () {
            updateDisplay('display-messages', this.checked);
        });
    });
</script>
@endsection
