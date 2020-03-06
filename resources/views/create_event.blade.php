@extends('layouts.app')
@section('content')

<h1>Create an event</h1>

<form action="" method="POST">

    {{ csrf_field() }}

    <label for="event_long_name">Event name</label>
    <input id="event_long_name" type="text" name="event_long_name" pattern="^[A-Za-z0-9 ]+$" size="50" maxlength="50" required>
    <br>Make this unique. If it's a recurring event, add the year to the event name. If it's an additional mission at an existing event, add something like &quot;optional second mission.&quot;

    <br><br>
    <label for="event_class">Event class</label>
    <input id="event_class" type="text" name="event_class" pattern="^[A-Za-z0-9 ]+$" size="50" maxlength="50" required>
    <br>This is just a word or a few words describing the type of event it is. Event class can be re-used for multiple similar events. You can create a new class, or use an existing class such as {{ $existing_event_classes }}.

    <br><br>
    <label for="event_date">Event date</label>
    <input id="event_date" type="date" name="event_date" required>

    <br><br>
    <label for="url">URL</label>
    <input id="url" type="url" name="url" size="50" maxlength="255" value="https://www.facebook.com/events/" required>
    <br>Facebook event or group page. The URL should look something like https://www.facebook.com/events/123456/ or https://www.facebook.com/groups/123456/.

    <br><br>
    <button id="submit" type="submit" class="yesyes">Create event</button>

</form>

@endsection
