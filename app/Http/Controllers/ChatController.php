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
    public function fetchHeaders()
    {
        return view('chat.snippets.message-headers');
    }

    public function fetchChatMessages(ChatGroup $group)
    {
        return view('chat.snippets.messages')->with('group', $group);
    }

    public function acceptAssociation(ChatAssociation $assoc)
    {
        $assoc->update([
            'status' => 'accepted'
        ]);

        return back()
            ->with('message', 'Request accepted!')
            ->with('subtitle', 'You can now chat with this user');
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

        $file = request()->file('profile_pic');
        if ($file) {

            $filename = sha1(time()) . '.' . $file->getClientOriginalExtension();
            $file->storePubliclyAs('public/chat/images', $filename);

            $group->update([
                'image_link' => $filename,
            ]);
        }

        foreach ($group->users()->get() as $user) {
            UserAlert::query()->create([
                'title' => 'Renamed group chat to ' . $group->name,
                'action' => (User::find($user->id)->role == 'Admin' ? '/admin/chat' : '/alumni/chat') . '?initiate=' . urlencode($group->internal_id),
                'content' => 'Change GC name ' . $previousName . ' -> ' . $group->name,
                'user_id' => $user->id,
            ]);
        }

        $sender = User::query()->find(Auth::user()->id);
        $message = 'Group renamed from "' . $previousName . '" to "' . $group->name . '"';
        $subtitle = 'Group details have been updated successfully';

        if ($sender->role == 'Admin') {
            return redirect('/admin/chat')
                ->with('message', $message)
                ->with('subtitle', $subtitle);
        } else {
            return redirect('/alumni/chat')
                ->with('message', $message)
                ->with('subtitle', $subtitle);
        }
    }

    public function removeUserFromGroup(string $roomId, User $user)
    {
        $group = ChatGroup::query()
            ->where('internal_id', '=', $roomId)
            ->first();

        ChatAssociation::query()
            ->where('chat_group_id', '=', $group->id)
            ->where('user_id', '=', $user->id)
            ->delete();

        foreach ($group->users()->get() as $usr) {
            UserAlert::query()->create([
                'title' => 'Removed ' . $user->name . ' from ' . $group->name,
                'action' => (User::find($usr->id)->role == 'Admin' ? '/admin/chat' : '/alumni/chat') . '?initiate=' . urlencode($group->internal_id),
                'content' => 'User: ' . $user->name . ' was removed from ' . $group->name . ' by ' . Auth::user()->name,
                'user_id' => $usr->id,
            ]);
        }

        $message = 'Removed ' . $user->name . ' from ' . $group->name;
        $subtitle = 'User has been successfully removed from the group';

        if (Auth::user()->role === 'Admin') {
            return redirect('/admin/chat')
                ->with('message', $message)
                ->with('subtitle', $subtitle);
        } else {
            return redirect('/alumni/chat')
                ->with('message', $message)
                ->with('subtitle', $subtitle);
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

        return back()
            ->with('message', 'Members added successfully!')
            ->with('subtitle', 'New members have been added to the group chat');
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

        $message = 'You have left the group "' . $group->name . '"';
        $subtitle = 'You will no longer receive messages from this group';

        foreach ($group->users()->get() as $usr) {
            if ($usr->id !== Auth::user()->id) {
                UserAlert::query()->create([
                    'title' => Auth::user()->name . ' left ' . $group->name,
                    'action' => (User::find($usr->id)->role == 'Admin' ? '/admin/chat' : '/alumni/chat') . '?initiate=' . urlencode($group->internal_id),
                    'content' => 'User ' . Auth::user()->name . ' has left the group chat',
                    'user_id' => $usr->id,
                ]);
            }
        }

        if ($sender->role == 'Admin') {
            return redirect('/admin/chat')
                ->with('message', $message)
                ->with('subtitle', $subtitle);
        } else {
            return redirect('/alumni/chat')
                ->with('message', $message)
                ->with('subtitle', $subtitle);
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
            'chat_group_id' => $group->id,
            'status' => 'accepted'
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

        if ($request->hasFile('message')) {
            $request->validate([
                'sender' => 'required',
                'message' => ['required', 'file', 'max:10000'],
                'room_id' => 'required',
            ]);
        } else {
            $request->validate([
                'sender' => 'required',
                'message' => 'required',
                'room_id' => 'required',
            ]);
        }

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
                    'action' => ($receiver->role == 'Admin' ? '/admin/chat' : '/alumni/chat') . '?initiate=' . $receiver->id,
                    'content' => $request->hasFile('message') ? 'File sent to you' : $request->post('message'),
                    'user_id' => $receiver->id,
                ]);

                if ($request->hasFile('message')) {
                    $file = $request->file('message');
                    $filename = sha1(time() . $file->getClientOriginalName()) . '.' . base64_encode($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                    $file->storePubliclyAs('public/chat/files', $filename);

                    ChatMessage::query()->create([
                        'sender_id' => $user->id,
                        'type' => 'file',
                        'content' => $filename,
                        'chat_group_id' => $group->id,
                    ]);
                } else {
                    ChatMessage::query()->create([
                        'sender_id' => $user->id,
                        'type' => 'text',
                        'content' => $request->post('message'),
                        'chat_group_id' => $group->id,
                    ]);
                }

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
                    'content' => $request->hasFile('message') ? 'File sent to you' : $request->post('message'),
                    'user_id' => $receiver->id,
                ]);

                if ($request->hasFile('message')) {
                    $file = $request->file('message');
                    $filename = sha1(time() . $file->getClientOriginalName()) . '.' . base64_encode($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                    $file->storePubliclyAs('public/chat/files', $filename);

                    ChatMessage::query()->create([
                        'sender_id' => $user->id,
                        'type' => 'file',
                        'content' => $filename,
                        'chat_group_id' => $group->id,
                    ]);
                } else {
                    ChatMessage::query()->create([
                        'sender_id' => $user->id,
                        'type' => 'text',
                        'content' => $request->post('message'),
                        'chat_group_id' => $group->id,
                    ]);
                }

                ChatAssociation::query()->create([
                    'chat_group_id' => $group->id,
                    'user_id' => $user->id,
                    'status' => 'accepted'
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
                if ($user->id == $userRec->id) {
                    continue;
                }

                UserAlert::query()->create([
                    'title' => 'You received a new message',
                    'action' => ($userRec->role == 'Admin' ? '/admin/chat' : '/alumni/chat') . '?initiate=' . urlencode($group->internal_id),
                    'content' => $group->name . ': ' . ($request->hasFile('message') ? 'File sent to you' : $request->post('message')),
                    'user_id' => $userRec->id,
                ]);
            }
            if ($request->hasFile('message')) {
                $file = $request->file('message');
                $filename = sha1(time() . $file->getClientOriginalName()) . '.' . base64_encode($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                $file->storePubliclyAs('public/chat/files', $filename);

                ChatMessage::query()->create([
                    'sender_id' => $user->id,
                    'type' => 'file',
                    'content' => $filename,
                    'chat_group_id' => $group->id,
                ]);
            } else {
                ChatMessage::query()->create([
                    'sender_id' => $user->id,
                    'type' => 'text',
                    'content' => $request->post('message'),
                    'chat_group_id' => $group->id,
                ]);
            }

            return response()->json(['group' => $group->id]);
        }
    }
}
