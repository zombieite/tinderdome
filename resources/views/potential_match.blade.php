@extends('layouts.app')
@section('content')
@php $previous_profile_id = isset($profiles[0]) ? $profiles[0]['profile_id'] : ''; @endphp
@if ($not_signed_up)
    You are not signed up for this event.
@else
    @if ($profiles)
        @foreach ($profiles as $profile)
            @include('user_block_search_result')
            @php $previous_profile_id = $profile['profile_id'] @endphp
        @endforeach
    @else
        @if ($show_met)
            No one else has signed up yet.
        @else
            We can't find any potential matches for you yet.
        @endif
        Encourage other people to sign up, keep an eye on the <a href="/">home page</a>, and be sure to rate all new users as they show up.
    @endif
@endif
@endsection
