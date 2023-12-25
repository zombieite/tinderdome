<div class="mission_match_bright">
@if ($number_photos) <a href="{{ $url }}"><img src="/uploads/image-{{ $user_id }}-1.jpg" style="height:100px;"></a><br> @endif
@if ($bounty_hunt)
Hunt down
@else
Matched to
@endif
<a href="{{ $url }}">{{ $name }}</a>
<br>{{ $event_long_name }}
<br>
@if ($ok_to_mark_user_found)
    @include('rating_form', ['action' => "", 'user_id_to_rate' => $user_id, 'number_photos' => $number_photos, 'current_choice' => null, 'nos_left' => 1, 'is_my_match' => 1, 'curse_interface' => $curse_interface])
@endif
</div>
