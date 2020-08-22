    <ul>
    @foreach ($comments as $comment)
        @if ($comment->approved)
            <li>
                <div class=" profile_search_block ">
                    <div style="display:inline-block;">
                        @if ($comment->user_number_photos)
                            <a href="/profile/{{ $comment->commenting_user_id }}/{{ $comment->commenting_user_wasteland_name_hyphenated }}"><img src="/uploads/image-{{ $comment->commenting_user_id }}-1.jpg" style="height:50px;"></a>
                        @endif
                    </div>
                    <div style="display:inline-block;">
                        <a href="/profile/{{ $comment->commenting_user_id }}/{{ $comment->commenting_user_wasteland_name_hyphenated }}">{{ $comment->name }}</a> {{ \Carbon\Carbon::parse($comment->created_at)->format('Y-m-d') }}:
                        {{ $comment->comment_content }}
                    </div>
                </div>
            </li>
        @elseif ($logged_in_user && $comment->commenting_user_id === $logged_in_user->id)
            <li>
                <div class=" profile_search_block ">
                    <div style="display:inline-block;">
                        @if ($comment->user_number_photos)
                            <a href="/profile/{{ $comment->commenting_user_id }}/{{ $comment->commenting_user_wasteland_name_hyphenated }}"><img src="/uploads/image-{{ $comment->commenting_user_id }}-1.jpg" style="height:50px;"></a>
                        @endif
                    </div>
                    <div style="display:inline-block;">
                        <span class="bright">Your comment will not become visible until it is approved.</span>
                        <a href="/profile/{{ $comment->commenting_user_id }}/{{ $comment->commenting_user_wasteland_name_hyphenated }}">{{ $comment->name }}</a>:
                        {{ $comment->comment_content }}
                    </div>
                </div>
            </li>
        @endif
    @endforeach
    </ul>
