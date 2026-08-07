@extends('layouts.app')
@section('content')

<form method="POST" action="/profile/delete">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
    Are you sure?
    <button type="submit" class="no">
        DELETE PROFILE
    </button>
</form>

@endsection
