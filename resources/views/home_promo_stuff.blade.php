@php $ads = [
'<a class="small" href="https://www.youtube.com/watch?v=zQqjvO-useM">Roadblock</a>',
'<a class="small" href="https://www.facebook.com/thewastelander/">The Wastelander</a>',
'<a class="small" href="https://www.facebook.com/WCCorp/">WCC</a>',
];
shuffle($ads);

// Can be used to remove some random ones from above
//array_pop($ads);
//array_pop($ads);

// Can be used to add mandatory ads that must always be shown
array_push($ads, '<a class="small" href="/awaited-nonfictional-delusion">Awaited: Nonfictional Delusion</a>');
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
