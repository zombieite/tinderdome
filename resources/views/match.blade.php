@extends('layouts.app')
@section('content')
@php $counter   = 0; @endphp
@php $unmatched = 0; @endphp
<h1>
	@if ($matches_complete)
		FINALIZED: 
	@endif
	{{ $event }} matches {{ $year }}
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
		@if ($matches_complete)

		@else
			<th><b>Mutual matches</b></th>
		@endif
	</tr>
@foreach ($users as $user)
	<tr style="
		@if ($id_to_cant_match_hash[$user->id])
			@php $unmatched++ @endphp
			background-color:red;
		@elseif ($user->gender === 'F' && $user->gender_of_match && $matched_users_hash[$user->id] && ($user->gender_of_match !== $id_to_gender_hash[$matched_users_hash[$user->id]]))
			background-color:orange;
		@endif
	">
		<td>{{ ++$counter }}</td>
		<td>
			<a href="/profile/{{ $user->id }}/{{ preg_replace('/-/', ' ', $user->name) }}" target="_blank" style="
			@if ($user->random_ok)
				color:#00ff00;
			@else
				color:#ff00ff;
			@endif
			">
				{{ $user->name }}
			</a>
			@if ($id_to_missions_completed_hash[$user->id]['points'])
				({{ $id_to_missions_completed_hash[$user->id]['points'] }})
			@endif
		</td>
		<td>{{ $user->id }}</td>
		<td>{{ isset($match_rating_hash[$user->id]) ? $match_rating_hash[$user->id] : '&nbsp;' }}</td>
		<td>{{ isset($match_rating_hash[$matched_users_hash[$user->id]]) ? $match_rating_hash[$matched_users_hash[$user->id]] : '&nbsp;' }}</td>
		<td>{{ $matched_users_hash[$user->id] }}</td>
		<td>
			@if ($matched_users_hash[$user->id]) 
				<a href="/profile/{{ $matched_users_hash[$user->id] }}/{{ preg_replace('/-/', ' ', $id_to_name_hash[$matched_users_hash[$user->id]]) }}" target="_blank">{{ $id_to_name_hash[$matched_users_hash[$user->id]] }}</a>
			@else
				&nbsp;
			@endif
		</td>
		<td>{{ $user->gender }}</td>
		<td>{{ $user->gender_of_match }}</td>
		<td>{{ $matched_users_hash[$user->id] ? $id_to_gender_hash[$matched_users_hash[$user->id]] : '' }}</td>
		<td>{{ $user->popularity }}</td>
		<td>{{ $matched_users_hash[$user->id] ? $id_to_popularity_hash[$matched_users_hash[$user->id]] : '' }}</td>
		@if ($matches_complete)

		@else
			<td>
				@foreach (array_keys($user->mutual_unmet_match_names) as $mutual_match_name)
					{{ $mutual_match_name }},
				@endforeach
			</td>
		@endif
	</tr>
@endforeach
</table>

<h4>{{ floor(($counter - $unmatched) / $counter * 100) }}% matched</h4>

@if ($matches_complete)
	
@else
	<form method="POST">
		{{ csrf_field() }}
		<input type="submit" value="Finalize matches" name="WRITE">
	</form>
@endif

@php //phpinfo() @endphp

@endsection
