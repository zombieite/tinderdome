<div class="mission_match">
@if ($number_photos) <a href="{{ $url }}"><img src="/uploads/image-{{ $user_id }}-1.jpg" style="height:100px;"></a><br> @endif
Found
<a href="{{ $url }}">{{ $name }}</a>
<br>{{ $event_long_name }}
<br>
@if ($bounty_hunt) 
Bounty Hunt
@else
You Are Awaited
@endif
</div>
