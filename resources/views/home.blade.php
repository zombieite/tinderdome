@extends('layouts.app')

@section('content')

@if ($success_message)
    <h2 class="bright">{{ $success_message }}</h2>
@endif

@foreach ($matched_to_users as $matched_to_user)
    @if ($matched_to_user->event_is_in_future and $matched_to_user->name and $matched_to_user->this_users_rating_of_logged_in_user !== 0)
        <h1>YOU ARE AWAITED AT {{ strtoupper($matched_to_user->event_long_name) }}! <a class="bright" href="/profile/match?event_id={{ $matched_to_user->event_id }}">Here's your match</a>.</h1>
    @endif
@endforeach

@if ($comments_to_approve)
    <h2 class="bright">You have new comments</h2>
    You can approve them or delete them. If you approve them they will appear on your profile.
    <ul class="nobullet">
    @foreach ($comments_to_approve as $comment)
        <li>
            <div class="profile_search_block ">
                <div style="display:inline-block;">
                    @if ($comment->user_number_photos)
                        <a href="/profile/{{ $comment->commenting_user_id }}/{{ $comment->commenting_user_wasteland_name_hyphenated }}"><img src="/uploads/image-{{ $comment->commenting_user_id }}-1.jpg" style="height:50px;"></a>
                    @endif
                </div>
                <div style="display:inline-block;">
                    <a href="/profile/{{ $comment->commenting_user_id }}/{{ $comment->commenting_user_wasteland_name_hyphenated }}">{{ $comment->name }}</a>:
                    {{ $comment->comment_content }}
                    <br>
                    <form action="/" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="comment_id" value="{{ $comment->comment_id }}">
                        <input type="submit" name="accept" class="yesyes" value="Approve">
                        <input type="submit" name="accept" class="no" value="Delete">
                    </form>
                </div>
            </div>
        </li>
    @endforeach
    </ul>
@endif

@if ($number_photos)
    @if (count($unrated_users) >= 3)
        <h2><a href="/profile/compatible?" class="bright">Let us know if you'd enjoy meeting these users</a></h2>
        @for ($i = 0; (($i < 6) && ($i < count($unrated_users))); $i++)
            @if ($unrated_users[$i]->number_photos)
                @include('user_block_enjoy_meeting', ['user_id' => $unrated_users[$i]->id])
            @endif
        @endfor
    @endif
    @if (count($users_who_say_they_know_you))
        <h2 class="bright">Do you know these users?</h2>
        @for ($i = 0; (($i < 6) && ($i < count($users_who_say_they_know_you))); $i++)
            @if ($users_who_say_they_know_you[$i]->number_photos)
                @include('user_block_maybe_known', ['user_id' => $users_who_say_they_know_you[$i]->user_id, 'wasteland_name_hyphenated' => $users_who_say_they_know_you[$i]->wasteland_name_hyphenated, 'name' => $users_who_say_they_know_you[$i]->name])
            @endif
        @endfor
    @endif
@endif

@if (count($mutuals))
    <h2>Users who have shared their contact info with you</h2>
    @foreach ($mutuals as $mutual)
        @include('user_block_mutual', ['number_photos' => $mutual->number_photos, 'user_id' => $mutual->id, 'wasteland_name_hyphenated' => $mutual->wasteland_name_hyphenated, 'name' => $mutual->name])
    @endforeach
@endif

<h2>Mission status</h2>
<ol>

@if ($number_photos)
    <li>COMPLETE: <a href="/profile/{{ $logged_in_user_id }}/{{ $wasteland_name_hyphenated }}">Profile</a> created.</li>
@else
    <li><a href="/image/upload" class="bright">INCOMPLETE: You must upload a photo</a>.</li>
@endif

@if ($upcoming_events_and_signup_status)
    <li><div>
        @foreach ($upcoming_events_and_signup_status as $upcoming_event)
            @if ($upcoming_event->attending_event_id)
                COMPLETE:
                @break
            @endif
        @endforeach
        Sign up for upcoming events.<br>
        @if ($number_photos)
            <form action="/" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="attending_event_form" value="1">
                @if ($upcoming_events_and_signup_status)
                    <br>
                    @foreach ($upcoming_events_and_signup_status as $upcoming_event)
                        <table>
                            <tr>
                                <td>Event</td>
                                <td>
                                    <input class="upcoming_event_checkbox" type="checkbox" name="attending_event_id_{{ $upcoming_event->event_id }}"
                                    @if ($upcoming_event->attending_event_id)
                                        @if ($upcoming_event->user_id_of_match || $upcoming_event->already_matched_but_dont_know_it)
                                            disabled
                                        @endif
                                        checked
                                    @endif
                                    >
                                    @if ($upcoming_event->url)
                                        <a href="{{ $upcoming_event->url }}">{{ $upcoming_event->event_long_name }}</a>
                                    @else
                                        {{ $upcoming_event->event_long_name }}
                                    @endif
                                    <br>{{ $upcoming_event->event_date }}
                                </td>
                            </tr>
                            <tr>
                                <td>Signups</td>
                                <td>
                                    @if ($upcoming_event->signups_still_needed)
                                        @if ($upcoming_event->signups_still_needed == 1)
                                            {{ $upcoming_event->attending_count }}/{{ $upcoming_event->attending_count + $upcoming_event->signups_still_needed }}, {{ $upcoming_event->signups_still_needed }} signup still needed.
                                        @else
                                            {{ $upcoming_event->attending_count }}/{{ $upcoming_event->attending_count + $upcoming_event->signups_still_needed }}, {{ $upcoming_event->signups_still_needed }} signups still needed.
                                        @endif
                                        @if ($upcoming_event->url)
                                            <a href="{{ $upcoming_event->url }}" class="bright">Get the word out</a>!
                                        @else
                                            Get the word out!
                                        @endif
                                    @else
                                        {{ $upcoming_event->attending_count }} signups, event is happening
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Match</td>
                                <td>
                                    @if ($upcoming_event->user_id_of_match)
                                        <a class="bright" href="/profile/match?event_id={{ $upcoming_event->event_id }}">Here's your match</a>.
                                    @else
                                        @if ($upcoming_event->can_claim_match)
                                            @if (isset($upcoming_event->time_until_can_re_request_match))
                                                @php $time_until_can_re_request_match = ceil($upcoming_event->time_until_can_re_request_match / 60) @endphp
                                                @if ($time_until_can_re_request_match == 1)
                                                    You can retry the matching algorithm in {{ $time_until_can_re_request_match }} minute.
                                                @else
                                                    You can retry the matching algorithm in {{ $time_until_can_re_request_match }} minutes.
                                                @endif
                                            @else
                                                <a href="/match-me?event_id={{ $upcoming_event->event_id }}" class="bright">You can now request your match!</a>
                                            @endif
                                        @else
                                            @if (isset($upcoming_event->seconds_till_user_can_match))
                                                @if ($upcoming_event->seconds_till_user_can_match > 360000)
                                                    You will be matched in about {{ ceil($upcoming_event->seconds_till_user_can_match / 60 / 60 / 24) }} days.
                                                @else
                                                    @if ($upcoming_event->seconds_till_user_can_match > 3600)
                                                        You will be matched in about {{ ceil($upcoming_event->seconds_till_user_can_match / 60 / 60) }} hours.
                                                    @else
                                                        You will be matched in less than one hour!
                                                    @endif
                                                @endif
                                                @if ($random_ok)
                                                    <a href="/potential-match">You will most likely be matched to one of these users</a>.
                                                @else
                                                    <a href="/potential-match">You will be matched to one of these users</a>.
                                                @endif
                                            @else
                                                @if ($upcoming_event->attending_event_id)
                                                    @if ($upcoming_event->signups_still_needed)
                                                        More signups are needed before anyone will be matched.
                                                    @endif
                                                @else
                                                    You are not signed up for this event.
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        </table>
                        <br>
                    @endforeach
                @endif
                <input type="submit" class="yesyesyes" value="Submit changes">
                <br>
            </form>
        @endif
    </div></li>
@else
    <li>When new events are added, they will appear here. Sign up to be matched at an upcoming event.</li>
@endif

@if ($unrated_users)
    @if ($number_photos)
        <li><a href="/profile/compatible?" class="bright">Choose who you'd like to meet ({{ count($unrated_users) }} left to view)</a>.</li>
    @else
        <li>Once you have uploaded a photo, you can view other users' profiles and choose who you'd like to meet.</li>
    @endif
@else
    <li>
        @foreach ($upcoming_events_and_signup_status as $upcoming_event)
            @if ($upcoming_event->attending_event_id)
                COMPLETE: You have viewed all profiles.
                @if ($recently_updated_users)
                    <a href="/profile/{{ $recently_updated_users[0]->id }}/{{ $recently_updated_users[0]->wasteland_name_hyphenated }}?review=1" class="bright">Some profiles were recently updated</a>.
                @else
                    Come back later to see new arrivals.
                @endif
                @php ($viewed_all = 1)
                @break
            @endif
        @endforeach
        @if (isset($viewed_all))
        @else
            Let us know who you'd like to meet.
        @endif
    </li>
@endif

<li>Check here a few days before the event to find out who you've been matched with.

<li>At the event, seek out your match and introduce yourself.</li>

</ol>

@if ($matched_to_users)
    <h2>Mission matches</h2>
    @foreach ($matched_to_users as $matched_to_user)
        @if ($matched_to_user->logged_in_users_rating_of_this_user === 0)
            @include('user_block_found_not_liked', [
                'event_long_name'                  => $matched_to_user->event_long_name,
            ])
        @else
            @if ($matched_to_user->logged_in_users_rating_of_this_user === -1)
                @if (($matched_to_user->this_users_rating_of_logged_in_user === 0) or (!$matched_to_user->name))
                    @include('user_block_found_but_deleted', [
                        'event_long_name'          => $matched_to_user->event_long_name,
                        'event_id'                 => $matched_to_user->event_id,
                    ])
                @else
                    @include('user_block_found', [
                        'number_photos'            => $matched_to_user->number_photos,
                        'url'                      => $matched_to_user->url,
                        'user_id'                  => $matched_to_user->id,
                        'name'                     => $matched_to_user->name,
                        'event_long_name'          => $matched_to_user->event_long_name,
                    ])
                @endif
            @else
                @if ($matched_to_user->name && $matched_to_user->this_users_rating_of_logged_in_user !== 0)
                    @include('user_block_not_found_yet', [
                        'number_photos'            => $matched_to_user->number_photos,
                        'url'                      => $matched_to_user->url,
                        'user_id'                  => $matched_to_user->id,
                        'name'                     => $matched_to_user->name,
                        'event_long_name'          => $matched_to_user->event_long_name,
                        'ok_to_delete_old_mission' => $matched_to_user->ok_to_delete_old_mission,
                        'event_id'                 => $matched_to_user->event_id,
                        'ok_to_mark_user_found'    => $matched_to_user->ok_to_mark_user_found,
                        'curse_interface'          => $curse_interface,
                    ])
                @else
                    @include('user_block_matched_to_deleted', [
                        'event_long_name'          => $matched_to_user->event_long_name,
                        'event_id'                 => $matched_to_user->event_id,
                    ])
                @endif
            @endif
        @endif
    @endforeach
@endif

@endsection
