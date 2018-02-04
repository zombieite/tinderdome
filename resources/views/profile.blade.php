@extends('layouts.app')
@section('content')
<h2>Wasteland name: {{ $wasteland_name }}</h2>
<p>
@if ($gender)
	Gender: {{ $gender }}
@endif
<br>
@if ($birth_year)
	@if ($birth_year === 1959)
		Born before 1960
	@else
		Born in the {{ $birth_year }}s
	@endif
@endif
<br>
@if ($height)
	@if ($height < 60)
		Height: Under 5 feet
	@elseif ($height > 72)
		Height: Over 6 feet
	@else
		Height: {{ floor($height / 12) }}&apos;{{ $height % 12 }}&quot;
	@endif
@endif
</p>
<p>
@if ($hoping_to_find_love or $hoping_to_find_friend or $hoping_to_find_acquaintance or $hoping_to_find_enemy)
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
@endif
</p>
<p>
	{{ $description }}
</p>
@if ($wasteland_name === 'Firebird')
<p>
	How to find me: {{ $how_to_find_me }}
</p>
@endif
@for ($i = 1; $i <= $number_photos; $i++)
<img src="/uploads/image-{{ $profile_id }}-{{ $i }}.jpg">
@endfor
@endsection
