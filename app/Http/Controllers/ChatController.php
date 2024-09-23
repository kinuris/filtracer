<?php

namespace App\Http\Controllers;

use App\Models\ChatAssociation;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    public function fetchChatMessages(ChatGroup $group)
    {
        return view('chat.snippets.messages')->with('group', $group);
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

            ChatMessage::query()->create([
                'sender_id' => $user->id,
                'content' => $request->post('message'),
                'chat_group_id' => $group->id,
            ]);

            return response()->json(['group' => $group->id]);
        }
    }
}
