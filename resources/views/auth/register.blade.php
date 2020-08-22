@extends('layouts.app')
@section('content')
@if (isset($update_errors))
	@if ($update_errors)
		<h2 class="bright">Error updating profile: {{ $update_errors }}</h2>
	@endif
@endif

<form method="POST" action="@guest {{ route('register') }} @endguest" enctype="multipart/form-data">
{{ csrf_field() }}

<label for="name">Nickname</label>
<input id="name" type="text" name="name" value="@guest{{ old('name') }}@else{{ $wasteland_name }}@endguest" pattern="^[A-Za-z0-9 ]+$" maxlength="50" required autofocus>
@if ($errors->has('name'))
<strong class="bright">{{ $errors->first('name') }}</strong>
@endif

<br><br>
<label for="email">Email</label>
<input id="email" type="email" name="email" value="@guest{{ old('email') }}@else{{ $email }}@endguest" maxlength="50" required>
@if ($errors->has('email'))
<strong class="bright">{{ $errors->first('email') }}</strong>
@endif

<br><br>
<label for="password">Password</label>
<input id="password" type="password" name="password" @guest required @endguest>
@if ($errors->has('password'))
<strong class="bright">{{ $errors->first('password') }}</strong>
@endif

<br>
<label for="password-confirm">Confirm password</label>
<input id="password-confirm" type="password" name="password_confirmation" @guest required @endguest>

<hr>

<h1><input class="upcoming_event_checkbox" type="checkbox" name="campaigning" id="campaigning" @guest @else @if ($campaigning) checked @endif @endguest><label for="president">&nbsp;I am running for the office of Prezident of the Restored United States of Murica</label></h1>

<label for="video_id">YouTube video id</label>
<input type="text" name="video_id" id="video_id" value="@guest{{ old('video_id') }}@else{{ $video_id }}@endguest"></input>

<hr>

I am...
<br>
<input type="checkbox" name="hoping_to_find_friend" id="hoping_to_find_friend" checked disabled>
<label for="hoping_to_find_friend">Open to finding a new friend.</label>
<br>
@guest
@else
    @if ($is_wastelander)
        <input type="checkbox" name="hoping_to_find_enemy" id="hoping_to_find_enemy" @guest @else @if ($hoping_to_find_enemy) checked @endif @endguest>
        <label for="hoping_to_find_enemy">Open to finding a new enemy.</label>
        <br>
    @endif
@endguest
<input type="checkbox" name="hoping_to_find_love" id="hoping_to_find_love" @guest @else @if ($hoping_to_find_love) checked @endif @endguest>
<label for="hoping_to_find_love">Open to finding a new romantic partner.</label>
<br>
<input type="checkbox" name="random_ok" id="random_ok" @guest checked @else @if ($random_ok) checked @endif @endguest>
<label for="random_ok">(Recommended) Open to a random match if a mutual match can't be found.</label>
<hr>

<input type="checkbox" name="share_info_with_favorites" id="share_info_with_favorites" @guest @else @if ($share_info_with_favorites) checked @endif @endguest>
<label for="share_info_with_favorites">Share my email address with mutuals.</label>

<hr>

@guest
@else
@if (isset($missions_completed) && $missions_completed > 0)
<label for="title_index">Title</label>
<select name="title_index" id="title_index">
    @for ($i = 0; $i <= $missions_completed; $i++)
        <option value="{{ $i }}" @if ($title_index === $i) selected @endif>{{ $titles[$i] }}</option>
    @endfor
</select>
<br>
@endif
@endguest

<label for="height">Height</label>
<select name="height" id="height">
	<option value="">No answer</option>
	<option value="59" @guest @else @if ($height === 59) selected @endif @endguest>Under 5&apos;</option>
	<option value="60" @guest @else @if ($height === 60) selected @endif @endguest>5&apos;</option>
	<option value="61" @guest @else @if ($height === 61) selected @endif @endguest>5&apos;1&quot;</option>
	<option value="62" @guest @else @if ($height === 62) selected @endif @endguest>5&apos;2&quot;</option>
	<option value="63" @guest @else @if ($height === 63) selected @endif @endguest>5&apos;3&quot;</option>
	<option value="64" @guest @else @if ($height === 64) selected @endif @endguest>5&apos;4&quot;</option>
	<option value="65" @guest @else @if ($height === 65) selected @endif @endguest>5&apos;5&quot;</option>
	<option value="66" @guest @else @if ($height === 66) selected @endif @endguest>5&apos;6&quot;</option>
	<option value="67" @guest @else @if ($height === 67) selected @endif @endguest>5&apos;7&quot;</option>
	<option value="68" @guest @else @if ($height === 68) selected @endif @endguest>5&apos;8&quot;</option>
	<option value="69" @guest @else @if ($height === 69) selected @endif @endguest>5&apos;9&quot;</option>
	<option value="70" @guest @else @if ($height === 70) selected @endif @endguest>5&apos;10&quot;</option>
	<option value="71" @guest @else @if ($height === 71) selected @endif @endguest>5&apos;11</option>
	<option value="72" @guest @else @if ($height === 72) selected @endif @endguest>6&apos;</option>
	<option value="73" @guest @else @if ($height === 73) selected @endif @endguest>Over 6&apos;</option>
</select>

<br>

<label for="birth_year">Birth decade (you must be 18 or older)</label>
<select name="birth_year" id="birth_year">
	<option value="">No answer</option>
	<option value="1959" @guest @else @if ($birth_year === 1959) selected @endif @endguest>Before 1960</option>
	<option value="1960" @guest @else @if ($birth_year === 1960) selected @endif @endguest>1960s</option>
	<option value="1970" @guest @else @if ($birth_year === 1970) selected @endif @endguest>1970s</option>
	<option value="1980" @guest @else @if ($birth_year === 1980) selected @endif @endguest>1980s</option>
	<option value="1990" @guest @else @if ($birth_year === 1990) selected @endif @endguest>1990s</option>
	<option value="2000" @guest @else @if ($birth_year === 2000) selected @endif @endguest>2000s</option>
</select>

<br>

<label for="gender">Gender</label>
<select name="gender" id="gender">
	<option value="">No answer</option>
	<option value="M" @guest @else @if ($gender === 'M') selected @endif @endguest>Man</option>
	<option value="W" @guest @else @if ($gender === 'W') selected @endif @endguest>Woman</option>
	<option value="O" @guest @else @if ($gender === 'O') selected @endif @endguest>Other</option>
</select>

<br>
<br>
We will try to match you to a user of your preferred gender, but you must be open to being matched to users of any gender.

<br>
<label for="gender_of_match">I would prefer to be matched with a person of gender...</label>
<select name="gender_of_match" id="gender_of_match">
	<option value="">Any</option>
	<option value="M" @guest @else @if ($gender_of_match === 'M') selected @endif @endguest>Man</option>
	<option value="W" @guest @else @if ($gender_of_match === 'W') selected @endif @endguest>Woman</option>
	<option value="O" @guest @else @if ($gender_of_match === 'O') selected @endif @endguest>Other</option>
</select>
<br>
<label for="gender_of_match_2">Or gender...</label>
<select name="gender_of_match_2" id="gender_of_match_2">
	<option value="">No answer</option>
	<option value="M" @guest @else @if ($gender_of_match_2 === 'M') selected @endif @endguest>Man</option>
	<option value="W" @guest @else @if ($gender_of_match_2 === 'W') selected @endif @endguest>Woman</option>
	<option value="O" @guest @else @if ($gender_of_match_2 === 'O') selected @endif @endguest>Other</option>
</select>

<hr>

<label for="description">Tell other users about yourself.</label> Feel free to include where you're from but do not include real names, emails, phone numbers, or addresses. Emojis and non-English characters are not yet supported. 2000 characters maximum.
<br>
<textarea rows="10" name="description" id="description">@guest{{ old('description') }}@else{{ $description }}@endguest</textarea>

<br><br>
<label for="how_to_find_me">Tell your matches how they can find you at the event.</label> Do not include real names, emails, phone numbers, or addresses. Emojis and non-English characters are not yet supported.
<br>
<input type="text" style="width:100%;" maxlength="200" name="how_to_find_me" id="how_to_find_me" value="@guest{{ old('how_to_find_me') }}@else{{ $how_to_find_me }}@endguest">

<hr>

<button id="submit" type="submit" class="yesyes">
@guest
Sign up
@else
Submit changes
@endguest
</button>

@guest
<input name="signup_code" id="signup_code" type="text" maxlength="50" required value="@guest{{ old('signup_code') }}@else @endguest"></input>
<label for="signup_code">Signup code</label> (please contact Firebird if you do not have one)
@endguest

</form>

@guest
@else
	@if ($wasteland_name === 'Firebird')
	@else
		<form method="POST" action="" style="width:100%;text-align:right;">
			{{ csrf_field() }}
			<button type="submit" name="delete" class="no">
				DELETE PROFILE
			</button>
		</form>
	@endif
@endguest

@endsection
