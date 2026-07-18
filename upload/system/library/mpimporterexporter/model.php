<?php
namespace MpImporterExporter;
use \Model as OpenCartModel;
class Model extends OpenCartModel {

	public function __construct($registry) {
		parent :: __construct($registry);

	}

	public function cleanKeyWord($keyword) {
		// remove white space and noise
		// https://stackoverflow.com/questions/2109325/how-do-i-strip-all-spaces-out-of-a-string-in-php/2109339
		// https://www.php.net/manual/en/function.preg-replace.php
		$keyword = preg_replace('/\s+/', '', $keyword);
		// non-breaking utf-8 0xc2a0 space and preg_replace strange behaviour
		// https://stackoverflow.com/questions/12837682/non-breaking-utf-8-0xc2a0-space-and-preg-replace-strange-behaviour/12838189#12838189
		// $str=preg_replace('~\xc2\xa0~', 'X', $str); OK
		// $str=preg_replace('~\x{C2A0}~siu', 'W', $str); NOT WORKS
		// non-breaking space is not found (and replaced).

		// Why? What is wrong with second regexp?

		// The format \x{C2A0} is correct, also I used u flag.

		// ANSWER

		// Actually the documentation about escape sequences in PHP is wrong. When you use \xc2\xa0 syntax, it searches for UTF-8 character. But with \x{c2a0} syntax, it tries to convert the Unicode sequence to UTF-8 encoded character.

		// A non breaking space is U+00A0 (Unicode) but encoded as C2A0 in UTF-8. So if you try with the pattern ~\x{00a0}~siu, it will work as expected.
		$keyword = preg_replace('~\x{00a0}~','',$keyword);

		return $keyword;
	}

	// get image from external url starts
	public function parseImage($img, $path, $data) {
		if (empty($img)) {
			return $img;
		}

		$urlofsamewebsite = $this->isImgUrlOfSameWebsite($img, $path, $data);

		if ((utf8_substr($img, 0, 7) === "http://" || utf8_substr($img, 0, 8) === "https://") && $this->isFileContentsExistInHeaders($img) && !$urlofsamewebsite['exists'] )	{
			return $this->saveImgFromURL($img, $path);
		} else if ($urlofsamewebsite['exists']) {
			return str_replace($urlofsamewebsite['urls']['web_store.url'],'',$img);
		} else {
			return $img;
		}
	}

	// check if image URL is website url. If true then do not save again as image already exists on server, just use part after "image" to store in db
	public function isImgUrlOfSameWebsite($img, $path, $data) {
		$exists = [];
		$urls = [];
		if ((utf8_substr($img, 0, 7) === "http://" || utf8_substr($img, 0, 8) === "https://")) {

			// https://www.php.net/manual/en/function.parse-url.php
			// Array
			// (
			//     [scheme] => https
			//     [host] => www.php.net
			//     [path] => /manual/en/function.parse-url.php
			// )
			$img_folder = basename(DIR_IMAGE) . '/';
			$parse_url = parse_url($img);


			if (!empty($data['store']) && !empty($data['web_stores'])) {
				foreach ($data['store'] as $store_id) {
					if (isset($data['web_stores'][ $store_id ])) {
						$web_store = $data['web_stores'][ $store_id ];
						if ($parse_url['scheme'] === "http") {
							$exists[] = (utf8_strpos($img, $web_store['url'] . $img_folder) !== false) ? 'true' : 'false';
							$urls[] = ['img' => $img, 'web_store.url' => $web_store['url'] . $img_folder];
						}
						if ($parse_url['scheme'] === "https") {
							$exists[] = (utf8_strpos($img, $web_store['ssl'] . $img_folder) !== false) ? 'true' : 'false';
							$urls[] = ['img' => $img, 'web_store.url' => $web_store['ssl'] . $img_folder];
						}
					}
				}
			}
		}

		$i = array_search('true', $exists);
		return ['exists' => in_array('true', $exists), 'urls' => isset($urls[$i]) ? $urls[$i] : ['img' => $img, 'web_store.url' => ''] ];
	}


	public function saveImgFromURL($url, $path) {
			$img = file_get_contents($url);
			$spath = DIR_IMAGE . $path;

			$this->mkDir(DIR_IMAGE . $path);

			$name = basename(html_entity_decode($url, ENT_QUOTES, 'UTF-8'));

			$name = str_replace([' ', '&nbsp;', '%20'], '_', $name);

			if(file_exists($spath . $name)) {
				$p_info = pathinfo($spath . $name);
				if($p_info) {
					$n_name = $p_info['filename'] .'_'. time() .'.'.$p_info['extension'];
				} else {
					$n_name = $name;
				}
			} else {
				$n_name = $name;
			}

			file_put_contents($spath . $n_name, $img);
			return $path . $n_name;

	}

	public function mkDir($dir,$permission=0777) {
		if(!is_dir($dir)) {
			$oldmask = umask(0);
			mkdir($dir, $permission);
			umask($oldmask);
		}
	}

	public function isFileContentsExistInHeaders($url, $response_code = 200) {
	    return (utf8_substr(get_headers($url)[0], 9, 3) == $response_code);
	}

	public function preg_trim($subject) {
    	$regex = "/\s*(\.*)\s*/s";
    	if (preg_match ($regex, $subject, $matches)) {
	        $subject = $matches[1];
	    }

	    return $subject;
	}

	// get image from external url ends

	/**
	 * @07 jun, 2024
	 *
	 * https://stackoverflow.com/questions/9560723/mysql-query-replace-null-with-empty-string-in-select
	 *
	 * If expr1 is not NULL, IFNULL() returns expr1; otherwise it returns expr2.
	 *
	 */
	protected function ifnull($expr1, $expr2 = '') {
		if (!is_null($expr1)) {
			return $expr1;
		}
		return $expr2;
	}
}