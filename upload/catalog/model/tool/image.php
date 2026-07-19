<?php
class ModelToolImage extends Model {
	public function resizeProportional($filename, $max_width, $max_height) {
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return;
		}

		$image_size = getimagesize(DIR_IMAGE . $filename);

		if (!$image_size || empty($image_size[0]) || empty($image_size[1])) {
			return;
		}

		$scale = min((int)$max_width / $image_size[0], (int)$max_height / $image_size[1], 1);
		$width = max(1, (int)round($image_size[0] * $scale));
		$height = max(1, (int)round($image_size[1] * $scale));

		return $this->resize($filename, $width, $height);
	}

	public function resizeCrop($filename, $width, $height) {
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		$image_old = $filename;
		$image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-crop-' . (int)$width . 'x' . (int)$height . '.' . $extension;

		if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);

			$supported_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);

			if (defined('IMAGETYPE_WEBP')) {
				$supported_types[] = IMAGETYPE_WEBP;
			}

			if (!in_array($image_type, $supported_types)) {
				return DIR_IMAGE . $image_old;
			}

			$path = '';
			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			$target_ratio = $width / $height;
			$source_ratio = $width_orig / $height_orig;
			$crop_x = 0;
			$crop_y = 0;
			$crop_width = $width_orig;
			$crop_height = $height_orig;

			if ($source_ratio > $target_ratio) {
				$crop_width = (int)round($height_orig * $target_ratio);
				$crop_x = (int)round(($width_orig - $crop_width) / 2);
			} elseif ($source_ratio < $target_ratio) {
				$crop_height = (int)round($width_orig / $target_ratio);
				$crop_y = (int)round(($height_orig - $crop_height) / 2);
			}

			$image = new Image(DIR_IMAGE . $image_old);
			$image->crop($crop_x, $crop_y, $crop_x + $crop_width, $crop_y + $crop_height);
			$image->resize((int)$width, (int)$height);
			$image->save(DIR_IMAGE . $image_new, 92);
		}

		$image_new = str_replace(' ', '%20', $image_new);

		if ($this->request->server['HTTPS']) {
			return $this->config->get('config_ssl') . 'image/' . $image_new;
		}

		return $this->config->get('config_url') . 'image/' . $image_new;
	}

	public function resize($filename, $width, $height) {
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '.' . $extension;

		if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);
				 
			$supported_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);

			if (defined('IMAGETYPE_WEBP')) {
				$supported_types[] = IMAGETYPE_WEBP;
			}

			if (!in_array($image_type, $supported_types)) {
				return DIR_IMAGE . $image_old;
			}
						
			$path = '';

			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			if ($width_orig != $width || $height_orig != $height) {
				$image = new Image(DIR_IMAGE . $image_old);
				$image->resize($width, $height);
				$image->save(DIR_IMAGE . $image_new);
			} else {
				copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
			}
		}
		
		$image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
		
		if ($this->request->server['HTTPS']) {
			return $this->config->get('config_ssl') . 'image/' . $image_new;
		} else {
			return $this->config->get('config_url') . 'image/' . $image_new;
		}
	}
}
