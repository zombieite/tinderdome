@extends('layouts.app')
@section('content')
@if (isset($update_errors))
	@if ($update_errors)
		<h2>Error updating images: {{ $update_errors }}</h2>
	@endif
@endif

<form method="POST" action="" enctype="multipart/form-data">
{{ csrf_field() }}

@for ($i = 1; $i <= $max_photos; $i++)
	@if ($i <= $number_photos)
		<div class="image_upload_block">
			<img src="/uploads/image-{{ $profile_id }}-{{ $i }}.jpg" style="height:150px;">
			<br>{{ $i }}
		</div>
	@else
	@endif
	</div>
@endfor
<br>
<label for="image1">Upload an image.</label>
Please make sure your image file is a maximum of 2MB, or you might get an error.
<br><br>
<input type="file" name="image1" value="image" id="image1">
<br><br>
<button id="submit" type="submit" class="yesyes">
Submit changes
</button>
</form>

@endsection
