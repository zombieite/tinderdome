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
            <td class="tight">@if ($match->event_is_in_future){{ $match->cap }}@endif</td>
            <td class="tight {{ $match->match_1_class }}"><a class="{{ $match->match_1_class }} @if ($match->random_ok == 0) bright @endif " href="/profile/{{ $match->user_id }}/{{ $match->wasteland_name_hyphenated }}">{{ $match->name }}</a></td>
            <td class="tight {{ $match->match_1_class }}">{{ $match->user_1_choice }}</td>
            <td class="tight {{ $match->match_2_class }}">{{ $match->user_2_choice }}</td>
            <td class="tight {{ $match->match_2_class }}">@if ($match->user_id_of_match) <a class="{{ $match->match_2_class }}" href="/profile/{{ $match->user_id_of_match }}/{{ $match->matchs_name_hyphenated }}">{{ $match->name_of_match }}</a> @else @if($match->failed_match_attempt) {{ $match->match_requested }} @else @if (isset($match->claimant_user_id)) <a class="{{ $match->match_2_class }}" href="/profile/{{ $match->claimant_user_id }}/{{ $match->claimant_name_hyphenated }}">{{ $match->claimant_name }}</a> @endif @endif @endif </td>
            <td class="tight">@if ($match->name_of_match && (($match->user_1_choice !== 0 && $match->user_2_choice !== 0)) && !($match->user_1_choice == -1 && $match->user_2_choice == -1)) <form action="" method="POST">{{ csrf_field() }}<input type="hidden" name="attending_id" value="{{ $match->attending_id }}"><input class="tight" type="submit" value="Mark {{ $match->name }}/{{ $match->name_of_match }} found"></form> @else &nbsp; @endif </td>
        </tr>
    @endforeach
</table>
<br>
@php if ($counter == 0) { $counter = 1; } @endphp
{{ $found }}/{{ $counter }} ({{ round($found/$counter * 100) }}%) marked their matches found.

@endsection
