@extends('layouts.app')

@section('content')
<p>1. COMPLETE: Profile created.</p>
@if (!$unrated_users)
<p> 2. COMPLETE: You have rated every profile. Check back later to rate new arrivals. Or you can <a href="/search">revisit profiles</a> you've already viewed.</p>
@else
<p>2. <a href="/profile/compatible?">Choose potential matches</a>.</p>
@endif
@if ($matched)
<p>3. <a href="/profile/match">COMPLETE: You are awaited! Click here to see who you're matched with.</a></p>
@else
<p>3. Next matching run will happen before Detonation Uranium Springs 2018. If you'll be attending <a href="/profile/edit">be sure your profile has the appropriate box checked</a>. Check back before then to see who you're matched with.</p>
@endif
<p>4. At the event, seek out your match.</p>
<p>5. Find <a href="/profile/Firebird">Firebird</a> to receive your reward.</p>
@endsection
