    @if ($match_knows_you_are_their_match && !$bounty_hunt)
	    <h1 class="bright">{{ $logged_in_user->name }}, you are awaited by {{ $wasteland_name }}!</h1>
        <h2>They have logged in to check their matches. They know you are their match.</h2>
    @else
        <h1 class="bright">{{ $logged_in_user->name }}, your mission is to find {{ $wasteland_name }}!</a>
        @if (!$bounty_hunt)
            <h2>They have not yet checked their matches, but they signed up to be found. They might not know you are their match, but you can still seek them out.</h2>
        @endif
    @endif
	@if ($count_with_same_name)
		@if ($count_with_same_name == 1)
			Another wastelander also goes by the name {{ $wasteland_name }}. Be sure to find the right one.
		@else
			{{ $count_with_same_name }} other wastelanders go by the name {{ $wasteland_name }}. Be sure to find the right one.
		@endif
	@endif
	Your mission is to seek out {{ $wasteland_name }} at {{ $event_long_name }}.
    @if ($match_knows_you_are_their_match && !$bounty_hunt)
    	{{ $wasteland_name }} will be looking for you, too.
    @endif
    You can find them during the event or after.
    This mission won't expire until one of you deletes the mission or deletes their profile.
    If you find them and meet them in person let us know. This will mark your mission as complete.
	<br>
    @if ($ok_to_mark_user_found)
    	@include('rating_form', ['action' => '/profile/compatible?', 'user_id_to_rate' => $unchosen_user_id, 'current_choice' => $choice, 'curse_interface' => $curse_interface])
    @endif
    @if ($recently_updated_users)
        <br><a href="/profile/{{ $recently_updated_users[0]->id }}/{{ $recently_updated_users[0]->wasteland_name_hyphenated }}?review=1" class="bright">Review another recently-updated profile</a><br>
    @endif
	<br>
