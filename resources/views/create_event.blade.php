@extends('layouts.app')
@section('content')

<h1>Create an event</h1>
<h2>This feature is coming coming soon for selected users!</h2>

<form action="" method="POST">

    {{ csrf_field() }}

    <label for="event_long_name">Event name</label>
    <input id="event_long_name" type="text" name="event_long_name" pattern="^[A-Za-z0-9 ]+$" maxlength="50" required>
    Make this unique. If it's a recurring event, include the year in the event name.

    <br><br>
    <label for="event_class">Event class.</label>
    <input id="event_class" type="text" name="event_class" pattern="^[A-Za-z0-9 ]+$" maxlength="50" required>
    This is just a word or a few words describing the type of event it is. Event class can be re-used for multiple similar events.

    <br><br>
    <label for="event_date">Event date</label>
    <input id="event_date" type="date" name="event_date" required>

    <br><br>
    <button id="submit" type="submit" class="yesyes">Create event</button>

</form>

@endsection
