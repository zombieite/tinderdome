THE WINNER FOR THE ELECTION TO THE OFFICE OF
<h1>@include('prezident', [])</h1>
WITH 16 VOTES, IS YOUR NEW PREZIDENT,
<h1 class="bright">MAD SKELLI!</h1>
<iframe style="width:100%;height:480px" src="https://www.youtube.com/embed/qXhp0xtCoy4" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
SHE WINS FABULOUS CASH AND PRIZES AND OH YEAH YOU HAVE TO DO WHATEVER SHE SAYS NOW

{{--

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
<input type="submit" name="submit" value="SUBMIT MY VOTE">
</form>

--}}
