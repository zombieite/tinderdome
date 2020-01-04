@extends('layouts.app')
@section('content')

<h1>Matches for {{ $event_data->event_long_name }} &middot; {{ $event_data->event_date }}</h1>
<table>
    <tr>
        <td><b>&nbsp;</b></td>
        <td><b>Score</b></td>
        <td><b>Name</b></td>
        <td><b>Name of match</b></td>
    </tr>
    @php $counter = 0; @endphp
    @foreach ($matches as $match)
        @php $counter++; @endphp
        <tr>
            <td>{{ $counter                   }}
            <td>{{ $match->score              }}</td>
            <td>{{ $match->name               }}</td>
            <td>{{ $match->name_of_match      }}</td>
        </tr>
    @endforeach
</table>

@endsection
