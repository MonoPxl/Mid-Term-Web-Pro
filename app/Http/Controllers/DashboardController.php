<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the authenticated user's personal dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_if($user === null, 401);

        $posts = $user->posts()
            ->with('category')
            ->latest()
            ->get();

        $bookmarks = $user->bookmarks()
            ->whereRelation('post', 'status', PostStatus::Published->value)
            ->whereRelation('post', 'published_at', '<=', now())
            ->with(['post.author', 'post.category'])
            ->latest()
            ->get();

        $stats = [
            'posts' => $posts->count(),
            'published' => $posts->where('status', PostStatus::Published)->count(),
            'drafts' => $posts->where('status', PostStatus::Draft)->count(),
            'bookmarks' => $bookmarks->count(),
        ];

        return view('dashboard', compact('posts', 'bookmarks', 'stats'));
    }
}
