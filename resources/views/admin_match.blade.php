@extends('layouts.app')
@section('content')

<h1>Matches for {{ $event_data->event_long_name }} &middot; {{ $event_data->event_date }}</h1>
<table>
    <tr>
        <td><b>&nbsp;</b></td>
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
            <td class="tight">{{ $counter }}</td>
            <td class="tight">{{ $match->cap }}</td>
            <td class=" @if ($match->user_1_choice == $match->user_2_choice && ($match->user_1_choice == 3 || $match->user_1_choice == -1)) {{ $choice_map[$match->user_1_choice] }} @else @if (!$match->user_id_of_match) @if (isset($users_who_are_matched_but_dont_know[$match->user_id])) caution @else no @endif @endif @endif tight"><a href="/profile/{{ $match->user_id }}/{{ $match->wasteland_name_hyphenated }}">{{ $match->name }}</a></td>
            <td class="{{ $choice_map[$match->user_1_choice] }} tight">{{ $match->user_1_choice }}</td>
            <td class="{{ $choice_map[$match->user_2_choice] }} tight">{{ $match->user_2_choice }}</td>
            <td class=" @if ($match->user_1_choice == $match->user_2_choice && ($match->user_1_choice == 3 || $match->user_1_choice == -1)) {{ $choice_map[$match->user_1_choice] }} @endif tight">@if ($match->user_id_of_match) <a href="/profile/{{ $match->user_id_of_match }}/{{ $match->matchs_name_hyphenated }}">{{ $match->name_of_match }}</a> @else &nbsp; @endif </td>
            <td class="tight">@if ($match->name_of_match && (($match->user_1_choice !== 0 && $match->user_2_choice !== 0)) && !($match->user_1_choice == -1 && $match->user_2_choice == -1)) <form action="" method="POST">{{ csrf_field() }}<input type="hidden" name="attending_id" value="{{ $match->attending_id }}"><input class="tight" type="submit" value="Mark {{ $match->name }}/{{ $match->name_of_match }} found"></form> @else &nbsp; @endif </td>
        </tr>
    @endforeach
</table>
<br>
@php if ($counter == 0) { $counter = 1; } @endphp
{{ $found }}/{{ $counter }} ({{ round($found/$counter * 100) }}%) marked their matches found.

@endsection
