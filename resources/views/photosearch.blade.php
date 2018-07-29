@extends('layouts.app')
@section('content')
@if ($users_who_must_be_rated)
	<a href="/profile/compatible?" class="bright">You must rate all users before you can view all users</a>.<br><br>
@else
	@foreach ($profiles as $profile)
		@if ($profile['number_photos'])
			@for ($i = 1; $i <= $profile['number_photos']; $i++)
				<a href="/profile/{{ $profile['profile_id'] }}/{{ $profile['wasteland_name_hyphenated'] }}">
					<img src="/uploads/image-{{ $profile['profile_id'] }}-{{ $i }}.jpg" style="height:150px;">
				</a>
			@endfor
		@endif
	@endforeach
@endif
@endsection
