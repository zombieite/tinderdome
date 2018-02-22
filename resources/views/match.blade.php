@extends('layouts.app')
@section('content')

<h2>Matches</h2>
<table>
	<tr>
		<th><b>Id</b></th>
		<th><b>Name</b></th>
		<th><b>Gender</b></th>
		<th><b>DGOM</b></th>
		<th><b>Popularity</b></th>
		<th><b>Random ok</b></th>
		<th><b>Mutual matches</b></th>
	</tr>
@foreach ($users as $user)
	<tr>
		<td>{{ $user->id }}</td>
		<td>{{ $user->name }}</td>
		<td>{{ $user->gender }} </td>
		<td>{{ $user->gender_of_match }} </td>
		<td>{{ $user->popularity }}</td>
		<td>{{ $user->random_ok}} </td>
		<td>
			@foreach ($user->mutual_matches as $mutual_match)
				{{ $mutual_match->name }},
			@endforeach
		</td>
	</tr>
@endforeach
</table>

@endsection
