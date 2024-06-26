@extends('layouts.app')
@section('content')
@if ($quarry_already_chosen)
    <h1>Your chosen quarry was just chosen by someone else. You must choose a new quarry.</h1>
@endif
@if ($matchme)
    @if ($my_match_user_id)
        <a href="/profile/match?event_id={{ $event_id }}">You now have a match for {{ $event_name }}!</a>
    @else
        We could not find you a match yet. You may still get a match, because new people are signing up and they could be matched to you. Try again later.
        @if ($logged_in_user->random_ok)
        @else
            To increase your chances, go to <a href="/profile/edit">Edit Profile</a> and set your preference to "Open to a random match."
        @endif
    @endif
@else
    @foreach ($profiles as $profile)
        @include('user_block_search_result')
    @endforeach
    @if (count($profiles) == 0)
        <h1>You can not hunt anyone until we get more signups. There is no one currently available to be hunted.</h1>
    @endif
@endif
@endsection
