@extends('layouts.app')
@section('content')
<p>
Since The Apocalypse, Wasteland has become known the world over as the place we come to find thems we're looking for and thems we've lost. Whether you're looking for love, a friend you haven't met yet, an enemy you haven't battled yet, or someone you knew once and lost, without telephones or a reliable post office we need a designated meeting place. That's Wasteland.
</p>
<h2>Mission steps</h2>
<h3>1.
@guest<a href="{{ route('register') }}">@endguest
Create a profile
@guest</a>@endguest</h3>
<p>
On this site you'll create a brief profile. Individuals, couples, or groups can create profiles. You may create more than one profile.
</p>
<h3>2. Let us know which profiles you'd enjoy meeting</h3>
<p>
Once you've created your profile, you'll either accept a random match or choose some other profiles as ones you'd enjoy searching for and meeting at the next event.
</p>
<h3>3. Find out which profile you've been matched with</h3>
<p>
Shortly before the next event the matching algorithm will run and you'll come back here to see if you have a mutual match.
</p>
<h3>4. At the next event, seek out the person or group you've been matched with</h3>
<p>
Your mission is to find them at the event. They'll be looking for you, too. 
</p>
<h3>5. Get your caps</h3>
<p>
If you find <a href="/profile/Firebird">Firebird</a> and tell him your story, he'll reward you with special caps.
</p>
@guest
<h2><a href="{{ route('register') }}">Get started by creating your profile</a>.</h2>
@endguest
@endsection
