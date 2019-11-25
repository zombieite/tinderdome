@extends('layouts.app')
@section('content')
@if ($matchme)
    @if ($my_match_user_id)
        <a href="/profile/match?event_id={{ $event_id }}">You now have a match for {{ $event_name }}!</a>
    @else
        We could not yet find you a match. You may still get a match, because new people are signing up and they could be matched to you. Try again later.
        @if ($logged_in_user->random_ok)
        @else
            To increase your chances, go to <a href="/profile/edit">Edit Profile</a> and set your preference to "Open to a random match."
        @endif
    @endif
@else
    Click to be matched for {{ $event_name }}.
    <form action="" method="POST">
        {{ csrf_field() }}
        <input type="submit" name="matchme" value="TRUST THE ALGORITHM" class="yesyesyes">
    </form>
@endif
@endsection
