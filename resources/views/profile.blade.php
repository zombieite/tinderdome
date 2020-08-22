@extends('layouts.app')
@section('content')
@if ($is_my_match and $choice !== 0 and $choice !== -1)
    @include('profile_is_an_unfound_match', [])
@elseif (!$is_my_match && !$is_me && $unchosen_user_id != 1)
    @include('not_match_but_needs_rating_form', [])
@else
    <h1>{{ $wasteland_name }}</h1>
@endif

@if ($recently_updated_users)
    <br><a href="/profile/{{ $recently_updated_users[0]->id }}/{{ $recently_updated_users[0]->wasteland_name_hyphenated }}?review=1" class="bright">Review another recently-updated profile</a>
@endif

@if ($missions_completed)
    <h1><span class="dull">{{ $titles[$title_index] }}</span> {{ $wasteland_name }}@if ($campaigning) is running for the office of Prezident of the Restored United States of Murica! @endif    </h1>
Missions completed: {{ $missions_completed }}.
@else
<h1>{{ $wasteland_name }}</h1>
@endif

@if ($show_how_to_find_me || $share_info || $is_me)
	@if ($how_to_find_me || $share_info)
		@if ($profile_id == 1)
		@else
			Do not share screenshots of this page. This information is confidential.
		@endif
		@if ($how_to_find_me)
			How to find {{ $wasteland_name }}:<br>
            <div class="profile_search_block">
			    {{ $how_to_find_me }}
            </div>
            <br><br>
		@endif
	@endif
@endif
@if ($share_info)
	<h3><a href="mailto:{{ $share_info }}" class="bright">{{ $share_info }}</a></h3>
@endif
@if ($events)
    @foreach ($events as $event)
        <h3>Attending {{ $event->event_long_name }}</h3>
    @endforeach
@endif
@if ($gender)
	Gender: {{ $gender === 'M' ? 'Man' : ($gender === 'W' ? 'Woman' : 'Other') }}.
@endif
@if ($birth_year)
	@if ($birth_year === 1959)
		Born before 1960.
	@else
		Born in the {{ intval($birth_year / 10) * 10 }}s.
	@endif
@endif
@if ($height)
	@if ($height < 60)
		Height: Under 5 feet.
	@elseif ($height > 72)
		Height: Over 6 feet.
	@else
		Height: {{ floor($height / 12) }}&apos;{{ $height % 12 }}&quot;.
	@endif
@endif
@if ($hoping_to_find_love or $hoping_to_find_friend or $hoping_to_find_enemy)
	Open to
	@if ($hoping_to_find_love)
		finding a new friend or romantic partner.
	@elseif ($hoping_to_find_friend)
		making a new friend.
	@endif
	@if ($hoping_to_find_enemy)
		@if ($hoping_to_find_love or $hoping_to_find_friend)
			Or
		@endif
		making an enemy.
	@endif
@endif
{{--
@if ($gender_of_match)
	Prefers to meet {{ $gender_of_match === 'M' ? 'men' : ($gender_of_match === 'W' ? 'women' : 'gender other') }}.
@endif
@if ($gender_of_match && $gender_of_match_2 && ($gender_of_match != $gender_of_match_2))
	Or {{ $gender_of_match_2 === 'M' ? 'men' : ($gender_of_match_2 === 'W' ? 'women' : 'gender other') }}.
@endif
--}}
@if ($description)
	<br>
	<br>
    <div class="profile_search_block">
    	{!! nl2br(e($description)) !!}
    </div>
@endif
<br>
<br>
@for ($i = 1; $i <= $number_photos; $i++)
	<a target="_blank" href="/uploads/image-{{ $profile_id }}-{{ $i }}.jpg{{ $image_query_string }}"><img src="/uploads/image-{{ $profile_id }}-{{ $i }}.jpg{{ $image_query_string }}" style="max-width:100%;"></a>
@endfor
@if ($we_know_each_other)
    <br><br>
    @include('comment_form', ['action' => '/profile/comment', 'user_id_to_rate' => $unchosen_user_id])
@endif
@if ($comments)
    <ul>
    @foreach ($comments as $comment)
        @if ($comment->approved)
            <li>
                <div class=" profile_search_block ">
                    <div style="display:inline-block;">
                        @if ($comment->user_number_photos)
                            <a href="/profile/{{ $comment->commenting_user_id }}/{{ $comment->commenting_user_wasteland_name_hyphenated }}"><img src="/uploads/image-{{ $comment->commenting_user_id }}-1.jpg" style="height:50px;"></a>
                        @endif
                    </div>
                    <div style="display:inline-block;">
                        <a href="/profile/{{ $comment->commenting_user_id }}/{{ $comment->commenting_user_wasteland_name_hyphenated }}">{{ $comment->name }}</a> {{ \Carbon\Carbon::parse($comment->created_at)->format('Y-m-d') }}:
                        {{ $comment->comment_content }}
                    </div>
                </div>
            </li>
        @elseif ($logged_in_user && $comment->commenting_user_id === $logged_in_user->id)
            <li>
                <div class=" profile_search_block ">
                    <div style="display:inline-block;">
                        @if ($comment->user_number_photos)
                            <a href="/profile/{{ $comment->commenting_user_id }}/{{ $comment->commenting_user_wasteland_name_hyphenated }}"><img src="/uploads/image-{{ $comment->commenting_user_id }}-1.jpg" style="height:50px;"></a>
                        @endif
                    </div>
                    <div style="display:inline-block;">
                        <span class="bright">Your comment will not become visible until it is approved.</span>
                        <a href="/profile/{{ $comment->commenting_user_id }}/{{ $comment->commenting_user_wasteland_name_hyphenated }}">{{ $comment->name }}</a>:
                        {{ $comment->comment_content }}
                    </div>
                </div>
            </li>
        @endif
    @endforeach
    </ul>
@endif
@if ($logged_in_user && $logged_in_user->id === 1)
	<br><br>
	<form method="POST" style="width:100%;text-align:right;">
		{{ csrf_field() }}
		<button type="submit" name="reset_password" class="no">
			(ADMIN ONLY) Reset password
		</button>
		<input type="hidden" name="user_id_to_reset" value="{{ $profile_id }}">
	</form>
@endif
@endsection
