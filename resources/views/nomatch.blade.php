@extends('layouts.app')
@section('content')

@if ($matches_done)
	@if ($deleted_match_or_match_said_no)
        <h2>You were matched for {{ $event }}, but your match's profile has been deleted.</h2>
        <span class="bright">There may still be time to request another match</span>. Go to your <a href="/">home page</a>, delete this mission, and sign up for this event again.
	@else
        @if ($event)
    		<h2>We have not yet found you a match for {{ $event }}.</h2>
            <p>
                There are a few possible reasons for this.
                <ul>
                    <li>You already know too many of the people who are attending. We only match you with people you don't know.</li>
                    <li>You were too picky. If you want to be more likely to get a match at the next event, rate more profiles as Neutral rather than Block This User, and <a href="/profile/edit">edit your profile</a> to indicate that you are open to a random match.</li>
                    <li>Random chance did not favor you this time. Part of our matching algorithm is pure luck.</li>
                    <li>You signed up too late to be matched at this event.</li>
                    <li>You were matched to someone, but one of you dropped out before the event began.</li>
                </ul>
            </p>
            <h2><a class="bright" href="/">You can retry the matching algorithm in {{ $time_until_can_re_request_match }} minutes.</a></h2>
        @else
            <h2>Event not found.</h2>
        @endif
	@endif
@else
    @if ($event)
    	Matches have not been run yet for {{ $event }}. Check back a few days before the event.
    @else
        Event not found.
    @endif
@endif
@endsection
