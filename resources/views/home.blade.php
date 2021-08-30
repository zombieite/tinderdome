@extends('layouts.app')
@section('content')
@if ($success_message)
    <h2 class="bright">{{ $success_message }}</h2>
@endif
@if ($number_photos)
    @include('prezidential_election', [])
    @foreach ($matched_to_users as $matched_to_user)
        @include('you_are_awaited', [])
    @endforeach
    @if ($comments_to_approve)
        @include('new_comments', [])
    @endif
    @if (count($unrated_users) >= 3)
        @include('enjoy_meeting', [])
    @endif
    @if (count($users_who_say_they_know_you))
        @include('do_you_know', [])
    @else
        @if (count($users_you_can_comment_on_but_havent))
            @include('leave_a_comment', [])
        @endif
    @endif
    @include('mutuals', [])
    @include('mission_status', [])
    @include('mission_matches', [])
@else
    <h1>You must <a href="/image/upload">upload a photo</a>.</h1>
@endif
@endsection
