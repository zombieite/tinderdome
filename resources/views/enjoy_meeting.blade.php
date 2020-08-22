        <h2><a href="/profile/compatible?" class="bright">Let us know if you'd enjoy meeting these users</a></h2>
        @for ($i = 0; (($i < 6) && ($i < count($unrated_users))); $i++)
            @if ($unrated_users[$i]->number_photos)
                @include('user_block_enjoy_meeting', ['user_id' => $unrated_users[$i]->id])
            @endif
        @endfor
