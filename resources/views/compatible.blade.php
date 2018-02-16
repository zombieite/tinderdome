@extends('layouts.app')
@section('content')
<h2>You have rated all current users. The next step is to check back here on the day before the event to find out if we were able to match you with another user.</h2>

{{--
<h3>List of current users:</h3>
@if ($has_photos)
@else
	These are other users without photos. To see profiles with photos, you must <a href="/profile/edit">add at least one photo</a>.
@endif
@if ($has_description)
@else
	These are other users with brief descriptions. To see profiles with longer descriptions, you must <a href="/profile/edit">add a longer description</a>.
@endif
<ul>
@foreach ($users as $user)
	<li>
		{{ $user->name }}
		@if ($user->number_photos > 1)
			({{ $user->number_photos }} photos)
		@elseif ($user->number_photos === 1)
			(1 photo)
		@endif
	</li>
@endforeach
</ul>
--}}

@endsection
