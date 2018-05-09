<form action="{{ $action }}" method="POST">
	{{ csrf_field() }}
	<input type="hidden" name="chosen" value="{{ $user_id_to_rate }}">
	@if ($logged_in_user_hoping_to_find_love)
		<input type="submit" name="YesYesYes" value="YES 🖤"@if (($current_choice == 3) || !isset($current_choice)) class="yesyesyes"@else class="unselected_emoji_button" @endif>
		<input type="submit" name="YesYes" value="Yes"@if (($current_choice == 2) || !isset($current_choice)) class="yesyes"@endif>
	@else
		<input type="submit" name="YesYes" value="Yes"@if (($current_choice >= 2) || !isset($current_choice)) class="yesyes"@endif>
	@endif
	<input type="submit" name="Yes" value="Neutral"@if (($current_choice == 1) || !isset($current_choice)) class="yes"@endif>
	<input type="submit" name="Met" value="I have met them"@if (($current_choice == -1) || !isset($current_choice)) class="met"@endif>
	@if ($nos_left > 0)
		<input type="submit" name="No" value="No ({{ $nos_left }} left)"@if (($current_choice == 0) || !isset($current_choice)) class="no"@endif>
	@else
		<br><br><span class="no">To mark more users as no, you must <a href="/search">change {{ -$nos_left+1 }} of your previous no ratings to yes</a>.</span>
	@endif
</form>
