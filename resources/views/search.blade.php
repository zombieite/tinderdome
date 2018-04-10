@extends('layouts.app')
@section('content')
@foreach ($profiles as $profile)
	<p>
		{{ $profile['wasteland_name'] }}
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
			<a target="_blank" href="/uploads/image-{{ $profile['profile_id'] }}-{{ preg_replace('/\s/', '-', $profile['wasteland_name']) }}-{{ $i }}.jpg"><img src="/uploads/image-{{ $profile['profile_id'] }}-{{ preg_replace('/\s/', '-', $profile['wasteland_name']) }}-{{ $i }}.jpg" style="height:100px;"></a>
		@endfor
	</p>
	<hr>
@endforeach
@endsection
