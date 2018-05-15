@extends('layouts.app')

@section('content')
<ol>
<li>COMPLETE: Profile created.</li>
@if (!$unrated_users)
<li>COMPLETE: You have rated every profile. Check back later to rate new arrivals. Or you can <a href="/search">revisit profiles</a> you've already viewed.</li>
@else
<li><a href="/profile/compatible?">Choose potential matches</a>.</li>
@endif
@if ($matched)
<li><a href="/profile/match">COMPLETE: You are awaited! Click here to see who you're matched with.</a></li>
@else
<li>Next matching run will happen on the night of May 23 for Detonation Uranium Springs 2018. If you'll be attending <a href="/profile/edit">be sure your profile has the appropriate box checked</a>. Check back before then to see who you're matched with.</li>
@endif
<li>At the event, seek out your match.</li>
<li>Find <a href="/profile/Firebird">Firebird</a> to receive your reward.</li>
</ol>
@endsection
