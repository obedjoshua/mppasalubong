@php
    $isAnnouncement = $post->type === 'announcement';
    $cardClass = $isAnnouncement ? 'card-announcement' : 'card-standard';
    $isAuthorOrAdmin = Auth::check() && (Auth::id() === $post->user_id || Auth::user()->role === 'admin');
@endphp

<!-- POST OPTIONS SCRIPT -->
<script>
    function toggleMenu(postId) {
        document.querySelectorAll('.post-menu-dropdown').forEach(menu => {
            if (menu.id !== 'menu-' + postId) menu.style.display = 'none';
        });
        const menu = document.getElementById('menu-' + postId);
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.post-options-container')) {
            document.querySelectorAll('.post-menu-dropdown').forEach(m => m.style.display = 'none');
        }
    });
</script>

@if($post->type === 'achievement')

    <div class="post-card" style="background: #FFF; border: 2px solid #000000; border-radius: 16px; padding: 20px; position: relative; margin-bottom: 25px;">
        
        <!-- Post Options -->
        <div class="post-options-container" style="position: absolute; top: 15px; right: 15px; z-index: 5;">
            <button type="button" onclick="toggleMenu('{{ $post->id }}')" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #9CA3AF; padding: 0 5px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                    <circle cx="12" cy="12" r="1"></circle>
                    <circle cx="19" cy="12" r="1"></circle>
                    <circle cx="5" cy="12" r="1"></circle>
                </svg>
            </button>

            <div id="menu-{{ $post->id }}" class="post-menu-dropdown" style="display: none; position: absolute; right: 0; top: 25px; background: #FFF; border: 2px solid #E5E7EB; border-radius: 12px; box-shadow: 0 10px 15px rgba(0,0,0,0.1); overflow: hidden; z-index: 10; width: 130px;">
                @if($isAuthorOrAdmin)
                    <a href="{{ route('community.post.edit', $post->id) }}" style="display: block; padding: 10px 15px; color: #374151; text-decoration: none; font-family: 'modern-bold'; font-size: 13px; border-bottom: 1px solid #F3F4F6;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 6px;">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
            </a>
                    <form action="{{ route('community.post.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?');">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 10px 15px; color: #EF4444; font-family: 'modern-bold'; font-size: 13px; cursor: pointer;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 6px;">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                Delete
            </button>
                    </form>
                @else
                    @auth
                        <form action="{{ route('community.post.report', $post->id) }}" method="POST" onsubmit="return confirm('Report this post?');">
                            @csrf
                            <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 10px 15px; color: #EF4444; font-family: 'modern-bold'; font-size: 13px; cursor: pointer;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 6px;">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" y1="22" x2="4" y2="15"></line>
                </svg>
                Report Post
            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" style="display: block; padding: 10px 15px; color: #EF4444; text-decoration: none; font-family: 'modern-bold'; font-size: 13px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 6px;">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" y1="22" x2="4" y2="15"></line>
                </svg>
                Report Post
            </a>
                    @endauth
                @endif
            </div>
        </div>

        <!-- Header -->
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
            <img src="{{ !empty($post->user->avatar) ? asset($post->user->avatar) : asset('images/default-avatar.jpeg') }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;">
            <div>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <span style="font-family: 'modern-bold', sans-serif; font-size: 16px; color: #374151;">{{ $post->user->name }}</span>
                </div>
                <div style="font-family: 'Manrope', sans-serif; font-size: 14px; color: #9CA3AF; font-weight: 700; margin-top: 2px;">
                    {{ $post->created_at->diffForHumans() }}
                </div>
            </div>
        </div>

        <!-- Body -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="flex: 1; padding-right: 15px;">
                <h3 style="font-family: 'modern-bold', sans-serif; font-size: 22px; color: #6B7280; margin: 0; line-height: 1.4;">
                    {{ $post->content }}
                </h3>
            </div>
            <div style="flex-shrink: 0; position: relative;">
                @if($post->badge)
                    <img src="{{ asset($post->badge->icon_path) }}" style="width: 100px; height: 100px; object-fit: contain; filter: drop-shadow(0px 8px 12px rgba(245,158,11,0.3));">
                @else
                    <div style="width: 100px; height: 100px; background: #FEF3C7; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" fill="currentColor" style="width: 40px; height: 40px; color: #F59E0B;">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 2px solid #F3F4F6; padding-top: 15px;">
            <button class="js-reaction-btn" data-post-id="{{ $post->id }}" data-emoji="🎉" style="background: #FFF; border: 2px solid #E5E7EB; border-radius: 12px; padding: 10px 16px; font-family: 'modern-bold', sans-serif; font-size: 13px; color: #4B5563; cursor: pointer; display: flex; gap: 8px; align-items: center; transition: 0.1s;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                CELEBRATE
            </button>

            @php
                $totalReactions = $post->reactions->count();
                $reactionEmojis = [
                    '🍰' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>',
                    '🔥' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3.5 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path></svg>',
                    '😍' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>',
                    '🎂' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>',
                    '🎉' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
                ];
            @endphp
            @if($totalReactions > 0)
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="display: flex; flex-direction: row-reverse;">
                        <?php $zCount = 1; ?>
                        @foreach($post->reactions->unique('emoji')->take(3) as $reaction)
                            <div style="width: 28px; height: 28px; background: #FFF; border: 2px solid #E5E7EB; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 14px; margin-left: -10px; z-index: <?php echo $zCount++; ?>;">
                                {!! $reactionEmojis[$reaction->emoji] ?? $reaction->emoji !!}
                            </div>
                        @endforeach
                    </div>
                    <span style="font-family: 'Manrope', sans-serif; font-size: 14px; font-weight: 800; color: #9CA3AF;">{{ $totalReactions }}</span>
                </div>
            @endif
        </div>
    </div>

@else
   
    <div class="post-card {{ $cardClass }}" id="post-{{ $post->id }}" style="position: relative; border-radius: 16px; margin-bottom: 25px;">
        @if($post->is_pinned)
            <div style="position: absolute; top: -12px; right: 50px; background: #EF4444; color: white; padding: 4px 12px; border-radius: 12px; font-family: 'modern-bold'; font-size: 12px; border: 2px solid #000;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 12px; height: 12px; vertical-align: middle; margin-right: 4px;">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            PINNED
            </div>
        @endif 

        <div class="post-options-container" style="position: absolute; top: 15px; right: 15px; z-index: 5;">
            <button type="button" onclick="toggleMenu('{{ $post->id }}')" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #6B7280; padding: 0 5px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                    <circle cx="12" cy="12" r="1"></circle>
                    <circle cx="19" cy="12" r="1"></circle>
                    <circle cx="5" cy="12" r="1"></circle>
                </svg>
            </button>
            <div id="menu-{{ $post->id }}" class="post-menu-dropdown" style="display: none; position: absolute; right: 0; top: 25px; background: #FFF; border: 2px solid #E5E7EB; border-radius: 12px; box-shadow: 0 10px 15px rgba(0,0,0,0.1); overflow: hidden; z-index: 10; width: 130px;">
                @if($isAuthorOrAdmin)
                    <a href="{{ route('community.post.edit', $post->id) }}" style="display: block; padding: 10px 15px; color: #374151; text-decoration: none; font-family: 'modern-bold'; font-size: 13px; border-bottom: 1px solid #F3F4F6;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 6px;">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
            </a>
                    <form action="{{ route('community.post.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?');">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 10px 15px; color: #EF4444; font-family: 'modern-bold'; font-size: 13px; cursor: pointer;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 6px;">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                Delete
            </button>
                    </form>
                @else
                    @auth
                        <form action="{{ route('community.post.report', $post->id) }}" method="POST" onsubmit="return confirm('Report this post?');">
                            @csrf
                            <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 10px 15px; color: #EF4444; font-family: 'modern-bold'; font-size: 13px; cursor: pointer;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 6px;">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" y1="22" x2="4" y2="15"></line>
                </svg>
                Report Post
            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" style="display: block; padding: 10px 15px; color: #EF4444; text-decoration: none; font-family: 'modern-bold'; font-size: 13px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 6px;">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" y1="22" x2="4" y2="15"></line>
                </svg>
                Report Post
            </a>
                    @endauth
                @endif
            </div>
        </div>

        <!-- 1. Tagged Post Header -->
        <div class="post-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
            @php
                $postAuthor = $post->user;
                $authorOrderCount = method_exists($postAuthor, 'orders') ? $postAuthor->orders()->count() : 0;
                $frameClass = 'frame_reg01';
                if ($postAuthor->role === 'admin' && $postAuthor->selected_frame === 'frame_exclusive') {
                    $frameClass = 'frame_exclusive';
                } elseif ($authorOrderCount >= 100) { $frameClass = 'frame_100'; }
                elseif ($authorOrderCount >= 20) { $frameClass = 'frame_20'; }
                elseif ($authorOrderCount >= 10) { $frameClass = 'frame_10'; }
            @endphp
            
            <div class="author-container" onclick="openProfileModal('{{ $postAuthor->id }}')">
                <img src="{{ !empty($postAuthor->avatar) ? asset($postAuthor->avatar) : asset('images/default-avatar.jpeg') }}" class="author-img">
                <img src="{{ asset('images/frames/' . ($postAuthor->selected_frame ?: $frameClass) . '.png') }}" class="author-frame">
            </div>
            <div>
                <span onclick="openProfileModal('{{ $postAuthor->id }}')" style="font-family: 'modern-bold', sans-serif; font-size: 16px; color: #000; cursor: pointer; display: block;">
                    {{ $postAuthor->name }}
                </span>
                <span style="font-size: 11px; font-family: 'Manrope', sans-serif; color: #6B7280; font-weight: 700;">
                    {{ $post->created_at->diffForHumans() }}
                    @if($isAnnouncement) <span style="color: #8A38F5; margin-left: 5px;">(Announcement)</span> @endif
                    @if($post->type === 'review') <span style="color: #F59E0B; margin-left: 5px;">(Review)</span> @endif
                </span>
            </div>
        </div>

        @if($post->type === 'review' && !empty($post->review_metrics))
            <div style="background: #FFFBEB; border: 2px dashed #FCD34D; border-radius: 12px; padding: 15px; margin-bottom: 15px;">
                @foreach($post->review_metrics as $category => $rating)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-family: 'Manrope', sans-serif; font-size: 14px; font-weight: 700;">
                        <span style="color: #4B5563;">{{ $category }}</span>
                        <div>
                            @for($i = 1; $i <= 5; $i++)
                                <span style="color: {{ $i <= $rating ? '#F59E0B' : '#D1D5DB' }}; font-size: 16px;">★</span>
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- 2. Tagged Text Content -->
        <p class="post-text-content" style="font-family: 'Manrope', sans-serif; font-size: 14px; color: #1F2937; line-height: 1.5; margin: 10px 0; padding-right: 20px;">
            {{ $post->content }}
        </p>

        @if(!empty($post->images))
            @php $imgCount = count($post->images); @endphp
            <div class="fb-image-grid grid-{{ min($imgCount, 4) }}" style="margin: 15px 0;">
                @foreach(array_slice($post->images, 0, 4) as $index => $img)
                    <div class="img-wrapper" onclick="openFbModal('{{ asset($img) }}', 'post-{{ $post->id }}')">
                        <img src="{{ asset($img) }}" alt="Post image">
                        @if($index === 3 && $imgCount > 4)
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; color: white; font-family: 'modern-bold', sans-serif; font-size: 28px;">
                                +{{ $imgCount - 4 }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- 3. Reactions -->
        <div class="reaction-row">
            @php
                $reactionEmojis = [
                    '🍰' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>',
                    '🔥' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3.5 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path></svg>',
                    '😍' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>',
                    '🎂' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>',
                ];
            @endphp
            @foreach(['🍰', '🔥', '😍', '🎂'] as $emoji)
                @php
                    $emojiCount = $post->reactions->where('emoji', $emoji)->count();
                    $userReacted = Auth::check() && $post->reactions->where('emoji', $emoji)->where('user_id', Auth::id())->isNotEmpty();
                @endphp
                <button class="reaction-btn {{ $userReacted ? 'user-reacted' : '' }} js-reaction-btn" data-post-id="{{ $post->id }}" data-emoji="{{ $emoji }}">
                    {!! $reactionEmojis[$emoji] !!}
                    <span class="js-reaction-count">{{ $emojiCount }}</span>
                </button>
            @endforeach
        </div>

        <div class="comments-section" style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #E5E7EB;">
            <h4 style="font-family: 'modern-bold', sans-serif; font-size: 14px; margin-bottom: 12px; cursor: pointer; display: inline-block; color: #4B5563;" 
                data-post="post-{{ $post->id }}" 
                data-author="{{ $postAuthor->name }}" 
                onclick="openCommentModal(this.dataset.post, this.dataset.author)">
                View all Comments ({{ $post->comments->count() }})
            </h4>
            
            <div style="max-height: 380px; overflow-y: auto; padding-right: 5px; margin-bottom: 12px;">
                @foreach($post->comments as $comment)
                    @php
                        $commentUser = $comment->user;
                        $commentAuthorName = $commentUser?->name ?? 'User';
                        
                        $cOrders = method_exists($commentUser, 'orders') ? $commentUser->orders()->count() : 0;
                        $cFrame = 'frame_reg01';
                        if ($commentUser?->role === 'admin' && $commentUser?->selected_frame === 'frame_exclusive') {
                            $cFrame = 'frame_exclusive';
                        } elseif ($cOrders >= 100) { $cFrame = 'frame_100'; }
                        elseif ($cOrders >= 20) { $cFrame = 'frame_20'; }
                        elseif ($cOrders >= 10) { $cFrame = 'frame_10'; }
                        if ($commentUser?->selected_frame) { $cFrame = $commentUser->selected_frame; }
                    @endphp

                    <!-- Top Level Comment -->
                    <div style="margin-bottom: 12px;" id="comment-container-{{ $comment->id }}">
                        <div style="display: flex; gap: 10px; background: #F9FAFB; padding: 10px; border-radius: 12px;">
                            
                            <!-- Avatar + Frame Container -->
                            <div style="position: relative; width: 36px; height: 36px; flex-shrink: 0; cursor: pointer;" onclick="openProfileModal('{{ $commentUser?->id }}')">
                                <img src="{{ !empty($commentUser?->avatar) ? asset($commentUser->avatar) : asset('images/default-avatar.jpeg') }}" style="width: 28px; height: 28px; border-radius: 25%; object-fit: cover; position: absolute; top: 4px; left: 4px; z-index: 1;">
                                <img src="{{ asset('images/frames/' . $cFrame . '.png') }}" style="position: absolute; top: 0; left: 0; width: 36px; height: 36px; object-fit: contain; z-index: 2; pointer-events: none;">
                            </div>

                            <div style="flex: 1;">
                                <div style="font-family: 'modern-bold', sans-serif; font-size: 13px; color: #1F2937;">
                                    {{ $commentUser?->name ?? 'Deleted User' }}
                                    <span style="font-family: 'Manrope'; font-weight: 500; font-size: 11px; color: #9CA3AF; margin-left: 5px;">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div style="font-family: 'Manrope', sans-serif; font-size: 13px; color: #4B5563; margin-top: 2px;">
                                    {{ $comment->comment }}
                                </div>
                                @auth
                                <button type="button" 
                                        data-post-id="{{ $post->id }}" 
                                        data-comment-id="{{ $comment->id }}" 
                                        data-author-name="{{ $commentAuthorName }}" 
                                        onclick="openInlineReplyBox(this.dataset.postId, this.dataset.commentId, this.dataset.authorName)" 
                                        style="background: none; border: none; color: #8A38F5; font-family: 'modern-bold'; font-size: 11px; cursor: pointer; padding: 2px 0; margin-top: 4px;">
                                    Reply
                                </button>
                                @endauth
                            </div>
                        </div>

                        <!-- Nested Replies (Collapsible) -->
                        @if($comment->replies && $comment->replies->count() > 0)
                            <div style="margin-left: 36px; margin-top: 6px;">
                                <button type="button" 
                                        id="toggle-replies-btn-{{ $comment->id }}"
                                        onclick="toggleReplies('{{ $post->id }}', '{{ $comment->id }}', '{{ addslashes($postAuthor->name) }}')"
                                        style="background: none; border: none; color: #65676B; font-family: 'modern-bold', sans-serif; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 4px; padding: 4px 0;">
                                    <span id="toggle-icon-{{ $comment->id }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;">
                                        <path d="M13 9l6 6-6 6"></path>
                                        <path d="M19 9v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5"></path>
                                    </svg>
                                </span> 
                                    <span id="toggle-text-{{ $comment->id }}">View {{ $comment->replies->count() }} {{ Str::plural('reply', $comment->replies->count()) }}</span>
                                </button>

                                <div id="replies-container-{{ $comment->id }}" style="display: none; margin-top: 8px; flex-direction: column; gap: 8px; border-left: 2px solid #E5E7EB; padding-left: 10px;">
                                    @foreach($comment->replies as $reply)
                                        @php
                                            $replyUser = $reply->user;
                                            $replyAuthorName = $replyUser?->name ?? 'User';
                                            
                                            $rOrders = method_exists($replyUser, 'orders') ? $replyUser->orders()->count() : 0;
                                            $rFrame = 'frame_reg01';
                                            if ($replyUser?->role === 'admin' && $replyUser?->selected_frame === 'frame_exclusive') {
                                                $rFrame = 'frame_exclusive';
                                            } elseif ($rOrders >= 100) { $rFrame = 'frame_100'; }
                                            elseif ($rOrders >= 20) { $rFrame = 'frame_20'; }
                                            elseif ($rOrders >= 10) { $rFrame = 'frame_10'; }
                                            if ($replyUser?->selected_frame) { $rFrame = $replyUser->selected_frame; }
                                        @endphp
                                        <div style="display: flex; gap: 8px; background: #F3F4F6; padding: 8px; border-radius: 10px;" id="comment-container-{{ $reply->id }}">
                                            
                                            <div style="position: relative; width: 30px; height: 30px; flex-shrink: 0; cursor: pointer;" onclick="openProfileModal('{{ $replyUser?->id }}')">
                                                <img src="{{ !empty($replyUser?->avatar) ? asset($replyUser->avatar) : asset('images/default-avatar.jpeg') }}" style="width: 24px; height: 24px; border-radius: 25%; object-fit: cover; position: absolute; top: 3px; left: 3px; z-index: 1;">
                                                <img src="{{ asset('images/frames/' . $rFrame . '.png') }}" style="position: absolute; top: 0; left: 0; width: 30px; height: 30px; object-fit: contain; z-index: 2; pointer-events: none;">
                                            </div>

                                            <div style="flex: 1;">
                                                <div style="font-family: 'modern-bold', sans-serif; font-size: 12px; color: #1F2937;">
                                                    {{ $replyUser?->name ?? 'Deleted User' }}
                                                    <span style="font-family: 'Manrope'; font-weight: 500; font-size: 10px; color: #9CA3AF; margin-left: 5px;">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <div style="font-family: 'Manrope', sans-serif; font-size: 12px; color: #4B5563; margin-top: 2px;">
                                                    {{ $reply->comment }}
                                                </div>
                                                @auth
                                                <button type="button" 
                                                        data-post-id="{{ $post->id }}" 
                                                        data-comment-id="{{ $comment->id }}" 
                                                        data-author-name="{{ $replyAuthorName }}" 
                                                        onclick="openInlineReplyBox(this.dataset.postId, this.dataset.commentId, this.dataset.authorName)" 
                                                        style="background: none; border: none; color: #8A38F5; font-family: 'modern-bold'; font-size: 10px; cursor: pointer; padding: 2px 0; margin-top: 2px;">
                                                    Reply
                                                </button>
                                                @endauth
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Dynamic Inline Reply Injection Slot -->
                        <div id="inline-reply-slot-{{ $comment->id }}" style="margin-left: 25px; margin-top: 8px;"></div>
                    </div>
                @endforeach
            </div>

            <!-- Main Comment Bar -->
            @auth
            @php
                $authUser = Auth::user();
                $aOrders = method_exists($authUser, 'orders') ? $authUser->orders()->count() : 0;
                $aFrame = 'frame_reg01';
                if ($authUser->role === 'admin' && $authUser->selected_frame === 'frame_exclusive') {
                    $aFrame = 'frame_exclusive';
                } elseif ($aOrders >= 100) { $aFrame = 'frame_100'; }
                elseif ($aOrders >= 20) { $aFrame = 'frame_20'; }
                elseif ($aOrders >= 10) { $aFrame = 'frame_10'; }
                if ($authUser->selected_frame) { $aFrame = $authUser->selected_frame; }
            @endphp
            <form action="{{ route('community.comment.store', $post->id) }}" method="POST" id="comment-form-{{ $post->id }}">
                @csrf
                <input type="hidden" name="parent_id" id="parent-id-input-{{ $post->id }}" value="">
                
                <div style="display: flex; align-items: center; gap: 10px;">
                    <!-- Main Comment Bar Avatar + Frame Container -->
                    <div style="position: relative; width: 36px; height: 36px; flex-shrink: 0;">
                        <img src="{{ !empty($authUser->avatar) ? asset($authUser->avatar) : asset('images/default-avatar.jpeg') }}" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; position: absolute; top: 4px; left: 4px; z-index: 1;">
                        <img src="{{ asset('images/frames/' . $aFrame . '.png') }}" style="position: absolute; top: 0; left: 0; width: 36px; height: 36px; object-fit: contain; z-index: 2; pointer-events: none;">
                    </div>

                    <div style="flex: 1; position: relative;">
                        <input type="text" 
                               name="comment" 
                               id="comment-input-{{ $post->id }}" 
                               placeholder="Comment as {{ $authUser->name }}" 
                               required 
                               style="width: 100%; padding: 10px 45px 10px 15px; border: 2px solid #D1D5DB; border-radius: 20px; font-family: 'Manrope'; font-size: 13px; outline: none; box-sizing: border-box; background: #F9FAFB;">
                        <button type="submit" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: #8A38F5; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
            @endauth
        </div>
    </div>
@endif