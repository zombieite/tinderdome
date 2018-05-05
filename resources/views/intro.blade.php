@extends('layouts.app')
@section('content')
<p>
Since The Apocalypse, Wasteland has become known as the place we come to find thems we're looking for and thems we've lost. Whether you're looking for love, a friend you haven't met yet, an enemy you haven't battled yet, or someone you knew once and lost, we need a designated meeting place. That's Wasteland.
</p>
<p>
You Are Awaited is a simple mission that is conducted during Wasteland Weekend and other post-apocalyptic events. Signups can be done year-round. There are no in-person signups. All signups are done online. Money is useless nowadays so participation is free. There's no punishment for failure, but there are rewards for victory.
</p>
<h2>Meet our top {{ $leader_count }} heroes... and {{ $nonleader_count }} others. Here's how.</h2>
@foreach ($leaderboard as $leader)
<div class="profile_search_block">
	@if ($leader['number_photos'])
		<a target="_blank" href="/uploads/image-{{ $leader['profile_id'] }}-{{ preg_replace('/\s/', '-', $leader['wasteland_name']) }}-1.jpg"><img src="/uploads/image-{{ $leader['profile_id'] }}-{{ preg_replace('/\s/', '-', $leader['wasteland_name']) }}-1.jpg" style="height:100px;"></a>
	@endif
	<br>
	{{ $leader['wasteland_name'] }} &middot; <span class="bright">{{ $leader['missions_completed']['points'] }}</span>
</div>
@endforeach
<h3>1.
@guest<a href="{{ route('register') }}">@endguest
Create a profile
@guest</a>@endguest</h3>
<p>
You'll create a profile that tells everyone a little bit about you. <a href="/profile/Firebird">Here's an example</a>.
</p>
<h3>2. Let us know who you'd enjoy meeting at the next event</h3>
<p>
Once you've created your profile, you can browse other profiles and choose who you'd enjoy meeting.
</p>
<h3>3. Find out who you're matched with</h3>
<p>
Shortly before each event, the matching algorithm will run. You will be matched with one other person. You'll come back to this site to see who you've been matched with. Upcoming events are Detonation Uranium Springs 2018 and Wasteland Weekend 2018.
</p>
<h3>4. Seek out your match</h3>
<p>
Your mission is to find your match at the event. They'll be looking for you, too. Optionally, when you meet, you can use this opportunity to merge the backstories of your Wasteland personas. Come up with a real or fictionalized account of how you met.
</p>
<h3>5. Get your caps</h3>
<p>
If you find <a href="/profile/Firebird">Firebird</a> and tell him your story, you'll be rewarded with caps. Every mission you complete earns you a different cap. Get started now by <a href="{{ route('register') }}">creating your profile</a>.
</p>
@endsection
