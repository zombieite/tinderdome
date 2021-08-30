<div class="profile_search_block">
    <div style="display:inline-block;">
        @if ($upcoming_event->elected_user_number_photos)
            <a href="/profile/{{ $upcoming_event->elected_user_id }}/{{ $upcoming_event->elected_user_wasteland_name_hyphenated }}">
                <img src="/uploads/image-{{ $upcoming_event->elected_user_id }}-1.jpg" style="height:100px;" loading="lazy">
            </a>
        @endif
    </div>
    <div style="display:inline-block;">
        <a href="/profile/{{ $upcoming_event->elected_user_id }}/{{ $upcoming_event->elected_user_wasteland_name_hyphenated }}">{{ $upcoming_event->elected_user_name }}</a>
    </div>
</div>
