@extends('layouts.app')
@section('content')
@php ($last_profile = 0)
@if ($show_mutuals)
	@if ($profiles_found_count)
		{{ $profiles_found_count }} mutuals have shared their contact info with you<br><br>
	@else
		No mutuals have shared their contact info with you yet<br><br>
	@endif
@else
	@if ($logged_in_user_hoping_to_find_love && $logged_in_user_share_info_with_favorites)
		<a href="/search?show_mutuals=1">See mutuals who have shared their contact info with you</a><br><br>
	@endif
@endif
@if ($show_nos)
	@if ($profiles_found_count)
		{{ $profiles_found_count }} users marked as No<br><br>
	@else
		No users marked as No<br><br>
	@endif
@else
	<a href="/search?show_nos=1">See users I've marked as No</a><br><br>
@endif
@foreach ($profiles as $profile)
	@if ($profile['mutual_favorite'] || !$show_mutuals)
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
	@endif
@endforeach
@endsection
