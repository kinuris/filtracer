<div id="renameGroupModal" class="fixed z-50 inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 pt-0 rounded-lg shadow-lg w-1/3">
        <!-- <img class="w-12 mx-auto" src="{{ asset('assets/leave.svg') }}" alt="Leave Group"> -->
        <h2 class="text-lg font-semibold text-center mt-6">Rename Group</h2>
        <form action="/chat/rename/{{ urlencode(request('initiate')) }}" method="POST">
            @csrf
            <?php

            use App\Models\ChatGroup;

            $initiate = request('initiate');
            if ($initiate && !is_numeric($initiate)) {
                $group = ChatGroup::query()->where('internal_id', '=', $initiate)->first();
                echo '<input class="p-2 border rounded-lg bg-gray-50 w-full my-6" type="text" name="name" id="name" value="' . $group->name . '">';
            }

            ?>
            <div class="flex justify-center">
                <button type="button" id="closeRenameGroupModal" class="mr-2 px-4 py-2 bg-white text-blue-500 border border-blue-500 rounded">No, Cancel</button>
                <button type="it" class="mr-2 px-4 py-2 bg-blue-600 text-white rounded">Yes, I'm Sure</button>
            </div>
        </form>
    </div>
</div>