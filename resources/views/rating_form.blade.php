<form action="{{ $action }}" method="POST">
	{{ csrf_field() }}
	<input type="hidden" name="chosen" value="{{ $user_id_to_rate }}">
	@if (isset($is_my_match) && $is_my_match)
	@else
		<input type="submit" name="YesYesYes" value="{{ $curse_interface ? 'Fuck yeah' : 'Heck yeah' }}"@if (($current_choice == 3) || !isset($current_choice)) class="yesyesyes"@else class="unselected_emoji_button" @endif>
		<input type="submit" name="YesYes" value="Yes"@if (($current_choice == 2) || !isset($current_choice)) class="yesyes"@endif>
		<input type="submit" name="Yes" value="Neutral"@if (($current_choice == 1) || !isset($current_choice)) class="yes"@endif>
	@endif
	@if (isset($is_my_match) && $is_my_match)
		<input type="submit" name="Met" value="I found them"@if (!isset($current_choice) || (isset($current_choice) && ($current_choice != 0))) class="met"@endif>
		&nbsp;&nbsp;&nbsp;
		<input type="submit" name="No" value="I found them but I want to block them"@if (!isset($current_choice) || (isset($current_choice) && ($current_choice != -1))) class="no"@endif>
	@else
		<input type="submit" name="Met" value="I have met them"@if (($current_choice == -1) || !isset($current_choice)) class="met"@endif>
		@if (($current_choice === 0) || ($nos_left > 0) || (!$number_photos))
			<input type="submit" name="No" value="Block this user"@if (($current_choice == 0) || !isset($current_choice)) class="no"@endif>
		@endif
		@if (($nos_left <= 0) && ($current_choice !== 0))
			<br><br><span class="no">To block this user, you must <a href="/search?show_nos=1">unblock some of your previously blocked users</a>.</span>
		@endif
	@endif
</form>
