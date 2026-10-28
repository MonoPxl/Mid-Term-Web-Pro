<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Show the public feed with search and filtering.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'exists:categories,slug'],
            'type' => ['nullable', Rule::enum(PostType::class)],
        ]);

        $keyword = trim($filters['q'] ?? '');
        $categorySlug = $filters['category'] ?? null;
        $type = $filters['type'] ?? null;

        $query = Post::query()
            ->published()
            ->with(['author', 'category'])
            ->withCount('comments');

        if ($request->user() !== null) {
            $query->withExists([
                'bookmarks as is_bookmarked' => fn ($bookmarkQuery) => $bookmarkQuery->where('user_id', $request->user()->id),
            ]);
        }

        if ($keyword !== '') {
            $query->where(function ($query) use ($keyword): void {
                $query->where('title', 'like', '%'.$keyword.'%')
                    ->orWhere('body', 'like', '%'.$keyword.'%');
            });
        }

        if ($categorySlug !== null) {
            $query->whereRelation('category', 'slug', $categorySlug);
        }

        if ($type !== null) {
            $query->where('type', $type);
        }

        $posts = $query
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get();

        $hasFilters = $keyword !== '' || $categorySlug !== null || $type !== null;

        return view('posts.index', compact('posts', 'categories', 'filters', 'hasFilters'));
    }

    /**
     * Show a single post.
     */
    public function show(Post $post): View
    {
        abort_unless(Gate::allows('view', $post), 404);

        $post->load(['author', 'category'])
            ->loadCount(['bookmarks', 'comments']);

        $isPublished = $post->isPublished();

        $comments = $isPublished
            ? $post->comments()
                ->whereNull('parent_id')
                ->with(['user', 'replies' => fn ($query) => $query->with('user')->oldest()])
                ->withCount('replies')
                ->oldest()
                ->get()
            : collect();

        $isBookmarked = $isPublished && auth()->check()
            && $post->bookmarks()->where('user_id', auth()->id())->exists();

        return view('posts.show', compact('post', 'comments', 'isBookmarked'));
    }

    /**
     * Show the form for creating a post.
     */
    public function create(): View
    {
        Gate::authorize('create', Post::class);

        $categories = Category::query()->orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created post.
     */
    public function store(PostRequest $request): RedirectResponse
    {
        Gate::authorize('create', Post::class);

        $user = $request->user();
        abort_if($user === null, 401);

        $data = $request->validated();
        $type = PostType::from($data['type']);
        $status = PostStatus::from($data['status']);
        $isEvent = $type === PostType::Event;

        $coverImage = $request->file('cover_image');
        $coverImagePath = $coverImage instanceof UploadedFile
            ? $coverImage->store('posts/covers', 'public')
            : null;

        $post = $user->posts()->create([
            'category_id' => $data['category_id'],
            'type' => $type,
            'status' => $status,
            'title' => $data['title'],
            'body' => $data['body'],
            'cover_image_path' => $coverImagePath,
            'published_at' => $status === PostStatus::Published ? now() : null,
            'event_starts_at' => $isEvent ? $data['event_starts_at'] : null,
            'event_ends_at' => $isEvent ? $data['event_ends_at'] : null,
            'event_location' => $isEvent ? $data['event_location'] : null,
        ]);

        if ($status === PostStatus::Published) {
            return redirect()
                ->route('posts.show', $post)
                ->with('status', __('Post published successfully.'));
        }

        return redirect()
            ->route('dashboard')
            ->with('status', __('Draft saved successfully.'));
    }

    /**
     * Show the form for editing a post.
     */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        $categories = Category::query()->orderBy('name')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Update an existing post.
     */
    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $data = $request->validated();
        $type = PostType::from($data['type']);
        $status = PostStatus::from($data['status']);
        $isEvent = $type === PostType::Event;

        $publishedAt = $post->published_at;

        if ($status === PostStatus::Published && ! $post->isPublished()) {
            $publishedAt = now();
        } elseif ($status === PostStatus::Draft) {
            $publishedAt = null;
        }

        $coverImagePath = $post->cover_image_path;

        if ($request->boolean('remove_cover_image') && filled($coverImagePath)) {
            Storage::disk('public')->delete($coverImagePath);
            $coverImagePath = null;
        }

        $coverImage = $request->file('cover_image');

        if ($coverImage instanceof UploadedFile) {
            if (filled($coverImagePath)) {
                Storage::disk('public')->delete($coverImagePath);
            }

            $coverImagePath = $coverImage->store('posts/covers', 'public');
        }

        $post->update([
            'category_id' => $data['category_id'],
            'type' => $type,
            'status' => $status,
            'title' => $data['title'],
            'body' => $data['body'],
            'cover_image_path' => $coverImagePath,
            'published_at' => $publishedAt,
            'event_starts_at' => $isEvent ? $data['event_starts_at'] : null,
            'event_ends_at' => $isEvent ? $data['event_ends_at'] : null,
            'event_location' => $isEvent ? $data['event_location'] : null,
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', __('Post updated successfully.'));
    }

    /**
     * Delete a post.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()
            ->route('dashboard')
            ->with('status', __('Post deleted successfully.'));
    }
}
