<form action="{{ $action }}" method="POST">
	{{ csrf_field() }}
	<input type="hidden" name="commented_upon_user_id" value="{{ $user_id_to_rate }}">
    <label for="comment">You know each other. You can comment on their profile. Comments must be approved. All comments are deleted after one year.</label>
    <br>
    <input type="text" name="comment" size="140" maxlength="140">
    <input type="submit" value="Submit comment" class="yesyes">
</form>
