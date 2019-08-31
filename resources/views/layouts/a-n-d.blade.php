<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta property="og:title" content="Awaited: Nonfictional Delusion">
<meta property="og:url" content="http://youareawaited.com/awaited-nonfictional-delusion">
<meta property="og:image" content="http://youareawaited.com/images/awaited-nonfictional-delusion.jpg">
<meta property="og:description" content="What if, by going to Wasteland Weekend, you really could enter another world?">
<meta name="description" content="What if, by going to Wasteland Weekend, you really could enter another world?">
<title>Awaited: Nonfictional Delusion</title>
<link href="/css/app.css?rev=3" rel="stylesheet">
</head>
<body>
<a href="{{ url('/') }}" style="text-decoration:none;"><img src="/images/YAA.png" style="width:100%;max-width:441px;"></a>
<br>
<hr>
        @yield('content')
<hr>
{{ $total_count }} total participants
@if ($next_event_count >= 20)
    &middot; {{ $next_event_count }} signed up for missions during {{ $pretty_event_names[$next_event] }} {{ $year }}
@endif
@if ($active_count >= 10)
    &middot; {{ $active_count }} active in the past 24 hours
@endif
<br>
<br>
For updates, <a href="https://www.facebook.com/YouAreAwaited">follow us on another social network</a>. For films, <a href="https://www.youtube.com/wastelandfirebird">follow us on this video site</a>. Contact <a href="mailto:wastelandfirebird@gmail.com">wastelandfirebird@gmail.com</a> (<a href="/profile/Firebird">Firebird</a>) to report bugs or abusive users.
</body>
</html>
