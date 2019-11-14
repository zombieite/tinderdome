<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta property="og:title" content="You Are Awaited">
<meta property="og:url" content="http://youareawaited.com">
<meta property="og:image" content="http://youareawaited.com/images/awaited_bw.jpg">
<meta property="og:description" content="Find thems you're looking for and thems you've lost.">
<meta name="description" content="Find thems you're looking for and thems you've lost.">
<meta name="robots" content="noindex">
<title>You Are Awaited</title>
<link href="/css/app.css?rev=3" rel="stylesheet">
</head>

<body>
<a href="{{ url('/') }}" style="text-decoration:none;"><img src="/images/YAA.png" style="width:100%;max-width:441px;"></a>
<br>

@guest
    <a class="navbar" href="{{ route('register') }}">Create a profile</a>
    &middot;
    <a class="navbar" href="{{ route('login') }}">Log in</a>
    &middot;
    <a class="navbar" href="mailto:wastelandfirebird@gmail.com?subject=I lost my YAA password, please send me a new one&body=I lost my YAA password, please send me a new one">Lost password</a>
    &middot;
    <a class="navbar" href="/awaited-nonfictional-delusion"><span class="bright">A</span>W<span class="bright">A</span>I<span class="bright">T</span>E<span class="bright">D</span>: <span class="bright">N</span>O<span class="bright">N</span>F<span class="bright">I</span>C<span class="bright">T</span>I<span class="bright">O</span>N<span class="bright">A</span>L <span class="bright">D</span>E<span class="bright">L</span>U<span class="bright">S</span>I<span class="bright">O</span>N</a>
    &middot;
    <a class="navbar" href="https://www.cultofcatmeat.com">CULT OF CATMEAT</a>
@else
    <form action="{{ route('logout') }}" method="POST">
    {{ csrf_field() }}
    Logged in as <a href="/profile/{{ Auth::user()->id}}/{{ preg_replace('/ /', '-', Auth::user()->name) }}">{{ Auth::user()->name }}</a>
    &middot;
    <a class="navbar" href="/">Home</a>
    &middot;
    <a class="navbar" href="/profile/edit">Edit profile</a>
    &middot;
    <a class="navbar" href="/image/upload">Upload images</a>
    &middot;
    <a class="navbar" href="/search">Search</a>
    &middot;
    <a class="navbar" href="/awaited-nonfictional-delusion"><span class="bright">A</span>W<span class="bright">A</span>I<span class="bright">T</span>E<span class="bright">D</span>: <span class="bright">N</span>O<span class="bright">N</span>F<span class="bright">I</span>C<span class="bright">T</span>I<span class="bright">O</span>N<span class="bright">A</span>L <span class="bright">D</span>E<span class="bright">L</span>U<span class="bright">S</span>I<span class="bright">O</span>N</a>
    &middot;
    <a class="navbar" href="https://www.cultofcatmeat.com">CULT OF CATMEAT</a>
    &middot;
    <input type="submit" value="Log out">
    </form>
@endguest

<hr>

    @yield('content')

<hr>
{{ $total_count }} total participants
&middot; {{ $active_count }} active in the past 24 hours
@if ($next_event_name)
&middot; {{ $next_event_count }} signed up for missions during {{ $next_event_name }}
@endif
<br>
<br>
For updates, <a href="https://www.facebook.com/YouAreAwaited">follow us on another social network</a>. For films, <a href="https://www.youtube.com/wastelandfirebird">follow us on this video site</a>. Contact <a href="mailto:wastelandfirebird@gmail.com">wastelandfirebird@gmail.com</a> (<a href="/profile/Firebird">Firebird</a>) to report bugs or abusive users.
</body>
</html>
