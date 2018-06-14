@extends('layouts.app')
@section('content')
@php ($last_profile = 0)
@foreach ($profiles as $profile)
	<div class="@if ($profile['mutual_favorite']) profile_search_block_mutual @else profile_search_block @endif">
		<a name="profile{{ $profile['profile_id'] }}"></a>
		<a href="/profile/{{ $profile['profile_id'] }}/{{ $profile['wasteland_name_hyphenated'] }}">{{ $profile['wasteland_name'] }}</a>
		@if ($profile['gender'])
			&middot; {{ $profile['gender'] === 'M' ? 'Male' : ($profile['gender'] === 'F' ? 'Female' : 'Other') }}
		@endif
		@if ($profile['birth_year'])
			&middot;
			@if ($profile['birth_year'] === 1959)
				Born before 1960
			@else
				Born in the {{ intval($profile['birth_year'] / 10) * 10 }}s
			@endif
		@endif
		@if ($profile['height'])
			&middot;
			@if ($profile['height'] < 60)
				Under 5 feet
			@elseif ($profile['height'] > 72)
				Over 6 feet
			@else
				{{ floor($profile['height'] / 12) }}&apos;{{ $profile['height'] % 12 }}&quot;
			@endif
		@endif
		@if ($profile['missions_completed']['points'])
			&middot;
			<span>Missions completed: {{ $profile['missions_completed']['points'] }}</span>
		@endif
		<br>
		<br>
		@for ($i = 1; $i <= $profile['number_photos']; $i++)
			<a target="_blank" href="/uploads/image-{{ $profile['profile_id'] }}-{{ $i }}.jpg"><img src="/uploads/image-{{ $profile['profile_id'] }}-{{ $i }}.jpg" style="height:100px;"></a>
		@endfor
		<br>
		<br>
		@if ($logged_in_user_id == $profile['profile_id'])
			(You)
		@else
			@include('rating_form', ['action' => "#profile$last_profile", 'user_id_to_rate' => $profile['profile_id'], 'current_choice' => $profile['choice']])
		@endif
		@php ($last_profile = $profile['profile_id'])
	</div>
@endforeach
@endsection
