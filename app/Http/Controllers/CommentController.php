<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    /**
     * Store a comment or reply on a published post.
     */
    public function store(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->isPublished(), 404);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id')->where('post_id', $post->id),
            ],
        ]);

        $user = $request->user();
        abort_if($user === null, 401);

        $parentId = $validated['parent_id'] ?? null;

        if ($parentId !== null) {
            $parent = $post->comments()->whereKey($parentId)->firstOrFail();

            // Keep discussions to one level so every reply stays visible in the thread.
            $parentId = $parent->parent_id ?? $parent->id;
        }

        $post->comments()->create([
            'user_id' => $user->id,
            'parent_id' => $parentId,
            'body' => trim($validated['body']),
        ]);

        return redirect()
            ->to(route('posts.show', $post).'#comments')
            ->with('status', __('Comment posted successfully.'));
    }

    /**
     * Delete the authenticated user's own comment.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('status', __('Comment deleted successfully.'));
    }
}
