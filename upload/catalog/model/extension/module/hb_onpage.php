<?php
class ModelExtensionModuleHbOnpage extends Model {
    public function generateByPage($page_type, $store_id, $auto) {
        $results = [];
        $data['elements'] = [
            'product'       => ['meta_title', 'meta_description', 'meta_keyword', 'h1', 'h2', 'image_alt', 'image_title'],
            'category'      => ['meta_title', 'meta_description', 'meta_keyword', 'h1', 'h2', 'image_alt', 'image_title'],
            'manufacturer'  => ['meta_title', 'meta_description', 'meta_keyword', 'h1', 'h2', 'image_alt', 'image_title'],
            'information'   => ['meta_title', 'meta_description', 'meta_keyword']
        ];

        foreach ($data['elements'][$page_type] as $element_type) {
            $results[] = $this->generateByElement($page_type, $element_type, $store_id, $auto);
        }

        return $results;
    }

    public function generateByElement($page_type, $element_type, $store_id, $auto) {
        $results = [];
        $output = '';

        if ($auto === true) {
            $limit_count = $this->config->get('hb_onpage_autolimit');
        } else {
            $limit_count = 10;
        }

        $limit_start = 0;

        $records = $this->getEmptyTags($page_type, $element_type, $limit_start, $limit_count);

        if (!empty($records)) {
            foreach ($records as $record) {
                $results[] = $this->generateSeoElement($page_type, $record['id'], $element_type, $store_id);
            }
            $output = 'Completed: ' . $element_type . ' Generated for ' . $page_type . ' (' . count($records) . ' records) [Store ID: ' . $store_id . ']';
        } else {
            $output = 'All ' . $element_type . ' for ' . $page_type . ' pages already generated [Store ID: ' . $store_id . ']';
        }

        $results[] = $output;
        $this->addlog($output);

        return $results;
    }

    public function generateSeoElement($page, $id, $element, $store_id) {
        $languages = $this->getLanguages();
        foreach ($languages as $language) {
            $language_id = $language['language_id'];
            if ($this->isEmptyTag($page, $element, $id, $language_id)) {
                $info = $this->getPageInfo($page, $id, $language_id);
                if ($info) {
                    $template = $this->getTemplate($page, $element, $language_id, $store_id);
                    if ($template) {
                        $seo_content = $this->replaceParameters($template, $info);
                        $this->addSeo($page, $seo_content, $element, $id, $language_id);
                        $this->addlog(strtoupper($element) . ' content added for ' . $page . ' ID ' . $id . ' (' . $language['name'] . '): [' . $seo_content. ']');
                    } else {
                        $this->addlog('*** No ' . strtoupper($element) . ' Template Found for ' . $page . ' pages - ' . $language['name']);
                        return false;
                    }
                } else {
                    $this->addlog('*** No ' . $page . ' data found for ' . $page . ' ID ' . $id);
                }
            }
        }
    }

    public function cleanwords($str, $options = array()) {
        $str = htmlspecialchars_decode($str);
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
        $options = array(
                        'delimiter' => ' ',
                        'limit' => null,
                        'lowercase' => true,
                        'replacements' => array(),
                        'transliterate' => true,
                    );
        
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

    public function replaceParameters($template, $info) {
        $cleanwords = true;
        foreach ($info as $key => $value) {
            if (!is_array($value)) {
                $replaceValue = ($value !== NULL && $value !== '') ? $value : '';
                $template = str_replace('{' . $key . '}', $replaceValue, $template);
                
                if ($cleanwords) {
                    $template = str_replace('{x' . $key . '}', $replaceValue, $template);
                }
            }
        }
        
        // Perform other sanitization and cleanup
        $template = htmlspecialchars_decode($template);
        $template = strip_tags($template);
        $template = preg_replace('!\s+!', ' ', $template);
        $template = str_replace('()', '', $template);
        $template = str_replace('( )', '', $template);
        $template = str_replace('| |', '|', $template);
        $template = str_replace('||', '', $template);
        
        return $template;
    }

    public function addlog($text = '') {
        if ($this->config->get('hb_onpage_logs')) {
            if (!file_exists(DIR_LOGS)) {
                mkdir(DIR_LOGS, 0777, true);
            }

            $file = DIR_LOGS . 'huntbee_seo_onpage_elements.txt';

            if (file_exists($file)) {
                $size = filesize($file);
                if ($size > 5242880) {
                    $handle = fopen($file, 'w+');
                    fclose($handle);
                }
            }

            $fp = fopen($file, 'a');
            fwrite($fp, "\r\n" . date('d-M-Y G:i:s A') . ' - ' . $text);
            fclose($fp);
        }        
    }

    public function getLanguages() {
        $results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE `status` = 1");
        return $results->rows;
    }

    public function getTemplate($page_type, $element_type, $language_id, $store_id) {
        $result = $this->db->query("SELECT `template` FROM `" . DB_PREFIX . "hb_onpage_templates` WHERE `page_type` = '" . $this->db->escape($page_type) . "' AND `element_type` = '" . $this->db->escape($element_type) . "' AND `language_id` = '" . (int)$language_id . "' AND `store_id` = '" . (int)$store_id . "' ORDER BY RAND() LIMIT 1");
        if ($result->row) {
            return $result->row['template'];
        } else {
            return false;
        }
    }

    public function getFirstLine($description) {        
        $description = (!empty($description)) ? strip_tags($description) : '';

        if (!empty($description)) {
            $description = html_entity_decode($description);
            $pos = strpos($description, '.');
            
            if ($pos === false) {
                return $description;
            } else {
                return substr($description, 0, $pos + 1);
            }
        } else {
            return '';
        }
    }

    public function getProductInfo($product_id, $language_id) {
        $result = $this->db->query("SELECT b.name AS name, b.description, b.tag AS tag, a.model AS model, (SELECT m.name FROM " . DB_PREFIX . "manufacturer m WHERE (m.manufacturer_id = a.manufacturer_id)) AS manufacturer, a.upc AS upc FROM `" . DB_PREFIX . "product` a, `" . DB_PREFIX . "product_description` b WHERE (a.product_id = b.product_id) AND a.product_id = '" . (int)$product_id . "' AND b.language_id = '" . (int)$language_id . "' LIMIT 1");
        if ($result->row) {
            $details = array(
                'name'          => $result->row['name'],
                'model'         => $result->row['model'],
                'manufacturer'         => $result->row['manufacturer'],
                'tag'           => $result->row['tag'],
                'upc'           => $result->row['upc'],
                'description'   => $this->getFirstLine($result->row['description']),
                'category'      => $this->getCategoriesName($product_id, $language_id)
            );
            return $details;
        } else {
            return [];
        }
    }
        
    public function getCategoriesName($product_id, $language_id) {
        $results = $this->db->query("SELECT group_concat((select name from " . DB_PREFIX . "category_description where category_id = a.category_id and language_id = " . $language_id . ") separator ', ') as category FROM `" . DB_PREFIX . "product_to_category` a where product_id = '" . $product_id . "' group by product_id");
        if ($results->row) {
            return $results->row['category'];
        } else {
            return $value = '';
        }
    }

    public function getCategoryInfo($category_id, $language_id) {
        $result = $this->db->query("SELECT name, description FROM `" . DB_PREFIX . "category_description` WHERE category_id = '" . (int)$category_id . "' AND language_id = '" . (int)$language_id . "' LIMIT 1");
        if ($result->row) {
            $details = array(
                'name'          => $result->row['name'],
                'description'   => $this->getFirstLine($result->row['description'])
            );
            return $details;
        } else {
            return [];
        }
    }

    public function getManufacturerInfo($manufacturer_id, $language_id){
		$result = $this->db->query("SELECT name, description FROM `" . DB_PREFIX . "manufacturer_description` WHERE manufacturer_id = '".(int)$manufacturer_id."' AND language_id = '".(int)$language_id."' LIMIT 1");
		if ($result->row) {
			$details = array(
				'name' 			=>  $result->row['name'],
				'description' 	=> 	$this->getFirstLine($result->row['description'])
			);
			return $details;
		}else{
			return false;
		}
	}

    public function getInformationInfo($information_id, $language_id) {
        $result = $this->db->query("SELECT `title`, description FROM `" . DB_PREFIX . "information_description` WHERE information_id = '" . (int)$information_id . "' AND language_id = '" . (int)$language_id . "' LIMIT 1");
        if ($result->row) {
            $details = array(
                'name'          => $result->row['title'],
                'description'   => $this->getFirstLine($result->row['description'])
            );
            return $details;
        } else {
            return [];
        }
    }

    public function isEmptyTag($page, $element, $id, $language_id) {
        if ($page == 'product') {
            $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "product_description` WHERE product_id = '" . (int)$id . "' AND language_id = '" . (int)$language_id . "' AND (trim(" . $element . ") = '' OR " . $element . " IS NULL)";
        }
        if ($page == 'category') {
            $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "category_description` WHERE category_id = '" . (int)$id . "' AND language_id = '" . (int)$language_id . "' AND (trim(" . $element . ") = '' OR " . $element . " IS NULL)";
        }
        if ($page == 'manufacturer') {
            $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "manufacturer_description` WHERE manufacturer_id = '" . (int)$id . "' AND language_id = '" . (int)$language_id . "' AND (trim(" . $element . ") = '' OR " . $element . " IS NULL)";
        }
        if ($page == 'information') {
            $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "information_description` WHERE information_id = '" . (int)$id . "' AND language_id = '" . (int)$language_id . "' AND (trim(" . $element . ") = '' OR " . $element . " IS NULL)";
        }
        $results = $this->db->query($sql);
        if ($results->row['total'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function addSeo($type, $content, $element, $id, $language_id) {
        $content = str_replace('"', '&quot;', $content);
        if ($type == 'product') {
            $this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET `" . $element . "` = '" . $this->db->escape($content) . "' WHERE product_id = '" . (int)$id . "' AND language_id = '" . (int)$language_id . "'");
        }
        if ($type == 'category') {
            $this->db->query("UPDATE `" . DB_PREFIX . "category_description` SET `" . $element . "` = '" . $this->db->escape($content) . "' WHERE category_id = '" . (int)$id . "' AND language_id = '" . (int)$language_id . "'");
        }
        if ($type == 'manufacturer') {
            $this->db->query("UPDATE `" . DB_PREFIX . "manufacturer_description` SET `" . $element . "` = '" . $this->db->escape($content) . "' WHERE manufacturer_id = '" . (int)$id . "' AND language_id = '" . (int)$language_id . "'");
        }
        if ($type == 'information') {
            $this->db->query("UPDATE `" . DB_PREFIX . "information_description` SET `" . $element . "` = '" . $this->db->escape($content) . "' WHERE information_id = '" . (int)$id . "' AND language_id = '" . (int)$language_id . "'");
        }
    }

    public function getTotalEmptyTags($type, $element) {
        $tables = [
            'product'     => DB_PREFIX . 'product_description',
            'category'    => DB_PREFIX . 'category_description',
            'manufacturer'=> DB_PREFIX . 'manufacturer_description',
            'information' => DB_PREFIX . 'information_description'
        ];

        if (!isset($tables[$type])) {
            return 0;
        }

        $table = $tables[$type];
        $sql = "SELECT COUNT(*) as total FROM $table WHERE (trim($element) = '' OR $element IS NULL)";
        
        $results = $this->db->query($sql);
        return $results->row['total'];
    }

    public function getEmptyTags($type, $element, $start, $end) {
        $columns = [
            'product'     => 'product_id',
            'category'    => 'category_id',
            'manufacturer'=> 'manufacturer_id',
            'information' => 'information_id'
        ];

        if (!isset($columns[$type])) {
            return [];
        }

        $column = $columns[$type];
        $table = DB_PREFIX . $type . '_description';

        $sql = "SELECT $column as id FROM $table WHERE (trim($element) = '' OR $element IS NULL) GROUP BY $column LIMIT " . (int)$start . "," . (int)$end;
        $results = $this->db->query($sql);

        return $results->rows;
    }

    public function getRandomId($page_type) {
        $table = '';
        $id_field = '';

        switch ($page_type) {
            case 'product':
                $table = DB_PREFIX . 'product_description';
                $id_field = 'product_id';
                break;
            case 'category':
                $table = DB_PREFIX . 'category_description';
                $id_field = 'category_id';
                break;
            case 'manufacturer':
                $table = DB_PREFIX . 'manufacturer_description';
                $id_field = 'manufacturer_id';
                break;
            case 'information':
                $table = DB_PREFIX . 'information_description';
                $id_field = 'information_id';
                break;
        }

        $result = $this->db->query("SELECT $id_field FROM `$table` ORDER BY RAND() LIMIT 1");
        return $result->row[$id_field];
    }

    public function getPageInfo($page_type, $id, $language_id) {
        switch ($page_type) {
            case 'product':
                return $this->model_extension_module_hb_onpage->getProductInfo($id, $language_id);
            case 'category':
                return $this->model_extension_module_hb_onpage->getCategoryInfo($id, $language_id);
            case 'manufacturer':
                return $this->model_extension_module_hb_onpage->getManufacturerInfo($id, $language_id);
            case 'information':
                return $this->model_extension_module_hb_onpage->getInformationInfo($id, $language_id);
        }
    }
}