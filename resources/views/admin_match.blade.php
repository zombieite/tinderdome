@extends('layouts.app')
@section('content')

<h1>Matches for {{ $event_data->event_long_name }} &middot; {{ $event_data->event_date }}</h1>
<table>
    <tr>
        <td><b>&nbsp;</b></td>
        <td><b>Name</b></td>
        <td><b>Score</b></td>
    </tr>
    @php $counter = 0; @endphp
    @foreach ($matches as $match)
        @php $counter++; @endphp
        <tr>
            <td>{{ $counter           }}
            <td>{{ $match->name       }}</td>
            <td>{{ $match->score      }}</td>
        </tr>
    @endforeach
</table>

@endsection
