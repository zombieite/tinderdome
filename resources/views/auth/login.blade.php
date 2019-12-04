@extends('layouts.app')
@section('content')
<form class="form-horizontal" method="POST" action="{{ route('login') }}">
{{ csrf_field() }}

<label for="email" class="col-md-4 control-label">Email</label>
<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

@if ($errors->has('email'))
<strong>{{ $errors->first('email') }}</strong>
@endif

<label for="password" class="col-md-4 control-label">Password</label>
<input id="password" type="password" class="form-control" name="password" required>

@if ($errors->has('password'))
<strong>{{ $errors->first('password') }}</strong>
@endif

<input type="hidden" name="remember" value="1">

<button type="submit" class="btn btn-primary">
Log in
</button>

</form>
@endsection
