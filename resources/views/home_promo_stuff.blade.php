@php $ads = [
'<a class="small" href="https://www.facebook.com/thewastelander/">The Wastelander</a>',
'<a class="small" href="https://www.youtube.com/wastelandfirebird">Firebird on YouTube</a>',
'<a class="small" href="https://www.youtube.com/channel/UCSS_L2Eka2LMn0hM9zaLdlg">Mad Skelli on YouTube</a>',
];
shuffle($ads);

// Can be used to remove some random ones from above so the ads section doesn't have too many in it
// array_pop($ads);

@endphp
@guest
@php
$ads = [];
@endphp
@endguest
@php

// Can be used to add mandatory ads that must always be shown
array_push($ads, '<a class="small bright" href="/awaited-nonfictional-delusion">Awaited: Nonfictional Delusion</a>');

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
@endphp
{!! $ad_string !!}
<hr>
