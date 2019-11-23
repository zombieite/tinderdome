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
            <td>user</td>
            <td>chosen's random ok (liuser: {{ $logged_in_user->random_ok }})</td>
            <td>user looking to be matcheds rating of this user</td>
            <td>this users rating of user looking to be matched</td>
            <td>chosen's gender (liuser's pref: {{ $logged_in_user->gender_of_match }})</td>
            <td>chosen's desired gender of match (liuser's gender: {{ $logged_in_user->gender }})</td>
            <td>score</td>
    </tr>
    @foreach ($potential_matches as $potential_match)
        @php $counter++; @endphp
        <tr>
            <td @if ($my_match_user_id == $potential_match->user_id) class="bright" @endif >{{ $counter }}|{{ $potential_match->user_id }}|{{ $potential_match->name }}</td>
            <td>{{ $potential_match->random_ok }}</td>
            <td>{{ $potential_match->user_looking_to_be_matcheds_rating_of_this_user }}</td>
            <td>{{ $potential_match->this_users_rating_of_user_looking_to_be_matched }}</td>
            <td>{{ $potential_match->gender }}</td>
            <td>{{ $potential_match->gender_of_match }}</td>
            <td>{{ $potential_match->score }}</td>
        </tr>
    @endforeach
    </table>
@else
    @if ($matchme)
        You do not yet have any potential matches. You may still get a match, because a new person could sign up and be matched to you. Check back later!
    @endif
@endif
@endsection
