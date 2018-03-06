@extends('layouts.app')
@section('content')

<h2>You do not yet have a match for the next event</h2>
<p>
	There are three possible reasons for this.
	<ol>
		<li>It's possible matches have not been run yet for the next event (this page doesn't check that yet to find out for sure).</li>
		<li>You were too picky. If you want to be more likely to get a match at the next event, rate more profiles with a yes, or edit your profile and mark that you are open to a random match.</li>
		<li>Random chance did not favor you this time. Part of our matching algorithm is pure luck.</li>
		<li>You signed up late and not enough people had time to rate your profile. Users at the next event will see your profile much sooner and you'll receive more ratings for the next event.</li>
	</ol>
</p>

@endsection
