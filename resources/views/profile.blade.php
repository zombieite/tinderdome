@extends('layouts.app')
@section('content')

@if ($is_my_match and $choice !== 0 and $choice !== -1)
    @include('profile_is_an_unfound_match', [])
@elseif (!$is_me && $profile_id != 1)
    @include('not_match_but_needs_rating_form', [])
@endif

@if ($recently_updated_users)
    <br><a href="/profile/{{ $recently_updated_users[0]->id }}/{{ $recently_updated_users[0]->wasteland_name_hyphenated }}?review=1" class="bright">Review another recently-updated profile</a>
@endif

@include('profile_name_section', [])

@if ($missions_completed)
    <span class="labelclass">Missions completed:</span> {{ $missions_completed }}<br>
@endif

@if ($how_to_find_me)
	@if ($profile_id == 1)
	@else
		<span class="labelclass">Do not share screenshots of this page. This information is confidential.</span>
	@endif
	@if ($how_to_find_me)
		<span class="labelclass">How to find {{ $wasteland_name }}:</span><br><div class="profile_search_block"><span class="bright">{{ $how_to_find_me }}</span></div><br><br>
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

{{--
@if ($gender)
	<span class="labelclass">Gender:</span> {{ $gender === 'M' ? 'Man' : ($gender === 'W' ? 'Woman' : 'Other') }}.
@endif
@if ($birth_year)
	@if ($birth_year === 1959) <span class="labelclass">Born before</span> 1960. @else <span class="labelclass">Born in the</span> {{ intval($birth_year / 10) * 10 }}s. @endif
@endif
@if ($height)
    @include('height', [])
@endif
@if ($hoping_to_find_love or $hoping_to_find_friend or $hoping_to_find_enemy)
    @include('hoping_to_find', [])
@endif
--}}

@if ($description)
	<div class="profile_search_block">{!! nl2br(e($description)) !!}</div>
@endif
<br><br>
@for ($i = 1; $i <= $number_photos; $i++)
	<a target="_blank" href="/uploads/image-{{ $profile_id }}-{{ $i }}.jpg{{ $image_query_string }}"><img src="/uploads/image-{{ $profile_id }}-{{ $i }}.jpg{{ $image_query_string }}" style="max-width:100%;"></a>
@endfor

@if ($we_know_each_other)
    <br><br>@include('comment_form', ['action' => '/profile/comment', 'user_id_to_rate' => $unchosen_user_id])
@endif

@if ($comments)
    @include('profile_comments', [])
@endif

@if ($logged_in_user && $logged_in_user->id === 1)
    @include('admin_password_reset', [])
@endif

@endsection
