@extends('layouts.app')
@section('content')
<p>
Since The Apocalypse, Wasteland has become known as the place we come to find thems we're looking for and thems we've lost. Whether you're looking for love, a friend you haven't met yet, an enemy you haven't battled yet, or someone you knew once and lost, we need a designated meeting place. That's Wasteland.
</p>
<h2>Mission steps</h2>
<h3>1.
@guest<a href="{{ route('register') }}">@endguest
Create a profile
@guest</a>@endguest</h3>
<p>
You'll create a profile that tells everyone a little bit about you. <a href="/profile/Firebird">Here's mine</a>.
</p>
<h3>2. Let us know who you'd enjoy meeting</h3>
<p>
Once you've created your profile, you can either accept a random match, or you can browse other profiles and choose those you'd enjoy meeting at the next event.
</p>
<h3>3. Find out who you're matched with</h3>
<p>
Shortly before each event, the matching algorithm will run. You'll come back here to see who you've been matched with.
</p>
<h3>4. Seek out your match</h3>
<p>
Your mission is to find them at the event. They'll be looking for you, too. When you find them, you can use this as an opportunity to merge the backstories of your Wasteland personas. How did you two meet?
</p>
<h3>5. Get your caps</h3>
<p>
If you find <a href="/profile/Firebird">Firebird</a> and tell him your story, he'll reward you with a cap. Every mission you complete earns you a different cap. The more missions you complete, the higher priority your profile will be given by the matching algorithm.
</p>
@guest
<h2><a href="{{ route('register') }}">Get started by creating your profile</a>.</h2>
@endguest
@endsection
