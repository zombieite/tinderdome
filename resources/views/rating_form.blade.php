<form action="{{ $action }}" method="POST">
	{{ csrf_field() }}
	<input type="hidden" name="chosen" value="{{ $user_id_to_rate }}">
	@if (isset($is_my_match) && $is_my_match)

	@else
		@if ($logged_in_user_hoping_to_find_love)
			<input type="submit" name="YesYesYes" value="YES 🖤"@if (($current_choice == 3) || !isset($current_choice)) class="yesyesyes"@else class="unselected_emoji_button" @endif>
			<input type="submit" name="YesYes" value="Yes"@if (($current_choice == 2) || !isset($current_choice)) class="yesyes"@endif>
		@else
			<input type="submit" name="YesYes" value="Yes"@if (($current_choice >= 2) || !isset($current_choice)) class="yesyes"@endif>
		@endif
		<input type="submit" name="Yes" value="Neutral"@if (($current_choice == 1) || !isset($current_choice)) class="yes"@endif>
	@endif
	@if (isset($is_my_match) && $is_my_match)
		<input type="submit" name="Met" value="I found them!"@if (($current_choice == -1) || !isset($current_choice)) class="met"@endif>
		&nbsp;&nbsp;&nbsp;
		<input type="submit" name="No" value="I found them but did not like them"@if (($current_choice == 0) || !isset($current_choice)) class="no"@endif>
	@else
		<input type="submit" name="Met" value="I have met them"@if (($current_choice == -1) || !isset($current_choice)) class="met"@endif>
		@if (($current_choice === 0) || ($nos_left > 0))
			<input type="submit" name="No" value="No ({{ $nos_left >= 0 ? $nos_left : 0 }} left)"@if (($current_choice == 0) || !isset($current_choice)) class="no"@endif>
		@endif
		@if ($nos_left <= 0)
			<br><br><span class="no">To mark more users as No, you must <a href="/search?show_nos=1">change {{ -$nos_left+1 }} of your previous No ratings to Neutral</a>.</span>
		@endif
	@endif
</form>
