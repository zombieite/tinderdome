@extends('layouts.app')
@section('content')
<h2>Coming soon: Choose potential matches here</h2>
<h3>List of current users:<h3>
<ul>
@foreach ($users as $user)
	<li>{{ $user->name }}</li>
@endforeach
<ul>
@endsection
