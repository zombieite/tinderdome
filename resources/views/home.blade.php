@extends('layouts.app')

@section('content')

@if ($success_message)
    <h2 class="bright">{{ $success_message }}</h2>
@endif

@foreach ($upcoming_events_and_signup_status as $upcoming_event)
    @if ($upcoming_event->user_id_of_match)
        <h1 class="bright">YOU ARE AWAITED AT {{ strtoupper($upcoming_event->event_long_name) }}! <a class="bright" href="/profile/match?event_id={{ $upcoming_event->event_id }}">Here's your match.</a></h1>
    @endif
@endforeach

@if ($comments_to_approve)
    <h2 class="bright">You have new comments</h2>
    You can approve them or delete them. If you approve them they will appear on your profile. All comments are deleted after one year.
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
        @for ($i = 0; (($i < 7) && ($i < count($unrated_users))); $i++)
                @if ($unrated_users[$i]->number_photos)
                    @include('user_block_enjoy_meeting', ['user_id' => $unrated_users[$i]->id])
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
        <form action="/" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="attending_event_form" value="1">
            @if ($upcoming_events_and_signup_status)
                <br>
                <table>
                    <tr>
                        <th>Event</th>
                        <th>Signups</th>
                        <th>Match</th>
                    </tr>
                @foreach ($upcoming_events_and_signup_status as $upcoming_event)
                    <tr>
                        <td>
                            <input class="upcoming_event_checkbox" type="checkbox" name="attending_event_id_{{ $upcoming_event->event_id }}"
                            @if ($upcoming_event->attending_event_id)
                                @if ($upcoming_event->user_id_of_match)
                                    disabled
                                @endif
                                checked
                            @endif
                            >
                            @if ($upcoming_event->user_id_of_match)
                                <a class="bright" href="/profile/match?event_id={{ $upcoming_event->event_id }}">{{ $upcoming_event->event_long_name }}</a>
                            @else
                                @if ($upcoming_event->url)
                                    <a href="{{ $upcoming_event->url }}">{{ $upcoming_event->event_long_name }}</a>
                                @else
                                    {{ $upcoming_event->event_long_name }}
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($upcoming_event->signups_still_needed)
                                {{ $upcoming_event->attending_count }}/{{ $upcoming_event->attending_count + $upcoming_event->signups_still_needed }}, {{ $upcoming_event->signups_still_needed }} signups still needed.
                                @if ($upcoming_event->url)
                                    <a href="{{ $upcoming_event->url }}" class="bright">Get the word out!</a>
                                @else
                                    Get the word out!
                                @endif
                            @else
                                {{ $upcoming_event->attending_count }} signups, event is happening
                            @endif
                        </td>
                        <td>
                            @if ($upcoming_event->can_claim_match)
                                <a href="/match-me?event_id={{ $upcoming_event->event_id }}" class="bright">You can now request your match!</a>
                            @else
                                @if (isset($upcoming_event->seconds_till_user_can_match))
                                    @if ($upcoming_event->seconds_till_user_can_match > 360000)
                                        You will be eligible to request your match in about {{ ceil($upcoming_event->seconds_till_user_can_match / 60 / 60 / 24) }} days.
                                    @else
                                        @if ($upcoming_event->seconds_till_user_can_match > 3600)
                                            You will be eligible to request your match in about {{ ceil($upcoming_event->seconds_till_user_can_match / 60 / 60) }} hours.
                                        @else
                                            You will be eligible to request your match in less than one hour!
                                        @endif
                                    @endif
                                    As you complete more missions, you become eligible to be matched sooner.
                                @else
                                    &nbsp;
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
                </table>
            @endif
            <br>
            <input type="submit" class="yesyesyes" value="Submit changes">
            <br>
        </form>
    </div></li>
@else
    <li>When new events are added, they will appear here. You can sign up to be matched during these events.</li>
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
                COMPLETE: You have viewed all profiles. Come back later to see new arrivals.
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

<li>Check here a few days before the event to find out who you're matched with.

<li>At the event, seek out your match and introduce yourself.</li>

</ol>

@if ($matched_to_users)
    <h2>Mission matches</h2>
    @foreach ($matched_to_users as $matched_to_user)
        {{-- If one of us set we've met but one of us said no --}}
        @if ($matched_to_user->choice === 0 or $matched_to_user->they_said_no)
            @include('user_block_found_not_liked', ['event_long_name' => $matched_to_user->event_long_name ])
        {{-- else who knows but we at least haven't met and said no --}}
        @else
            {{-- If match exists and was not deleted --}}
            @if ($matched_to_user->name) {{-- Name populated means join to users succeeded --}}
                {{-- If I say I've met them --}}
                @if ($matched_to_user->choice === -1)
                    @include('user_block_found', ['number_photos' => $matched_to_user->number_photos, 'url' => $matched_to_user->url, 'user_id' => $matched_to_user->id, 'name' => $matched_to_user->name, 'event_long_name' => $matched_to_user->event_long_name ])
                {{-- else I haven't found them yet --}}
                @else
                    @include('user_block_not_found_yet', ['number_photos' => $matched_to_user->number_photos, 'url' => $matched_to_user->url, 'user_id' => $matched_to_user->id, 'name' => $matched_to_user->name, 'event_long_name' => $matched_to_user->event_long_name, 'ok_to_delete_old_mission' => $matched_to_user->ok_to_delete_old_mission, 'event_id' => $matched_to_user->event_id ])
                @endif
            {{-- else match does not exist yet or match deleted themselves --}}
            @else
                {{-- I found my match before they deleted themselves --}}
                @if ($matched_to_user->choice === -1)
                    @include('user_block_found_but_deleted', ['event_long_name' => $matched_to_user->event_long_name, 'event_id' => $matched_to_user->event_id])
                {{-- else I haven't found them, and they either don't exist yet, or they deleted themselves --}}
                @else
                    {{-- If I was indeed matched to them at some point, then they must have deleted themselves --}}
                    @if ($matched_to_user->user_id_of_match) {{-- user_id populated but no name populated means they deleted themselves --}}
                        @include('user_block_matched_to_deleted', ['event_long_name' => $matched_to_user->event_long_name, 'event_id' => $matched_to_user->event_id])
                    {{-- else I was never matched to anyone, either because the mission gave me no match, or because the mission hasn't happened yet --}}
                    @else
                        {{-- If the mission never gave me a match (ok to delete flag is set in that case, among others) --}}
                        @if ($matched_to_user->ok_to_delete_old_mission)
                            {{-- Don't even show an old mission that didn't get them a match --}}
                        {{-- else I just haven't gotten my match yet --}}
                        @else
                            @include('user_block_no_match_yet', ['event_long_name' => $matched_to_user->event_long_name])
                        @endif
                    @endif
                @endif
            @endif
        @endif
    @endforeach
@endif
@endsection
