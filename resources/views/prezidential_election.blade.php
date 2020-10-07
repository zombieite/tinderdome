THE WINNER FOR THE ELECTION TO THE OFFICE OF
<h1>@include('prezident', [])</h1>
WITH 16 VOTES, IS YOUR NEW PREZIDENT,
<h1 class="bright">MAD SKELLI!</h1>
SHE WINS FABULOUS CASH AND PRIZES AND OH YEAH YOU HAVE TO DO WHATEVER SHE SAYS NOW

{{--

An election is being held for the office of
<h1>@include('prezident', [])</h1>
<h1 class="bright">Vote on Tuesday, Oct 6, 2020</h1>
@if ($campaigning)
    You are running for this office. <a href="/profile/edit">Update your profile</a> with your campaign platform and YouTube video campaign ad!
@else
    If you'd like to run for this office, check the appropriate box under <a href="/profile/edit">Edit Profile</a>.
@endif
<br><br>
<form action="" method="POST">
{{ csrf_field() }}
@foreach ($candidates as $profile)
    @include('candidate')
@endforeach
<br>
<input type="submit" name="submit" value="SUBMIT MY VOTE">
</form>

--}}
