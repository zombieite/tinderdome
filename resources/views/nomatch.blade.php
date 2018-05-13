@extends('layouts.app')
@section('content')

<h2>You do not have a match for the next event</h2>
<p>
	There are a few possible reasons for this.
	<ul>
		<li>It's possible that matches have not been run yet. When matches have been run you will receive an email letting you know.</li>
		<li>It's possible you were not marked as attending the next event. Check <a href="/profile/edit">your profile</a>.</li>
		<li>You already know too many of the people who are attending. We only match you with people you don't know.</li>
		<li>You were too picky. If you want to be more likely to get a match at the next event, rate fewer profiles as No, and edit your profile to indicate that you are open to a random match.</li>
		<li>Random chance did not favor you this time. Part of our matching algorithm is pure luck.</li>
		<li>You signed up late and not enough people had time to rate your profile. Users at the next event will see your profile much sooner and you'll receive more ratings for the next event.</li>
	</ul>
</p>

@endsection
