<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Bookmark a published post for the authenticated user.
     */
    public function store(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->isPublished(), 404);

        $user = $request->user();
        abort_if($user === null, 401);

        $user->bookmarks()->firstOrCreate([
            'post_id' => $post->id,
        ]);

        return back()->with('status', __('Post bookmarked.'));
    }

    /**
     * Remove the authenticated user's bookmark from a post.
     */
    public function destroy(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->isPublished(), 404);

        $user = $request->user();
        abort_if($user === null, 401);

        $user->bookmarks()
            ->where('post_id', $post->id)
            ->delete();

        return back()->with('status', __('Bookmark removed.'));
    }
}
