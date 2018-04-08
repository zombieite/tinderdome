@extends('layouts.app')

@section('content')
<h2>Find thems you're looking for and thems you've lost</h2>
<h3>Mission steps</h3>
<h4>1. COMPLETE: Profile created.</h4>
@if ($all_seen)
<h4> 2. COMPLETE: You have rated all other profiles. Check back later to rate new arrivals. Next matching run will happen before Detonation Uranium Springs. If you'll be attending <a href="/profile/edit">be sure your profile has the appropriate box checked</a>.</h4>
@else
<h4>2. <a href="/profile/compatible">Choose potential matches</a>.</h4>
@endif
@if ($matched)
<h4>3. <a href="/profile/match">COMPLETE: You are awaited! Click here to go to your match page to see who you're matched with.</a></h4>
@else
<h4>3. Check back here before the next event to find your match.</h4>
@endif
<h4>4. At the event, seek out the person you've been matched with.</h4>
<h4>5. Find <a href="/profile/Firebird">Firebird</a> to receive your reward.</h4>
@endsection
