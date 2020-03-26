@extends('layouts.app')
@section('content')

<form action="" method="POST">
    {{ csrf_field() }}

    <h1>{{ $event->event_long_name }}: {{ $event->event_date }}</h1>
    @if ($event->url)
        <h3><a href="{{ $event->url }}">{{ $event->url }}</a></h3>
    @endif
    {{ $event->description }}<br><br>
    @if ($event->created_by_name)
        @if ($logged_in_user_created_this_event)
            <span class="small">Event created by you</span>
        @else
            <span class="small">Event created by {{ $event->created_by_name }}</span>
        @endif
    @endif
    @guest
        <a href="/register">Sign up here</a>.
    @endguest
    @if ($logged_in_user_created_this_event)
        <br><button id="submit" type="submit" class="yesyes">Submit changes</button>
    @endif
</form>

@endsection
