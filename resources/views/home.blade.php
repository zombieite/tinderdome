@extends('layouts.app')

@section('content')
<ol>
<li>COMPLETE: Profile created.</li>
@if ($unrated_users)
	@if ($random_ok) 
		<li>COMPLETE: <a href="/profile/edit">Your preferences</a> indicate you are ok with a random match, so you don't have to rate other profiles. <a href="/profile/compatible?">But you can if you want to</a>.</li>
	@else
		<li><a href="/profile/compatible?">Choose who you'd like to meet</a>.</li>
	@endif
@else
	<li>COMPLETE: You have rated every profile. Check back later to rate new arrivals. Or you can <a href="/search">revisit profiles</a> you've already viewed.</li>
@endif
@if ($attending_next_event)
	@if ($matched)
		<li><a href="/profile/match?event={{ $next_event }}&year={{ $year }}">COMPLETE: YOU ARE AWAITED AT {{ strtoupper($pretty_names[$next_event]) }} {{ $year }}! Find out who you're matched with</a>.</li>
	@else
		<li>Matches are complete for {{ $pretty_names[$next_event] }} {{ $year }}, but you were not matched. <a href="/profile/match?event={{ $next_event }}&year={{ $year }}">Find out why</a>.</li>
	@endif
@else
	<li>Check back here a few days before the next event you'll be attending to find out who you've been matched with. Let us know what events you'll be attending by <a href="/profile/edit">updating your profile</a>.</li>
@endif
<li>At the event, seek out your match. If you've found your match, <a href="/search">let us know that you've met them by updating their profile rating</a>.</li>
<li>Find <a href="/profile/Firebird">Firebird</a> to receive your reward.</li>
</ol>
@endsection
