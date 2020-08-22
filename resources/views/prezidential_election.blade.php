<h1>An election will be held for the office of @include('prezident', []) on Tuesday, Oct 6, 2020.</h1>
@if ($campaigning)
    You are running for this office.
@else
    If you'd like to run for this office, check the appropriate box under <a href="/profile/edit">Edit Profile</a>.
@endif
