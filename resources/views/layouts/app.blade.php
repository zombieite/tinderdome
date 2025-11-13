<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<title>You Are Awaited</title>
<link rel="manifest" href="/manifest.json">
<link rel="apple-touch-icon" href="/icon-192.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="You Are Awaited">
<meta name="theme-color" content="#000000">
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
<link href="/css/yaa.css" rel="stylesheet">
</head>
<body>
<a href="{{ url('/') }}" style="text-decoration:none;"><img src="/images/yaa-header.png" style="filter:none;width:100%;max-width:530px;"></a>
<br>
@guest
    <div class="navbar"><a href="{{ route('register') }}">Sign up</a></div>
    &middot;
    <div class="navbar"><a href="{{ route('login') }}">Log in</a></div>
    &middot;
    <div class="navbar"><a href="mailto:wastelandfirebird@gmail.com?subject=I lost my YAA password, please send me a new one&body=I lost my YAA password, please send me a new one">Lost password</a></div>
@else
    <div class="navbar">@if($logged_in_user_title){{ $logged_in_user_title }} @endif<a href="/profile/{{ Auth::user()->id}}/{{ preg_replace('/ /', '-', Auth::user()->name) }}">{{ Auth::user()->name }}</a>@if($logged_in_user_missions_completed) [{{ $logged_in_user_missions_completed }}]@endif</div>
    &middot;
    <div class="navbar"><a href="/profile/edit">Edit profile</a></div>
    &middot;
    <div class="navbar"><a href="/image/upload">Upload images</a></div>
    @if (Auth::user()->number_photos)
        &middot;
        <div class="navbar"><a href="/search">Search</a></div>
{{--
        &middot;
        <div class="navbar"><a href="/create-event">Create event</a></div>
--}}
    @endif
@endguest
<hr class="top">
@include('home_promo_stuff')
    @yield('content')
<hr>
{{ $active_count }} active in the past 24 hours
<br>
<br>
Contact <a href="mailto:wastelandfirebird@gmail.com">wastelandfirebird@gmail.com</a> (<a href="/profile/Firebird">Firebird</a>) for questions, lost passwords, bug reports, abusive user reports, or to set up new events. For updates, <a href="https://www.facebook.com/YouAreAwaited">follow us on another social network</a>.
<br>
<br>
@guest
@else
    <form action="{{ route('logout') }}" method="POST">
        {{ csrf_field() }}
        <input type="submit" value="Log out">
    </form>
@endguest
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
    .then(() => console.log('Service Worker registered'))
    .catch(err => console.error('Service Worker failed:', err));
}
</script>
</body>
</html>
