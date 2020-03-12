@extends('layouts.app')
@section('content')

<h1>Event</h1>

<form action="" method="POST">
    {{ csrf_field() }}

    {{ $event->event_long_name }}
    <br>{{ $event->event_class }}
    <br>{{ $event->event_date }}
    <br>{{ $event->url }}

    @if ($logged_in_user_created_this_event)
        <br><button id="submit" type="submit" class="yesyes">Submit changes</button>
    @endif
</form>

@endsection
