<h2>Upcoming missions</h2>

@if ($upcoming_events_and_signup_status)
    @if ($number_photos)
        <form action="/" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="attending_event_form" value="1">
            @foreach ($upcoming_events_and_signup_status as $upcoming_event)
                <table>
                    <tr>
                        <td>Event</td>
                        <td>
                            <input class="upcoming_event_checkbox" type="checkbox" name="attending_event_id_{{ $upcoming_event->event_id }}"
                            @if ($upcoming_event->attending_event_id)
                                @if ($upcoming_event->user_id_of_match || $upcoming_event->already_matched_but_dont_know_it)
                                    disabled
                                @endif
                                checked
                            @endif
                            >
                            <a href="/event/{{ $upcoming_event->event_id }}/{{ $upcoming_event->event_long_name_hyphenated }}">{{ $upcoming_event->event_long_name }}</a>: 
                            @if ($upcoming_event->url)
                                <a href="{{ $upcoming_event->url }}">{{ $upcoming_event->event_date }}</a>
                            @else
                                {{ $upcoming_event->event_date }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Type</td>
                        @if ($upcoming_event->bounty_hunt)
                            <td>Bounty Hunt. You secretly choose who you will look for. Someone else might secretly choose to look for you.</td>
                        @else
                            <td>You Are Awaited. The Algorithm chooses who you will look for. They'll be looking for you, too.</td>
                        @endif
                    </tr>
                    <tr>
                        <td>Signups</td>
                        <td>
                            <a href="/potential-match?event_id={{ $upcoming_event->event_id }}&show_met=1">
                            @if ($upcoming_event->signups_still_needed)
                                @if ($upcoming_event->signups_still_needed == 1)
                                    {{ $upcoming_event->attending_count }}/{{ $upcoming_event->attending_count + $upcoming_event->signups_still_needed }}, {{ $upcoming_event->signups_still_needed }} signup still needed
                                @else
                                    {{ $upcoming_event->attending_count }}/{{ $upcoming_event->attending_count + $upcoming_event->signups_still_needed }}, {{ $upcoming_event->signups_still_needed }} signups still needed
                                @endif
                            @else
                                {{ $upcoming_event->attending_count }} signups
                            @endif
                            </a>
                        </td>
                    </tr>
                    <tr>
                        @if ($upcoming_event->bounty_hunt)
                            <td>Quarry</td>
                        @else
                            <td>Match</td>
                        @endif
                        <td>
                            @if ($upcoming_event->user_id_of_match)
                                @if ($upcoming_event->bounty_hunt)
                                    <a class="bright" href="/profile/match?event_id={{ $upcoming_event->event_id }}">Here's your quarry</a>.
                                @else
                                    <a class="bright" href="/profile/match?event_id={{ $upcoming_event->event_id }}">Here's your match</a>.
                                @endif
                            @else
                                @if ($upcoming_event->can_claim_match)
                                    @if (isset($upcoming_event->time_until_can_re_request_match) && $upcoming_event->time_until_can_re_request_match)
                                        @php $time_until_can_re_request_match = ceil($upcoming_event->time_until_can_re_request_match / 60) @endphp
                                        @if ($time_until_can_re_request_match == 1)
                                            You can retry the matching algorithm in {{ $time_until_can_re_request_match }} minute.
                                        @else
                                            You can retry the matching algorithm in {{ $time_until_can_re_request_match }} minutes.
                                        @endif
                                    @else
                                        @if ($upcoming_event->bounty_hunt)
                                            <a href="/hunt?event_id={{ $upcoming_event->event_id }}" class="bright">You can now choose who you will hunt</a>!
                                        @else
                                            <a href="/match-me?event_id={{ $upcoming_event->event_id }}" class="bright">You can now request your match!</a>
                                        @endif
                                        
                                    @endif
                                @else
                                    @if (isset($upcoming_event->seconds_till_user_can_match))
                                        @if ($upcoming_event->seconds_till_user_can_match > 360000)
                                            You can get your match in about {{ ceil($upcoming_event->seconds_till_user_can_match / 60 / 60 / 24) }} days.
                                        @else
                                            @if ($upcoming_event->seconds_till_user_can_match > 3600)
                                                You can get your match in about {{ ceil($upcoming_event->seconds_till_user_can_match / 60 / 60) }} hours.
                                            @else
                                                You can get your match in less than one hour!
                                            @endif
                                        @endif
                                        @if ($random_ok)
                                            <a href="/potential-match?event_id={{ $upcoming_event->event_id }}">You will most likely be matched to one of these users</a>.
                                        @else
                                            <a href="/potential-match?event_id={{ $upcoming_event->event_id }}">You will be matched to one of these users</a>.
                                        @endif
                                    @else
                                        @if ($upcoming_event->attending_event_id)
                                            @if ($upcoming_event->signups_still_needed)
                                                More signups are needed before anyone will be matched.
                                            @endif
                                        @else
                                            You are not signed up for this event.
                                        @endif
                                    @endif
                                @endif
                            @endif
                        </td>
                    </tr>
                </table>
                <br>
            @endforeach
        <input type="submit" class="yesyesyes" value="Submit event attendance changes">
        <br>
        </form>
    @endif
@else
    When new events are added, they will appear here. Then you can sign up to be matched. Or, contact us if you'd like us to set up a new event.
@endif

<ol>

<li>Sign up for events by checking the boxes and clicking Submit.</li>

<li>
@if ($unrated_users)
    @if ($number_photos)
        <a href="/profile/compatible?" class="bright">Choose who you'd like to meet ({{ count($unrated_users) }} left to view)</a>.
    @else
        Once you have uploaded a photo, you can view other users' profiles and choose who you'd like to meet.
    @endif
@else
    Let us know who you'd like to meet.
    @foreach ($upcoming_events_and_signup_status as $upcoming_event)
        @if ($upcoming_event->attending_event_id)
            You have viewed all profiles. Come back later to see new arrivals.
        @endif
        @php ($viewed_all = 1)
        @break
    @endforeach
    @if ($recently_updated_users)
        <a href="/profile/{{ $recently_updated_users[0]->id }}/{{ $recently_updated_users[0]->wasteland_name_hyphenated }}?review=1" class="bright">Some profiles were recently updated</a>.
    @endif
@endif
</li>

<li>Check here a few days before the event to find out who you've been matched with.</li>

<li>At the event, seek out your match and introduce yourself.</li>

</ol>
