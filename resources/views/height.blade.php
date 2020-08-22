	@if ($height < 60)
		<span class="labelclass">Height:</span> Under 5 feet.
	@elseif ($height > 72)
		<span class="labelclass">Height:</span> Over 6 feet.
	@else
		<span class="labelclass">Height:</span> {{ floor($height / 12) }}&apos;{{ $height % 12 }}&quot;.
	@endif
