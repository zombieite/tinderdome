@extends('layouts.app')
@section('content')
@if ($success_message)
	<h1><a href="/profile/me">Your profile</a> has been created. Now take a look at these other users.</h1>
@endif
@if ($unchosen_user_id)
	<h3>Would you enjoy meeting this user at the next event?</h3>
	@if ($count_left)<h4>{{$count_left}} profiles left to view</h4>@endif
	<form action="?" method="POST">
		{{ csrf_field() }}
		<input type="hidden" name="chosen" value="{{ $unchosen_user_id }}">
		<input type="submit" name="YesYesYes" value="Yes Yes Yes" class="yes">
		<input type="submit" name="YesYes" value="Yes Yes" class="yes">
		<input type="submit" name="Yes" value="Yes" class="yes">
		<input type="submit" name="Met" value="I've already met this person" class="met">
		@if ($nos_left > 0)
			<input type="submit" name="No" value="No ({{ $nos_left }} left)" class="no">
		@else
			<br><br><span class="no">To mark more users as no, you must <a href="/search">change some of your previous ratings to yes</a>.</span>
		@endif
	</form>
@endif
@if ($is_my_match)
<h1>{{ $auth_user->name }}, YOU ARE AWAITED by {{ $wasteland_name }}!</h1>
<h2>Your mission is to seek them out and merge the backstories of your wasteland personas.</h2>
	@if ($how_to_find_me)
	<h3>How to find {{ $wasteland_name }}:</h3>
	<h3>&quot;{{ $how_to_find_me }}&quot;</h3>
	@endif
@else
<h2>Profile
	for {{ $wasteland_name }}
</h2>
@endif
@if ($gender or $gender_of_match)
	<p>
	@if ($gender)
		Gender: {{ $gender === 'M' ? 'Male' : ($gender === 'F' ? 'Female' : 'Other') }}
		<br>
		@if ($gender_of_match)
			Preferring
		@endif
	@else
		Preferring
	@endif
	@if ($gender_of_match)
		to be matched with a person of gender: {{ $gender_of_match === 'M' ? 'Male' : ($gender_of_match === 'F' ? 'Female' : 'Other') }}
	@endif
	</p>
@endif
@if ($birth_year)
	<p>
	@if ($birth_year === 1959)
		Born before 1960
	@else
		Born in the {{ intval($birth_year / 10) * 10 }}s
	@endif
	</p>
@endif
@if ($height)
	<p>
	@if ($height < 60)
		Height: Under 5 feet
	@elseif ($height > 72)
		Height: Over 6 feet
	@else
		Height: {{ floor($height / 12) }}&apos;{{ $height % 12 }}&quot;
	@endif
	</p>
@endif
@if ($hoping_to_find_love or $hoping_to_find_friend or $hoping_to_find_enemy)
	<p>
	Open to
	@if ($hoping_to_find_love)
		finding a new friend or romantic partner
	@elseif ($hoping_to_find_friend)
		making a new friend
	@endif
	@if ($hoping_to_find_enemy)
		@if ($hoping_to_find_love or $hoping_to_find_friend)
			or
		@endif
		making an enemy
	@endif
	</p>
@endif
@if ($description)
<p>
	{{ $description }}
</p>
@endif
@if ($wasteland_name === 'Firebird')
<p>
	How to find me: {{ $how_to_find_me }}
</p>
@endif
@for ($i = 1; $i <= $number_photos; $i++)
<a target="_blank" href="/uploads/image-{{ $profile_id }}-{{ preg_replace('/\s/', '-', $wasteland_name) }}-{{ $i }}.jpg"><img src="/uploads/image-{{ $profile_id }}-{{ preg_replace('/\s/', '-', $wasteland_name) }}-{{ $i }}.jpg" style="height:250px;"></a>
@endfor
@endsection
