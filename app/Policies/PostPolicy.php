<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the post may be viewed.
     *
     * Published posts are public; drafts are only visible to their author.
     */
    public function view(?User $user, Post $post): bool
    {
        return $post->isPublished()
            || ($user !== null && $post->user_id === $user->id);
    }

    /**
     * Any authenticated user may create a post.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the author may update the post.
     */
    public function update(User $user, Post $post): bool
    {
        return $post->user_id === $user->id;
    }

    /**
     * Only the author may delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $post->user_id === $user->id;
    }
}
