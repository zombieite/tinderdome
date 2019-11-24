@extends('layouts.app')
@section('content')
@if ($is_my_match)
	<h1 class="bright">{{ $auth_user->name }}, you are awaited by {{ $wasteland_name }}!</h1>
	@if ($count_with_same_name)
		@if ($count_with_same_name == 1)
			Another wastelander also goes by the name {{ $wasteland_name }}. Be sure to find the right one.
		@else
			{{ $count_with_same_name }} other wastelanders go by the name {{ $wasteland_name }}. Be sure to find the right one.
		@endif
	@endif
	Your mission is to seek out {{ $wasteland_name }} at {{ $event_long_name }}.
	{{ $wasteland_name }} will be looking for you, too.
    You can find them during the event or after.
    This mission won't expire until one of you deletes the mission or deletes their profile.
    If you find them and meet them in person let us know. This will mark your mission as complete.
	<br>
	<br>
	@include('rating_form', ['action' => '/profile/compatible?', 'user_id_to_rate' => $unchosen_user_id, 'current_choice' => $choice])
	<br>
@else
	@if (!$is_me && $unchosen_user_id != 1 )
		<h3>Would you enjoy meeting this user? @if ($count_left)({{$count_left}} profiles left to view) @endif</h3>
		@include('rating_form', ['action' => '/profile/compatible?', 'user_id_to_rate' => $unchosen_user_id, 'current_choice' => $choice])
	@endif
	@if ($missions_completed)
		<h2>{{ $titles[$title_index] }} <span class="bright">{{ $wasteland_name }}</span> &middot; Missions completed: {{ $missions_completed }}</h2>
	@else
		<h2 class="bright">{{ $wasteland_name }}</h2>
	@endif
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
	Gender: {{ $gender === 'M' ? 'Male' : ($gender === 'F' ? 'Female' : 'Other') }}.
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
@if ($gender_of_match)
	Prefers to meet gender: {{ $gender_of_match === 'M' ? 'Male' : ($gender_of_match === 'F' ? 'Female' : 'Other') }}.
@endif
@if ($gender_of_match && $gender_of_match_2 && ($gender_of_match != $gender_of_match_2))
	Or gender: {{ $gender_of_match_2 === 'M' ? 'Male' : ($gender_of_match_2 === 'F' ? 'Female' : 'Other') }}.
@endif
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
        @elseif ($auth_user && $comment->commenting_user_id === $auth_user->id)
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
@if ($auth_user && $auth_user->id === 1)
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
