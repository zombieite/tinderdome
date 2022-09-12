<form action="/" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="chosen" value="{{ $user_id_of_match }}">
    <input type="submit" name="Met" value="I found them" class="met">
</form>
