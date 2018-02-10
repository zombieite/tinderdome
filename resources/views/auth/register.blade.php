@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
{{ csrf_field() }}

<label for="name">Name</label>

<input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>

@if ($errors->has('name'))
<strong>{{ $errors->first('name') }}</strong>
@endif

<label for="email">E-Mail Address</label>

<input id="email" type="email" name="email" value="{{ old('email') }}" required>

@if ($errors->has('email'))
<strong>{{ $errors->first('email') }}</strong>
@endif

<label for="password">Password</label>

<input id="password" type="password" name="password" required>

@if ($errors->has('password'))
<strong>{{ $errors->first('password') }}</strong>
@endif

<label for="password-confirm">Confirm Password</label>

<input id="password-confirm" type="password" name="password_confirmation" required>

<label for="number_people">Number of people in this profile</label>
<select name="number_people" id="number_people">
    <option value="1" selected>1</option>
    <option value="2">2 people</option>
    <option value="3">A group of 3 or more</option>
</select>

@if ($errors->has('number_people'))
<strong>{{ $errors->first('number_people') }}</strong>
@endif

	<br>
	Upload images
	<br>
	<input type="file" name="image1" value="image">
	<br>
	<input type="file" name="image2" value="image">
	<br>
	<input type="file" name="image3" value="image">
	<br>
	<input type="file" name="image4" value="image">
	<br>
	<input type="file" name="image5" value="image">

<button type="submit">
Register
</button>
















@endsection
