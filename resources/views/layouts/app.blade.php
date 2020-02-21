<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<title>You Are Awaited</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:type" content="website">
<meta property="og:title" content="You Are Awaited">
<meta property="og:url" content="https://youareawaited.com">
<meta property="og:image" content="https://youareawaited.com/images/awaited.jpg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:description" content="Find thems you're looking for and thems you've lost.">
<meta name="description" content="Find thems you're looking for and thems you've lost.">
<link href="/css/app.css" rel="stylesheet">
</head>
<body>
<a href="{{ url('/') }}" style="text-decoration:none;"><img src="/images/YAA.png" style="filter:none;width:100%;max-width:530px;"></a>
<br>
@guest
    <a class="navbar" href="{{ route('register') }}">Create a profile</a>
    &middot;
    <a class="navbar" href="{{ route('login') }}">Log in</a>
    &middot;
    <a class="navbar" href="mailto:wastelandfirebird@gmail.com?subject=I lost my YAA password, please send me a new one&body=I lost my YAA password, please send me a new one">Lost password</a>
@else
    <form action="{{ route('logout') }}" method="POST">
    {{ csrf_field() }}
    @if($title){{ $title }} @endif<a href="/profile/{{ Auth::user()->id}}/{{ preg_replace('/ /', '-', Auth::user()->name) }}">{{ Auth::user()->name }}</a>@if($missions_completed) [{{ $missions_completed }}]@endif
    &middot;
    <a class="navbar" href="/profile/edit">Edit profile</a>
    &middot;
    <a class="navbar" href="/image/upload">Upload images</a>
    &middot;
    <a class="navbar" href="/search">Search</a>
    &middot;
    <input type="submit" value="Log out">
    </form>
@endguest
<hr>
@include('home_promo_stuff')
    @yield('content')
<hr>
{{ $active_count }} active in the past 24 hours
<br>
<br>
Contact <a href="mailto:wastelandfirebird@gmail.com">wastelandfirebird@gmail.com</a> (<a href="/profile/Firebird">Firebird</a>) for questions, lost passwords, bug reports, abusive user reports, or to set up new events. For updates, <a href="https://www.facebook.com/YouAreAwaited">follow us on another social network</a>.
</body>
</html>
