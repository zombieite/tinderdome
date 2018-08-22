@extends('layouts.app')
@section('content')
@if ($show_all)
	@if ($users_who_must_be_rated)
		<a href="/profile/compatible?" class="bright">You must rate all users before you can view all users</a>.<br><br>
	@else
		All users<br><br>
	@endif
@else
	<a href="/search?show_all=1">Show all users</a><br><br>
@endif
@if ($show_mutuals)
	@if ($users_who_must_be_rated)
		<a href="/profile/compatible?" class="bright">You must rate all users before you can see your mutuals</a>.<br><br>
	@else
		@if ($profiles_found_count)
			@if ($profiles_found_count === 1)
				You have a mutual favorite who has shared their contact info with you!<br><br>
			@else
				{{ $profiles_found_count }} mutuals have shared their contact info with you<br><br>
			@endif
		@else
			No mutuals have shared their contact info with you yet<br><br>
		@endif
	@endif
@else
	@if ($logged_in_user_hoping_to_find_love && $logged_in_user_share_info_with_favorites)
		<a href="/search?show_mutuals=1">See mutual favorites who have shared their contact info with you</a><br><br>
	@endif
@endif
@if ($show_preferred_gender)
	@if ($users_who_must_be_rated)
		<a href="/profile/compatible?" class="bright">You must rate all users before you can view users of your preferred gender</a>.<br><br>
	@else
		All users of your preferred gender, gender Other, and gender unspecified<br><br>
	@endif
@else
	@if ($logged_in_user_preferred_gender_of_match)
		<a href="/search?show_preferred_gender=1">Show users of your preferred gender to meet, gender Other, and gender unspecified</a><br><br>
	@endif
@endif
@if ($show_nos)
	@if ($profiles_found_count)
		@if ($profiles_found_count === 1)
			One user marked as No<br><br>
		@else
			{{ $profiles_found_count }} users marked as No<br><br>
		@endif
	@else
		No users marked as No<br><br>
	@endif
@else
	<a href="/search?show_nos=1">See users you've marked as No</a><br><br>
@endif
<a href="/photosearch">Show all photos</a><br><br>
<a href="/photosearch?gender=o">Show all photos of users of gender other and unspecified</a><br><br>
<a href="/photosearch?gender=f">Show all photos of women</a><br><br>
<a href="/photosearch?gender=m">Show all photos of men</a><br><br>
@foreach ($profiles as $profile)
	@if ($profile['mutual_favorite'] || !$show_mutuals)
		<div class="@if ($profile['mutual_favorite']) profile_search_block_bright @else profile_search_block @endif">
			<div style="display:inline-block;">
				@if ($profile['number_photos'])
					<a href="/profile/{{ $profile['profile_id'] }}/{{ $profile['wasteland_name_hyphenated'] }}">
						<img src="/uploads/image-{{ $profile['profile_id'] }}-1.jpg" style="height:100px;">
					</a>
				@endif
			</div>
			<div style="display:inline-block;">
				<a name="profile{{ $profile['profile_id'] }}"></a>
				@if ($profile['missions_completed']['points'])
					{{ $profile['missions_completed']['title'] }}
				@endif
				<a href="/profile/{{ $profile['profile_id'] }}/{{ $profile['wasteland_name_hyphenated'] }}">{{ $profile['wasteland_name'] }}</a>
				@if ($profile['birth_year'])
					<br>
					@if ($profile['birth_year'] === 1959)
						Born before 1960
					@else
						Born in the {{ intval($profile['birth_year'] / 10) * 10 }}s
					@endif
				@endif
				@if ($profile['height'])
					@if ($profile['birth_year'])
						&middot;
					@else
						<br>
					@endif
					@if ($profile['height'] < 60)
						Under 5 feet
					@elseif ($profile['height'] > 72)
						Over 6 feet
					@else
						{{ floor($profile['height'] / 12) }}&apos;{{ $profile['height'] % 12 }}&quot;
					@endif
				@endif
				<br>
				@if ($profile['missions_completed']['points'])
					<span>Missions completed: {{ $profile['missions_completed']['points'] }}</span>
				@endif
			</div>
			<br>
			<br>
			@if ($logged_in_user_id == $profile['profile_id'])
				(You)
			@else
				@include('rating_form', ['action' => "#profile".$profile['profile_id'], 'user_id_to_rate' => $profile['profile_id'], 'current_choice' => $profile['choice'], 'number_photos' => $profile['number_photos']])
			@endif
		</div>
	@endif
@endforeach
@endsection
