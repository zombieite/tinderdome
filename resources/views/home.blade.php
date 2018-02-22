@extends('layouts.app')

@section('content')
<h2>Find thems you're looking for and thems you've lost</h2>
<h3>Mission steps</h3>
<h4>1. COMPLETE: Profile created.</h4>
@if ($all_seen)
<h4> 2. COMPLETE: Other profiles rated. Next event is Wastelanders Ball. Rating of profiles for Ball attendees will begin soon.</h4>
@else
<h4>2. NEXT: <a href="/profile/compatible">Choose potential matches</a>.</h4>
@endif
<h4>3. <a href="/profile/match">COMPLETE: You are awaited! Click here to go to your match page to see who you're matched with.</a></h4>
<h4>4. At the event, seek out the person or group you've been matched with. When you find them, the topic of conversation will be the merging of the backstories of your wasteland personas.</h4>
<h4>5. (Optional) Together, find <a href="/profile/Firebird">Firebird</a>, tell him your stories, and get your caps.</h4>
@endsection
