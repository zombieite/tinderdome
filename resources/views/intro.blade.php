@extends('layouts.app')
@section('content')

@include('home_promo_stuff')

<p>You Are Awaited is a simple mission that you can sign up for during Wasteland Weekend and other post-apocalyptic events. Participation is free. <a href="{{ route('register') }}">Signups are done on this site, year-round</a>.</p>
<h2>Meet our top {{ $leader_count }} heroes... and {{ $nonleader_count }} others. Here's how.</h2>
@foreach ($leaderboard as $leader)
	<div class="centered_block">
		@if ($leader['number_photos'])
			<a target="_blank" href="/uploads/image-{{ $leader['profile_id'] }}-1.jpg"><img src="/uploads/image-{{ $leader['profile_id'] }}-1.jpg" style="height:100px;"></a>
		@endif
		<br>
		@if ($leader['missions_completed']['points'] > 0)
			{{ $titles[$leader['title_index']] }}
		@endif
		{{ $leader['wasteland_name'] }} &middot; {{ $leader['missions_completed']['points'] }}
	</div>
@endforeach
<h3>1.
@guest<a href="{{ route('register') }}" class="bright">@endguest
Create a profile
@guest</a>@endguest</h3>
<p>
You'll create a profile that tells everyone a little bit about you. <a href="/profile/Firebird">Here's an example</a>.
</p>
<h3>2. Let us know who you'd enjoy meeting at the next event</h3>
<p>
Browse other profiles and choose who you'd enjoy meeting.
</p>
<h3>3. Find out who you're matched with</h3>
<p>
Shortly before each event, the matching algorithm will run. You will be matched with one other person at the event. Check your email or come back to this site to see who you're matched with.
@if ($next_event)
    Next event: {{ $next_event_long_name }}!
@endif
</p>
<h3>4. Seek out your match</h3>
<p>
Your mission is to find your match at the event and introduce yourself. That's it!
</p>
@endsection
