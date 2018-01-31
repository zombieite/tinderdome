@extends('layouts.app')
@section('content')
<h2>{{ $wasteland_name }}</h2>
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
@if ($number_people === 1)
	I&apos;m here because I&apos;m
@elseif ($number_people > 1)
	We&apos;re a group of {{ $number_people }} here because we are
@endif
@if ($hoping_to_find_acquaintance)
	open to making new acquaintances.
@endif
@if ($hoping_to_find_friend)
	open to making new friends.
@endif
@if ($hoping_to_find_love)
	open to making new friends or possibly building new romantic relationships.
@endif
@if ($hoping_to_find_lost)
	looking for someone specific.
@endif
@if ($hoping_to_find_enemy)
	looking for adversaries to battle in the Thunderdome.
@endif
</p>
<p>
	{{ $description }}
</p>
<p>
	How to find me: {{ $how_to_find_me }}
</p>
@endsection
