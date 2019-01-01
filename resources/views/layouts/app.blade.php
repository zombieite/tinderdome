<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta property="og:image" content="http://youareawaited.com/images/awaited_bw.jpg">
<meta property=”og:description” content="Since The Apocalypse, Wasteland has become known as the place we come to find thems we're looking for and thems we've lost. Sign up now to find a new friend, enemy, or romantic partner.">
<meta name="description" content="Since The Apocalypse, Wasteland has become known as the place we come to find thems we're looking for and thems we've lost. Sign up now to find a new friend, enemy, or romantic partner.">
<meta name="robots" content="noindex">
<title>You Are Awaited</title>
<link href="/css/app.css?rev=3" rel="stylesheet">
</head>

<body>
<a href="{{ url('/') }}" style="text-decoration:none;"><img src="/images/YAA.png"></a>
<br>

@guest
	<a href="{{ route('register') }}">Create a profile</a>
	&middot;
	<a href="{{ route('login') }}">Log in</a>
	&middot;
	<a href="mailto:wastelandfirebird@gmail.com?subject=I lost my YAA password, please send me a new one&body=I lost my YAA password, please send me a new one">Lost password</a>
	&middot;
	<a href="/save-deadline">SAVE DEADLINE</a>
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
	<a href="/save-deadline">SAVE DEADLINE</a>
	&middot;
	<input type="submit" value="Log out">
	</form>
@endguest

<hr>

{{--
@guest
@else
	@if ((Auth::user()->id === 1) || (Auth::user()->id === 50))
--}}

		@yield('content')

{{--
<img src="/images/fun/other/under_construction.gif"><br>
<h1>UNDER CONSTRUCTION</h1>
<h3>You Are Awaited will return to service on November 26</h3>
	@else
		<h1>TRUST THE ALGORITHM</h1>
		Matches are being run right now.<br><br>
		@for ($i = 1; $i <=30; $i++)
			<div style="display:inline-block;padding:2em;"><a href="/images/fun/{{ $i }}.jpg">{{ $i }}</a></div>
		@endfor
	@endif
@endguest
--}}

<hr>
{{ $total_count }} total participants &middot; {{ $next_event_count }} signed up for missions during {{ $pretty_event_names[$next_event] }} {{ $year }}
@if ($active_count >= 10)
	&middot; {{ $active_count }} active in the past 24 hours
@endif
<br>
<br>
For updates, <a href="https://www.facebook.com/YouAreAwaited">follow us on another social network</a>. Contact <a href="mailto:wastelandfirebird@gmail.com">wastelandfirebird@gmail.com</a> (<a href="/profile/Firebird">Firebird</a>) to report bugs or abusive users.

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
