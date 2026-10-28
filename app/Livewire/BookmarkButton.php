<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BookmarkButton extends Component
{
    public int $postId;

    public bool $bookmarked = false;

    public string $variant = 'icon';

    public int $count = 0;

    /**
     * Mount the component.
     */
    public function mount(Post $post, bool $bookmarked = false, string $variant = 'icon', int $count = 0): void
    {
        $this->postId = $post->id;
        $this->bookmarked = $bookmarked;
        $this->variant = $variant;
        $this->count = $count;
    }

    /**
     * Toggle the current user's bookmark for the post.
     */
    public function toggle(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $post = Post::query()->findOrFail($this->postId);

        abort_unless($post->isPublished(), 404);

        $existing = $user->bookmarks()->where('post_id', $post->id);

        if ($existing->exists()) {
            $existing->delete();

            $this->bookmarked = false;

            Flux::toast(variant: 'success', text: __('Bookmark removed.'));
        } else {
            $user->bookmarks()->firstOrCreate(['post_id' => $post->id]);

            $this->bookmarked = true;

            Flux::toast(variant: 'success', text: __('Post bookmarked.'));
        }

        $this->count = $post->bookmarks()->count();

        $this->dispatch(
            'bookmark-toggled',
            postId: $post->id,
            bookmarked: $this->bookmarked,
            count: $this->count,
        );
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.bookmark-button');
    }
}
