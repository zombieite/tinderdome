@extends('layouts.app')
@section('content')
<h2>Find thems you're looking for and thems you've lost</h2>
<p>
Since The Apocalypse, Wasteland has become known the world over as the place we come to find thems we're looking for and thems we've lost. Whether you're looking for love, a friend you haven't met yet, an enemy you haven't battled yet, or someone you knew once and lost, without telephones or a reliable post office we need a designated meeting place. That's Wasteland.
</p>
<h3>1. <a href="{{ route('register') }}">Create a profile</a></h3>
<p>
On this site you'll create a brief profile. Individuals, couples, or groups can create profiles. You may create more than one profile.
</p>
<h3>2. Let us know which profiles you'd be willing to meet</h3>
<p>
Once you've created your profile, you'll either accept a random match or choose some other profiles as ones you'd be open to searching for and meeting at the next Wasteland event.
</p>
<h3>3. Find out which profile you've been matched with</h3>
<p>
Shortly before the next Wasteland event the matching algorithm will run and you'll come back here to see if you have a mutual match.
</p>
<h3>4. At the next event, seek out the person or group you've been matched with</h3>
<p>
Your mission is to find them at the event. They'll be looking for you, too. When you meet, you are to merge the backstories of your Wasteland personas. If you don't have one, now's the time to make one up. You'll come up with a story of how you met. The story can be realistic (we just met here today at this Wasteland event) or wild (we fought each other in a battle 200 years ago before we were both cryogenically frozen) or romantic (we were once in love but haven't seen each other since The Apocalypse separated us ten years ago). If your backstories are utterly incompatible, you can each declare the other to be insane.
</p>
<h3>5. Get your caps</h3>
<p>
If you come together to find me, Firebird, and tell me your story, I'll reward you with special caps. <a href="/profile/Firebird">Here's my profile</a>.
</p>
@guest
<h3><a href="{{ route('register') }}">Get started by creating your profile</a>.</h3>
@endguest
@endsection
