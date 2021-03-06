<div class="profile_search_block">
    <div style="display:inline-block;">
        <a href="/profile/{{ $profile->profile_id }}/{{ $profile->wasteland_name_hyphenated }}">
            <img src="/uploads/image-{{ $profile->profile_id }}-1.jpg" style="height:100px;" loading="lazy">
        </a>
    </div>
    <div style="display:inline-block;">
        {{ $titles[$profile->title_index] }}
        <a href="/profile/{{ $profile->profile_id }}/{{ $profile->wasteland_name_hyphenated }}">{{ $profile->name }} (View profile)</a>
        <br>
        Votes: <span class="bright">{{ $profile->votes }}</span>
        @if ($profile->video_id)
            <a href="https://www.youtube.com/watch?v={{ $profile->video_id }}">(View video)</a>
        @endif
        <br><br>
        @include('voting_form', [])
    </div>
</div>
