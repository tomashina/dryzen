<?php

namespace Mpimporterexporter;

class Mpimporttmpfiles {
	/**
	 * Manage .json files creation, deletion at particular location i.e: inside storage folder
	 * https://www.php.net/manual/en/resource.php
	 * https://stackoverflow.com/questions/66871564/php-8-assign-resource-as-property-parameter-or-return-type
	 */
	const FORMAT = ".json";
	const MAX_RECORDS = 1000;
	const TMP_DIR = 'mpoints_tmp_impexp/';

	// i.e: category_import, product_import, etc
	private $action;
	private $data = [];
	private $filename;
	private $path;
	private $debug = [];

	public function getLocation() {
		return DIR_CACHE . self :: TMP_DIR;
	}

	public function open($action) {
		$this->mkDir(DIR_CACHE . self :: TMP_DIR);

		// explode action to detect if it having path
		$parts = explode("/", $action);

		$filename = array_pop($parts);

		$route = '';
		foreach ($parts as $key => $value) {
			$route .= $value;
			$this->mkDir(DIR_CACHE . self :: TMP_DIR . $route .'/');
		}
		// $this->mkDir(DIR_CACHE . self :: TMP_DIR . $action . '/');

		$this->path = DIR_CACHE . self :: TMP_DIR . ($route ? $route . '/' : $route);

		$this->action = $filename;

		$this->debug[] = "route : {$route}";
		$this->debug[] = "<br/>";
		$this->debug[] = "this.path : {$this->path}";
		$this->debug[] = "<br/>";
		$this->debug[] = "this.action : {$this->action}";
		$this->debug[] = "<br/>";
		$this->debug[] = "this.fopen()";
		$this->debug[] = "<br/>";

		$this->fopen();

		$this->debug[] = "this.path : {$this->path}";
		$this->debug[] = "<br/>";
		$this->debug[] = "this.filename : {$this->filename}";
		$this->debug[] = "<br/>";

		// use this.getAllFiles(this.action . '*') to complare this.getFiles() results. if any dicrepency then call this.emptyDir(this.path);

		$files = $this->getFiles();
		$files1 = $this->getAllFiles($this->action . '*');

		$this->debug[] = "this.getFiles()";
		$this->debug[] = print_r($files, 1);
		$this->debug[] = "<br/>";

		$this->debug[] = "this.getAllFiles({$this->action}*)";
		$this->debug[] = print_r($files1, 1);
		$this->debug[] = "<br/>";

		// if we see any difference then empty current path.
		$diff = array_diff($files, $files1);

		$this->debug[] = "diff";
		$this->debug[] = print_r($diff, 1);
		$this->debug[] = "<br/>";

		if ($diff) {
			$this->debug[] = "calling this.emptyDir({$this->path})";
			$this->debug[] = "<br/>";

			// delete all files and folders inside given location. one time only temporary
			$this->emptyDir($this->path);
		}

		// unlink any existing file
		if (file_exists($this->path . $this->filename)) {
			$this->debug[] = "file_extis(true: {$this->path} {$this->filename})";
			$this->debug[] = "<br/>";
			@unlink($this->path . $this->filename);
		}

	}

	protected function emptyDir($path) {
		$this->debug[] = "emptyDir().path : {$path}";
		$this->debug[] = "<br/>";

		$dirs = scandir($path);

		$this->debug[] = "emptyDir().scandir()";
		$this->debug[] = "<br/>";
		$this->debug[] = print_r($dirs, 1);
		$this->debug[] = "<br/>";

		foreach ($dirs as $dir) {
			$dir = trim($dir);

			$this->debug[] = "emptyDir().loop dirs - dir {$dir} ";
			$this->debug[] = "<br/>";

			if (in_array($dir, ['.','..'])) {
				continue;
			}

			$this->debug[] = "emptyDir().checking dir is folder or file : {$path}{$dir} ";
			$this->debug[] = "<br/>";

			if (is_dir($path . $dir)) {

				$this->debug[] = "emptyDir().dir is folder : {$path}{$dir} ";
				$this->debug[] = "<br/>";

				$this->debug[] = "calling self";
				$this->debug[] = "<br/>";

				$this->emptyDir($path . $dir .'/');

				$this->debug[] = "after calling self";
				$this->debug[] = "<br/>";
				$this->debug[] = "emptyDir().dir is folder : {$path}{$dir} should be empty.";

				rmdir($path . $dir);
			}


			if (is_file($path . $dir)) {
				$this->debug[] = "emptyDir().dir is file : {$path}{$dir} ";
				$this->debug[] = "<br/>";

				$this->debug[] = "unlink ({$path}{$dir})";
				$this->debug[] = "<br/>";

				unlink($path . $dir);
			}

			$this->debug[] = "emptyDir().loop end dirs - dir {$dir} ";
			$this->debug[] = "<br/>";
		}
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	protected function fopen($i = 0) {
		$this->filename = "tmp." . $this->action . $i . self :: FORMAT;
	}

	protected function getAllFiles($name) {

		$this->debug[] = "this.getAllFiles({$name})";
		$this->debug[] = "<br/>";


		$filename = "tmp." . $name . self :: FORMAT;

		$this->debug[] = "filename : {$filename}";
		$this->debug[] = "<br/>";

		$return_files = [];

		$this->debug[] = "glob ({$this->path}{$filename})";
		$this->debug[] = "<br/>";

		$files = glob($this->path . $filename);

		$this->debug[] = "files : {$filename}";
		$this->debug[] = print_r($files, 1);
		$this->debug[] = "<br/>";

		foreach ($files as $file) {
			$return_files[] = str_replace($this->path, '', $file);
		}

		$this->debug[] = "return files";
		$this->debug[] = print_r($return_files, 1);
		$this->debug[] = "<br/>";

		return $return_files;

	}

	public function save() {

		$files = $this->getAllFiles($this->action . '*');

		$file_data = [];

		foreach ($files as $file) {

			$data = $this->getContent($file);
			if ($data) {
				// $file_data = array_merge($file_data, $data);
				$file_data = ($file_data + $data);
			}
		}

		if ($file_data) {
			// $this->data = array_merge($this->data, $file_data);
			$this->data = ($this->data + $file_data);
		}

		// check length of data array, as soon as reach 1000 save into file.
		// https://www.php.net/manual/en/function.array-chunk.php
		$chunks = array_chunk($this->data, self :: MAX_RECORDS, true);

		foreach ($chunks as $k => $chunk) {
			$this->fopen($k);

			$this->write($chunk);
		}

		// close and reset, so we can read them next time when adding more data
		$this->close();
	}

	protected function write($message) {
		file_put_contents($this->path . $this->filename, json_encode($message));
	}

	protected function close() {

		// empty data
		$this->data = [];
	}

	public function getFiles() {
		$files = [];

		$dirs = scandir($this->path);

		foreach ($dirs as $dir) {
			if (in_array($dir, ['.','..'])) {
				continue;
			}
			if (is_file($this->path . $dir)) {

				// get files that name hold in this.action

				if (strpos($dir, $this->action) !== false && strpos($dir, $this->action) == 4) {
					$files[] = $dir;
				}
			}
		}

		return $files;
	}

	public function get($key) {
		$return = null;
		$this->debug[] =  "this.get({$key})";
		$this->debug[] =  "<br/>";

		$dirs = scandir($this->path);
		$this->debug[] = print_r($dirs, 1);
		$this->debug[] =  "<br/>";
		$this->debug[] =  "key : {$key}";
		$this->debug[] =  "<br/>";

		foreach ($dirs as $dir) {
			if (in_array($dir, ['.','..'])) {
				continue;
			}

			$content = $this->getContent($dir);
			if (isset($content[$key])) {
				$return = $content[$key];
				break;
			}
		}

		/**
		 * 12 july, 2023
		 * try to find result from this.data
		 *
		 * */
		// if (!$return) {
		// 	if (isset($this->data[$key])) {
		// 		$return = $this->data[$key];
		// 	}
		// }

		return $return;
	}

	public function getContent($filename) {
		$content = [];
		$this->debug[] = "<br/>";
		$this->debug[] = "getContent.";
		$this->debug[] = "<br/>";
		$this->debug[] = $this->path . $filename;
		$this->debug[] = "<br/>";

		// check filename contain this.action.
		if (strpos($filename, $this->action) !== false && file_exists($this->path . $filename)) {
			// thanks to stack overflow
			// https://stackoverflow.com/questions/5167313/php-problem-filesize-return-0-with-file-containing-few-data
			// clearstatcache();
			// $size = filesize($this->path . $filename);
			// not using filesize function due to abnormal behaviour
			if (is_file($this->path . $filename)) {
				$data = file_get_contents($this->path . $filename, FILE_USE_INCLUDE_PATH, null);

				if ($data) {
					$content = json_decode($data, 1);
				}
			}
		}

		return $content;
	}

	protected function mkDir($dir, $permission=0777) {
		if(!is_dir($dir)) {
			$oldmask = umask(0);
			mkdir($dir, $permission);
			umask($oldmask);
		}
	}

	public function debug() {
		print_r($this->debug);
	}
}