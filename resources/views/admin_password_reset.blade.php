	<br><br>
	<form method="POST" action="/admin/password-reset" style="width:100%;text-align:right;">
		{{ csrf_field() }}
		<button type="submit" class="no">
			(SITE OWNER ONLY) Reset password
		</button>
		<input type="hidden" name="user_id_to_reset" value="{{ $profile_id }}">
	</form>
