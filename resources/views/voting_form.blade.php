<span class="bright">THIS VOTING FORM WILL BECOME ACTIVE ON OCT. 6</span>
<form action="" method="POST">
	{{ csrf_field() }}
	<input type="radio" name="vote" id="vote_{{ $profile->profile_id }}" value="{{ $profile->profile_id }}" disabled="true">
    <label for="vote_{{ $profile->profile_id }}"><a href="/profile/{{ $profile->profile_id }}/{{ $profile->wasteland_name_hyphenated }}">{{ $profile->name }}</a></label>
    <input type="submit" name="submit" value="THIS BUTTON WILL BECOME ACTIVE ON OCT. 6" disabled="true">
</form>
