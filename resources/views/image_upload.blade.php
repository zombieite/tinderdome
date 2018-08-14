@extends('layouts.app')
@section('content')
@if ($new_user && $number_photos > 0)
	<h2>Signup complete! You can upload more photos or <a href="/profile/compatible?">choose who you'd like to meet</a>.</h2>
@endif
@if ($random_ok)

@else

@endif
@if ($errors)
	<h2>Error updating images: {{ $errors }}</h2>
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
	<label for="submit">Upload an image.</label>
	<br><br>
	Your first image must be a picture of you, but you can wear a mask if you want to be mysterious. Your file must be no more than 2MB.
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
		Submit changes
	</button>
	<br><br>
	<div style="width:100%;text-align:right;">
		<button id="submit" name="delete" value="1" type="submit" class="no">
			Delete all images
		</button>
	</div>
</form>

@endsection
