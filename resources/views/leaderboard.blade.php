@foreach ($leaderboard as $leader)
	<div class="centered_block">
		@if ($leader['number_photos'])
			<a target="_blank" href="/uploads/image-{{ $leader['profile_id'] }}-1.jpg"><img src="/uploads/image-{{ $leader['profile_id'] }}-1.jpg" style="height:100px;"></a>
		@endif
		<br>
		@if ($leader['missions_completed'] > 0)
			{{ $titles[$leader['title_index']] }}
		@endif
		{{ $leader['wasteland_name'] }} &middot; {{ $leader['missions_completed'] }}
	</div>
@endforeach
