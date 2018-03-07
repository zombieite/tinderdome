@extends('layouts.app')
@section('content')

<h2>Matches</h2>
<table>
	<tr>
		<th><b>Name</b></th>
		<th><b>Id</b></th>
		<th><b>Matched to id</b></th>
		<th><b>Matched to name</b></th>
		<th><b>Random match</b></th>
		<th><b>Random ok</b></th>
		<th><b>Gender</b></th>
		<th><b>DGOM</b></th>
		<th><b>Match's gender</b></th>
		<th><b>Popularity</b></th>
		<th><b>Mutual matches</b></th>
	</tr>
@foreach ($users as $user)
	<tr @if ($user->cant_match) style="background-color:red;" @endif>
		<td>{{ $user->name }}</td>
		<td>{{ $user->id }}</td>
		<td>{{ $matched_users_hash[$user->id] }}</td>
		<td>{{ $matched_users_hash[$user->id] ? $id_to_name_hash[$matched_users_hash[$user->id]] : '' }}</td>
		<td>{{ $user->random_match ? 'Random' : '' }}</td>
		<td>{{ $user->random_ok }}</td>
		<td>{{ $user->gender }}</td>
		<td>{{ $user->gender_of_match }}</td>
		<td>{{ $matched_users_hash[$user->id] ? $id_to_gender_hash[$matched_users_hash[$user->id]] : '' }}</td>
		<td>{{ $user->popularity }}</td>
		<td>
			@foreach ($user->mutual_unmet_matches as $mutual_match)
				{{ $mutual_match->name }},
			@endforeach
		</td>
	</tr>
@endforeach
</table>

@endsection
