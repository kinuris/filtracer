<?php

namespace App\Http\Controllers;

use App\Models\PinnedPost;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $isAnnouncement = $request->post('post_category') == 'Announcement';

        if (!$isAnnouncement) {
            $validator = Validator::make($request->all(), [
                'title' => ['required'],
                'content' => ['required'],
                'source' => ['nullable'],
                'post_category' => ['required'],
                'post_status' => ['required'],
                'attachment' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10000'],
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with('openModal', 1);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'title' => ['required'],
                'content' => ['required'],
                'source' => ['nullable'],
                'post_category' => ['required'],
                'attachment' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10000'],
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with('openModal', 1);
            }
        }

        $validated = $validator->validated();

        if ($request->hasFile('attachment')) {
            $filename = sha1(time()) . '.' . $request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->storePubliclyAs('public/post/attachments', $filename);
            $validated['attached_image'] = $filename;
        }

        $validated['user_id'] = Auth::user()->id;
        $post = Post::query()->create($validated);

        if (User::find(Auth::user()->id)->admin() !== null) {
           $post->update(['status' => 'Approved']); 
        }

        foreach (User::query()->where('role', '=', 'Admin')->get() as $admin) {
            UserAlert::query()->create([
                'title' => 'New ' . $request->post('post_category'),
                'content' => 'New post has been created by ' . Auth::user()->name,
                'action' => '/admin/post',
                'user_id' => $admin->id
            ]);
        }

        if (Auth::user()->role == 'Admin') {
            return redirect('/admin/post')->with('message', 'Post created successfully');
        } else {
            return redirect('/alumni/post')->with('message', 'Post created successfully');
        }
    }

    /**
     * Display the specified resource.
     */
    public function togglePinPost(Post $post)
    {
        $user = User::query()->find(Auth::user()->id);
        $pinned = $user->pinnedPosts()->where('post_id', $post->id)->exists();

        if ($pinned) {
            PinnedPost::query()->where('post_id', $post->id)->delete();
        } else {
            PinnedPost::query()->create([
                'post_id' => $post->id,
                'user_id' => Auth::user()->id,
            ]);
        }

        return back()->with('message', 'Pinned post toggled successfully');
    }

    public function toggleSavePost(Post $post)
    {
        $user = User::query()->find(Auth::user()->id);
        $saved = $user->savedPosts()->where('post_id', $post->id)->exists();

        if ($saved) {
            SavedPost::query()->where('post_id', $post->id)->delete();
        } else {
            SavedPost::query()->create([
                'post_id' => $post->id,
                'user_id' => Auth::user()->id,
            ]);
        }

        return back()->with('message', 'Saved post toggled successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $isAnnouncement = $request->post('post_category') == 'Announcement';

        if (!$isAnnouncement) {
            $validated = $request->validate([
                'title' => ['required'],
                'content' => ['required'],
                'source' => ['nullable'],
                'post_category' => ['required'],
                'post_status' => ['required'],
                'attachment' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10000'],
            ]);
        } else {
            $validated = $request->validate([
                'title' => ['required'],
                'content' => ['required'],
                'source' => ['nullable'],
                'post_category' => ['required'],
                'attachment' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10000'],
            ]);
        }

        if ($request->hasFile('attachment')) {
            Storage::delete('public/post/attachments/' . $post->attached_image);

            $filename = sha1(time()) . '.' . $request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->storePubliclyAs('public/post/attachments', $filename);

            $validated['attached_image'] = $filename;
        }

        $post->update($validated);

        if (Auth::user()->role == 'Admin') {
            return redirect('/admin/post')->with('message', 'Post updated successfully');
        } else {
            return redirect('/alumni/post')->with('message', 'Post updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->creator->id != Auth::user()->id) {
            return redirect('/admin/post')->with('message', 'You are not authorized to delete this post');
        }

        $post->delete();

        if (Auth::user()->role == 'Admin') {
            return redirect('/admin/post')->with('message', 'Post deleted successfully');
        } else {
            return redirect('/alumni/post')->with('message', 'Post deleted successfully');
        }
    }
}
