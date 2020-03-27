@extends('layouts.app')
@section('content')

<h1>Create an event</h1>

<form action="" method="POST">

    {{ csrf_field() }}

    <label for="event_long_name">Event name</label>
    <input id="event_long_name" type="text" name="event_long_name" size="50" maxlength="50" required>
    <br>Make this unique. If it's a recurring event, add the year to the event name. If it's an additional mission at an existing event, add something like &quot;optional second mission.&quot;

    <br><br>
    <label for="event_class">Event class</label>
    <input id="event_class" type="text" name="event_class" size="50" maxlength="50" value="{{ $logged_in_user_name }}'s events" required>
    <br>This is just a word or a few words to classify the type of event it is. Event class can be re-used for multiple similar events. Some example classes would be "post-apocalyptic," "burning man," ""concert," "festival," "wedding reception," or "corporate retreat."

    <br><br>
    <label for="event_date">Event date</label>
    <input id="event_date" type="date" name="event_date" required>

    <br><br>
    <label for="url">URL</label>
    <input id="url" type="url" name="url" size="50" maxlength="255">
    <br>URL of the event. Often this can be a Facebook event or group page.

    <br><br>
    <label for="description">Time, location, and description</label>
    <textarea rows="10" name="description" id="description"></textarea>
    <br>Time, location, and description of the event. 2000 characters maximum.

    @if ($logged_in_user_can_create_public_missions)
        <br><br>
        <label for="public">Publicly visible event</label>
        <input id="public" name="public" type="checkbox">
        <br>If the event is publicly visible, anyone can see it on the homepage and anyone can sign up. If the event is not public, you must share a link to the event and you must approve or deny participants.
    @else
        <br><br>
        This event will be a private event. You must share a link to the event and you must approve or deny participants.
    @endif

    <br><br>
    <button id="submit" type="submit" class="yesyes">Create event</button>

</form>

@endsection
