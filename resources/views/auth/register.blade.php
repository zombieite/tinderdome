@extends('layouts.app')
@section('content')
@guest
<h2>Create profile</h2>
@else
<h2>Edit profile</h2>
@endguest

<form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
{{ csrf_field() }}

<h3>Required</h3>

<label for="name">Wasteland nickname (English letters, numbers, spaces only)</label>
<input id="name" type="text" name="name" value="{{ old('name') }}" pattern="^[A-Za-z0-9 ]+$" maxlength="50" required autofocus>
@if ($errors->has('name'))
<strong>{{ $errors->first('name') }}</strong>
@endif

<br>
<label for="email">Email. It will not be shown or given out to anyone, not even your mutual matches.</label>
<input id="email" type="email" name="email" value="{{ old('email') }}" maxlength="50" required>
@if ($errors->has('email'))
<strong>{{ $errors->first('email') }}</strong>
@endif

<br>
<label for="password">Password (English letters, numbers, spaces only)</label>
<input id="password" type="password" name="password" pattern="^[A-Za-z0-9 ]+$" required>
@if ($errors->has('password'))
<strong>{{ $errors->first('password') }}</strong>
@endif

<br>
<label for="password-confirm">Confirm Password</label>
<input id="password-confirm" type="password" name="password_confirmation" pattern="^[A-Za-z0-9 ]+$" required>

<br>
<label for="number_people">Number of people in this profile</label>
<select name="number_people" id="number_people">
<option value="1" selected>1</option>
<option value="2">2 people</option>
<option value="3">A group of 3 or more</option>
</select>

@if ($errors->has('number_people'))
<strong>{{ $errors->first('number_people') }}</strong>
@endif

<br><br>
Check at least one. I/we...
<br>
<input type="checkbox" name="attending_winter_games" id="attending_winter_games">
<label for="attending_winter_games">Will be attending the next Wasteland Winter Games</label>
<br>
<input type="checkbox" name="attending_ball" id="attending_ball">
<label for="attending_ball">Will be attending the next Wastelanders Ball</label>
<br>
<input type="checkbox" name="attending_detonation" id="attending_detonation">
<label for="attending_detonation">Will be attending the next Detonation Uranium Springs</label>
<br>
<input type="checkbox" name="attending_wasteland" id="attending_wasteland" checked>
<label for="attending_wasteland">Will be attending the next Wasteland Weekend</label>

<br><br>
Check as many as possible. I am/we are...
<br>
<input type="checkbox" name="random_ok" id="random_ok" checked>
<label for="random_ok">Open to a random match (if unchecked you must come back later to mark some profiles as favorites, and even then you might not get a mutual match)</label>
<br>
<input type="checkbox" name="hoping_to_find_friend" id="hoping_to_find_friend" checked>
<label for="hoping_to_find_friend">Open to making a new friend</label>
<br>
<input type="checkbox" name="hoping_to_find_love" id="hoping_to_find_love">
<label for="hoping_to_find_love">Open to making a new friend or romantic partner</label>
<br>
<input type="checkbox" name="hoping_to_find_lost" id="hoping_to_find_lost">
<label for="hoping_to_find_lost">Looking for someone specific, a missed connection, or someone I once knew but have lost</label>
<br>
<input type="checkbox" name="hoping_to_find_enemy" id="hoping_to_find_enemy">
<label for="hoping_to_find_enemy">Looking for an enemy</label>

<h3>Suggested</h3>

Upload images
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
<label for="gender">Gender</label>
<select name="gender" id="gender">
	<option value="" selected>No answer</option>
	<option value="M">M</option>
	<option value="F">F</option>
</select>
<br>
<label for="gender_of_match">Match me with a person of gender</label>
<select name="gender_of_match" id="gender_of_match">
	<option value="" selected>Any</option>
	<option value="M">M</option>
	<option value="F">F</option>
</select>

<br>
<label for="height">Height</label>
<select name="height" id="height">
	<option value="" selected>No answer</option>
	<option value="59">Under 5&apos;</option>
	<option value="60">5&apos;</option>
	<option value="61">5&apos;1&quot;</option>
	<option value="62">5&apos;2&quot;</option>
	<option value="63">5&apos;3&quot;</option>
	<option value="64">5&apos;4&quot;</option>
	<option value="65">5&apos;5&quot;</option>
	<option value="66">5&apos;6&quot;</option>
	<option value="67">5&apos;7&quot;</option>
	<option value="68">5&apos;8&quot;</option>
	<option value="69">5&apos;9&quot;</option>
	<option value="70">5&apos;10&quot;</option>
	<option value="71">5&apos;11</option>
	<option value="72">6&apos;</option>
	<option value="73">Over 6&apos;</option>
</select>

<br>
<label for="birth_year">Birth decade</label>
<select name="birth_year" id="birth_year">
	<option value="" selected>No answer</option>
	<option value="1959">Before 1960</option>
	<option value="1960">1960s</option>
	<option value="1970">1970s</option>
	<option value="1980">1980s</option>
	<option value="1990">1990s</option>
	<option value="2000">2000s</option>
</select>

<br><br>
<label for="description">Tell other users about yourself. Do not include real names, emails, phone numbers, or addresses. 2000 characters max.</label>
<br>
<input type="text" size="100" maxlength="2000" name="description" id="description">

<br><br>
<label for="how_to_find_me">Give other users a hint how they can find you at the event. Do not include real names, emails, phone numbers, or addresses. 200 characters max.</label>
<br>
<input type="text" size="100" maxlength="200" name="how_to_find_me" id="how_to_find_me">

<h3>Submit your profile</h3>

<label for="submit">Everything you submit on this page, except your email, will be publicly visible.</label>
<br><br>

<button id="submit" type="submit">
Sign up
</button>

</form>
















@endsection
