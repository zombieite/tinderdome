@extends('layouts.app')
@section('content')

<h1>Event</h1>

<form action="" method="POST">

    {{ csrf_field() }}
    <br><br>
    <button id="submit" type="submit" class="yesyes">Submit changes</button>

</form>

@endsection
