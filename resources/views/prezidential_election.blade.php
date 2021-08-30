@if ($candidates)
An election is being held for the office of
<h1>@include('prezident', [])</h1>
<h1 class="bright">VOTE NOW!</h1>
@if ($campaigning)
    You are running for this office. <a href="/profile/edit">Update your profile</a> with your campaign platform and YouTube video campaign ad!
@endif
<br>
<form action="" method="POST">
{{ csrf_field() }}
@foreach ($candidates as $profile)
    @include('candidate')
@endforeach
<br>
<input type="submit" name="submit" class="yesyesyes" value="Submit vote">
</form>
@endif
