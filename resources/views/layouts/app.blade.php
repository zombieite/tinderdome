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
    <div id="app">
        <nav>
            <div>
                <div>
                    <h1><a href="{{ url('/') }}">You Are Awaited</a></h1>
                </div>
                <div>
                        @guest
                            <a href="{{ route('login') }}">Login</a>
                            <a href="{{ route('register') }}">Register</a>
                        @else
                            {{ Auth::user()->name }}
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                        @endguest
                </div>
            </div>
        </nav>
        @yield('content')
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
