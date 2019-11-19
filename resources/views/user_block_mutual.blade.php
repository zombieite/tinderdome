<div class="centered_block_bright">
    @if ($number_photos)
        <a href="/profile/{{ $user_id }}/{{ $wasteland_name_hyphenated }}"><img src="/uploads/image-{{ $user_id }}-1.jpg" style="height:100px;"></a>
        <br>
    @endif
    <a href="/profile/{{ $user_id }}/{{ $wasteland_name_hyphenated }}">{{ $name }}</a>
</div>
