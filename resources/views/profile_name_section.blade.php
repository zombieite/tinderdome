<h1>@if ($missions_completed) <span class="dull">{{ $titles[$title_index] }}</span> @endif {{ $wasteland_name }}</h1>
@if ($video_id)
    <iframe style="width:100%;height:480px" src="https://www.youtube.com/embed/{{ $video_id }}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
@endif
