<form action="/" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="chosen" value="{{ $user_id_of_match }}">
    <input type="submit" name="Met" value="I found them" class="met">
    <input type="submit" name="delete_mission_{{ $event_id }}" value="Delete this mission" class="no">
</form>