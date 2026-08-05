<form action="{{ $action }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="commented_upon_user_id" value="{{ $user_id_to_rate }}">
    <label for="comment">You can comment on this profile. Comments must be approved.</label>
    <br>
    <input type="text" name="comment" style="width:100%;" maxlength="280">
    <input type="submit" value="Submit comment" class="yesyes">
</form>
