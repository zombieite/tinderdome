@extends('layouts.app')
@section('content')
@if ($errors)
	<h2>Error updating images: {{ $errors }}</h2>
@endif

@for ($i = 1; $i <= $number_photos; $i++)
	<div class="profile_search_block">
		<img src="/uploads/image-{{ $profile_id }}-{{ $i }}.jpg?time={{ $time }}" style="height:150px;">
		<br>Image {{ $i }}
	</div>
@endfor

<form method="POST" action="" enctype="multipart/form-data">
	{{ csrf_field() }}
	<br>
	<label for="submit">Upload an image.</label>
	Please make sure your image file is a maximum of 2MB.
	<br><br>
	<select name="imagenum">
		@if ($number_photos < $max_photos)
			<option value="new">New image</option>
		@endif
		@for ($i = 1; $i <= $number_photos; $i++)
			<option value="{{ $i }}">Replace image {{ $i }}</option>
		@endfor
	</select>
	<br><br>
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
