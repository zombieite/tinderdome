<div class="profile_search_block">
    <div style="display:inline-block;">
        @if ($profile['number_photos'])
            <a href="/profile/{{ $profile['profile_id'] }}/{{ $profile['wasteland_name_hyphenated'] }}">
                <img src="/uploads/image-{{ $profile['profile_id'] }}-1.jpg" style="height:100px;">
            </a>
        @endif
    </div>
    <div style="display:inline-block;">
        <a name="profile{{ $profile['profile_id'] }}"></a>
        @if ($profile['missions_completed'])
            {{ $titles[$profile['title_index']] }}
        @endif
        <a href="/profile/{{ $profile['profile_id'] }}/{{ $profile['wasteland_name_hyphenated'] }}">{{ $profile['wasteland_name'] }}</a>
        @if ($profile['birth_year'])
            <br>
            @if ($profile['birth_year'] === 1959)
                Born before 1960
            @else
                Born in the {{ intval($profile['birth_year'] / 10) * 10 }}s
            @endif
        @endif
        @if ($profile['height'])
            @if ($profile['birth_year'])
                &middot;
            @else
                <br>
            @endif
            @if ($profile['height'] < 60)
                Under 5 feet
            @elseif ($profile['height'] > 72)
                Over 6 feet
            @else
                {{ floor($profile['height'] / 12) }}&apos;{{ $profile['height'] % 12 }}&quot;
            @endif
        @endif
        <br>
        @if ($profile['missions_completed'])
            <span>Missions completed: {{ $profile['missions_completed'] }}</span>
        @endif
    </div>
    <br>
    <br>
    @if ($logged_in_user_id == $profile['profile_id'])
        (You)
    @else
        @if ($profile['ok_to_rate_user'])
            @include('rating_form', ['action' => "#profile".$previous_profile_id, 'user_id_to_rate' => $profile['profile_id'], 'current_choice' => $profile['choice'], 'number_photos' => $profile['number_photos'], 'curse_interface' => $curse_interface])
        @endif
    @endif
</div>
