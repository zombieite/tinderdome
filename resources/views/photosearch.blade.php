@extends('layouts.app')
@section('content')
@foreach ($photos as $photo)
	<a href="/profile/{{ $photo['profile_id'] }}/{{ $photo['wasteland_name_hyphenated'] }}"><img src="/uploads/image-{{ $photo['profile_id'] }}-{{ $photo['number'] }}.jpg" style="height:150px;"></a>
@endforeach
@endsection
