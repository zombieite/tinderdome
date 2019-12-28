@extends('layouts.app')
@section('content')
@if ($show_mutuals)
    @if ($logged_in_user_number_photos)
        @if ($users_who_must_be_rated)
            You must <a href="/profile/compatible?">rate all users</a> before you can see your mutuals.<br><br>
        @else
            @if ($profiles_found_count)
                @if ($profiles_found_count === 1)
                    You have a user who has shared their contact info with you<br><br>
                @else
                    {{ $profiles_found_count }} users have shared their contact info with you<br><br>
                @endif
            @else
                No mutuals have shared their contact info with you yet<br><br>
            @endif
        @endif
    @else
        You must <a href="/image/upload">upload a photo</a> before you can see mutuals.<br><br>
    @endif
@else
    @if ($logged_in_user_hoping_to_find_love && $logged_in_user_share_info_with_favorites)
        <a href="/search?show_mutuals=1">See mutuals who have shared their contact info with you</a><br><br>
    @endif
@endif
@if ($show_yeses)
    @if ($profiles_found_count)
        @if ($profiles_found_count === 1)
            You have said you would enjoy meeting just one user.<br><br>
        @else
            You have said you would enjoy meeting {{ $profiles_found_count }} users.<br><br>
        @endif
    @else
        You have not let us know that you would enjoy meeting any users yet.<br><br>
    @endif
@else
    <a href="/search?show_yeses=1">See users you've said you would enjoy meeting</a><br><br>
@endif
@if ($show_met)
    @if ($profiles_found_count)
        @if ($profiles_found_count === 1)
            You've said you've met one user.<br><br>
        @else
            You've said you've met {{ $profiles_found_count }} users.<br><br>
        @endif
    @else
        You have not let us know that you have met any users yet.<br><br>
    @endif
@else
    <a href="/search?show_met=1">See users you've said you've met</a><br><br>
@endif
@if ($show_nos)
    @if ($profiles_found_count)
        @if ($profiles_found_count === 1)
            One user marked as No<br><br>
        @else
            {{ $profiles_found_count }} users marked as No<br><br>
        @endif
    @else
        No users marked as No<br><br>
    @endif
@else
    <a href="/search?show_nos=1">See users you've marked as No</a><br><br>
@endif
@if ($logged_in_user_number_photos)
    <a href="/photosearch">Show all photos</a><br><br>
    <a href="/photosearch?gender=o">Show all photos of users of gender other and unspecified</a><br><br>
    <a href="/photosearch?gender=m">Show all photos of men</a><br><br>
    <a href="/photosearch?gender=w">Show all photos of women</a><br><br>
@endif
@php $previous_profile_id = isset($profiles[0]) ? $profiles[0]['profile_id'] : ''; @endphp
@foreach ($profiles as $profile)
    @if ($profile['mutual_favorite'] || !$show_mutuals)
        <div class="@if ($profile['mutual_favorite']) profile_search_block_bright @else profile_search_block @endif">
            <div style="display:inline-block;">
                @if ($profile['number_photos'])
                    <a href="/profile/{{ $profile['profile_id'] }}/{{ $profile['wasteland_name_hyphenated'] }}">
                        <img src="/uploads/image-{{ $profile['profile_id'] }}-1.jpg" style="height:100px;">
                    </a>
                @endif
            </div>
            @if ($show_yeses)
                <br>
                <a href="/profile/{{ $profile['profile_id'] }}/{{ $profile['wasteland_name_hyphenated'] }}">{{ $profile['wasteland_name'] }}</a>
            @else
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
            @endif
            <br>
            <br>
            @if ($logged_in_user_id == $profile['profile_id'])
                (You)
            @else
                @if ($profile['ok_to_mark_user_found'])
                    @include('rating_form', ['action' => "#profile".$previous_profile_id, 'user_id_to_rate' => $profile['profile_id'], 'current_choice' => $profile['choice'], 'number_photos' => $profile['number_photos'], 'curse_interface' => $curse_interface])
                @endif
            @endif
        </div>
    @endif
    @php $previous_profile_id = $profile['profile_id'] @endphp
@endforeach
@endsection
