@extends('layouts.app')
@section('content')
@php $previous_profile_id = isset($profiles[0]) ? $profiles[0]['profile_id'] : ''; @endphp
@foreach ($profiles as $profile)
    @include('user_block_search_result')
    @php $previous_profile_id = $profile['profile_id'] @endphp
@endforeach
@endsection
