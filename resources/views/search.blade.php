@extends('layouts.app')
@section('content')
@if ($show_heckyeses)
    @if ($profiles_found_count)
        @if ($profiles_found_count === 1)
            You have said you would very much enjoy meeting just one user.<br><br>
        @else
            You have said you would very much enjoy meeting {{ $profiles_found_count }} users.<br><br>
        @endif
    @else
        You have not let us know that you would very much enjoy meeting any users yet.<br><br>
    @endif
@else
    <a href="/search?show_heckyeses=1">See users you've said you would very much enjoy meeting</a><br><br>
@endif
@if ($show_yeses)
    @if ($profiles_found_count)
        @if ($profiles_found_count === 1)
            You have said you would enjoy meeting just one user.<br><br>
        @else
            You have said you would enjoy meeting {{ $profiles_found_count }} users.<br><br>
        @endif
    @else
        You have not let us know that you would enjoy meeting any users yet.<br><br>
    @endif
@else
    <a href="/search?show_yeses=1">See users you've said you would enjoy meeting</a><br><br>
@endif
@if ($show_neutrals)
    @if ($profiles_found_count)
        @if ($profiles_found_count === 1)
            You've said you feel neutral about meeting one user.<br><br>
        @else
            You've said you feel neutral about meeting {{ $profiles_found_count }} users.<br><br>
        @endif
    @else
        You have not said you feel neutral about meeting any users yet.<br><br>
    @endif
@else
    <a href="/search?show_neutrals=1">See users you've said you feel neutral about meeting</a><br><br>
@endif
@if ($show_met)
    @if ($profiles_found_count)
        @if ($profiles_found_count === 1)
            You've said you've met one user.<br><br>
        @else
            You've said you've met {{ $profiles_found_count }} users.<br><br>
        @endif
    @else
        You have not let us know that you have met any users yet.<br><br>
    @endif
@else
    <a href="/search?show_met=1">See users you've said you've met</a><br><br>
@endif
@if ($show_nos)
    @if ($profiles_found_count)
        @if ($profiles_found_count === 1)
            One user blocked.<br><br>
        @else
            {{ $profiles_found_count }} users blocked.<br><br>
        @endif
    @else
        No users blocked.<br><br>
    @endif
@else
    <a href="/search?show_nos=1">See users you've blocked</a><br><br>
@endif
@php $previous_profile_id = isset($profiles[0]) ? $profiles[0]['profile_id'] : ''; @endphp
@foreach ($profiles as $profile)
    @include('user_block_search_result')
    @php $previous_profile_id = $profile['profile_id'] @endphp
@endforeach
@endsection
