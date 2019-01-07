@extends('layouts.app')
@section('content')
@php $counter     = 0; @endphp
@php $unmatched   = 0; @endphp
@php $found_match = 0; @endphp
<h1>
	@if ($matches_complete)
		FINALIZED
	@endif
    @if ($event_attending_count == 0)
        AND EVENT ATTENDANCE PREFERENCES RESET
    @endif
    <br>
	{{ $pretty_event_names[$event] }} matches {{ $year }}
</h1>
<hr>
<table style="font-size:small;">
	<tr>
		<th>&nbsp;</th>
		<th><b>Name</b></th>
		<th><b>Id</b></th>
		<th><b>Rating<br>of match</b></th>
		<th><b>Match's<br>rating of</b></th>
		<th><b>Matched<br>to id</b></th>
		<th><b>Matched<br>to name</b></th>
		<th><b>Gender</b></th>
		<th><b>DGOM</b></th>
		<th><b>Match's<br>gender</b></th>
		<th><b>Popularity</b></th>
		<th><b>Match's<br>popularity</b></th>
		<th><b>Photos</b></th>
		@if ($matches_complete)
			<th><b>Mark found</b></th>
		@else
			<th><b>&nbsp;</b></th>
		@endif
	</tr>
@foreach ($users as $user)
	<tr
		@if ($id_to_cant_match_hash[$user->id])
			@php $unmatched++ @endphp
			style="background-color:#660000;"
		@elseif (isset($match_rating_hash[$user->id]) && isset($match_rating_hash[$matched_users_hash[$user->id]]) && $match_rating_hash[$user->id] === 3 && $match_rating_hash[$matched_users_hash[$user->id]] === 3)
			style="background-color:#003300;"
		@elseif ($user->greylist)
			style="background-color:#333333;"
		@elseif (!$user->number_photos)
			style="background-color:#666666;"
		@endif
	>
		<td>{{ ++$counter }}</td>
		<td>
			<a href="/profile/{{ $user->id }}/{{ preg_replace('/-/', ' ', $user->name) }}" target="_blank" style="
			@if ($user->random_ok)
				color:#00ff00;
			@else
				color:#ff0000;
			@endif
			">
				{{ $user->name }}
			</a>
			@if ($id_to_missions_completed_hash[$user->id]['points'])
				({{ $id_to_missions_completed_hash[$user->id]['points'] }})
			@endif
		</td>
		<td>{{ $user->id }}</td>
		<td>
			@if (isset($match_rating_hash[$user->id]) && (($match_rating_hash[$user->id] === 0) || ($match_rating_hash[$user->id] == -1))) <span class="bright"> @endif
			{{ isset($match_rating_hash[$user->id]) ? $match_rating_hash[$user->id] : '&nbsp;' }}
			@if (isset($match_rating_hash[$user->id]) && (($match_rating_hash[$user->id] === 0) || ($match_rating_hash[$user->id] == -1))) </span> @endif
		</td>
		<td>
			@if (isset($match_rating_hash[$matched_users_hash[$user->id]]) && (($match_rating_hash[$matched_users_hash[$user->id]] === 0) || ($match_rating_hash[$matched_users_hash[$user->id]] == -1))) <span class="bright"> @endif
			{{ isset($match_rating_hash[$matched_users_hash[$user->id]]) ? $match_rating_hash[$matched_users_hash[$user->id]] : '&nbsp;' }}
			@if (isset($match_rating_hash[$matched_users_hash[$user->id]]) && (($match_rating_hash[$matched_users_hash[$user->id]] === 0) || ($match_rating_hash[$matched_users_hash[$user->id]] == -1))) </span> @endif
		</td>
@php
	if ((isset($match_rating_hash[$user->id]) && $match_rating_hash[$user->id] !== 'NULL' && $match_rating_hash[$user->id] <= 0) || (isset($match_rating_hash[$matched_users_hash[$user->id]]) && $match_rating_hash[$matched_users_hash[$user->id]] !== 'NULL' && $match_rating_hash[$matched_users_hash[$user->id]] <= 0)) {
		$found_match++;
	} else {
	}
@endphp
		<td>{{ $matched_users_hash[$user->id] }}</td>
		<td>
		@if ($matched_users_hash[$user->id])
			@if (isset($id_to_name_hash[$matched_users_hash[$user->id]]))
				<a href="/profile/{{ $matched_users_hash[$user->id] }}/{{ preg_replace('/-/', ' ', $id_to_name_hash[$matched_users_hash[$user->id]]) }}" target="_blank">{{ $id_to_name_hash[$matched_users_hash[$user->id]] }}</a>
			@else
				<span class="bright">DELETED</span>
			@endif
		@else
		@endif
		</td>
		<td>{{ $user->gender }}</td>
		<td>{{ $user->gender_of_match }}</td>
		<td>
		@if (isset($id_to_name_hash[$matched_users_hash[$user->id]]))
			{{ $matched_users_hash[$user->id] ? $id_to_gender_hash[$matched_users_hash[$user->id]] : '' }}
		@else
			&nbsp;
		@endif
		</td>
		<td>{{ $user->popularity }}</td>
		<td>
		@if (isset($id_to_name_hash[$matched_users_hash[$user->id]]))
			{{ $matched_users_hash[$user->id] ? $id_to_popularity_hash[$matched_users_hash[$user->id]] : '' }}
		@endif
		</td>
		<td>{{ $user->number_photos }}</td>
		@if ($matches_complete)
			<td>
				@if (
					$matched_users_hash[$user->id]
					&&
					(
						(
							($match_rating_hash[$user->id] === 'NULL')
							||
							($match_rating_hash[$matched_users_hash[$user->id]] === 'NULL')
						)
						||
						(
							($match_rating_hash[$user->id] > 0)
							||
							($match_rating_hash[$matched_users_hash[$user->id]] > 0)
						)
					)
				)
					@if (isset($id_to_name_hash[$matched_users_hash[$user->id]]))
						<form method="POST">{{ csrf_field() }}<input type="hidden" name="user_1" value="{{ $user->id }}"><input type="hidden" name="user_2" value="{{ $matched_users_hash[$user->id] }}"><input type="submit" name="found" value="Mark {{ $user->name }}/{{ $id_to_name_hash[$matched_users_hash[$user->id]] }} found"></form>
					@endif
				@endif
			</td>

		@else
		@endif
	</tr>
@endforeach
</table>
@if ($matches_complete && $days_ago_matching < 14)
	<h4>{{ $found_match }}/{{ $counter }} ({{ floor($found_match / $counter * 100) }}%) found their match</h4>
    @if ($event_attending_count > 0)
        <form method="POST">{{ csrf_field() }}<input type="hidden" name="event" value="{{ $event }}"><input type="hidden" name="year" value="{{ $year }}"><input class="no" type="submit" name="mark_event_complete" value="Reset event attendance preferences"></form>
    @endif
@else
	<h4>{{ floor(($counter - $unmatched) / $counter * 100) }}% matched</h4>
	<form method="POST">
		{{ csrf_field() }}
		<input type="submit" value="Finalize matches" name="WRITE">
	</form>
@endif
@endsection
