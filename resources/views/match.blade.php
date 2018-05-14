@extends('layouts.app')
@section('content')

<table style="font-size:small;">
	<tr>
		<th style="width:10%;"><b>Name</b></th>
		<th style="width:4%;"><b>Id</b></th>
		<th style="width:4%;"><b>Matched to id</b></th>
		<th style="width:10%;"><b>Matched to name</b></th>
		<th style="width:4%;"><b>Gender</b></th>
		<th style="width:4%;"><b>DGOM</b></th>
		<th style="width:4%;"><b>Match's gender</b></th>
		<th style="width:4%;"><b>Popularity</b></th>
		<th style="width:4%;"><b>Match's popularity</b></th>
		<th style="width:44%;"><b>Mutual matches</b></th>
	</tr>
@foreach ($users as $user)
	<tr
		@if ($user->cant_match)
			style="background-color:red;"
		@elseif ($user->gender === 'F' && $user->gender_of_match && $matched_users_hash[$user->id] && ($user->gender_of_match !== $id_to_gender_hash[$matched_users_hash[$user->id]]))
			style="background-color:orange;"
		@endif
	>
		<td>{{ $user->name }}</td>
		<td>{{ $user->id }}</td>
		<td>{{ $matched_users_hash[$user->id] }}</td>
		<td>{{ $matched_users_hash[$user->id] ? $id_to_name_hash[$matched_users_hash[$user->id]] : '' }}</td>
		<td>{{ $user->gender }}</td>
		<td>{{ $user->gender_of_match }}</td>
		<td>{{ $matched_users_hash[$user->id] ? $id_to_gender_hash[$matched_users_hash[$user->id]] : '' }}</td>
		<td>{{ $user->popularity }}</td>
		<td>{{ $matched_users_hash[$user->id] ? $id_to_popularity_hash[$matched_users_hash[$user->id]] : '' }}</td>
		<td>
			@foreach ($user->mutual_unmet_matches as $mutual_match)
				{{ $mutual_match->name }},
			@endforeach
		</td>
	</tr>
@endforeach
</table>

@endsection
