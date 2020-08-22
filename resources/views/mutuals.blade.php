@if (count($mutuals))
    <h2>Users who have shared their contact info with you</h2>
    @foreach ($mutuals as $mutual)
        @include('user_block_mutual', ['number_photos' => $mutual->number_photos, 'user_id' => $mutual->id, 'wasteland_name_hyphenated' => $mutual->wasteland_name_hyphenated, 'name' => $mutual->name])
    @endforeach
@endif
