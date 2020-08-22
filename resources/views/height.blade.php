	@if ($height < 60)
		Height: Under 5 feet.
	@elseif ($height > 72)
		Height: Over 6 feet.
	@else
		Height: {{ floor($height / 12) }}&apos;{{ $height % 12 }}&quot;.
	@endif
