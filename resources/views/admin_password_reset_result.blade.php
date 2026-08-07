@extends('layouts.app')
@section('content')

<h1>Password reset for {{ $wasteland_name }}</h1>

Temporary password: <strong class="bright">{{ $temporary_password }}</strong>
<br><br>
Copy this password now. It is not stored in readable form and cannot be shown again.

@endsection
