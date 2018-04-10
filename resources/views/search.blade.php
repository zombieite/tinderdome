@extends('layouts.app')
@section('content')
@php ($last_profile = 0)
@foreach ($profiles as $profile)
	<p>
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
	</p>
	<p>
		@for ($i = 1; $i <= $profile['number_photos']; $i++)
			<a target="_blank" href="/uploads/image-{{ $profile['profile_id'] }}-{{ preg_replace('/\s/', '-', $profile['wasteland_name']) }}-{{ $i }}.jpg"><img src="/uploads/image-{{ $profile['profile_id'] }}-{{ preg_replace('/\s/', '-', $profile['wasteland_name']) }}-{{ $i }}.jpg" style="height:100px;" alt="{{ $profile['description'] }}"></a>
		@endfor
	</p>
	<form action="#profile{{ $last_profile }}" method="POST">
		{{ csrf_field() }}
		<input type="hidden" name="chosen" value="{{ $profile['profile_id'] }}">
		<input type="submit" name="YesYesYes" value="YES YES YES"@if ($profile['choice'] == 3) class="yes"@endif>
		<input type="submit" name="YesYes" value="Yes YES"@if ($profile['choice'] == 2) class="yes"@endif>
		<input type="submit" name="Yes" value="Yes"@if ($profile['choice'] == 1) class="yes"@endif>
		<input type="submit" name="No" value="No"@if ($profile['choice'] == 0) class="no"@endif>
	</form>
	@php ($last_profile = $profile['profile_id'])
	<hr>
@endforeach
@endsection
