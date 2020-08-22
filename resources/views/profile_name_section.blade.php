<h1>
    @if ($missions_completed)
        <span class="dull">{{ $titles[$title_index] }}</span>
    @endif
    {{ $wasteland_name }}
    @if ($campaigning)
        is running for the office of
        @include('prezident', [])
    @endif
</h1>
