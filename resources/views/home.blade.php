@extends('layouts.app')

@section('content')
<h2>Find thems you're looking for and thems you've lost</h2>
<h3>1. COMPLETE: Profile created.</h3>
@if ($all_seen)
<h3> 2. COMPLETE: Other profiles rated.</h3>
@else
<h3>2. NEXT: <a href="/profile/compatible">Choose potential matches</a>.</h3>
@endif
<h3>3. Find out who you've been matched with. Next matching run will be complete Feb 22, 2018, just before the Winter Games.</h3>
<h3>4. At the event, seek out the person or group you've been matched with. When you find them, the topic of conversation will be the merging of the backstories of your wasteland personas.</h3>
<h3>5. Find <a href="/profile/Firebird">Firebird</a> and get your caps.</h3>
@endsection
