@extends('layouts.app')
@section('content')
@if ($matchme)
@else
    Click to be matched for {{ $event_name }}.
    <form action="" method="POST">
        {{ csrf_field() }}
        <input type="submit" name="matchme" value="TRUST THE ALGORITHM" class="yesyesyes">
    </form>
@endif
@if ($potential_matches)
    @php $counter = 0; @endphp
    <table>
    <tr>
            <td>&nbsp;</td>
            <td>id</td>
            <td>name</td>
            <td>email</td>
            <td>chooser's desired gender of match</td>
            <td>chosen's gender</td>
            <td>chosen's desired gender of match</td>
            <td>chooser's gender</td>
            <td>score</td>
            <td>photos</td>
            <td>greylist</td>
    </tr>
    @foreach ($potential_matches as $potential_match)
        @php $counter++; @endphp
        <tr>
            <td>{{ $counter }}</td>
            <td>{{ $potential_match->id }}</td>
            <td>{{ $potential_match->name }}</td>
            <td>{{ $potential_match->email }}</td>
            <td>{{ $potential_match->choosers_desired_gender_of_match }}</td>
            <td>{{ $potential_match->gender }}</td>
            <td>{{ $potential_match->gender_of_match }}</td>
            <td>{{ $logged_in_user->gender }}</td>
            <td>{{ $potential_match->score }}</td>
            <td>{{ $potential_match->number_photos }}</td>
            <td>{{ $potential_match->greylist }}</td>
        </tr>
    @endforeach
    </table>
@else
    @if ($matchme)
        You do not yet have any potential matches. You may still get a match, because a new person could sign up and be matched to you. Check back later!
    @endif
@endif
@endsection
