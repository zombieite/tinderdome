@extends('layouts.app')
@section('content')
@if ($new_user && $number_photos > 0)
	<h2>Account created! You can upload more photos or <a class="bright" href="/">sign up for events on the home page</a>.</h2>
@endif
@if ($errors)
	<h2 class="bright">Error updating images: {!! $errors !!} Problems? You can also just email images to Firebird.</h2>
@endif

@for ($i = 1; $i <= $number_photos; $i++)
	<div class="centered_block">
		<a target="_blank" href="/uploads/image-{{ $profile_id }}-{{ $i }}.jpg?time={{ $time }}"><img src="/uploads/image-{{ $profile_id }}-{{ $i }}.jpg?time={{ $time }}" style="height:150px;"></a>
		<br>Image {{ $i }}
	</div>
@endfor

<form method="POST" action="" enctype="multipart/form-data">
	{{ csrf_field() }}
	<br>
	<label for="submit">
		@if ($number_photos > 0)
			@if ($number_photos === 1)
				You have uploaded 1 image. You can upload up to 5.
			@else
				@if ($number_photos === 5)
					You have uploaded the maximum number of images. You can replace them with new images if you like.
				@else
					You have uploaded {{ $number_photos }} images. You can upload up to 5.
				@endif
			@endif
		@else
			Upload an image. Your first image must be a picture of you, but you can wear a mask if you want to be mysterious.
		@endif
	</label>
	<br><br>
	Image files must be no more than 5MB.
	<br><br>
	@if ($number_photos == 0)
		<input type="hidden" name="imagenum" value="new">
	@else
		<select name="imagenum">
			@if ($number_photos < $max_photos)
				<option value="new">New image</option>
			@endif
			@for ($i = 1; $i <= $number_photos; $i++)
				<option value="{{ $i }}">Replace image {{ $i }}</option>
			@endfor
		</select>
		<br><br>
	@endif
	<input type="file" name="image" value="image" id="image">
	<br><br>
	<button id="submit" name="upload" value="1" type="submit" class="yesyes">
		Submit upload
	</button>
	<br><br>
	<div style="width:100%;text-align:right;">
		<button id="submit" name="delete" value="1" type="submit" class="no">
			Delete all images
		</button>
	</div>
</form>
@if ($number_photos > 0)
	<a href="/profile/{{ $profile_id }}/{{ $wasteland_name_hyphenated }}">View my profile page</a>
@endif

@endsection
