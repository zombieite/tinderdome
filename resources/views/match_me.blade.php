@extends('layouts.app')
@section('content')
@if ($matchme)
    @if ($my_match_user_id)
        <a href="/profile/match?event_id={{ $event_id }}">You now have a match for {{ $event_name }}!</a>
    @else
        You do not yet have a match. You may still get a match, because new people are signing up and they could be matched to you. Check back later!
    @endif
@else
    Click to be matched for {{ $event_name }}.
    <form action="" method="POST">
        {{ csrf_field() }}
        <input type="submit" name="matchme" value="TRUST THE ALGORITHM" class="yesyesyes">
    </form>
@endif
@endsection
