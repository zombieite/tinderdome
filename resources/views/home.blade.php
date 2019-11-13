@extends('layouts.app')

@section('content')

@if ($success_message)
    <h2 class="bright">{{ $success_message }}</h2>
@endif

@include('home_promo_stuff')

@if ($number_photos)
    @if (count($unrated_users) >= 3)
        <h2><a href="/profile/compatible?">Let us know if you'd enjoy meeting these users</a>.</h2>
        @for ($i = 0; (($i < 7) && ($i < count($unrated_users))); $i++)
                @if ($unrated_users[$i]->number_photos)
                    <div class="profile_search_block">
                        <a href="/profile/compatible?"><img src="/uploads/image-{{ $unrated_users[$i]->id }}-1.jpg" style="height:100px;"></a>
                    </div>
                @endif
        @endfor
    @endif
@else
@endif

@if ($comments_to_approve)
    <h2 class="bright">You have new comments from people you know.</h2>
    You can approve them or delete them. If you approve them they will appear on your profile. All comments are deleted after one year.
    <ul class="nobullet">
    @foreach ($comments_to_approve as $comment)
        <li>
            <div class=" profile_search_block ">
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

@if (count($mutuals))
    <h2>Users who have shared their contact info with you</h2>
    @foreach ($mutuals as $mutual)
        <div class="centered_block_bright">
            @if ($mutual->number_photos)
                <a href="/profile/{{ $mutual->id }}/{{ $mutual->wasteland_name_hyphenated }}"><img src="/uploads/image-{{ $mutual->id }}-1.jpg" style="height:100px;"></a>
                <br>
            @endif
            <a href="/profile/{{ $mutual->id }}/{{ $mutual->wasteland_name_hyphenated }}">{{ $mutual->name }}</a>
        </div>
    @endforeach
@endif

<h2>Mission status</h2>
<ol>
@if ($number_photos)
    <li>COMPLETE: <a href="/profile/{{ $logged_in_user_id }}/{{ $wasteland_name_hyphenated }}">Profile</a> created.</li>
@else
    <li><a href="/image/upload" class="bright">INCOMPLETE: You must upload a photo</a>.</li>
@endif
@if ($unrated_users)
    @if ($number_photos)
        @if ($random_ok)
            <li><a href="/profile/compatible?">Choose who you'd like to meet ({{ count($unrated_users) }} left to view)</a>.</li>
        @else
            @if ($rated_enough)
                <li><a href="/profile/compatible?">Choose who you'd like to meet ({{ count($unrated_users) }} left to view)</a>.</li>
            @else
                <li><a href="/profile/compatible?" class="bright">INCOMPLETE: Since you are not ok with a random match, you must rate {{ $min_percent_to_count_as_rated_enough_users }}% of our users</a>. You have rated {{ $rated_percent }}%.</li>
            @endif
        @endif
    @else
        <li>Once you have uploaded a photo, you can view other users' profiles and choose who you'd like to meet.</li>
    @endif
@else
    <li>COMPLETE: You have viewed all profiles. Check back later to see new arrivals. Or you can <a href="/search?show_all=1">revisit profiles</a> you've already viewed.</li>
@endif
@if ($upcoming_events_and_signup_status)
    <li>Let us know if you'll be attending these upcoming events.<br>
        <div>
            <form action="/" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="attending_event_form" value="1">
                @foreach ($upcoming_events_and_signup_status as $upcoming_event)
                    <input class="upcoming_event_checkbox" type="checkbox" name="attending_event_id_{{ $upcoming_event->event_id }}" @if ($upcoming_event->attending_event_id) @if ($upcoming_event->user_id_of_match) disabled @endif checked @endif > {{ $upcoming_event->event_long_name }} @if ($upcoming_event->attending_event_id) </span> @endif @if ($upcoming_event->user_id_of_match) <b><a class="bright" href="/profile/match?event={{ $upcoming_event->event_short_name }}&date={{ $upcoming_event->event_date }}">YOU ARE AWAITED AT {{ strtoupper($upcoming_event->event_long_name) }}! Here's your match.</a></b> @endif <br>
                @endforeach
                <input type="submit" value="Submit">
            </form>
        </div>
    </li>
@else
    <li>When new events are added, they will appear here. You can sign up to be matched during these events.</li>
@endif
<li>At the event, seek out your match and introduce yourself. If you find them, <a href="/search?show_all=1">let us know</a>.</li>
</ol>

@if ($matched_to_users)
    <h2>Mission matches</h2>
    @foreach ($matched_to_users as $matched_to_user)
        <div class="centered_block">
        @if ($matched_to_user->choice === 0)
            Found match
            <br>{{ $matched_to_user->event_long_name }}
        @else
            @if ($matched_to_user->they_said_no)
                Found match
                <br>{{ $matched_to_user->event_long_name }}
            @else
                @if ($matched_to_user->name)
                    @if ($matched_to_user->number_photos)
                        <a href="{{ $matched_to_user->url }}"><img src="/uploads/image-{{ $matched_to_user->id }}-1.jpg" style="height:100px;"></a>
                        <br>
                    @endif
                    @if ($matched_to_user->choice === -1)
                        Found
                    @else
                        Matched to
                    @endif
                    <a href="{{ $matched_to_user->url }}">{{ $matched_to_user->name }}</a>
                    <br>{{ $matched_to_user->event_long_name }}
                    @if ($matched_to_user->choice === -1)
                    @else
                        @if ($matched_to_user->event_has_not_yet_happened)
                        @else
                            <br><form action="/" method="POST">{{ csrf_field() }}<input type="submit" name="delete_mission_{{ $matched_to_user->event_id }}" value="Delete this mission"></form>
                        @endif
                    @endif
                @else
                    @if ($matched_to_user->choice === -1 or $matched_to_user->choice === 0)
                        Found match
                        <br>{{ $matched_to_user->event_long_name }}
                    @else
                        @if ($matched_to_user->id)
                            Matched to deleted user;<br>mission incomplete
                            <br>{{ $matched_to_user->event_long_name }}
                        @else
                            No match yet for
                            <br>{{ $matched_to_user->event_long_name }}
                        @endif
                    @endif
                @endif
            @endif
        @endif
        </div>
    @endforeach
@endif

@if ($why_not_share_email)
    <p>Looking for romance? You can get in touch with mutual Fuck-yeahs between events by <a href="/profile/edit">sharing your email address with them</a>.</p>
@endif

@endsection
