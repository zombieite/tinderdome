@extends('layouts.app')

@section('content')
<h2>Find thems you're looking for and thems you've lost</h2>
<h3>Mission steps</h3>
<h4>1. COMPLETE: Profile created.</h4>
@if ($all_seen)
<h4> 2. COMPLETE: You have rated all other profiles. Check back later to rate new arrivals. Next event is Wastelanders Ball.</h4>
@else
<h4>2. <a href="/profile/compatible">Choose potential matches</a>.</h4>
@endif
@if ($matched && false)
<h4>3. <a href="/profile/match">COMPLETE: You are awaited! Click here to go to your match page to see who you're matched with.</a></h4>
@else
<h4>3. Check back shortly before the next event to see who you are matched with.</h4>
@endif
<h4>4. At the event, seek out the person or group you've been matched with.</h4>
<h4>5. Find <a href="/profile/Firebird">Firebird</a>, tell him your stories, and get your caps.</h4>
@endsection
