@extends('layouts.app')

@section('content')
@if (count($unrated_users) >= 3)
	<h2><a href="/profile/compatible?">Let us know if you'd enjoy meeting these new users</a>.</h2>
	@for ($i = 0; (($i < 7) && ($i < count($unrated_users))); $i++)
		<div class="profile_search_block">
			@if ($unrated_users[$i]->number_photos)
				<a href="/profile/compatible?"><img src="/uploads/image-{{ $unrated_users[$i]->id }}-1.jpg" style="height:100px;"></a>
			@endif
		<br>
		</div>
	@endfor
@else
	<h2>Meet our top {{ $leader_count }} heroes... and {{ $nonleader_count }} others.</h2>
	@foreach ($leaderboard as $leader)
	<div class="centered_block">
		@if ($leader['number_photos'])
			<a target="_blank" href="/uploads/image-{{ $leader['profile_id'] }}-1.jpg"><img src="/uploads/image-{{ $leader['profile_id'] }}-1.jpg" style="height:100px;"></a> @endif
		<br>
		{{ $leader['wasteland_name'] }} &middot; {{ $leader['missions_completed']['points'] }}
	</div>
	@endforeach
@endif
<ol>
@if ($number_photos)
	<li>COMPLETE: <a href="/profile/me">Profile</a> created.</li>
@else
	<li><a href="/image/upload" class="bright">INCOMPLETE: You must upload a photo of yourself</a>.</li>
@endif
@if ($unrated_users)
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
	<li>COMPLETE: You have rated every profile. Check back later to rate new arrivals. Or you can <a href="/search?show_all=1">revisit profiles</a> you've already viewed.</li>
@endif
@if ($attending_next_event)
	@if ($matched)
		<li><b><a class="bright" href="/profile/match?event={{ $next_event }}&year={{ $year }}">COMPLETE: YOU ARE AWAITED AT {{ strtoupper($pretty_names[$next_event]) }} {{ $year }}! Find out who you're matched with.</a></b></li>
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
		At the event, seek out your match. When you find your match, <a href="/search?show_all=1">let us know that you've met them</a>.
	@endif
</li>
<li>Find <a href="/profile/Firebird">Firebird</a> to receive your reward.</li>
</ol>
@if ($matched_to_users)
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

					@endif
				@endif
			@endif
		@endif
		</div>
	@endforeach
@endif
@endsection
