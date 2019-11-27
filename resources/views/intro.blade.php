@extends('layouts.app')
@section('content')
@include('home_promo_stuff')
<h2>Turn strangers into friends by meeting them.</h2>
<p>You Are Awaited is a simple game. You prepare for it online, but play it during various real-world events. Participation is free. <a href="{{ route('register') }}">Sign up now</a>.
@if ($next_event_name)
    Our next event will be {{ $next_event_name }}.</p>
@endif
<h2>Meet our top {{ $leader_count }} heroes, and {{ $nonleader_count }} others. Here's how.</h2>
@foreach ($leaderboard as $leader)
	<div class="centered_block">
		@if ($leader['number_photos'])
			<a target="_blank" href="/uploads/image-{{ $leader['profile_id'] }}-1.jpg"><img src="/uploads/image-{{ $leader['profile_id'] }}-1.jpg" style="height:100px;"></a>
		@endif
		<br>
		@if ($leader['missions_completed'] > 0)
			{{ $titles[$leader['title_index']] }}
		@endif
		{{ $leader['wasteland_name'] }} &middot; {{ $leader['missions_completed'] }}
	</div>
@endforeach
<h3>1.
@guest<a href="{{ route('register') }}" class="bright">@endguest
Create a profile
@guest</a>@endguest</h3>
<p>
Create a profile that tells everyone a little bit about you. <a href="/profile/Firebird">Here's an example</a>.
</p>
<h3>2. Sign up for an event</h3>
<p>
Let us know what upcoming events you'll be attending.
@if ($next_event_name)
    The next event is {{ $next_event_name }}.
@endif
</p>
<h3>3. Let us know who you'd enjoy meeting</h3>
<p>
Browse other profiles and choose who you'd enjoy meeting at upcoming events.
</p>
<h3>4. Find out who you're matched with</h3>
<p>
Before each event, return to this site to be matched with one of the people you said you'd enjoy meeting.
</p>
<h3>4. Seek out your match</h3>
<p>
Your mission is to find your match at the event and introduce yourself. They'll be looking for you, too. That's it!
</p>
@endsection
