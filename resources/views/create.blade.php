@extends('layouts.app')
@section('content')
<h2>Create profile</h2>
<p>
	Once submitted, this profile cannot be edited. Contact me, <a href="mailto:wastelandfirebird@gmail.com">wastelandfirebird@gmail.com</a>, to delete your profile or to report users for bad behavior. Any data may be deleted by me at any time for any reason without notice. <a href="/profile/1/Firebird">Here&apos;s my profile</a>.
</p>
<form action="/profile" method="post" enctype="multipart/form-data">

	<h3>Required</h3>

	<label for="wasteland_name">Wasteland nickname (English letters, numbers, spaces only)</label>
	<input type="text" size="50" maxlength="50" name="wasteland_name" id="wasteland_name" pattern="^[A-Za-z0-9 ]+$" autofocus required>

	<br><br>
	<label for="number_people">Number of people in this profile</label>
	<select name="number_people" id="number_people">
		<option value="1" selected>1</option>
		<option value="2">2 people</option>
		<option value="3">A group of 3 or more</option>
	</select>

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
	<label for="random_ok">Open to a random match, but would prefer to choose one</label>
	<br>
	<input type="checkbox" name="hoping_to_find_acquaintance" id="hoping_to_find_acquaintance">
	<label for="hoping_to_find_acquaintance">Open to making a new acquaintance</label>
	<br>
	<input type="checkbox" name="hoping_to_find_friend" id="hoping_to_find_friend">
	<label for="hoping_to_find_friend">Open to making a new acquaintance or friend</label>
	<br>
	<input type="checkbox" name="hoping_to_find_love" id="hoping_to_find_love">
	<label for="hoping_to_find_love">Open to making a new acquaintance, friend, or romantic partner</label>
	<br>
	<input type="checkbox" name="hoping_to_find_lost" id="hoping_to_find_lost">
	<label for="hoping_to_find_lost">Looking for someone specific, a missed connection, or someone I once knew but have lost</label>
	<br>
	<input type="checkbox" name="hoping_to_find_enemy" id="hoping_to_find_enemy">
	<label for="hoping_to_find_enemy">Looking for an adversary to battle in the Thunderdome</label>

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

	<br><br>
	<label for="email">Email. It will not be shown or given out to anyone, not even your mutual matches.</label>
	<br>
	<input type="email" size="50" maxlength="50" name="email" id="email">


	<h3>Submit your profile</h3>

	{{ csrf_field() }}
	<label for="submit">Everything you submit on this page, except your email, will be publicly visible.</label>
	<br><br>
	<input type="submit" value="Submit" id="submit">

</form>
@endsection
