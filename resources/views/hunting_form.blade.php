<form action="/hunt?event_id={{ $event_id }}" method="POST">
	{{ csrf_field() }}
	<input type="hidden" name="chosen_quarry_id" value="{{ $hunted_user_id }}">
	@if (isset($is_my_match) && $is_my_match)
	@else
		<input type="submit" name="huntme" value="Hunt me">
	@endif
</form>
