<!doctype html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta charset="utf-8">
		<title>You Are Awaited</title>
	</head>
	<body>
		<div class="container">
			<h1>You Are Awaited</h1>
			<a href="/">Home</a>
			<a href="/profile/create">Create profile</a>
			@yield('content')
		</div>
	</body>
</html>
