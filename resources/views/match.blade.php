@extends('layouts.app')
@section('content')

<h2>Matches</h2>
<ul>
@foreach ($users as $user)
	<li>{{ $user->name }} {{ $user->popularity }} </li>
@endforeach
</ul>

@endsection
