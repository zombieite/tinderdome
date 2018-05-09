@extends('layouts.app')
@section('content')
@if (isset($update_errors))
	@if ($update_errors)
		<h2>Error updating profile: {{ $update_errors }}</h2>
	@endif
@endif

<form method="POST" action="@guest {{ route('register') }} @endguest" enctype="multipart/form-data">
{{ csrf_field() }}

<label for="name">Nickname</label>
<input id="name" type="text" name="name" value="@guest{{ old('name') }}@else{{ $wasteland_name }}@endguest" pattern="^[A-Za-z0-9 ]+$" maxlength="50" required autofocus>
@if ($errors->has('name'))
<strong>{{ $errors->first('name') }}</strong>
@endif

<br><br>
<label for="email">Email</label>
<input id="email" type="email" name="email" value="@guest{{ old('email') }}@else{{ $email }}@endguest" maxlength="50" required>
@if ($errors->has('email'))
<strong>{{ $errors->first('email') }}</strong>
@endif
<input type="checkbox" name="share_info_with_favorites" id="share_info_with_favorites" @guest @else @if ($share_info_with_favorites) checked @endif @endguest>
<label for="share_info_with_favorites">Share my email address with mutual favorites</label>

<br><br>
<label for="password">Password</label>
<input id="password" type="password" name="password" @guest required @endguest>
@if ($errors->has('password'))
<strong>{{ $errors->first('password') }}</strong>
@endif

<br>
<label for="password-confirm">Confirm password</label>
<input id="password-confirm" type="password" name="password_confirmation" @guest required @endguest>

<br><br>
Check all that apply. I will be attending the next...
{{--
<br>
<input type="checkbox" name="attending_winter_games" id="attending_winter_games" @guest @else @if ($attending_winter_games) checked @endif @endguest>
<label for="attending_winter_games">Wasteland Winter Games.</label>
<br>
<input type="checkbox" name="attending_ball" id="attending_ball" @guest @else @if ($attending_ball) checked @endif @endguest>
<label for="attending_ball">Wastelanders Ball.</label>
--}}
<br>
<input type="checkbox" name="attending_detonation" id="attending_detonation" @guest @else @if ($attending_detonation) checked @endif @endguest>
<label for="attending_detonation">Detonation Uranium Springs.</label>
<br>
<input type="checkbox" name="attending_wasteland" id="attending_wasteland" @guest checked @else @if ($attending_wasteland) checked @endif @endguest>
<label for="attending_wasteland">Wasteland Weekend.</label>

<br><br>
Check all that apply. I am...
<br>
<input type="checkbox" name="random_ok" id="random_ok" @guest checked @else @if ($random_ok) checked @endif @endguest>
<label for="random_ok">Open to a random match if a mutual match can't be found.</label>
<br>
<input type="checkbox" name="hoping_to_find_friend" id="hoping_to_find_friend" @guest checked @else @if ($hoping_to_find_friend) checked @endif @endguest>
<label for="hoping_to_find_friend">Open to finding a new friend.</label>
<br>
<input type="checkbox" name="hoping_to_find_love" id="hoping_to_find_love" @guest @else @if ($hoping_to_find_love) checked @endif @endguest>
<label for="hoping_to_find_love">Open to finding a new friend or romantic partner.</label>

<br><br>
Upload images. No nudity. Please resize them to around 500 pixels in height before uploading.
@guest
@else
To remove old images just upload new ones.
<br>
@for ($i = 1; $i <= $number_photos; $i++)
	<a target="_blank" href="/uploads/image-{{ $profile_id }}-{{ preg_replace('/\s/', '-', $wasteland_name) }}-{{ $i }}.jpg"><img src="/uploads/image-{{ $profile_id }}-{{ preg_replace('/\s/', '-', $wasteland_name) }}-{{ $i }}.jpg" style="height:50px;"></a>
@endfor
@endguest
<br>
<input type="file" name="image1" value="image">
<br>
<input type="file" name="image2" value="image">
<br>
<input type="file" name="image3" value="image">
@if (isset($number_photos))
@if ($number_photos > 3)
<br>
<input type="file" name="image4" value="image">
@endif
@if ($number_photos > 4)
<br>
<input type="file" name="image5" value="image">
@endif
@endif

<br><br>
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

<br><br>
<label for="birth_year">Birth decade</label>
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
<label for="gender">Gender</label>
<select name="gender" id="gender">
	<option value="">No answer</option>
	<option value="M" @guest @else @if ($gender === 'M') selected @endif @endguest>M</option>
	<option value="F" @guest @else @if ($gender === 'F') selected @endif @endguest>F</option>
	<option value="O" @guest @else @if ($gender === 'O') selected @endif @endguest>Other</option>
</select>

<br><br>
<label for="gender_of_match">I would prefer to be matched with a person of gender...</label>
<select name="gender_of_match" id="gender_of_match">
	<option value="">Any</option>
	<option value="M" @guest @else @if ($gender_of_match === 'M') selected @endif @endguest>M</option>
	<option value="F" @guest @else @if ($gender_of_match === 'F') selected @endif @endguest>F</option>
	<option value="O" @guest @else @if ($gender_of_match === 'O') selected @endif @endguest>Other</option>
</select>

<br><br>
<label for="description">Tell other users about yourself. What makes you weird? Feel free to include where you're from but do not include real names, emails, phone numbers, or addresses.</label>
<br>
<input type="text" size="100" maxlength="2000" name="description" id="description" value="@guest{{ old('description') }}@else{{ $description }}@endguest">

<br><br>
<label for="how_to_find_me">Give other users a hint how they will be able to find you at the event. Do not include real names, emails, phone numbers, or addresses.</label>
<br>
<input type="text" size="100" maxlength="200" name="how_to_find_me" id="how_to_find_me" value="@guest{{ old('how_to_find_me') }}@else{{ $how_to_find_me }}@endguest">

<br><br>
<button id="submit" type="submit" class="yesyes">
@guest
Sign up
@else
Submit changes
@endguest
</button>
</form>

@guest
@else
<form method="POST" action="" style="width:100%;text-align:right;">
{{ csrf_field() }}
<button type="submit" name="delete" class="no">
DELETE PROFILE
</button>
</form>
@endguest

@endsection
