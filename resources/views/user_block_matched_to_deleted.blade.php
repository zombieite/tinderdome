<div class="mission_match">
Matched to deleted user
<br>{{ $event_long_name }}
<form action="/" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="chosen" value="{{ $user_id }}">
    <input type="submit" name="Met" value="I found them" class="met">
    <input type="submit" name="delete_mission_{{ $event_id }}" value="Delete this mission">
</form>
</div>
