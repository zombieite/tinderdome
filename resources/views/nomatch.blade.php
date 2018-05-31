@extends('layouts.app')
@section('content')

@if ($logged_in_is_signed_up)

	@if ($matches_done)

	<h2>You do not have a match for the next event.</h2>
	<p>
		There are a few possible reasons for this.
		<ul>
			<li>You already know too many of the people who are attending. We only match you with people you don't know.</li>
			<li>You were too picky. If you want to be more likely to get a match at the next event, rate fewer profiles as No, and <a href="/profile/edit">edit your profile</a> to indicate that you are open to a random match.</li>
			<li>Random chance did not favor you this time. Part of our matching algorithm is pure luck.</li>
			<li>You signed up late and not enough people had time to rate your profile. The good news is, you are much more likely to get a match at the next event.</li>
		</ul>
	</p>

	@else

		Matches have not been run yet for {{ $pretty_event_names[$event] }} {{ $year }}. Check back a few days before the event.

	@endif

@else

	You are not signed up for {{ $pretty_event_names[$event] }} {{ $year }}. <a href="/profile/edit">Edit your profile</a> to let us know what events you'll be attending.

@endif

@endsection
