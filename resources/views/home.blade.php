@extends('layouts.app')

@section('content')
@if ($matched && $attending_next_event)
	<h1>YOU ARE AWAITED AT {{ strtoupper($pretty_names[$next_event]) }} {{ $year }}!</h1>
@else
	@if ($attending_next_event)
		<p>You are signed up for a You Are Awaited mission during {{ $pretty_names[$next_event] }} {{ $year }}. If you cannot attend, please <a href="/profile/edit">let us know</a>.</p>
	@else
		<p>If you will be attending {{ $pretty_names[$next_event] }} {{ $year }}, please <a href="/profile/edit">let us know</a>.</p>
	@endif
	@if ($good_ratings_percent >= 50)
		<p>{{ $good_ratings_percent }}% of users who have rated you have said they'd enjoy meeting you.</p>
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
			@if ( $recently_updated_users && count($recently_updated_users) >= 3 )
				<h2>Recently updated profiles</h2>
				@foreach ($recently_updated_users as $recently_updated_user)
					<div class="centered_block">
						<a href="/profile/{{ $recently_updated_user->id }}/{{ $recently_updated_user->wasteland_name_hyphenated }}"><img src="/uploads/image-{{ $recently_updated_user->id }}-1.jpg" style="height:100px;"></a>
						<br>
						<a href="/profile/{{ $recently_updated_user->id }}/{{ $recently_updated_user->wasteland_name_hyphenated }}">{{ $recently_updated_user->name }}</a>
					</div>
				@endforeach
			@else
				@if (count($leaderboard))
					<h2>Meet our top {{ $leader_count }} heroes... and {{ $nonleader_count }} others.</h2>
					@foreach ($leaderboard as $leader)
					<div class="centered_block">
						@if ($leader['number_photos'])
							<a href="/profile/{{ $leader['profile_id'] }}/{{ $leader['wasteland_name_hyphenated'] }}"><img src="/uploads/image-{{ $leader['profile_id'] }}-1.jpg" style="height:100px;"></a> @endif
						<br>
						@if ($leader['missions_completed']['points'] > 0)
							{{ $leader['missions_completed']['title'] }}
						@endif
						{{ $leader['wasteland_name'] }} &middot; {{ $leader['missions_completed']['points'] }}
					</div>
					@endforeach
				@else
					<iframe width="560" height="315" src="https://www.youtube.com/embed/pMKM1d0IsNs" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				@endif
			@endif
		@endif
	@else

	@endif
@endif
<ol>
@if ($number_photos)
	<li>COMPLETE: <a href="/profile/{{ $auth_user_id }}/{{ $wasteland_name_hyphenated }}">Profile</a> created.</li>
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
@if ($attending_next_event)
	@if ($matched)
		<li><b><a class="bright" href="/profile/match?event={{ $next_event }}&year={{ $year }}">COMPLETE: YOU ARE AWAITED AT {{ strtoupper($pretty_names[$next_event]) }} {{ $year }}! Here's your match.</a></b></li>
	@else
		@if ($matches_done)
			<li>Matches are complete for {{ $pretty_names[$next_event] }} {{ $year }}, but you were not matched. <a href="/profile/match?event={{ $next_event }}&year={{ $year }}">Find out why</a>.</li>
		@else
			<li>Matches have not yet been run for {{ $pretty_names[$next_event] }} {{ $year }}. Check back here a few days before the event to find out who you're matched with.</li>
		@endif
	@endif
@else
	<li>Let us know what events you'll be attending by <a href="/profile/edit">updating your profile</a>. Check back here a few days before the next event to find out who you've been matched with.</li>
@endif
<li>
	@if ($attending_next_event && $matched)
		@if ($found_my_match)
			COMPLETE: You found your match!
		@else
			Did you find your match? <a href="/profile/match?event={{ $next_event }}&year={{ $year }}">Let us know</a>!
		@endif
	@else
		@if ($number_photos)
			At the event, seek out your match. When you find your match, <a href="/search?show_all=1">let us know that you've met them</a>.
		@else
			At the event, seek out your match.
		@endif
	@endif
</li>
<li>Find <a href="/profile/Firebird">Firebird</a> to receive your reward.</li>
</ol>
@if (count($mutuals))
	<h2>These users have shared their contact info with you.</h2>
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
@if ($matched_to_users)
	@if (count($mutuals))
		<h2>Let us know when you find your mission matches.</h2>
	@endif
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
					{{-- Match deleted account --}}
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
	<p>Looking for romance? You can get in touch with mutual fuck-yeahs between events by <a href="/profile/edit">sharing your email address with them</a>.</p>
@endif
@endsection
