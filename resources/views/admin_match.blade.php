@extends('layouts.app')
@section('content')

<h1>Matches for {{ $event_data->event_long_name }} &middot; {{ $event_data->event_date }}</h1>
<table>
    <tr>
        <td><b>&nbsp;</b></td>
        <td><b>Score</b></td>
        <td><b>Name</b></td>
        <td><b>Choice</b></td>
        <td><b>Match's<br>choice</b></td>
        <td><b>Name of match</b></td>
    </tr>
    @php $counter = 0; @endphp
    @foreach ($matches as $match)
        @php $counter++; @endphp
        <tr>
            <td>{{ $counter }}</td>
            <td>{{ $match->score }}</td>
            <td @if ($match->user_1_choice == $match->user_2_choice) class="{{ $choice_map[$match->user_1_choice] }}" @endif>{{ $match->name }}</td>
            <td class="{{ $choice_map[$match->user_1_choice] }}">{{ $match->user_1_choice }}</td>
            <td class="{{ $choice_map[$match->user_2_choice] }}">{{ $match->user_2_choice }}</td>
            <td @if ($match->user_1_choice == $match->user_2_choice) class="{{ $choice_map[$match->user_1_choice] }}" @endif>{{ $match->name_of_match }}</td>
        </tr>
    @endforeach
</table>

@endsection
