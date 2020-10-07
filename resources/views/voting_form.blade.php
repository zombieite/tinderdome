<input type="radio" name="vote" id="vote_{{ $profile->profile_id }}" value="{{ $profile->profile_id }}" @if ($vote == $profile->profile_id) checked @endif>
<label class="dull" for="vote_{{ $profile->profile_id }}">{{ $profile->name }} (check box, scroll down, click Submit My Vote)</label>
