@extends('layouts.app')
@section('content')

<h1>Matches for {{ $event_data->event_long_name }} &middot; {{ $event_data->event_date }}</h1>
<table>
    <tr>
        <td><b>&nbsp;</b></td>
        <td><b>Score</b></td>
        <td><b>Cap</b></td>
        <td><b>Name</b></td>
        <td><b>Choice</b></td>
        <td><b>Match's<br>choice</b></td>
        <td><b>Name of match</b></td>
        <td><b>Mark found</b></td>
    </tr>
    @php
        $counter = 0;
        $found   = 0;
    @endphp
    @foreach ($matches as $match)
        @php
            $counter++;
            if ($match->user_1_choice == $match->user_2_choice && $match->user_1_choice == -1) { $found++; }
        @endphp
        <tr>
            <td>{{ $counter }}</td>
            <td>{{ $match->score }}</td>
            <td>{{ $match->caps }}</td>
            <td @if ($match->user_1_choice == $match->user_2_choice && ($match->user_1_choice == 3 || $match->user_1_choice == -1)) class="{{ $choice_map[$match->user_1_choice] }}" @else @if (!$match->user_id_of_match) class="no" @endif @endif><a href="/profile/{{ $match->user_id }}/{{ $match->wasteland_name_hyphenated }}">{{ $match->name }}</a></td>
            <td class="{{ $choice_map[$match->user_1_choice] }}">{{ $match->user_1_choice }}</td>
            <td class="{{ $choice_map[$match->user_2_choice] }}">{{ $match->user_2_choice }}</td>
            <td @if ($match->user_1_choice == $match->user_2_choice && ($match->user_1_choice == 3 || $match->user_1_choice == -1)) class="{{ $choice_map[$match->user_1_choice] }}" @endif><a href="/profile/{{ $match->user_id_of_match }}/{{ $match->matchs_name_hyphenated }}">{{ $match->name_of_match }}</a></td>
            <td><form action="" method="POST">{{ csrf_field() }}<input type="submit" name="found" value="{{ $match->attending_id }}"></form></td>
        </tr>
    @endforeach
</table>
<br>
{{ $found }}/{{ $counter }} ({{ round($found/$counter * 100) }}%) marked their matches found.

@endsection
