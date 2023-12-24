@extends('layouts.app')
@section('content')

<h1>Turn strangers into friends by meeting them.</h1>

@include('leaderboard')

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
<h2>5. Seek out your match at the event</h2>
<p>
Your mission is to find your match and introduce yourself. They'll be looking for you, too. That's it!
</p>

@endsection
