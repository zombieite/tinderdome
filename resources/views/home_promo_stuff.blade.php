@php $ads = [
'<a href="/awaited-nonfictional-delusion"><img src="/images/stuff/awaited-nonfictional-delusion.jpg"></a>',
'<a href="https://www.youtube.com/watch?v=pMKM1d0IsNs"><img src="/images/stuff/awaited-youtube.jpg"></a>',
'<a href="https://www.youtube.com/watch?v=zQqjvO-useM"><img src="/images/stuff/roadblock-youtube.jpg"></a>',
'<a href="https://www.facebook.com/thewastelander/"><img src="/images/stuff/wastelander.jpg"></a>',
'<a href="https://www.facebook.com/WCCorp/"><img src="/images/stuff/wcc.png"></a>',
'<a href="https://cultofcatmeat.com"><img src="/images/stuff/catmeat.png"></a>',
];
shuffle($ads);
@endphp
@foreach ($ads as $ad)
    {!! $ad !!}
@endforeach
<hr>
