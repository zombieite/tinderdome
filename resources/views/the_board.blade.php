@extends('layouts.app')
@section('content')
<table>
<tr><td>Chapter</td><td>Pages</td><td>Beats</td><td>Title</td><td>Setting</td><td>Summary</td><td>Answered Question</td><td>New Question</td></tr>
@foreach ($acts as $act)
    <tr><td colspan="11">{{ $act['name'] }}</td></tr>
    @foreach ($act['chapters'] as $number => $chapter)
    <tr>
        <td>{{ $number }}</td>
        <td>
            {{ round($number_minutes * ($chapter['percent_start'] / 100)) }}-{{ round($number_minutes * ($chapter['percent_end'] / 100)) }}
            <br>
            ({{ $chapter['percent_start'] }}%-{{ $chapter['percent_end'] }}%)
        </td>
        <td style="font-size:xx-small;">{{ $chapter['beats'] }}</td>
        <td>{{ $chapter_specifics[$number]['title'] }}</td>
        <td>{{ $chapter_specifics[$number]['location']}}</td>
        <td style="font-size:xx-small;">{{ $chapter_specifics[$number]['summary']}}</td>
        <td>{{ $chapter_specifics[$number]['answered_question']}}</td>
        <td>{{ $chapter_specifics[$number]['new_question']}}</td>
    </tr>
    @endforeach
@endforeach
</table>
@endsection
