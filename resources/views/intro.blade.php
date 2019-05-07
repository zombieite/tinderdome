@extends('layouts.app')
@section('content')
<h2>A simple but profound mission.</h2>
<iframe style="width:100%;max-width:720px;height:480px" src="https://www.youtube.com/embed/kdXWJ4crKkE" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
<p>
Since The Apocalypse, Wasteland has become known as the place we come to find thems we're looking for and thems we've lost. Whether you're looking for love, a friend you haven't met yet, an enemy you haven't battled yet, or someone you knew once and lost, we need a designated meeting place. That's Wasteland.
</p>
<p>
You Are Awaited is a simple mission that is conducted during Wasteland Weekend and other post-apocalyptic events. There are no in-person signups. <a href="{{ route('register') }}">Signups are done on this site, year-round</a>. Money is useless nowadays so participation is free. There's no punishment for failure, but there are rewards for victory.
</p>
<p>
Rebuilding the world from its ashes, one conversation at a time.
</p>
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
Shortly before each event, the matching algorithm will run. You will be matched with one other person at the event. Check your email or come back to this site to see who you've been matched to. Next event: {{ $pretty_names[$next_event] }} {{ $year }}!
</p>
<h3>4. Seek out your match</h3>
<p>
Your mission is to find your match at the event. They'll be looking for you, too.
</p>
<h3>5. Get your caps</h3>
<p>
You will be rewarded with caps. Upon mission completion, go to the WCC Post Office, or at Atomic Falls, find <a href="/profile/34/Blank">Blank</a>. Every mission you complete earns you a different cap. <a href="{{ route('register') }}">Get started by creating your profile</a>.
</p>
@endsection
