@extends('layouts.app')
@section('content')
@if ($is_my_match)
	<h1 class="bright">{{ $auth_user->name }}, YOU ARE AWAITED by {{ $wasteland_name }}!</h1>
	@if ($count_with_same_name)
		@if ($count_with_same_name == 1)
			Another user also goes by the name {{ $wasteland_name }}. Be sure to find the right one.
		@else
			{{ $count_with_same_name }} other users go by the name {{ $wasteland_name }}. Be sure to find the right one.
		@endif
	@endif
	Your mission is to seek out {{ $wasteland_name }} at {{ $pretty_event_names[$event] }} {{ $year }}.
	{{ $wasteland_name }} will be looking for you, too.
	If you've found them and met them in person, during or after the event, let us know. This will mark your mission as complete.
	<br>
	<br>
	@include('rating_form', ['action' => '/profile/compatible?', 'user_id_to_rate' => $unchosen_user_id, 'current_choice' => $choice])
	<br>
@else
	@if (!$is_me && $unchosen_user_id != 1 )
		<h3>Would you enjoy meeting this user? @if ($count_left)({{$count_left}} profiles left to view) @endif</h3>
		@include('rating_form', ['action' => '/profile/compatible?', 'user_id_to_rate' => $unchosen_user_id, 'current_choice' => $choice])
	@endif
	@if ($missions_completed['points'])
		<h2>{{ $missions_completed['title'] }} <span class="bright">{{ $wasteland_name }}</span> &middot; Missions completed: {{ $missions_completed['points'] }}</h2>
	@else
		<h2 class="bright">{{ $wasteland_name }}</h2>
	@endif
@endif
@if ((($show_how_to_find_me || $share_info)) || $is_me)
	@if ($how_to_find_me)
		@if ($profile_id === 1)
		@else
			Do not post screenshots of this page. This information is confidential. How to find {{ $wasteland_name }}:
		@endif
		<h3 class="bright">&quot;{{ $how_to_find_me }}&quot;</h3>
	@endif
@endif
@if ($share_info)
	<h3><a href="mailto:{{ $share_info }}" class="bright">{{ $share_info }}</a></h3>
@endif
@if ($is_my_match)

@else
	@foreach ($events_to_show as $event)
		@if (isset($attending[$event]))
			@if ($attending[$event])
				<h3>Attending {{ $pretty_event_names[$event] }} {{ $year }}</h3>
			@endif
		@endif
	@endforeach
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
	<br>
	<br>
	{{ $description }}
@endif
<br>
<br>
@for ($i = 1; $i <= $number_photos; $i++)
	<a target="_blank" href="/uploads/image-{{ $profile_id }}-{{ $i }}.jpg{{ $image_query_string }}"><img src="/uploads/image-{{ $profile_id }}-{{ $i }}.jpg{{ $image_query_string }}" style="height:250px;"></a>
@endfor
@if ($auth_user && $auth_user->id === 1)
	<br><br>
	<form method="POST" style="width:100%;text-align:right;">
		{{ csrf_field() }}
		<button type="submit" name="reset_password" class="no">
			(ADMIN ONLY) Reset password
		</button>
		<input type="hidden" name="user_id_to_reset" value="{{ $profile_id }}">
	</form>
@endif
@endsection
