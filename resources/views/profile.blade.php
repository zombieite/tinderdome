@extends('layouts.app')
@section('content')
@if ($save_message)
<h1 style="color:red;">Profile created.</h1>
@endif
<h2>Profile for {{ $wasteland_name }}</h2>
@if ($gender)
	<p>
	Gender: {{ $gender }}
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
@if ($hoping_to_find_love or $hoping_to_find_friend or $hoping_to_find_acquaintance or $hoping_to_find_enemy)
	<p>
	@if ($number_people === 1)
		I&apos;m
	@elseif ($number_people > 1)
		We&apos;re a group of {{ $number_people }} people, here because we are
	@endif
	open to finding
	@if ($hoping_to_find_love)
		a new acquaintance, friend, or romantic partner.
	@elseif ($hoping_to_find_friend)
		a new acquaintance or a friend.
	@elseif ($hoping_to_find_acquaintance)
		a new acquaintance.
	@endif
	@if ($hoping_to_find_enemy)
		@if ($hoping_to_find_love or $hoping_to_find_friend or $hoping_to_find_acquaintance)
			Or
		@endif
		an adversary to battle in the Thunderdome.
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
@endsection
