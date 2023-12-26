@if ($matched_to_users)
    <h2>Mission matches</h2>
    @foreach ($matched_to_users as $matched_to_user)
        @if ($matched_to_user->event_is_in_future && !$matched_to_user->match_requested)
            <a href="/profile/match?event_id={{ $matched_to_user->event_id }}" class="bright">Here's your match for {{ $matched_to_user->event_long_name }}</a>.<br><br>
        @else
            @if ($matched_to_user->logged_in_users_rating_of_this_user === 0)
                @include('user_block_found_not_liked', [
                    'event_long_name'                  => $matched_to_user->event_long_name,
                ])
            @else
                @if ($matched_to_user->logged_in_users_rating_of_this_user === -1)
                    @if (($matched_to_user->this_users_rating_of_logged_in_user === 0) or (!$matched_to_user->name))
                        @include('user_block_found_but_deleted', [
                            'event_long_name'          => $matched_to_user->event_long_name,
                            'event_id'                 => $matched_to_user->event_id,
                        ])
                    @else
                        @include('user_block_found', [
                            'number_photos'            => $matched_to_user->number_photos,
                            'url'                      => $matched_to_user->url,
                            'user_id'                  => $matched_to_user->id,
                            'name'                     => $matched_to_user->name,
                            'event_long_name'          => $matched_to_user->event_long_name,
                            'bounty_hunt'              => $matched_to_user->bounty_hunt,
                        ])
                    @endif
                @else
                    @if ($matched_to_user->name && $matched_to_user->this_users_rating_of_logged_in_user !== 0)
                        @include('user_block_not_found_yet', [
                            'number_photos'            => $matched_to_user->number_photos,
                            'url'                      => $matched_to_user->url,
                            'user_id'                  => $matched_to_user->id,
                            'name'                     => $matched_to_user->name,
                            'event_long_name'          => $matched_to_user->event_long_name,
                            'event_id'                 => $matched_to_user->event_id,
                            'ok_to_mark_user_found'    => $matched_to_user->ok_to_mark_user_found,
                            'bounty_hunt'              => $matched_to_user->bounty_hunt,
                            'curse_interface'          => $curse_interface,
                        ])
                    @else
                        @include('user_block_matched_to_deleted', [
                            'event_long_name'                     => $matched_to_user->event_long_name,
                            'event_id'                            => $matched_to_user->event_id,
                            'user_id_of_match'                    => $matched_to_user->user_id_of_match,
                            'logged_in_users_rating_of_this_user' => $matched_to_user->logged_in_users_rating_of_this_user,
                        ])
                    @endif
                @endif
            @endif
        @endif
    @endforeach
@endif
