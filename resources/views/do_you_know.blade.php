        <h2 class="bright">Do you know these users?</h2>
        @for ($i = 0; (($i < 6) && ($i < count($users_who_say_they_know_you))); $i++)
            @if ($users_who_say_they_know_you[$i]->number_photos)
                @include('user_block_maybe_comment', ['user_id' => $users_who_say_they_know_you[$i]->user_id, 'wasteland_name_hyphenated' => $users_who_say_they_know_you[$i]->wasteland_name_hyphenated, 'name' => $users_who_say_they_know_you[$i]->name])
            @endif
        @endfor
