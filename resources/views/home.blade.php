@extends('layouts.app')

@section('content')
<h2>Find thems you're looking for and thems you've lost</h2>
<h3>Mission steps</h3>
<h4>1. COMPLETE: Profile created.</h4>
@if ($all_seen)
<h4> 2. COMPLETE: Other profiles rated.</h4>
@else
<h4>2. NEXT: <a href="/profile/compatible">Choose potential matches</a>.</h4>
@endif
<h4>3. Find out who you've been matched with. Next matching run will be complete Feb 22, 2018, just before the Winter Games.</h4>
<h4>4. At the event, seek out the person or group you've been matched with. When you find them, the topic of conversation will be the merging of the backstories of your wasteland personas.</h4>
<h4>5. (Optional) Together, find <a href="/profile/Firebird">Firebird</a>, tell him your stories, and get your caps.</h4>
@endsection
