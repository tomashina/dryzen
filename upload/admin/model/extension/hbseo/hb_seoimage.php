<?php
class ModelExtensionHbseoHbSeoimage extends Model {
	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "image_rename_logs` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `old_path` text NOT NULL,
			  `new_path` text,
			  `status` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '1 = success, 2 = missing',
			  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
		)DEFAULT CHARSET=utf8");
			
		if ((version_compare(VERSION,'2.0.0.0','>=' )) and (version_compare(VERSION,'2.3.0.0','<' ))) {
			$ocmod_filename = 'ocmod_image_rename_2200.txt';
			$ocmod_name = 'SEO - Image Rename [2000-2200]';
		}else if ((version_compare(VERSION,'2.3.0.0','>=' )) and (version_compare(VERSION,'2.3.0.2','<=' ))) {
			$ocmod_filename = 'ocmod_image_rename_23xx.txt';
			$ocmod_name = 'SEO - Image Rename [23xx]';
		}else if (version_compare(VERSION,'3.0.0.0','>=' )) {
			$ocmod_filename = 'ocmod_image_rename_3xxx.txt';
			$ocmod_name = 'SEO - Image Rename [3xxx]';
		}
		
		$ocmod_version = $this->hb_extension_version;
		$ocmod_code = 'huntbee_seo_image_rename';	
		$ocmod_author = 'HuntBee OpenCart Services';
		$ocmod_link = 'https://www.huntbee.com/';
		
		$file = DIR_APPLICATION . 'view/template/extension/hbseo/ocmod/'.$ocmod_filename;
		if (file_exists($file)) {
			$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
		}	
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "image_rename_logs`");
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_seo_image_rename'");
	}
	
	public function getrecords($data){
		$sql = "SELECT * FROM `".DB_PREFIX."image_rename_logs`";
		if (isset($data['missing'])) {
			$sql .=  " WHERE status = 2";
		}
		$sql .=  " ORDER BY date_added DESC";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}	

		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getTotalrecords($data){
		$sql = "SELECT * FROM `".DB_PREFIX."image_rename_logs`";	
		if (isset($data['missing'])) {
			$sql .=  " WHERE status = 2";
		}	
		$sql .=  " ORDER BY date_added DESC";
		$results = $this->db->query($sql);
		return $results->num_rows;
	}
	
	public function getDisputes($data){
		$sql = "SELECT * FROM `".DB_PREFIX.$data['table']."` WHERE image NOT LIKE ".$data['folders']." AND trim(image) <> ''";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}	

		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getTotalDisputes($data){
		$sql = "SELECT * FROM `".DB_PREFIX.$data['table']."` WHERE image NOT LIKE ".$data['folders']." AND trim(image) <> ''";		
		$results = $this->db->query($sql);
		return $results->num_rows;
	}
	
	public function getOrgList($data){
		$sql = "SELECT * FROM `".DB_PREFIX.$data['table']."` WHERE image LIKE '%".$this->db->escape($data['target_folder'])."%' AND trim(image) <> ''";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}	

		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getTotalOrgList($data){
		$sql = "SELECT * FROM `".DB_PREFIX.$data['table']."` WHERE image LIKE '%".$this->db->escape($data['target_folder'])."%' AND trim(image) <> ''";		
		$results = $this->db->query($sql);
		return $results->num_rows;
	}
	
	public function getCategoriesName($product_id,$language_id){
		$category = '';
		$results = $this->db->query("SELECT group_concat((select name from " . DB_PREFIX . "category_description where category_id = a.category_id and language_id = ".(int)$language_id.") separator ' / ')as category FROM `" . DB_PREFIX . "product_to_category` a where product_id = '".(int)$product_id."' group by product_id");
		if (isset($results->row['category'])){
			$category = $results->row['category'];
			if (strpos($category,'/') !== false) {
				$cats = explode('/',$category);
				foreach ($cats as $c) {
					$cat_slug[] = $this->urlslug($c);
				}
				$category = implode('/',$cat_slug);
				//return $cats[0]; //multiple category but taking the parent category
			}else{
				$category = $this->urlslug($category);
			}
		}
		return $category;
	}
	
	public function checkimagename($tablename, $image){
    	$results = $this->db->query("SELECT count(*) as count FROM `" . DB_PREFIX . $tablename."` WHERE `image` = '".$this->db->escape($image)."'");
		return $results->row['count'];
	}
	
	public function updateimagefield($image, $newname){
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET image = '".$this->db->escape($newname)."' WHERE image = '".$this->db->escape($image)."'");
		$this->db->query("UPDATE `" . DB_PREFIX . "product_image` SET image = '".$this->db->escape($newname)."' WHERE image = '".$this->db->escape($image)."'");
		$this->db->query("UPDATE `" . DB_PREFIX . "category` SET image = '".$this->db->escape($newname)."' WHERE image = '".$this->db->escape($image)."'");
		$this->db->query("UPDATE `" . DB_PREFIX . "manufacturer` SET image = '".$this->db->escape($newname)."' WHERE image = '".$this->db->escape($image)."'");
		$this->db->query("UPDATE `" . DB_PREFIX . "option_value` SET image = '".$this->db->escape($newname)."' WHERE image = '".$this->db->escape($image)."'");
		$this->db->query("UPDATE `" . DB_PREFIX . "banner_image` SET image = '".$this->db->escape($newname)."' WHERE image = '".$this->db->escape($image)."'");
		
		//$this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET description = replace(description, '".$this->db->escape($image)."', '".$this->db->escape($newname)."')");
		//$this->db->query("UPDATE `" . DB_PREFIX . "category_description` SET description = replace(description, '".$this->db->escape($image)."', '".$this->db->escape($newname)."')");
		//$this->db->query("UPDATE `" . DB_PREFIX . "information_description` SET description = replace(description, '".$this->db->escape($image)."', '".$this->db->escape($newname)."')");
	}
	
	public function getProductsImage($target_folder, $start, $end) {
		$query = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE image NOT LIKE '%".$this->db->escape($target_folder)."%' AND trim(image) <> '' ORDER BY RAND() LIMIT " . (int)$start . "," . (int)$end);
		if ($query->num_rows == 0){
			$query = $this->db->query("SELECT distinct(product_id) as product_id FROM `" . DB_PREFIX . "product_image` WHERE image NOT LIKE '%".$this->db->escape($target_folder)."%' AND trim(image) <> '' ORDER BY RAND() LIMIT " . (int)$start . "," . (int)$end);
			return $query->rows;
		}else{
			return $query->rows;
		}
	}
	
	public function getTotalProductsImage($target_folder) {
		$query = $this->db->query("SELECT count(product_id) as total FROM `" . DB_PREFIX . "product` WHERE image NOT LIKE '%".$this->db->escape($target_folder)."%' AND trim(image) <> ''");
		if ($query->row['total'] == 0){
			$query = $this->db->query("SELECT count(distinct(product_id)) as total FROM `" . DB_PREFIX . "product_image` WHERE image NOT LIKE '%".$this->db->escape($target_folder)."%' AND trim(image) <> ''");
			return $query->row['total'];
		}else{
			return $query->row['total'];
		}
	}
	
	public function getTotalUnorganizedImage($tablename, $target_folders) {		
		$query = $this->db->query("SELECT count(*) as total FROM `" . DB_PREFIX . $tablename ."` WHERE image NOT LIKE ".$target_folders." AND trim(image) <> ''");
		return $query->row['total'];
	}
	
	public function getTotalOrganizedImage($tablename, $target_folder) {		
		$query = $this->db->query("SELECT count(*) as total FROM `" . DB_PREFIX . $tablename ."` WHERE image LIKE '%".$this->db->escape($target_folder)."%' AND trim(image) <> ''");
		return $query->row['total'];
	}

	
	//NEW UPDATES

	public function urlslug($str, $options = array()) {
		$options = array(
				'delimiter' 	=> '-',
				'lowercase' 	=> true,
				'transliterate' => true,
				);
		$str = htmlspecialchars_decode($str);
		// Make sure string is in UTF-8 and strip invalid UTF-8 characters
		$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());	
		
		//$str = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $str);
		
		$char_map = array(
		// Latin
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
		'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
		'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
		'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
		'ß' => 'ss',
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
		'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
		'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
		'ÿ' => 'y',
		 
		// Latin symbols
		'©' => '(c)',
		 
		// Greek
		'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
		'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
		'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
		'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
		'Ϋ' => 'Y', 
		'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
		'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
		'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
		'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
		'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
		 
		// Turkish
		'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
		'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
		 
		// Russian
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
		'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
		'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
		'Я' => 'Ya',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
		'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
		'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
		'я' => 'ya',
		 
		// Ukrainian
		'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
		'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
		 
		// Czech
		'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
		'Ž' => 'Z',
		'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
		'ž' => 'z',
		 
		// Polish
		'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
		'Ż' => 'Z',
		'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
		'ż' => 'z',
		
		//Arabic
		"ا"=>"a", "أ"=>"a", "آ"=>"a", "إ"=>"e", "ب"=>"b", "ت"=>"t", "ث"=>"th", "ج"=>"j",
		"ح"=>"h", "خ"=>"kh", "د"=>"d", "ذ"=>"d", "ر"=>"r", "ز"=>"z", "س"=>"s", "ش"=>"sh",
		"ص"=>"s", "ض"=>"d", "ط"=>"t", "ظ"=>"z", "ع"=>"'e", "غ"=>"gh", "ف"=>"f", "ق"=>"q",
		"ك"=>"k", "ل"=>"l", "م"=>"m", "ن"=>"n", "ه"=>"h", "و"=>"w", "ي"=>"y", "ى"=>"a",
		"ئ"=>"'e", "ء"=>"'",   
		"ؤ"=>"'e", "لا"=>"la", "ة"=>"h", "؟"=>"?", "!"=>"!", 
		"ـ"=>"", 
		"،"=>",", 
		"َ‎"=>"a", "ُ"=>"u", "ِ‎"=>"e", "ٌ"=>"un", "ً"=>"an", "ٍ"=>"en", "ّ"=>"",
		
		//persian
		"ا" => "a", "أ" => "a", "آ" => "a", "إ" => "e", "ب" => "b", "ت" => "t", "ث" => "th",
		"ج" => "j", "ح" => "h", "خ" => "kh", "د" => "d", "ذ" => "d", "ر" => "r", "ز" => "z",
		"س" => "s", "ش" => "sh", "ص" => "s", "ض" => "d", "ط" => "t", "ظ" => "z", "ع" => "'e",
		"غ" => "gh", "ف" => "f", "ق" => "q", "ك" => "k", "ل" => "l", "م" => "m", "ن" => "n",
		"ه" => "h", "و" => "w", "ي" => "y", "ى" => "a", "ئ" => "'e", "ء" => "'", 
		"ؤ" => "'e", "لا" => "la", "ک" => "ke", "پ" => "pe", "چ" => "che", "ژ" => "je", "گ" => "gu",
		"ی" => "a", "ٔ" => "", "ة" => "h", "؟" => "?", "!" => "!", 
		"ـ" => "", 
		"،" => ",", 
		"َ‎" => "a", "ُ" => "u", "ِ‎" => "e", "ٌ" => "un", "ً" => "an", "ٍ" => "en", "ّ" => "",
		 
		// Latvian
		'Ā'  =>  'A', 'Č'  =>  'C', 'Ē'  =>  'E', 'Ģ'  =>  'G', 'Ī'  =>  'i', 'Ķ'  =>  'k', 'Ļ'  =>  'L', 'Ņ'  =>  'N',
		'Š'  =>  'S', 'Ū'  =>  'u', 'Ž'  =>  'Z',
		'ā'  =>  'a', 'č'  =>  'c', 'ē'  =>  'e', 'ģ'  =>  'g', 'ī'  =>  'i', 'ķ'  =>  'k', 'ļ'  =>  'l', 'ņ'  =>  'n',
		'š'  =>  's', 'ū'  =>  'u', 'ž'  =>  'z'
		);

		// Transliterate characters to ASCII
		if ($options['transliterate']) {
		$str = str_replace(array_keys($char_map), $char_map, $str);
		}
		// Replace non-alphanumeric characters with our delimiter
		$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
		// Remove duplicate delimiters
		$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

		// Remove delimiter from ends
		$str = trim($str, $options['delimiter']);
		return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
	}
	
	public function add_new_log($image){
		$query = $this->db->query("SELECT id FROM `" . DB_PREFIX . "image_rename_logs` WHERE `old_path` = '" . $this->db->escape($image). "' LIMIT 1");
		if ($query->row) {
			$log_id = $query->row['id'];
		}else{
			$this->db->query("INSERT INTO `" . DB_PREFIX . "image_rename_logs` (`old_path`) VALUES ('".$this->db->escape($image)."')");
			$log_id = $this->db->getLastId();
		}
		return $log_id;
	}
	
	public function status_text($status) {
		switch ($status) {
			case '1':
				$text = '<span style="color:green;"><i class="fa fa-check"></i></span>';
				break;
			case '2':
				$text = '<span style="color:red;"><i class="fa fa-exclamation-triangle"></i> File Missing</span>';
				break;
			default:
				$text = '<span style="color:orange;"><i class="fa fa-exclamation"></i></span>';
		}
		return $text;
	}
	
	public function coreFunction($input = array()){
		if ($this->config->get('hb_seoimage_status')) {
			$this->load->model('setting/setting');
			$store_info = $this->model_setting_setting->getSetting('hb_seoimage', $input['store_id']);
			$delete_original = isset($store_info['hb_seoimage_delete_original'])? $store_info['hb_seoimage_delete_original']:false;
			
			if ($input['image'] <> ''){ //checking if image is not empty
				$log_id = $this->add_new_log($input['image']);
				
				$oldpath = DIR_IMAGE.$input['image'];		
				
				if (!file_exists($oldpath)) { // The file doesn't even exist - no possible way to rename it
					$this->db->query("UPDATE `" . DB_PREFIX . "image_rename_logs` SET status = 2 WHERE id = '".(int)$log_id."'");
					$this->log->write($input['type'].' : '.$input['id'].' : Image - '.$oldpath . ' - cannot be located.' );
					return;	// Immediately exit the coreFunction routine
				}
				
				if (strpos($input['image'],$input['target_folder']) === false) {
					$target_directory 		= DIR_IMAGE.$input['target_folder'];
					
					if (!file_exists($target_directory)) {
						mkdir($target_directory, 0777, true);
					}
					
					$seoimage_name = $this->urlslug($input['name']);
					
					$orig_file_extn = substr(strrchr($input['image'],'.'),1);
					if ($input['jpg_convert'] == 1){
						$file_type = strtolower($orig_file_extn);
						switch ($file_type) {
						case 'jpg':
						case 'jpeg':
							$conversiontype = 'none';
							break;
						case 'gif':
							$conversiontype = 'gif';
							break;
						case 'png':
							$conversiontype = 'png';
							break;
						case 'bmp':
							$conversiontype = 'bmp';
							break;
						default:
							$conversiontype 	= 'none';
							$filetypeextension 	= $orig_file_extn;	// Do not convert when graphic support is questionable
							$this->log->write($input['type'].' : '.$input['id'].' : Warning! No attempt to convert unsupported image format ' . $file_type . ' used by oldfile ' . $oldpath);
						}
						$filetypeextension 	= 'jpg';
					}else{
						$filetypeextension 	= $orig_file_extn;
						$conversiontype 	= 'none';
					}
					
					$renamedimage = $input['target_folder'].'/'.$seoimage_name.'.'.$filetypeextension;
	
					if ($this->checkimagename($input['type'], $renamedimage) > 0){ //this checks if same image name is already present
						$renamedimage = $input['target_folder'].'/'.$seoimage_name.'-'.$input['id'].'.'.$filetypeextension;
					}
					
					$renamedimage = str_replace('//','/',$renamedimage);
					
					$newpath = DIR_IMAGE.$renamedimage;
					
					if (strlen($renamedimage) > 254) {
						$this->log->write($input['type'].' : '.$input['id'].' : Image Rename Aborted, New Image file location length is greater than 255 characters - ' . $renamedimage);
					}else{
						if (!$this->copy_image($oldpath, $newpath, $conversiontype)) {
							$this->log->write($input['type'].' : '.$input['id'].' : Unable to copy old file ' . $oldpath . ' to newfile ' . $newpath);
						}else{
							$this->updateimagefield($input['image'], $renamedimage);
							$this->db->query("UPDATE `" . DB_PREFIX . "image_rename_logs` SET new_path = '".$this->db->escape($renamedimage)."', status = 1, date_added = now() WHERE id = '".(int)$log_id."'");
							if ($delete_original) {
								if (!unlink($oldpath)) {
									$this->log->write($input['type'].' : '.$input['id'].' : Unable to delete old file  ' . $oldpath);
								}
							}
						}
					}
					
				}
			}
		}
	}
	
	public function copy_image($oldpath, $newpath, $conversiontype) {
		// Converts the file format of the image (if required) before copying the image to the new location
		switch ($conversiontype) {
			case 'none':
				break;
			case 'png':
				$new_pic = imagecreatefrompng($oldpath);
				break;
			case 'gif':
				$new_pic = imagecreatefromgif($oldpath);
				break;
			case 'bmp':
				$new_pic = imagecreatefromwbmp($oldpath);
				break;
			default:
				// We should not reach here
				$this->log->write('Unexpected conversion type [' . $conversiontype . '] encountered while processing oldfile '.$oldpath . ' and newfile ' . $newpath);
				$conversiontype = 'invalid';
		}
		if ($conversiontype == 'none') {
			return copy($oldpath, $newpath);
		}
		if ($conversiontype == 'invalid') {
			return false;
		}else {
			// Copy the CONVERTED file over to the new location
			// Create a new true color image with the same size
			$w = imagesx($new_pic);
			$h = imagesy($new_pic);
			$white = imagecreatetruecolor($w, $h);

			// Fill the new image with white background
			$bg = imagecolorallocate($white, 255, 255, 255);
			imagefill($white, 0, 0, $bg);

			// Copy original transparent image onto the new image
			imagecopy($white, $new_pic, 0, 0, 0, 0, $w, $h);

			$new_pic = $white;

			$returncode = imagejpeg($new_pic, $newpath, -1);	// Use default quality setting
			imagedestroy($new_pic);
			return $returncode;
		}
	}
	
	
	
}
?>