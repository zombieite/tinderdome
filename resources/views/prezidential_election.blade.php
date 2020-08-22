An election will be held for the office of
<h1>@include('prezident', [])</h1>
<span class="bright">Tuesday, Oct 6, 2020</span>
<br><br>
@if ($campaigning)
    You are running for this office. <a href="/profile/edit">Update your profile</a> with your campaign platform and YouTube video campaign ad!
@else
    If you'd like to run for this office, check the appropriate box under <a href="/profile/edit">Edit Profile</a>.
@endif
