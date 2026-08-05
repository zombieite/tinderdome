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
                        @if ($logged_in_user && ($profile_id == $logged_in_user->id || $comment->commenting_user_id == $logged_in_user->id))
                            <form action="/profile/comment/{{ $comment->comment_id }}" method="POST" style="display:inline;">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <input type="submit" class="no tight" value="Delete comment">
                            </form>
                        @endif
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
                        <form action="/profile/comment/{{ $comment->comment_id }}" method="POST" style="display:inline;">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <input type="submit" class="no tight" value="Delete comment">
                        </form>
                    </div>
                </div>
            </li>
        @endif
    @endforeach
    </ul>
