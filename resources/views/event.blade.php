@extends('layouts.app')
@section('content')

    {{ csrf_field() }}

    <h1>{{ $event->event_long_name }}: {{ $event->event_date }}</h1>
    @if ($event->url)
        <h3><a href="{{ $event->url }}">{{ $event->url }}</a></h3>
    @endif
    @if ($event->created_by_name)
        @if ($logged_in_user_created_this_event)
            <span class="small">Event created by you</span><br><br>
        @else
            <span class="small">Event created by {{ $event->created_by_name }}</span><br><br>
        @endif
    @endif
    {{ $event->description }}<br>
    @guest
        <a href="/register" class="bright">Sign up here</a>.
    @else
        <form action="/" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="attending_event_form" value="1">
            <input type="hidden" name="attending_event_id" value="{{ $event->event_id }}">
            <input type="hidden" name="attending_event_name" value="{{ $event->event_long_name }}">
            <br><input class="upcoming_event_checkbox" type="checkbox" name="attending_event_id_{{ $event->event_id }}"
                @if ($event->attending_event_id)
                    @if ($event->user_id_of_match || $event->already_matched_but_dont_know_it)
                        disabled
                    @endif
                    checked
                @endif
            > I will be attending this event.
            <br><br><button id="submit" type="submit" class="yesyes">Submit changes</button>
        </form>
    @endguest

@endsection
