<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Reaction;
use App\Models\User;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        
        $query = Post::with([
            'user.badges', 
            'comments.user', 
            'comments.replies.user', // <-- ADD THIS to fix the memory leak!
            'reactions', 
            'badge'
        ])
        ->orderByDesc('is_pinned')
        ->latest();
        
        if ($filter !== 'all') {
            $query->where('type', $filter);
        }
        
        $posts = $query->paginate(20);

        // Calculate User Progress (For Right Sidebar)
        $nextOrderBadge = null;
        $nextPostBadge = null;
        $orderCount = 0;
        $postCount = 0;

        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $orderCount = method_exists($user, 'orders') ? $user->orders()->count() : 0;
            $postCount = $user->posts()->count();

            $nextOrderBadge = Badge::where('order_requirement', '>', $orderCount)
                                   ->orderBy('order_requirement', 'asc')
                                   ->first();

            $postBadges = Badge::where('slug', 'like', 'post_%')->get();
            foreach ($postBadges as $badge) {
                $req = (int) str_replace('post_', '', $badge->slug);
                if ($req > $postCount) {
                    $nextPostBadge = (object) ['badge' => $badge, 'req' => $req];
                    break;
                }
            }
        }

        // Fetch ALL unshared achievements for the logged-in user
        $latestBadge = null;
        $unsharedBadges = collect();

        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Find all badge IDs the user has ALREADY posted to the community feed
            $postedBadgeIds = Post::where('user_id', $user->id)
                ->where('type', 'achievement')
                ->whereNotNull('badge_id')
                ->pluck('badge_id')
                ->toArray();

            // Get unlocked badges that have NOT been shared yet
            $unsharedBadges = $user->badges()
                ->whereNotIn('badges.id', $postedBadgeIds)
                ->orderBy('user_badge.unlocked_at', 'desc')
                ->get();

            $latestBadge = $unsharedBadges->first();
        }

        return view('community.index', compact(
            'posts', 
            'filter', 
            'orderCount', 
            'postCount', 
            'nextOrderBadge', 
            'nextPostBadge', 
            'latestBadge', 
            'unsharedBadges'
        ));
    }

    public function create()
    {
        $isAdmin = Auth::check() && Auth::user()->email === 'obeddanieljoshua@gmail.com';
        return view('community.create', compact('isAdmin'));
    }

    public function storePost(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isAdmin = $user->email === 'obeddanieljoshua@gmail.com'; 

        $allowedTypes = $isAdmin ? 'thread,review,announcement,achievement' : 'thread,review,achievement';

        $request->validate([
            'type'       => 'required|in:' . $allowedTypes,
            'content'    => 'required|string|max:2000',
            'images'     => 'nullable|array|max:10', 
            'images.*'   => 'image|max:10240', 
            'categories' => 'nullable|array', 
            'ratings'    => 'nullable|array',
            'badge_id'   => 'nullable|exists:badges,id',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $imagePaths[] = 'storage/' . $imageFile->store('community_uploads', 'public');
            }
        }

        $reviewMetrics = null;
        if ($request->type === 'review' && $request->has('categories') && $request->has('ratings')) {
            $reviewMetrics = [];
            foreach ($request->categories as $index => $categoryName) {
                if (!empty($categoryName) && isset($request->ratings[$index])) {
                    $reviewMetrics[$categoryName] = (int) $request->ratings[$index];
                }
            }
        }

        $isPinned = false;
        if ($isAdmin && $request->has('is_pinned')) {
            $isPinned = true;
        }

        Post::create([
            'user_id'        => $user->id,
            'type'           => $request->type,
            'content'        => $request->content,
            'images'         => !empty($imagePaths) ? $imagePaths : null,
            'is_pinned'      => $isPinned,
            'review_metrics' => $reviewMetrics,
            'badge_id'       => $request->badge_id,
        ]);

        if (method_exists($user, 'checkAndAwardBadges')) {
            $user->checkAndAwardBadges();
        }

        return redirect()->route('community.index')->with('status', 'Post published successfully!');
    }

    public function storeComment(Request $request, $postId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $request->validate([
            'comment'   => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        Comment::create([
            'post_id'   => $postId,
            'user_id'   => $user->id,
            'parent_id' => $request->parent_id,
            'comment'   => $request->comment,
        ]);

        return back()->with('status', 'Reply posted!');
    }

    public function toggleReaction(Request $request, $postId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $request->validate(['emoji' => 'required|string']);
        
        $existing = Reaction::where('post_id', $postId)
            ->where('user_id', $user->id)
            ->where('emoji', $request->emoji)
            ->first();
            
        $action = 'added';
        if ($existing) {
            $existing->delete();
            $action = 'removed';
        } else {
            Reaction::create([
                'post_id' => $postId,
                'user_id' => $user->id,
                'emoji'   => $request->emoji,
            ]);
        }
        
        $newCount = Reaction::where('post_id', $postId)->where('emoji', $request->emoji)->count();
        
        return response()->json([
            'status' => 'success',
            'action' => $action,
            'count'  => $newCount,
            'emoji'  => $request->emoji
        ]);
    }

    public function getUserProfileJson($userId)
    {
        /** @var \App\Models\User $user */
        $user = User::with('badges')->findOrFail($userId);
        $orderCount = method_exists($user, 'orders') ? $user->orders()->count() : 0;
        
        $frameClass = 'frame_reg01';
        if ($user->email === 'obeddanieljoshua@gmail.com' && $user->selected_frame === 'frame_exclusive') {
            $frameClass = 'frame_exclusive';
        } elseif ($orderCount >= 100) { $frameClass = 'frame_100'; }
        elseif ($orderCount >= 20) { $frameClass = 'frame_20'; }
        elseif ($orderCount >= 10) { $frameClass = 'frame_10'; }
        
        $allBadges = Badge::all()->map(function ($badge) use ($user) {
            return [
                'name'        => $badge->name,
                'description' => $badge->description,
                'icon_path'   => asset($badge->icon_path),
                'unlocked'    => $user->badges->contains($badge->id),
            ];
        });
        
        return response()->json([
            'name'      => $user->name,
            'avatar'    => !empty($user->avatar) ? asset($user->avatar) : asset('images/default-avatar.jpeg'),
            'frame_src' => asset('images/frames/' . ($user->selected_frame ?: $frameClass) . '.png'),
            'orders'    => $orderCount,
            'badges'    => $allBadges,
        ]);
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();
        $isAdmin = $user->email === 'obeddanieljoshua@gmail.com';

        if ($post->user_id !== $user->id && !$isAdmin) {
            abort(403, 'Unauthorized action.');
        }

        return view('community.edit', compact('post', 'isAdmin'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();
        $isAdmin = $user->email === 'obeddanieljoshua@gmail.com';

        if ($post->user_id !== $user->id && !$isAdmin) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $post->update([
            'content' => $request->content,
        ]);

        return redirect()->route('community.index')->with('status', 'Post updated successfully!');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();
        $isAdmin = $user->email === 'obeddanieljoshua@gmail.com';

        if ($post->user_id !== $user->id && !$isAdmin) {
            abort(403, 'Unauthorized action.');
        }

        $post->delete();

        return back()->with('status', 'Post deleted successfully!');
    }
}