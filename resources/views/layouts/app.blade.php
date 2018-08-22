<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta property="og:image" content="http://youareawaited.com:8080/images/awaited_bw.jpg">
<meta property=”og:description” content="Since The Apocalypse, Wasteland has become known as the place we come to find thems we're looking for and thems we've lost. Sign up now to find a new friend, enemy, or romantic partner.">
<meta name="description" content="Since The Apocalypse, Wasteland has become known as the place we come to find thems we're looking for and thems we've lost. Sign up now to find a new friend, enemy, or romantic partner.">
<title>You Are Awaited</title>
<link href="/css/app.css?rev=3" rel="stylesheet">
</head>

<body>
<h1><a href="{{ url('/') }}" style="text-decoration:none;">You Are Awaited</a></h1>

@guest
	<a href="{{ route('register') }}">Create a profile</a> &middot; <a href="{{ route('login') }}">Log in</a> &middot; <a href="mailto:wastelandfirebird@gmail.com?subject=I lost my YAA password, please send me a new one&body=I lost my YAA password, please send me a new one">Lost password</a>
@else
	<form action="{{ route('logout') }}" method="POST">
	{{ csrf_field() }}
	Logged in as <a href="/profile/{{ Auth::user()->id}}/{{ preg_replace('/ /', '-', Auth::user()->name) }}">{{ Auth::user()->name }}</a>
	&middot;
	<a href="/">Home</a>
	&middot;
	<a href="/profile/edit">Edit profile</a>
	&middot;
	<a href="/image/upload">Upload images</a>
	&middot;
	<a href="/search">Search</a>
	&middot;
	<input type="submit" value="Log out">
	</form>
@endguest

<hr>

@yield('content')

<hr>
Contact <a href="mailto:wastelandfirebird@gmail.com">wastelandfirebird@gmail.com</a> (<a href="/profile/Firebird">Firebird</a>) to report bugs or abusive profiles.
<br>
<br>
@if ($active_count >= 10)
	{{ $active_count }} users active in the past 24 hours
@endif

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
