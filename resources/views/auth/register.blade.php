@extends('layouts.app')
@section('content')
@guest
<h2>Create profile</h2>
@else
<h2>Edit profile for {{ $email }}</h2>
@endguest

@if (isset($update_errors))
	@if ($update_errors)
		<h2>Error updating profile: {{ $update_errors }}</h2>
	@endif
@endif

<form method="POST" action="@guest {{ route('register') }} @endguest" enctype="multipart/form-data">
{{ csrf_field() }}

<h3>Required</h3>

<label for="name"><b>Wasteland nickname</b>. If you don't have one, make one up. English letters, numbers, spaces only.</label>
<input id="name" type="text" name="name" value="@guest{{ old('name') }}@else{{ $wasteland_name }}@endguest" pattern="^[A-Za-z0-9 ]+$" maxlength="50" required autofocus>
@if ($errors->has('name'))
<strong>{{ $errors->first('name') }}</strong>
@endif

@guest
<br>
<label for="email"><b>Email</b>. It will not be shown or given out to anyone, not even your mutual matches.</label>
<input id="email" type="email" name="email" value="{{ old('email') }}" maxlength="50" required>
@if ($errors->has('email'))
<strong>{{ $errors->first('email') }}</strong>
@endif
@endguest

<br>
<label for="password"><b>Password</b>. English letters, numbers, spaces only.</label>
<input id="password" type="password" name="password" pattern="^[A-Za-z0-9 ]+$" @guest required @endguest>
@if ($errors->has('password'))
<strong>{{ $errors->first('password') }}</strong>
@endif

<br>
<label for="password-confirm"><b>Confirm password.</b></label>
<input id="password-confirm" type="password" name="password_confirmation" pattern="^[A-Za-z0-9 ]+$" @guest required @endguest>

<br>
<label for="number_people"><b>Number of people</b> in this profile.</label>
<select name="number_people" id="number_people">
<option value="1" @guest @else @if ($number_people === 1) selected @endif @endguest>1</option>
<option value="2" @guest @else @if ($number_people === 2) selected @endif @endguest>2 people</option>
<option value="3" @guest @else @if ($number_people === 3) selected @endif @endguest>A group of 3 or more</option>
</select>

@if ($errors->has('number_people'))
<strong>{{ $errors->first('number_people') }}</strong>
@endif

<br><br>
Check at least one. <b>I/we will be attending</b> the next...
<br>
<input type="checkbox" name="attending_winter_games" id="attending_winter_games" @guest @else @if ($attending_winter_games) checked @endif @endguest>
<label for="attending_winter_games">Wasteland Winter Games.</label>
<br>
<input type="checkbox" name="attending_ball" id="attending_ball" @guest @else @if ($attending_ball) checked @endif @endguest>
<label for="attending_ball">Wastelanders Ball.</label>
<br>
<input type="checkbox" name="attending_detonation" id="attending_detonation" @guest @else @if ($attending_detonation) checked @endif @endguest>
<label for="attending_detonation">Detonation Uranium Springs.</label>
<br>
<input type="checkbox" name="attending_wasteland" id="attending_wasteland" @guest checked @else @if ($attending_wasteland) checked @endif @endguest>
<label for="attending_wasteland">Wasteland Weekend.</label>

<br><br>
Check at least one. <b>I am/we are</b>...
<br>
<input type="checkbox" name="random_ok" id="random_ok" @guest checked @else @if ($random_ok) checked @endif @endguest>
<label for="random_ok">Open to a random match. If unchecked, you are less likely to get a match. If checked, we may disregard your ratings of others' profiles if we can't find you a mutual match.</label>
<br>
<input type="checkbox" name="hoping_to_find_friend" id="hoping_to_find_friend" @guest checked @else @if ($hoping_to_find_friend) checked @endif @endguest>
<label for="hoping_to_find_friend">Open to making a new friend.</label>
<br>
<input type="checkbox" name="hoping_to_find_love" id="hoping_to_find_love" @guest @else @if ($hoping_to_find_love) checked @endif @endguest>
<label for="hoping_to_find_love">Open to making a new friend or romantic partner.</label>
<br>
<input type="checkbox" name="hoping_to_find_lost" id="hoping_to_find_lost" @guest @else @if ($hoping_to_find_lost) checked @endif @endguest>
<label for="hoping_to_find_lost">Looking for someone specific, a missed connection, or someone I once knew but have lost.</label>
<br>
<input type="checkbox" name="hoping_to_find_enemy" id="hoping_to_find_enemy" @guest @else @if ($hoping_to_find_enemy) checked @endif @endguest>
<label for="hoping_to_find_enemy">Looking for an enemy.</label>

<h3>Suggested</h3>

<b>Upload images</b>. No nudity. If you don't include a picture of your face that's ok, but you might be assigned a lower ranking in search results.
@guest 
@else
To remove old images just upload new ones.
@endguest
<br>
<input type="file" name="image1" value="image">
<br>
<input type="file" name="image2" value="image">
<br>
<input type="file" name="image3" value="image">
<br>
<input type="file" name="image4" value="image">
<br>
<input type="file" name="image5" value="image">

<br><br>
<label for="gender"><b>Gender.</b></label>
<select name="gender" id="gender">
	<option value="">No answer</option>
	<option value="M" @guest @else @if ($gender === 'M') selected @endif @endguest>M</option>
	<option value="F" @guest @else @if ($gender === 'F') selected @endif @endguest>F</option>
	<option value="O" @guest @else @if ($gender === 'O') selected @endif @endguest>Other</option>
</select>
<br>
<label for="gender_of_match"><b>I would prefer to be matched with</b> a person of gender...</label>
<select name="gender_of_match" id="gender_of_match">
	<option value="">Any</option>
	<option value="M" @guest @else @if ($gender_of_match === 'M') selected @endif @endguest>M</option>
	<option value="F" @guest @else @if ($gender_of_match === 'F') selected @endif @endguest>F</option>
	<option value="O" @guest @else @if ($gender_of_match === 'O') selected @endif @endguest>Other</option>
</select>

<br>
<label for="height"><b>Height.</b></label>
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
<label for="birth_year"><b>Birth decade.</b></label>
<select name="birth_year" id="birth_year">
	<option value="">No answer</option>
	<option value="1959" @guest @else @if ($birth_year === 1959) selected @endif @endguest>Before 1960</option>
	<option value="1960" @guest @else @if ($birth_year === 1960) selected @endif @endguest>1960s</option>
	<option value="1970" @guest @else @if ($birth_year === 1970) selected @endif @endguest>1970s</option>
	<option value="1980" @guest @else @if ($birth_year === 1980) selected @endif @endguest>1980s</option>
	<option value="1990" @guest @else @if ($birth_year === 1990) selected @endif @endguest>1990s</option>
	<option value="2000" @guest @else @if ($birth_year === 2000) selected @endif @endguest>2000s</option>
</select>

<br><br>
<label for="description"><b>Tell other users about yourself.</b> Feel free to give your age and where you're from but do not include real names, emails, phone numbers, or addresses. 2000 characters max.</label>
<br>
<input type="text" size="100" maxlength="2000" name="description" id="description" value="@guest{{ old('description') }}@else{{ $description }}@endguest">

<br><br>
<label for="how_to_find_me"><b>Give other users a hint how they will be able to find you</b> at the event. Do not include real names, emails, phone numbers, or addresses. 200 characters max.</label>
<br>
<input type="text" size="100" maxlength="200" name="how_to_find_me" id="how_to_find_me" value="@guest{{ old('how_to_find_me') }}@else{{ $how_to_find_me }}@endguest">

<h3>Submit your profile</h3>

<label for="submit">Everything you submit on this page, except your email, will be publicly visible.</label>
<br><br>

<button id="submit" type="submit">
@guest
Sign up
@else
Submit changes
@endguest
</button>

@guest
@else
<br><br><br><br>
<button type="submit" name="delete">
Delete profile
</button>
@endguest

</form>
















@endsection
