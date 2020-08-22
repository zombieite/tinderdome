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
