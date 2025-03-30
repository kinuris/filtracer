@extends('layouts.alumni')

@section('title', 'Alumni Chat')

@section('content')
@if (request('initiate'))
@include('components.chat-members-modal')
@include('components.leave-group-modal')
@include('components.rename-group-modal')
@include('components.see-images-modal')
@include('components.see-files-modal')
@endif
<div class="max-h-[calc(100%-4rem)]">
    <div class="bg-gray-100 w-full h-full p-8 flex flex-col overflow-auto max-h-[calc(100%-0.01px)]">
        <h1 class="font-medium tracking-widest text-lg">Chats</h1>

        <div class="shadow rounded-lg mt-4 max-h-[calc(100vh-4rem)] overflow-auto">
            <div class="bg-white justify-between rounded-lg">
                <div class="text-lg font-semibold relative tracking-wide grid grid-rows-[auto,1fr] grid-cols-[auto,1fr] h-full w-full">
                    <div class="border-b px-6 py-3">
                        <div class="flex justify-between place-items-center h-full">
                            <p class="text-lg tracking-wide font-semibold">Messages</p>
                            <a href="/alumni/chat">
                                <img class="w-5" src="{{ asset('assets/compose_message.svg') }}" alt="Compose Message">
                            </a>
                        </div>
                    </div>
                    <div class="border-b px-6 py-2 pl-4 border-l">
                        @if ($selected)
                        <div class="flex place-items-center h-full">
                            <img class="w-9 h-9 rounded-full object-cover shadow" src="{{ $selected->image() }}" alt="">
                            <p class="font-normal text-base tracking-normal ml-3">{{ $selected->name }}</p>

                            <div class="flex-1"></div>

                            <div class="group relative">
                                <img class="w-5 h-5 transition-transform hover:scale-110" src="{{ asset('assets/option.svg') }}" alt="Close">

                                <div class="w-48 right-0 absolute hidden group-hover:block font-light text-sm bg-white shadow-lg rounded-lg overflow-hidden">
                                    <div class="flex p-2 group hover:bg-gray-100 cursor-pointer">
                                        <img class="w-5" src="{{ asset('assets/file.svg') }}" alt="Chat Members">
                                        <button id="openSeeFilesModal" class="text-left w-full block px-4 py-2 hover:bg-gray-100">See Files</button>
                                    </div>

                                    <div class="flex p-2 group hover:bg-gray-100 cursor-pointer">
                                        <img class="w-5" src="{{ asset('assets/image.svg') }}" alt="Chat Members">
                                        <button id="openSeeImagesModal" class="text-left w-full block px-4 py-2 hover:bg-gray-100">See Images</button>
                                    </div>

                                    @if (!is_numeric(request('initiate')))
                                    <div id="openChatMembersModal" class="flex p-2 group hover:bg-gray-100 rounded-t-lg cursor-pointer">
                                        <img class="w-5" src="{{ asset('assets/accounts.svg') }}" alt="Chat Members">
                                        <button class="text-left w-full block px-4 py-2">Members</button>
                                    </div>

                                    <div class="flex p-2 group hover:bg-gray-100 cursor-pointer">
                                        <img class="w-5" src="{{ asset('assets/rename.svg') }}" alt="Chat Members">
                                        <button class="text-left w-full block px-4 py-2 hover:bg-gray-100" id="openRenameGroupModal">Edit Group</button>
                                    </div>

                                    <div class="flex p-2 group hover:bg-gray-100 cursor-pointer">
                                        <img class="w-5" src="{{ asset('assets/leave.svg') }}" alt="Chat Members">
                                        <button class="text-left w-full block px-4 py-2 hover:bg-gray-100" id="openLeaveGroupModal">Leave Group</button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="flex place-items-center h-full">
                            <p class="font-normal text-sm mr-3">To:</p>
                            <select multiple name="receivers[]" id="receivers"></select>
                            <button class="hidden h-5 w-5 transition-transform hover:scale-110 bg-slate-700 text-white text-xs font-light rounded" id="addBtn">+</button>
                        </div>
                        @endif
                    </div>
                    <div class="px-6 py-3 flex flex-col h-full">
                        <form class="self-center mb-2" action="">
                            <input class="border rounded-lg bg-gray-50 font-light text-sm p-2 min-w-72" placeholder="Search..." type="text" name="search">
                        </form>

                        <div id="messageHeadersContainer">

                        </div>
                    </div>
                    <div class="border-l px-6 py-3 h-full min-h-[calc(100vh-14.1rem)] max-h-[calc(100vh-14.1rem)] overflow-scroll">
                        @if (!$selected)
                        <div class="div w-full h-full flex flex-col justify-center place-items-center">
                            <img class="w-16" src="{{ asset('assets/chat_gray.svg') }}" alt="Messages">
                            <h1 class="text-gray-400 font-light">(No Chat Selected)</h1>
                        </div>
                        @else
                        <div class="flex flex-col mb-12 overflow-auto" id="messages">
                            <!-- NOTE: Messages go here -->
                        </div>
                        <div class="div w-full bg-white/80 backdrop-blur-sm py-3 flex flex-col justify-end absolute px-4 right-0 bottom-0">
                            <div class="flex">
                                <input class="border rounded-l-lg border-r-0 bg-gray-50 font-light text-sm  min-w-72 flex-1 focus:outline-none px-2" placeholder="Type something here..." type="text" name="message" id="message">
                                <button class="bg-gray-50 border border-l-0 w-10 h-10 flex justify-center place-items-center rounded-r-lg">
                                    <label class="cursor-pointer p-2" for="attachment">
                                        <img class="w-5 h-5" src="{{ asset('assets/attachment.svg') }}" alt="Attachment">
                                    </label>
                                </button>
                                <input class="hidden" type="file" name="attachment" id="attachment" accept="image/png,image/jpg,image/jpeg,application/pdf">
                                <button id="send" class="shadow bg-blue-600 w-10 h-10 flex justify-center place-items-center rounded-lg ml-3 transition-transform hover:scale-110">
                                    <img class="w-5 h-5" src="{{ asset('assets/send.svg') }}" alt="Send">
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const messageHeadersContainer = document.getElementById('messageHeadersContainer');

    // Initial fetch for headers
    (async function loadHeaders() {
        const headersRes = await fetch('/chat/headers');
        if (headersRes.status === 200) {
            messageHeadersContainer.innerHTML = await headersRes.text();
        }

        // Set interval for headers
        setInterval(async () => {
            const headersRes = await fetch('/chat/headers');
            if (headersRes.status === 200) {
                messageHeadersContainer.innerHTML = await headersRes.text();

                // Track read messages with localStorage
                const lastReadMessages = JSON.parse(localStorage.getItem('lastReadMessages') || '{}');

                // Apply read/unread styling to all message headers
                const messageHeaders = messageHeadersContainer.querySelectorAll('[data-latest]');
                messageHeaders.forEach(header => {
                    const latest = header.getAttribute('data-latest');
                    let chatId = '';
                    
                    // Get the link (either the header itself or a child)
                    let link = header.tagName === 'A' ? header : header.querySelector('a');
                    if (link && link.href && link.href.includes('initiate=')) {
                        chatId = link.href.split('initiate=')[1];
                    }
                    
                    if (chatId) {
                        // Check if message is unread (no record or timestamp changed)
                        const isUnread = !lastReadMessages[chatId] || lastReadMessages[chatId] !== latest;
                        
                        // Apply appropriate styling
                        header.classList.toggle('bg-blue-50', isUnread);
                        const preview = header.querySelector('p.text-xs');
                        if (preview) {
                            preview.classList.toggle('text-gray-400', !isUnread);
                            preview.classList.toggle('font-medium', isUnread);
                            preview.classList.toggle('text-blue-600', isUnread);
                        }
                    }
                });

                // If we haven't added a click handler yet, add one with event delegation
                if (!messageHeadersContainer._hasClickListener) {
                    messageHeadersContainer.addEventListener('click', event => {
                        const header = event.target.closest('[data-latest]');
                        if (header) {
                            const latest = header.getAttribute('data-latest');
                            let chatId = '';
                            
                            let link = header.tagName === 'A' ? header : header.querySelector('a');
                            if (link && link.href && link.href.includes('initiate=')) {
                                chatId = link.href.split('initiate=')[1];
                                
                                // Mark as read in storage
                                lastReadMessages[chatId] = latest;
                                localStorage.setItem('lastReadMessages', JSON.stringify(lastReadMessages));
                                
                                // Update styling
                                header.classList.remove('bg-blue-50');
                                const preview = header.querySelector('p.text-xs');
                                if (preview) {
                                    preview.classList.add('text-gray-400');
                                    preview.classList.remove('font-medium', 'text-blue-600');
                                }
                            }
                        }
                    });
                    messageHeadersContainer._hasClickListener = true;
                }
            }
        }, 1000);
    })();
</script>
<script>
    const groupImagePreviewSelector = document.getElementById('groupImagePreviewSelector');
    const groupImagePreview = document.getElementById('groupImagePreview');

    if (groupImagePreviewSelector) {
        groupImagePreviewSelector.addEventListener('change', () => {
            groupImagePreview.src = URL.createObjectURL(groupImagePreviewSelector.files[0]);
        });
    }
</script>
<script>
    const openRenameGroupModal = document.getElementById('openRenameGroupModal');
    const renameGroupModal = document.getElementById('renameGroupModal');
    const closeRenameGroupModal = document.getElementById('closeRenameGroupModal');

    if (openRenameGroupModal) {
        openRenameGroupModal.addEventListener('click', () => {
            renameGroupModal.classList.remove('hidden');
        });
    }

    if (closeRenameGroupModal) {
        closeRenameGroupModal.addEventListener('click', () => {
            renameGroupModal.classList.add('hidden');
        });
    }

    if (renameGroupModal) {
        renameGroupModal.addEventListener('click', (e) => {
            if (e.target === renameGroupModal) {
                renameGroupModal.classList.add('hidden');
            }
        })
    }
</script>
<script>
    const openChatMembersModal = document.getElementById('openChatMembersModal');
    const chatMembersModal = document.getElementById('chatMembersModal');
    const closeChatMembersModal = document.getElementById('closeChatMembersModal');

    if (openChatMembersModal && chatMembersModal && closeChatMembersModal) {
        openChatMembersModal.addEventListener('click', () => {
            chatMembersModal.classList.remove('hidden');
        });

        closeChatMembersModal.addEventListener('click', () => {
            chatMembersModal.classList.add('hidden');
        });

        chatMembersModal.addEventListener('click', (e) => {
            if (e.target === chatMembersModal) {
                chatMembersModal.classList.add('hidden');
            }
        });
    }
</script>
<script>
    const messages = document.getElementById('messages');
    const message = document.getElementById('message');
    const send = document.getElementById('send');

    if (send) {

        document.addEventListener('keypress', (e) => {
            if (message.value.length < 1) return;

            if (e.key === 'Enter') {
                send.click();
            }
        });

        send.addEventListener('click', async () => {
            if (message.value.length < 1) return;

            <?php

            use App\Models\User;
            use Illuminate\Support\Facades\Auth;
            ?>

            const formData = new FormData();
            formData.append('message', message.value);
            formData.append('sender', <?php echo Auth::user()->id ?>);
            formData.append('room_id', '<?php echo isset($selected->internal_id) ? $selected->internal_id : ($selected ? $selected->id : 'null') ?>');

            message.value = '';

            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': <?php echo '"' . csrf_token() . '"' ?>,
                },
                body: formData
            });

            const {
                group
            } = await response.json();

            const htmlRes = await fetch(`/chat/messages/${group}`);
            const text = await htmlRes.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const msg = doc.querySelectorAll('.ind-chat-msg');

            if (msg && msg.length === 1) {
                window.location.reload();
            }

            messages.innerHTML = text;
        });
    }
</script>

@if ($selected && $selected instanceof App\Models\ChatGroup)
<script>
    const addtiions = document.getElementById('additions');
    new Choices(addtiions, {
        choices: [
            <?php
            $raw = $selected->users->pluck('id');

            foreach (User::query()->whereNotIn('id', $raw->toArray())->get() as $user) {
                if ($user->id !== Auth::user()->id) {
                    echo '{ value: ' . $user->id . ', ' . 'label: "' .  $user->name . '"'  . '},';
                }
            }
            ?>
        ],
        removeItemButton: true,
    })
</script>
@endif

@if ($selected)
<script>
    const seeFilesModal = document.getElementById('seeFilesModal');
    const closeSeeFilesModal = document.getElementById('closeSeeFilesModal');
    const openSeeFilesModal = document.getElementById('openSeeFilesModal');

    if (openSeeFilesModal && seeFilesModal && closeSeeFilesModal) {
        openSeeFilesModal.addEventListener('click', () => {
            seeFilesModal.classList.remove('hidden');
        });

        closeSeeFilesModal.addEventListener('click', () => {
            seeFilesModal.classList.add('hidden');
        });

        seeFilesModal.addEventListener('click', (e) => {
            if (e.target === seeFilesModal) {
                seeFilesModal.classList.add('hidden');
            }
        });
    }
</script>
<script>
    const seeImagesModal = document.getElementById('seeImagesModal');
    const closeSeeImagesModal = document.getElementById('closeSeeImagesModal');
    const openSeeImagesModal = document.getElementById('openSeeImagesModal');

    if (openSeeImagesModal && seeImagesModal && closeSeeImagesModal) {
        openSeeImagesModal.addEventListener('click', () => {
            seeImagesModal.classList.remove('hidden');
        });

        closeSeeImagesModal.addEventListener('click', () => {
            seeImagesModal.classList.add('hidden');
        });

        seeImagesModal.addEventListener('click', (e) => {
            if (e.target === seeImagesModal) {
                seeImagesModal.classList.add('hidden');
            }
        });
    }
</script>
<script>
    const attachment = document.getElementById('attachment');
    attachment.addEventListener('change', async (e) => {
        const file = e.target.files[0];

        if (!file) {
            return;
        }

        const formData = new FormData();
        formData.append('message', file);
        formData.append('sender', <?php echo Auth::user()->id ?>);
        formData.append('room_id', '<?php echo isset($selected->internal_id) ? $selected->internal_id : ($selected ? $selected->id : 'null') ?>');

        const response = await fetch('/chat/send', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': <?php echo '"' . csrf_token() . '"' ?>,
            },
            body: formData,
        })
    });
</script>
<script>
    const openLeaveGroupModal = document.getElementById('openLeaveGroupModal');
    const leaveGroupModal = document.getElementById('leaveGroupModal');
    const closeLeaveGroupModal = document.getElementById('closeLeaveGroupModal');

    openLeaveGroupModal.addEventListener('click', () => {
        leaveGroupModal.classList.remove('hidden');
    });

    closeLeaveGroupModal.addEventListener('click', () => {
        leaveGroupModal.classList.add('hidden');
    });

    leaveGroupModal.addEventListener('click', (e) => {
        if (e.target === leaveGroupModal) {
            leaveGroupModal.classList.add('hidden');
        }
    })
</script>


<script>
    (async function repeat() {
        const response = await fetch(`/chat/getgroup/<?php echo urlencode(isset($selected->internal_id) ? $selected->internal_id : ($selected ? $selected->id : 'null')) ?>`);
        const group = Number(await response.text());

        async function sync() {
            const htmlRes = await fetch(`/chat/messages/${group}`);
            if (htmlRes.status !== 200) return;
            messages.innerHTML = await htmlRes.text();
        }

        sync();

        if (group !== -1) {
            setInterval(sync, 1000);
        }
    })();
</script>
@else
<script>
    let selectedCount = 0;
    const receivers = document.getElementById('receivers');
    const addBtn = document.getElementById('addBtn');

    addBtn.addEventListener('click', async () => {
        if (selectedCount < 0) {
            return;
        }

        const formData = new FormData();
        for (const opt of receivers.selectedOptions) {
            formData.append('receivers[]', opt.value);
        }
        formData.append('creator', <?php echo Auth::user()->id ?>);

        const response = await fetch('/chat/makegroup', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': <?php echo '"' . csrf_token() . '"' ?>,
            },
            body: formData
        });

        if (response.status === 200) {
            window.location.href = '/alumni/chat';
        }
    });

    function updateAddBtnVis() {
        if (selectedCount > 0) {
            addBtn.classList.remove('hidden');
        } else {
            addBtn.classList.add('hidden');
        }
    }

    receivers.addEventListener('addItem', () => {
        selectedCount++;
        updateAddBtnVis();
    });

    receivers.addEventListener('removeItem', () => {
        selectedCount--;
        updateAddBtnVis();
    });

    new Choices(receivers, {
        choices: [
            <?php
            foreach (User::compSet()->get()->merge(User::query()->where('role', '=', 'Admin')->get()) as $user) {
                if ($user->id !== Auth::user()->id) {
                    echo '{ value: ' . $user->id . ', ' . 'label: "' .  $user->name . '"'  . '},';
                }
            }
            ?>
        ],
        removeItemButton: true,
        classNames: {
            containerOuter: ['m-0', 'choices'],
            containerInner: ['p-0'],
            listDropdown: ['min-w-96', 'bg-gray-100'],
            item: ['choices__item']
        }
    })
</script>
@endif
@endsection