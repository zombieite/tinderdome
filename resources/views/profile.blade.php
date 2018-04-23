@extends('layouts.app')
@section('content')
@if ($success_message)
	<h1><a href="/profile/me">Your profile</a> has been created. Now take a look at these other users.</h1>
@endif
@if (!$is_me && $unchosen_user_id != 1 )
	<h3>Would you enjoy meeting this user at the next event? @if ($count_left)({{$count_left}} profiles left to view) @endif</h3>
	<form action="/profile/compatible?" method="POST">
		{{ csrf_field() }}
		<input type="hidden" name="chosen" value="{{ $unchosen_user_id }}">
		<input type="submit" name="YesYesYes" value="Yes Yes Yes"@if (($choice == 3) || !isset($choice)) class="yes"@endif>
		<input type="submit" name="YesYes" value="Yes Yes"@if (($choice == 2) || !isset($choice)) class="yes"@endif>
		<input type="submit" name="Yes" value="Yes"@if (($choice == 1) || !isset($choice)) class="yes"@endif>
		<input type="submit" name="Met" value="I have met them"@if (($choice == -1) || !isset($choice)) class="met"@endif>
		@if ($nos_left > 0)
			<input type="submit" name="No" value="No ({{ $nos_left }} left)"@if (($choice == 0) || !isset($choice)) class="no"@endif>
		@else
			<br><br><span class="no">To mark more users as no, you must <a href="/search">change {{ -$nos_left+1 }} of your previous no ratings to yes</a>.</span>
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
	<h2>{{ $wasteland_name }}@if ($missions_completed['points']) &middot; <span class="score">Missions completed: {{ $missions_completed['points'] }}</span> @endif</h2>
@endif
@if ($gender)
	Gender: {{ $gender === 'M' ? 'Male' : ($gender === 'F' ? 'Female' : 'Other') }}.
@endif
@if ($birth_year)
	@if ($birth_year === 1959)
		Born before 1960.
	@else
		Born in the {{ intval($birth_year / 10) * 10 }}s.
	@endif
@endif
@if ($height)
	@if ($height < 60)
		Height: Under 5 feet.
	@elseif ($height > 72)
		Height: Over 6 feet.
	@else
		Height: {{ floor($height / 12) }}&apos;{{ $height % 12 }}&quot;.
	@endif
@endif
@if ($hoping_to_find_love or $hoping_to_find_friend or $hoping_to_find_enemy)
	Open to
	@if ($hoping_to_find_love)
		finding a new friend or romantic partner.
	@elseif ($hoping_to_find_friend)
		making a new friend.
	@endif
	@if ($hoping_to_find_enemy)
		@if ($hoping_to_find_love or $hoping_to_find_friend)
			Or
		@endif
		making an enemy.
	@endif
@endif
@if ($gender_of_match)
	Prefers to meet gender: {{ $gender_of_match === 'M' ? 'Male' : ($gender_of_match === 'F' ? 'Female' : 'Other') }}.
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
<br>
@for ($i = 1; $i <= $number_photos; $i++)
<a target="_blank" href="/uploads/image-{{ $profile_id }}-{{ preg_replace('/\s/', '-', $wasteland_name) }}-{{ $i }}.jpg"><img src="/uploads/image-{{ $profile_id }}-{{ preg_replace('/\s/', '-', $wasteland_name) }}-{{ $i }}.jpg" style="height:250px;"></a>
@endfor
@endsection
