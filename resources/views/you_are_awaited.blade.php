    @if ($matched_to_user->event_is_in_future and $matched_to_user->name and $matched_to_user->this_users_rating_of_logged_in_user !== 0)
        @if ($matched_to_user->bounty_hunt)
            <h1>YOU ARE SIGNED UP FOR A BOUNTY HUNT AT {{ strtoupper($matched_to_user->event_long_name) }}! <a class="bright" href="/profile/match?event_id={{ $matched_to_user->event_id }}">Here's your quarry</a>. Someone else might be hunting you down, too.</h1>
        @else
            <h1>YOU ARE AWAITED AT {{ strtoupper($matched_to_user->event_long_name) }}! <a class="bright" href="/profile/match?event_id={{ $matched_to_user->event_id }}">Here's your match</a>.</h1>
        @endif
    @endif
