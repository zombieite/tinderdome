{{--
                @if ( $recently_updated_users && count($recently_updated_users) >= 5 )
                    <h2>Recently updated profiles</h2>
                    @foreach ($recently_updated_users as $recently_updated_user)
                        <div class="centered_block">
                            <a href="/profile/{{ $recently_updated_user->id }}/{{ $recently_updated_user->wasteland_name_hyphenated }}"><img src="/uploads/image-{{ $recently_updated_user->id }}-1.jpg" style="height:100px;"></a>
                            <br>
                            <a href="/profile/{{ $recently_updated_user->id }}/{{ $recently_updated_user->wasteland_name_hyphenated }}">{{ $recently_updated_user->name }}</a>
                        </div>
                    @endforeach
                @else
                    @if (count($leaderboard))
                        <h2>Meet our top {{ $leader_count }} heroes... and {{ $nonleader_count }} others.</h2>
                        @foreach ($leaderboard as $leader)
                        <div class="centered_block">
                            @if ($leader['number_photos'])
                                <a href="/profile/{{ $leader['profile_id'] }}/{{ $leader['wasteland_name_hyphenated'] }}"><img src="/uploads/image-{{ $leader['profile_id'] }}-1.jpg" style="height:100px;"></a> @endif
                            <br>
                            @if ($leader['missions_completed']['points'] > 0)
                                {{ $titles[$leader['title_index']] }}
                            @endif
                            {{ $leader['wasteland_name'] }} &middot; {{ $leader['missions_completed']['points'] }}
                        </div>
                        @endforeach
                    @else
--}}
                        <iframe style="width:100%;max-width:720px;height:480px" src="https://www.youtube.com/embed/kdXWJ4crKkE" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
{{--
                    @endif
                @endif
--}}
