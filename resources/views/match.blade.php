@extends('layouts.app')
@section('content')
@php $counter   = 0; @endphp
@php $unmatched = 0; @endphp
<table style="font-size:small;">
	<tr>
		<th style="width:2%;">&nbsp;</th>
		<th style="width:10%;"><b>Name</b></th>
		<th style="width:4%;"><b>Id</b></th>
		<th style="width:4%;"><b>Matched to id</b></th>
		<th style="width:10%;"><b>Matched to name</b></th>
		<th style="width:4%;"><b>Gender</b></th>
		<th style="width:4%;"><b>DGOM</b></th>
		<th style="width:4%;"><b>Match's gender</b></th>
		<th style="width:4%;"><b>Popularity</b></th>
		<th style="width:4%;"><b>Match's popularity</b></th>
		<th><b>Mutual matches</b></th>
	</tr>
@foreach ($users as $user)
	<tr style="
		@if ($user->cant_match)
			@php $unmatched++ @endphp
			background-color:red;
		@elseif ($user->gender === 'F' && $user->gender_of_match && $matched_users_hash[$user->id] && ($user->gender_of_match !== $id_to_gender_hash[$matched_users_hash[$user->id]]))
			background-color:orange;
		@endif
	">
		<td>{{ ++$counter }}</td>
		<td style="
			@if ($user->random_ok)
				color:#00ff00;
			@else
				color:#ff00ff;
			@endif
		">{{ $user->name }}</td>
		<td>{{ $user->id }}</td>
		<td>{{ $matched_users_hash[$user->id] }}</td>
		<td>{{ $matched_users_hash[$user->id] ? $id_to_name_hash[$matched_users_hash[$user->id]] : '' }}</td>
		<td>{{ $user->gender }}</td>
		<td>{{ $user->gender_of_match }}</td>
		<td>{{ $matched_users_hash[$user->id] ? $id_to_gender_hash[$matched_users_hash[$user->id]] : '' }}</td>
		<td>{{ $user->popularity }}</td>
		<td>{{ $matched_users_hash[$user->id] ? $id_to_popularity_hash[$matched_users_hash[$user->id]] : '' }}</td>
		<td>
			@foreach (array_keys($user->mutual_unmet_match_names) as $mutual_match_name)
				{{ $mutual_match_name }},
			@endforeach
		</td>
	</tr>
@endforeach
</table>

<h4>{{ floor(($counter - $unmatched) / $counter * 100) }}% matched</h4>

@endsection
