<?php

namespace App\Http\Controllers;

use App\Models\ChatAssociation;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    public function fetchChatMessages(ChatGroup $group)
    {
        return view('chat.snippets.messages')->with('group', $group);
    }

    public function renameGroup(string $roomId)
    {
        $validated = request()->validate([
            'name' => 'required',
        ]);

        if (is_numeric($roomId)) {
            return abort(403);
        }

        $group = ChatGroup::query()->where('internal_id', '=', $roomId)->first();
        $previousName = $group->name;
        $group->update($validated);

        foreach ($group->users()->get() as $user) {
            UserAlert::query()->create([
                'title' => 'Renamed group chat to ' . $group->name,
                'action' => (User::find($user->id)->role == 'Admin' ? '/admin/chat' : '/alumni/chat') . '?initiate=' . urlencode($group->internal_id),
                'content' => 'Change GC name ' . $previousName . ' -> ' . $group->name,
                'user_id' => $user->id,
            ]);
        }

        $sender = User::query()->find(Auth::user()->id);
        if ($sender->role == 'Admin') {
            return redirect('/admin/chat')->with('message', 'Left Group');
        } else {
            return redirect('/alumni/chat')->with('message', 'Left Group');
        }
    }

    public function addMembers(Request $request, string $roomId)
    {
        if (is_numeric($roomId)) {
            return abort(403);
        }

        $validated = $request->validate([
            'additions' => ['required', 'array'],
        ]);

        $group = ChatGroup::query()->where('internal_id', '=', $roomId)->first();

        foreach ($validated['additions'] as $user) {
            UserAlert::query()->create([
                'title' => 'You were added to a group chat',
                'action' => (User::find($user)->role == 'Admin' ? '/admin/chat' : '/alumni/chat') . '?initiate=' . urlencode($group->internal_id),
                'content' => 'Welcome to ' . $group->name,
                'user_id' => $user,
            ]);
        }

        foreach ($validated['additions'] as $user) {
            ChatAssociation::query()->create([
                'user_id' => $user,
                'chat_group_id' => $group->id,
            ]);
        }

        return back()->with('message', 'Members added successfully!');
    }

    public function leaveGroup(string $roomId)
    {
        $sender = User::query()->find(Auth::user()->id);
        if (is_numeric($roomId)) {
            return abort(403);
        }

        $group = ChatGroup::query()->where('internal_id', '=', $roomId)->first();

        ChatAssociation::query()
            ->where('chat_group_id', '=', $group->id)
            ->where('user_id', '=', Auth::user()->id)
            ->delete();

        if ($sender->role == 'Admin') {
            return redirect('/admin/chat')->with('message', 'Left Group');
        } else {
            return redirect('/alumni/chat')->with('message', 'Left Group');
        }
    }

    public function getGroup(string $roomId)
    {
        if (is_numeric($roomId)) {
            $roomId = (int) $roomId;

            $sender = User::query()->find(Auth::user()->id);
            $receiver = User::query()->find($roomId);
            $group = $sender->chatGroupWith($receiver);

            if (is_null($group)) {
                return -1;
            }

            return $group->id;
        } else {
            return ChatGroup::query()->where('internal_id', '=', $roomId)->first()->id;
        }
    }

    public function makeGroup(Request $request)
    {
        $request->validate([
            'receivers' => ['required', 'array'],
            'creator' => ['required'],
        ]);

        $creator = User::query()->find($request->post('creator'));

        $now = date_create()->format('Y-m-d');
        $groupName = "($now) - " . $creator->name;

        $group = ChatGroup::query()->create([
            'internal_id' => ChatGroup::genInternalNoCollision(),
            'name' => $groupName,
            'creator_id' => $request->post('creator'),
            'image_link' => fake()->imageUrl(),
        ]);

        foreach (array_merge($request->post('receivers'), [$request->post('creator')]) as $user) {
            UserAlert::query()->create([
                'title' => 'You were part of a created group',
                'action' => (User::find($user)->role == 'Admin' ? '/admin/chat' : '/alumni/chat') . '?initiate=' . urlencode($group->internal_id),
                'content' => 'Welcome to ' . $group->name,
                'user_id' => $user,
            ]);
        }

        ChatAssociation::query()->create([
            'user_id' => $request->post('creator'),
            'chat_group_id' => $group->id
        ]);

        foreach ($request->post('receivers') as $receiver) {
            ChatAssociation::query()->create([
                'user_id' => $receiver,
                'chat_group_id' => $group->id
            ]);
        }

        return response('Success', 200);
    }

    public function send(Request $request)
    {
        $user = User::query()->find(Auth::user()->id);
        $request->validate([
            'sender' => 'required',
            'message' => 'required',
            'room_id' => 'required',
        ]);

        if ($request->post('sender') != $user->id) {
            return abort(403);
        }

        $roomId = $request->post('room_id');
        if (is_numeric($roomId)) {
            $roomId = (int) $roomId;
            $receiver = User::query()->find($roomId);

            if ($user->chatsWith($receiver)) {
                $group = $user->chatGroupWith($receiver);

                UserAlert::query()->create([
                    'title' => 'You received a new message',
                    'content' => $request->post('message'),
                    'user_id' => $receiver->id,
                ]);

                ChatMessage::query()->create([
                    'sender_id' => $user->id,
                    'content' => $request->post('message'),
                    'chat_group_id' => $group->id,
                ]);

                return response()->json(['group' => $group->id]);
            } else {
                $group = ChatGroup::query()->create([
                    'internal_id' => ChatGroup::genInternalNoCollision(),
                    'name' => $user->name . ' & ' . $receiver->name,
                    'image_link' => fake()->imageUrl(),
                    'creator_id' => $user->id,
                ]);

                UserAlert::query()->create([
                    'title' => 'You received a new message',
                    'action' => ($receiver->role == 'Admin' ? '/admin/chat' : '/alumni/chat') . '?initiate=' . $receiver->id,
                    'content' => $request->post('message'),
                    'user_id' => $receiver->id,
                ]);

                ChatMessage::query()->create([
                    'sender_id' => $user->id,
                    'content' => $request->post('message'),
                    'chat_group_id' => $group->id,
                ]);

                ChatAssociation::query()->create([
                    'chat_group_id' => $group->id,
                    'user_id' => $user->id,
                ]);

                ChatAssociation::query()->create([
                    'chat_group_id' => $group->id,
                    'user_id' => $receiver->id,
                ]);

                return response()->json(['group' => $group->id]);
            }
        } else {
            $group = ChatGroup::query()->where('internal_id', '=', $request->post('room_id'))->first();

            foreach ($group->users()->get() as $userRec) {
                UserAlert::query()->create([
                    'title' => 'You received a new message',
                    'action' => ($userRec->role == 'Admin' ? '/admin/chat' : '/alumni/chat') . '?initiate=' . urlencode($group->internal_id),
                    'content' => $group->name . ': ' .  $request->post('message'),
                    'user_id' => $userRec->id,
                ]);
            }

            ChatMessage::query()->create([
                'sender_id' => $user->id,
                'content' => $request->post('message'),
                'chat_group_id' => $group->id,
            ]);

            return response()->json(['group' => $group->id]);
        }
    }
}
