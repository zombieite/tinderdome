@extends('layouts.app')
@section('content')

{{--
An election is being held for the office of
<h1>@include('prezident', [])</h1>
<h1 class="bright">Vote on Tuesday, Oct 6, 2020</h1>
To vote, please contact <a href="mailto:wastelandfirebird@gmail.com">Firebird</a> to create an account.
--}}

<h1>Turn strangers into friends by meeting them.</h1>
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
<p>You Are Awaited is a simple game. You prepare online using this website, but the game itself happens during various real-world events. Participation is free. Here's how to play.
</p>
<h2>1.
@guest<a href="{{ route('register') }}" class="bright">@endguest
Create a profile
@guest</a>@endguest</h2>
<p>
Create a profile that tells everyone a little bit about you. <a href="/profile/Firebird">Here's an example</a>.
</p>
<h2>2. Sign up for an event</h2>
<p>
Let us know what upcoming events you'll be attending, or contact us to set up an event of your own. Any event where twenty or more strangers come together in the same physical space will work!
</p>
<h2>3. Choose who you'd like to meet</h2>
<p>
Browse other profiles and let us know who you'd enjoy meeting.
</p>
<h2>4. Find out who you're matched with</h2>
<p>
A few days before the event, return to this site to get your match.
</p>
<h2>4. Seek out your match at the event</h2>
<p>
Your mission is to find your match and introduce yourself. They'll be looking for you, too. That's it!
</p>

@endsection
