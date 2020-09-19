@php $ads = [
'<a class="small" href="https://www.youtube.com/watch?v=zQqjvO-useM">Roadblock</a>',
'<a class="small" href="https://www.facebook.com/thewastelander/">The Wastelander</a>',
'<a class="small" href="/images/awaited-nonfictional-delusion/kelli_BT_ending.m4a">Time Counts</a>',
'<a class="small" href="/images/awaited-nonfictional-delusion/create_value.m4a">Create Value</a>',
'<a class="small" href="/videolist">Videos</a>',
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
array_push($ads, '<a class="small bright" href="/awaited-nonfictional-delusion">Awaited: Nonfictional Delusion</a>');
array_push($ads, '<a class="small" href="https://cultofcatmeat.com">Cult of Catmeat</a>');

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
