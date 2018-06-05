@extends('layouts.app')

@section('content')
<h2>Meet our top {{ $leader_count }} heroes<a class="bright" style="text-decoration:none;" href="#RATT"><sup>*</sup></a>... and {{ $nonleader_count }} others.</h2>
@foreach ($leaderboard as $leader)
<div class="profile_search_block">
	@if ($leader['number_photos'])
		<a href="/profile/{{ $leader['profile_id'] }}/{{ preg_replace('/\s/', '-', $leader['wasteland_name']) }}"><img src="/uploads/image-{{ $leader['profile_id'] }}-1.jpg" style="height:100px;"></a> @endif
	<br>
	{{ $leader['wasteland_name'] }} &middot; {{ $leader['missions_completed']['points'] }}
</div>
@endforeach
<ol>
<li>COMPLETE: <a href="/profile/me">Profile</a> created.</li>
@if ($unrated_users)
	@if ($random_ok) 
		<li>COMPLETE: Your preferences indicate you are ok with a random match, so you don't have to rate other profiles. <a href="/profile/compatible?">But you can if you want to</a>.</li>
	@else
		<li><a href="/profile/compatible?">Choose who you'd like to meet</a>.</li>
	@endif
@else
	<li>COMPLETE: You have rated every profile. Check back later to rate new arrivals. Or you can <a href="/search">revisit profiles</a> you've already viewed.</li>
@endif
@if ($attending_next_event)
	@if ($matched)
		<li><b><a class="bright" href="/profile/match?event={{ $next_event }}&year={{ $year }}">COMPLETE: YOU ARE AWAITED AT {{ strtoupper($pretty_names[$next_event]) }} {{ $year }}! Find out who you're matched with.</a></b></li>
	@else
		<li>Matches are complete for {{ $pretty_names[$next_event] }} {{ $year }}, but you were not matched. <a href="/profile/match?event={{ $next_event }}&year={{ $year }}">Find out why</a>.</li>
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
		At the event, seek out your match. When you find your match, <a href="/search">let us know that you've met them</a>.
	@endif
</li>
<li>Find <a href="/profile/Firebird">Firebird</a> to receive your reward.</li>
</ol>
<p><sup id="RATT" class="bright">*</sup> RATT BOY prefers to be known as a VILLAIN</p>
@endsection
