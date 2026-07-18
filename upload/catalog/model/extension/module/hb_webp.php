<?php
class ModelExtensionModuleHbWebp extends Model {
    public function getUncompressedImages(){
		$sql = "SELECT * FROM `".DB_PREFIX."image_cache` ORDER BY RAND() LIMIT 0, ".$this->config->get('hb_webp_cron_limit');
		$query = $this->db->query($sql);
		return $query->rows;
	}

    public function getCachedImages(){		
		$this->deleteAll();
		
		$image_cache_folder = DIR_IMAGE.'cache/';
		
		if(is_dir($image_cache_folder)) {
			$files = $this->getDirContents($image_cache_folder);
			
			foreach ($files as $file) {
				if (!empty($file)) {
					$this->insertPath($file);
				}
			}
			$this->addlog('Uncompressed WebP images logged to database!');
		}else{
            $this->addlog('No Image Cache folder found!');
		}
	}

    public function getDirContents($dir, &$results = array()) {		
		$files = scandir($dir);
	
		foreach ($files as $key => $value) {
			$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
			if (!is_dir($path)) {
				if ((strpos($path, '.html') === false) and (strpos($path, '.gif') === false) and (strpos($path, '.webp') === false)) {
					$wp_path 		= utf8_substr($path, 0, utf8_strrpos($path, "."));
					$wp_path		= str_replace('/cache/','/webp/',$wp_path);
					$wp_path		= str_replace('\\cache\\','\\webp\\',$wp_path); //localhost windows testing
					if (!file_exists($wp_path.'.webp') and strpos($wp_path, '.webp') === false) {
						$results[] = $path;
					}
				}
			} else if ($value != "." && $value != "..") {
				$this->getDirContents($path, $results );
				//$results[] = $path; //stores the directory name
			}
		}
	
		return $results;
	}

    public function insertPath($path){
		$this->db->query("INSERT INTO `".DB_PREFIX."image_cache` (`path`) VALUES ('".$this->db->escape($path)."')");
	}

    public function deleteAll(){
		$this->db->query("TRUNCATE`".DB_PREFIX."image_cache`");
	}

    public function deleteId($id){
		$this->db->query("DELETE FROM `".DB_PREFIX."image_cache` WHERE `id` = '".(int)$id."'");
	}

    public function addlog($text = ''){
        if (!file_exists(DIR_LOGS)) {
            mkdir(DIR_LOGS, 0777, true);
        }

        $file = DIR_LOGS . 'hb_webp.txt';

        if (file_exists($file)) {
            $size = filesize($file);
            if ($size > 5242880){
                $handle = fopen($file, 'w+');
                fclose($handle);
            }
        }

        $fp = fopen($file, 'a');
        fwrite($fp, "\r\n".date('d-M-Y G:i:s A') . ' - ' .$text);
        fclose($fp);
    }
}