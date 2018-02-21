@extends('layouts.app')
@section('content')
<h3>Next matches will be run Wed, Feb 21, 2018 for missions during the Wasteland Winter Games. Sign in on Feb 22 to find out who you're matched with.</h3>
<h2>Find thems you're looking for and thems you've lost</h2>
<p>
Since The Apocalypse, Wasteland has become known the world over as the place we come to find thems we're looking for and thems we've lost. Whether you're looking for love, a friend you haven't met yet, an enemy you haven't battled yet, or someone you knew once and lost, without telephones or a reliable post office we need a designated meeting place. That's Wasteland.
</p>
<h3>Mission steps</h3>
<h4>1.
@guest<a href="{{ route('register') }}">@endguest
Create a profile
@guest</a>@endguest</h4>
<p>
On this site you'll create a brief profile. Individuals, couples, or groups can create profiles. You may create more than one profile.
</p>
<h4>2. Let us know which profiles you'd be willing to meet</h4>
<p>
Once you've created your profile, you'll either accept a random match or choose some other profiles as ones you'd be open to searching for and meeting at the next Wasteland event.
</p>
<h4>3. Find out which profile you've been matched with</h4>
<p>
Shortly before the next Wasteland event the matching algorithm will run and you'll come back here to see if you have a mutual match.
</p>
<h4>4. At the next event, seek out the person or group you've been matched with</h4>
<p>
Your mission is to find them at the event. They'll be looking for you, too. When you meet, you are to merge the backstories of your Wasteland personas. If you don't have one, now's the time to make one up. You'll come up with a story of how you met. The story can be realistic (we just met here today at this Wasteland event) or wild (we fought each other in a battle 200 years ago before we were both cryogenically frozen) or romantic (we were once in love but haven't seen each other since The Apocalypse separated us ten years ago). If your backstories are utterly incompatible, you can each declare the other to be insane.
</p>
<h4>5. Get your caps</h4>
<p>
If you find <a href="/profile/Firebird">Firebird</a> and tell him your story, he'll reward you with special caps.
</p>
@guest
<h3><a href="{{ route('register') }}">Get started by creating your profile</a>.</h3>
@endguest
@endsection
