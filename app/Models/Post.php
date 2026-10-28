<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Enums\PostType;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property PostType $type
 * @property PostStatus $status
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property string|null $cover_image_path
 * @property bool $is_pinned
 * @property-read bool $is_bookmarked
 * @property Carbon|null $published_at
 * @property Carbon|null $event_starts_at
 * @property Carbon|null $event_ends_at
 * @property string|null $event_location
 * @property-read User $author
 * @property-read Category $category
 */
#[Fillable([
    'user_id',
    'category_id',
    'type',
    'status',
    'title',
    'slug',
    'body',
    'cover_image_path',
    'is_pinned',
    'published_at',
    'event_starts_at',
    'event_ends_at',
    'event_location',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => PostType::class,
            'status' => PostStatus::class,
            'is_pinned' => 'boolean',
            'published_at' => 'datetime',
            'event_starts_at' => 'datetime',
            'event_ends_at' => 'datetime',
        ];
    }

    /**
     * Generate a unique slug when a post is created without one.
     */
    protected static function booted(): void
    {
        static::creating(function (Post $post): void {
            if (blank($post->slug)) {
                $post->slug = static::uniqueSlug($post->title);
            }
        });

        static::deleting(function (Post $post): void {
            if (filled($post->cover_image_path)) {
                Storage::disk('public')->delete($post->cover_image_path);
            }
        });
    }

    /**
     * The user who wrote the post.
     *
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The category the post belongs to.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Comments left on the post.
     *
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Bookmarks saved for the post.
     *
     * @return HasMany<Bookmark, $this>
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Registrations for this event post.
     *
     * @return HasMany<EventRegistration, $this>
     */
    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Limit the query to posts that are visible in the public feed.
     *
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Limit the query to pinned posts.
     *
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Determine whether the post is an event.
     */
    public function isEvent(): bool
    {
        return $this->type === PostType::Event;
    }

    /**
     * Determine whether the post is currently visible to readers.
     */
    public function isPublished(): bool
    {
        return $this->status === PostStatus::Published
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    /**
     * Get the public URL for the post's cover image.
     */
    public function coverImageUrl(): ?string
    {
        if (blank($this->cover_image_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->cover_image_path);
    }

    /**
     * Build a short plain-text preview of the body.
     */
    public function excerpt(int $length = 155): string
    {
        return Str::limit(strip_tags($this->body), $length);
    }

    /**
     * Use the slug in URLs instead of the numeric ID.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Find an unused slug based on the given title.
     */
    protected static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $suffix = 2;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
