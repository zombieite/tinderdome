@if ($is_my_match)
@else
    <h3>Would you enjoy meeting this user? @if ($count_left)({{$count_left}} profiles left to view) @endif</h3>
@endif
@include('rating_form', ['action' => '/profile/compatible?', 'user_id_to_rate' => $unchosen_user_id, 'current_choice' => $choice, 'curse_interface' => $curse_interface])
