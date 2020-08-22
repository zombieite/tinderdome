	<br><br>
	<form method="POST" style="width:100%;text-align:right;">
		{{ csrf_field() }}
		<button type="submit" name="reset_password" class="no">
			(ADMIN ONLY) Reset password
		</button>
		<input type="hidden" name="user_id_to_reset" value="{{ $profile_id }}">
	</form>
