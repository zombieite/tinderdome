@php $ads = [
'<a class="small" href="https://www.facebook.com/thewastelander/">The Wastelander</a>',
'<a class="small" href="https://www.youtube.com/wastelandfirebird">Firebird on YouTube</a>',
'<a class="small" href="https://www.youtube.com/channel/UCSS_L2Eka2LMn0hM9zaLdlg">Mad Skelli on YouTube</a>',
];
shuffle($ads);

// Can be used to remove some random ones from above so the ads section doesn't have too many in it
array_pop($ads);
array_pop($ads);

@endphp
@guest
@php
$ads = [];
@endphp
@endguest
@php

// Can be used to add mandatory ads that must always be shown
array_push($ads, '<a class="small" href="/awaited-nonfictional-delusion">Awaited: Nonfictional Delusion</a>');
array_push($ads, '<a class="small bright" href="/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem">Heads Will Rock</a>');

shuffle($ads);
$ad_string = '';
$last = end($ads);
foreach ($ads as $ad) {
    if ($ad == $last) {
        $ad_string .= $ad;
    } else {
        $ad_string .= "$ad &middot; ";
    }
}

// Use this below the endphp to show the circulating ads
// {!! $ad_string !!}
@endphp
<a class="small bright" href="/awaited-nonfictional-delusion">AWAITED</a>&nbsp;&nbsp;&nbsp;
<a class="small bright" href="/heads-will-rock-a-chronicle-of-postapocalyptic-mayhem">HEADS WILL ROCK</a>&nbsp;&nbsp;&nbsp;
<a class="small bright" href="/1981-a-film-in-honour-of-the-40th-anniversary-of-mad-max-2">1981</a>
<hr>
