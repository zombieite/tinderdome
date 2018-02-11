<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>You Are Awaited</title>
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
<h1><a href="{{ url('/') }}">You Are Awaited</a></h1>

@guest
	<a href="{{ route('register') }}">Create a profile</a> &middot; <a href="{{ route('login') }}">Log in</a>
@else
	You are logged in as  <a href="/profile/{{ Auth::user()->id}}/{{ Auth::user()->name }}">{{ Auth::user()->name }}</a>
	<form action="{{ route('logout') }}" method="POST">
	{{ csrf_field() }}
	<input type="submit" value="Log out">
	</form>
@endguest

<hr>

@yield('content')
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
