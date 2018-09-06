@extends('layouts.app')
@section('content')
@if ($users_who_must_be_rated)
	You must <a href="/profile/compatible?">rate all users</a> before you can view all users.<br><br>
@else
	@foreach ($photos as $photo)
		<a href="/profile/{{ $photo['profile_id'] }}/{{ $photo['wasteland_name_hyphenated'] }}"><img src="/uploads/image-{{ $photo['profile_id'] }}-{{ $photo['number'] }}.jpg" style="height:150px;"></a>
	@endforeach
@endif
@endsection
