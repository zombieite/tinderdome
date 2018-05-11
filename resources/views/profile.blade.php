@extends('layouts.app')
@section('content')
@if ($success_message)
	<h1><a href="/profile/me">Your profile</a> has been created. Now take a look at these other users.</h1>
@endif
@if (!$is_me && $unchosen_user_id != 1 )
	<h3>Would you enjoy meeting this user? @if ($count_left)({{$count_left}} profiles left to view) @endif</h3>
	@include('rating_form', ['action' => '/profile/compatible?', 'user_id_to_rate' => $unchosen_user_id, 'current_choice' => $choice])
@endif
@if ($is_my_match)
<h1 class="bright">{{ $auth_user->name }}, YOU ARE AWAITED by {{ $wasteland_name }}!</h1>
<h2 class="bright">Your mission is to seek them out and merge the backstories of your wasteland personas.</h2>
	@if ($how_to_find_me)
	<h3 class="bright">How to find {{ $wasteland_name }}:</h3>
	<h3 class="bright">&quot;{{ $how_to_find_me }}&quot;</h3>
	@endif
@else
	<h2>{{ $wasteland_name }}@if ($missions_completed['points']) &middot; Missions completed: {{ $missions_completed['points'] }} @endif</h2>
@endif
@foreach ($events_to_show as $event)
	@if (isset($attending[$event]))
		@if ($attending[$event])
			<h3 class="bright">Attending {{ $pretty_event_names[$event] }}</h3>
		@endif
	@endif
@endforeach
@if ($share_info)
	<h3><a href="mailto:{{ $share_info }}">{{ $share_info }}</a></h3>
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
