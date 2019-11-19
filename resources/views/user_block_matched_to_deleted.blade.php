<div class="mission_match">
Matched to deleted user;<br>mission incomplete
<br>{{ $event_long_name }}
<form action="/" method="POST">{{ csrf_field() }}<input type="submit" name="delete_mission_{{ $event_id }}" value="Delete this mission"></form>
</div>
