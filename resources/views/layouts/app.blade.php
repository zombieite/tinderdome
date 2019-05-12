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
<h6>THE WASTELAND COMMUNICATION CORPORATION PROUDLY PRESENTS THE WORLD'S FIRST SOCIAL NETWORK</h6>
<a href="{{ url('/') }}" style="text-decoration:none;"><img src="/images/YAA.png" style="width:100%;max-width:441px;"></a>
<br>

@guest
    <a class="navbar" href="{{ route('register') }}">Create a profile</a>
    &middot;
    <a class="navbar" href="{{ route('login') }}">Log in</a>
    &middot;
    <a class="navbar" href="mailto:wastelandfirebird@gmail.com?subject=I lost my YAA password, please send me a new one&body=I lost my YAA password, please send me a new one">Lost password</a>
    &middot;
    <a class="navbar" href="/404">404</a>
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
    <a class="navbar" href="/404">404</a>
    &middot;
    <a class="navbar" href="https://www.cultofcatmeat.com">CULT OF CATMEAT</a>
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
<h3>You Are Awaited will return to service soon</h3>
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
{{ $total_count }} total participants
@if ($next_event_count >= 20)
    &middot; {{ $next_event_count }} signed up for missions during {{ $pretty_event_names[$next_event] }} {{ $year }}
@endif
@if ($active_count >= 10)
    &middot; {{ $active_count }} active in the past 24 hours
@endif
<br>
<br>
For updates, <a href="https://www.facebook.com/YouAreAwaited">follow us on another social network</a>. Contact <a href="mailto:wastelandfirebird@gmail.com">wastelandfirebird@gmail.com</a> (<a href="/profile/Firebird">Firebird</a>) to report bugs or abusive users.
<h6>All data submitted to this social network will be sold to the highest bidder by the Wasteland Communication Corporation</h6>
</body>
</html>
