@extends('layouts.app')

@section('content')

@if ($success_message)
    <h2 class="bright">{{ $success_message }}</h2>
@endif

@if ($matched && $next_event_attending)
    <h1>YOU ARE AWAITED AT {{ strtoupper($pretty_names[$next_event_attending]) }} {{ $next_event_attending_year }}!</h1>
@else
    @if ($matches_done)
    @else
        @if ($attending_next_event)
            <p>You are signed up for a You Are Awaited mission during {{ $pretty_names[$next_event] }} {{ $year }}. If you cannot attend, please <a href="/profile/edit">let us know</a>.</p>
        @else
            <p>If you will be attending {{ $pretty_names[$next_event] }} {{ $year }}, please <a href="/profile/edit">let us know</a>.</p>
        @endif
    @endif
    @if ($good_ratings_percent >= 50)
        <p>{{ $good_ratings_percent }}% of users who have rated you said they'd enjoy meeting you.</p>
    @else
        @if ($recent_good_ratings_count >= 10)
            <p>{{ $recent_good_ratings_count }} users have said they'd enjoy meeting you in the past week.</p>
        @else
            @if ($good_ratings_count >= 50)
                <p>{{ $good_ratings_count }} users have said they'd enjoy meeting you.</p>
            @else
                @if ($mutual_ok_ratings_count >= 3)
                @endif
            @endif
        @endif
    @endif
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
        @else
            @if (!$comments_to_approve && !$success_message)
                @include('home_promo_stuff', ['recently_updated_users' => $recently_updated_users, 'leaderboard' => $leaderboard, 'leader_count' => $leader_count, 'nonleader_count' => $nonleader_count])
            @endif
        @endif
    @else
    @endif
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

<h2>
Mission status
@if ($next_event_attending)
	for {{ $pretty_names[$next_event_attending] }} {{ $next_event_attending_year }}
@endif
</h2>
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
@if ($next_event_attending)
    @if ($matched)
        <li><b><a class="bright" href="/profile/match?event={{ $next_event_attending }}&year={{ $next_event_attending_year }}">COMPLETE: YOU ARE AWAITED AT {{ strtoupper($pretty_names[$next_event_attending]) }} {{ $next_event_attending_year }}! Here's your match.</a></b></li>
    @else
        @if ($matches_done)
            <li>Matches are complete for {{ $pretty_names[$next_event_attending] }} {{ $next_event_attending_year }}, but you were not matched. <a href="/profile/match?event={{ $next_event_attending }}&year={{ $next_event_attending_year }}">Find out why</a>.</li>
        @else
            <li>Matches have not yet been run for {{ $pretty_names[$next_event_attending] }} {{ $next_event_attending_year }}. Check back here before the event to find out who you're matched with.</li>
        @endif
    @endif
@else
    <li>Let us know what events you'll be attending by <a href="/profile/edit">updating your profile</a>. Check back here before the next event to find out who you've been matched with.</li>
@endif
<li>
    @if ($next_event_attending && $matched)
        @if ($found_my_match)
            COMPLETE: You found your match!
        @else
            Did you find your match? <a href="/profile/match?event={{ $next_event_attending }}&year={{ $next_event_attending_year }}">Let us know</a>.
        @endif
    @else
        @if ($number_photos)
            At the event, seek out your match. When you find your match, <a href="/search?show_all=1">let us know that you've met them</a>.
        @else
            At the event, seek out your match.
        @endif
    @endif
</li>
<li>To receive your reward, go to the <a href="https://www.facebook.com/WCCorp/">Wasteland Communication Corporation</a> Post Office.</li>
</ol>

@if ($matched_to_users)
    <h2>Mission matches</h2>
    @foreach ($matched_to_users as $matched_to_user)
        <div class="centered_block">
        @if ($matched_to_user->choice === 0)
            Found match
            <br>{{ $pretty_names[$matched_to_user->event] }} {{ $matched_to_user->year }}
        @else
            @if ($matched_to_user->they_said_no)
                Found match
                <br>{{ $pretty_names[$matched_to_user->event] }} {{ $matched_to_user->year }}
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
                    <br>{{ $pretty_names[$matched_to_user->event] }} {{ $matched_to_user->year }}
                @else
                    @if ($matched_to_user->choice === -1 or $matched_to_user->choice === 0)
                        Found match
                        <br>{{ $pretty_names[$matched_to_user->event] }} {{ $matched_to_user->year }}
                    @else
                        Matched to deleted user;<br>mission incomplete
                        <br>{{ $pretty_names[$matched_to_user->event] }} {{ $matched_to_user->year }}
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
