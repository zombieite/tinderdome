<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Util;
use Image;
use File;
use Log;

class ImageController extends Controller
{
	public function upload() {
		$profile    = Auth::user();
		$profile_id = Auth::id();
		if ($profile_id && $profile) {
			// All good
		} else {
			abort(403);
		}

		$number_photos             = $profile->number_photos;
		$wasteland_name            = $profile->name;
		$wasteland_name_hyphenated = preg_replace('/\s/', '-', $wasteland_name);
		$image_height              = 500;
		$max_photos                = 5;
		$errors                    = '';
		$max_filesize              = 2500000;

		if (isset($_POST['delete'])) {
			$number_photos = 0;
		} elseif (isset($_POST['upload'])) {
			if (isset($_FILES["image"])) {
				$uploaded_file = $_FILES["image"]['tmp_name'];
				if ($uploaded_file) {
					if (isset($_POST['imagenum'])) {
						$image_number = $_POST['imagenum'];
						if ($image_number == 'new') {
						   if ($number_photos < $max_photos) {
								$number_photos++;
								$image_number = $number_photos;
							} else {
								$image_number = 1;
							}
						} elseif (($image_number < 1) || ($image_number > $number_photos)) {
							$image_number = 1;
						}
						$destination = getenv("DOCUMENT_ROOT") . "/uploads/image-$profile_id-$image_number.jpg";
						$size = filesize($uploaded_file);
						if ($size > $max_filesize) {
							$errors .= 'Image file is too large. Please resize it and retry.';
							if ( $_POST['imagenum'] == 'new' ) {
								$number_photos--;
							}
						} else {
							File::copy($uploaded_file, $destination);
							$img = Image::make($destination);
							$img->orientate();
							$img->heighten($image_height);
							$img->encode('jpg');
							$img->save($destination);
						}
					}
				} else {
					$errors = 'Please choose an image file to upload.';
				}
			} else {
				$errors = 'Please choose an image file to upload.';
			}
		}

		if ($errors) {

		} else {
			if (isset($_POST['delete']) || isset($_POST['upload'])) {
				DB::update('update users set number_photos=?, updated_at=now() where id=? limit 1', [$number_photos, $profile_id]);
			}
		}

		$time = time();

		$new_user = false;
		if (isset($_GET['new_user'])) {
			$new_user = true;
		}

		return view('image_upload', [
			'profile_id'                => $profile_id,
			'wasteland_name_hyphenated' => $wasteland_name_hyphenated,
			'max_photos'                => $max_photos,
			'number_photos'             => $number_photos,
			'errors'                    => $errors,
			'time'                      => $time,
			'new_user'                  => $new_user,
		]);
	}
}
