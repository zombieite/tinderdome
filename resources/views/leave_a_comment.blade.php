            <h2>Leave a comment for these users</h2>
            @for ($i = 0; (($i < 6) && ($i < count($users_you_can_comment_on_but_havent))); $i++)
                @if ($users_you_can_comment_on_but_havent[$i]->number_photos)
                    @include('user_block_maybe_comment', ['user_id' => $users_you_can_comment_on_but_havent[$i]->user_id, 'wasteland_name_hyphenated' => $users_you_can_comment_on_but_havent[$i]->wasteland_name_hyphenated, 'name' => $users_you_can_comment_on_but_havent[$i]->name])
                @endif
            @endfor
