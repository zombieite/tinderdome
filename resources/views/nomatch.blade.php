@extends('layouts.app')
@section('content')

@if ($matches_done)
	@if ($deleted_match_or_match_said_no)
		<h2>You were matched for {{ $event }}, but your match has deleted their profile.</h2>
	@else
		<h2>We did not find you a match for {{ $event }}.</h2>
		<p>
			There are a few possible reasons for this.
			<ul>
				<li>You already know too many of the people who are attending. We only match you with people you don't know.</li>
				<li>You were too picky. If you want to be more likely to get a match at the next event, rate fewer profiles as No, and <a href="/profile/edit">edit your profile</a> to indicate that you are open to a random match.</li>
				<li>Random chance did not favor you this time. Part of our matching algorithm is pure luck.</li>
				<li>You signed up late and not enough people had time to rate your profile. If that is the case, you are much more likely to get a match at the next event.</li>
				<li>You signed up after matches had already been run.</li>
				<li>You were matched to someone, but one of you dropped out before the event began.</li>
			</ul>
		</p>
	@endif
@else
	Matches have not been run yet for {{ $event }}. Check back a few days before the event.
@endif
@endsection
