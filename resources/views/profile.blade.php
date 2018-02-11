@extends('layouts.app')
@section('content')
@if ($save_message)
<h1 style="color:red;">Profile created.</h1>
@endif
<h2>Profile for {{ $wasteland_name }}</h2>
@if ($gender or $gender_of_match)
	<p>
	@if ($gender)
		{{ $gender === 'M' ? 'Male' : 'Female' }}
		@if ($gender_of_match)
			looking
		@endif
	@else
		Looking
	@endif
	@if ($gender_of_match)
		to be matched with a {{ $gender_of_match === 'M' ? 'male' : 'female' }}
	@endif
	</p>
@endif
@if ($birth_year)
	<p>
	@if ($birth_year === 1959)
		Born before 1960
	@else
		Born in the {{ intval($birth_year / 10) * 10 }}s
	@endif
	</p>
@endif
@if ($height)
	<p>
	@if ($height < 60)
		Height: Under 5 feet
	@elseif ($height > 72)
		Height: Over 6 feet
	@else
		Height: {{ floor($height / 12) }}&apos;{{ $height % 12 }}&quot;
	@endif
	</p>
@endif
@if ($hoping_to_find_love or $hoping_to_find_friend or $hoping_to_find_enemy)
	<p>
	Open to
	@if ($hoping_to_find_love)
		finding a new friend or romantic partner
	@elseif ($hoping_to_find_friend)
		making a new friend
	@endif
	@if ($hoping_to_find_enemy)
		@if ($hoping_to_find_love or $hoping_to_find_friend)
			Or
		@endif
		making an enemy
	@endif
	</p>
@endif
@if ($description)
<p>
	{{ $description }}
</p>
@endif
@if ($wasteland_name === 'Firebird')
<p>
	How to find me: {{ $how_to_find_me }}
</p>
@endif
@for ($i = 1; $i <= $number_photos; $i++)
<a target="_blank" href="/uploads/image-{{ $profile_id }}-{{ preg_replace('/\s/', '-', $wasteland_name) }}-{{ $i }}.jpg"><img src="/uploads/image-{{ $profile_id }}-{{ preg_replace('/\s/', '-', $wasteland_name) }}-{{ $i }}.jpg" style="height:250px;"></a>
@endfor
@if ($its_me)
	<h2>To edit your profile contact <a href="mailto:wastelandfirebird@gmail.com">wastelandfirebird@gmail.com</a></h2>
@endif
@endsection
