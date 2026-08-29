@extends('layouts.app')
@section('content')
@if ($quarry_already_chosen)
    <h1>Your chosen quarry was just chosen by someone else. You must choose a new quarry.</h1>
@endif
@foreach ($profiles as $profile)
    @include('user_block_search_result')
@endforeach
@if (count($profiles) == 0)
    <h1>You can not hunt anyone until we get more signups. There is no one currently available to be hunted.</h1>
@endif
@endsection
