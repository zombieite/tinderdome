<div class="centered_block">
@if ($number_photos) <a href="{{ $url }}"><img src="/uploads/image-{{ $user_id }}-1.jpg" style="height:100px;"></a><br> @endif
Matched to
<a href="{{ $url }}">{{ $name }}</a>
<br>{{ $event_long_name }}
<br><form action="/" method="POST">{{ csrf_field() }}<input type="hidden" name="chosen" value="{{ $user_id }}"><input type="submit" name="Met" class="met" value="Found!"><br><input type="submit" name="No" class="no" value="Found but did not enjoy meeting"></form>
@if ($ok_to_delete_old_mission)
    <form action="/" method="POST">{{ csrf_field() }}<input type="submit" name="delete_mission_{{ $event_id }}" class="no" value="Delete this mission"></form>
@endif
</div>
