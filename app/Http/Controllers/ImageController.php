<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Util;
use Image;
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
		$image_width               = $image_height * 3;
		$max_photos                = 5;
		$errors                    = '';
		$max_filesize_mb           = 40;
		$max_filesize              = $max_filesize_mb * 1024 * 1024;

		if (isset($_POST['delete'])) {
			\App\Util::delete_user_photos($profile_id);
			$number_photos = 0;
		} elseif (isset($_POST['upload'])) {
			if (isset($_FILES["image"])) {
				$uploaded_file = $_FILES["image"]['tmp_name'];
				if ($uploaded_file) {
					if (isset($_POST['imagenum'])) {
						$image_number = $_POST['imagenum'];
						$adding_new_image = false;
						if ($image_number == 'new') {
						   if ($number_photos < $max_photos) {
								$image_number = $number_photos + 1;
								$adding_new_image = true;
							} else {
								$image_number = 1;
							}
						} elseif (($image_number < 1) || ($image_number > $number_photos)) {
							$image_number = 1;
						}
						$destination = getenv("DOCUMENT_ROOT") . "/uploads/image-$profile_id-$image_number.jpg";
						$size = filesize($uploaded_file);
						if ($size > $max_filesize) {
							$errors .= 'Image file is too large. Please <a href="https://duckduckgo.com/?q=online+image+resizer">resize it</a> and retry.';
						} else {
							try {
								// Decode the temporary upload before writing anything into the public directory.
								$img = \Intervention\Image\ImageManagerStatic::make($uploaded_file);
								$img->orientate();
								$img->heighten($image_height);
								if ($img->width() > $image_width) {
									$img->widen($image_width);
								}
								$img->encode('jpg');
								$img->save($destination);

								if ($adding_new_image) {
									$number_photos++;
								}
							} catch (\Throwable $e) {
								$errors = 'The uploaded file is not a valid image.';
								Log::warning('Invalid image upload', [
									'user_id' => $profile_id,
									'error' => $e->getMessage(),
								]);
							}
						}
					}
				} else {
					$errors = 'Uploaded file temp name not found, file may be too large.';
				}
			} else {
				$errors = 'Image file not found, file may be too large..';
			}
		}

		if ($errors) {

		} else {
			if (isset($_POST['delete']) || isset($_POST['upload'])) {
				DB::update('update users set number_photos = ?, profile_vetted = null, updated_at = now() where id = ? limit 1', [$number_photos, $profile_id]);
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
			'max_filesize_mb'           => $max_filesize_mb,
			'time'                      => $time,
			'new_user'                  => $new_user,
		]);
	}
}
