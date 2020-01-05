@extends('layouts.app')
@section('content')
@php $previous_profile_id = isset($profiles[0]) ? $profiles[0]['profile_id'] : ''; @endphp
@if ($profiles)
    @foreach ($profiles as $profile)
        @include('user_block_search_result')
        @php $previous_profile_id = $profile['profile_id'] @endphp
    @endforeach
@else
    We can't find any potential matches for you yet. Keep an eye on your <a href="/">home page</a> and be sure to rate all new users as they show up.
@endif
@endsection
