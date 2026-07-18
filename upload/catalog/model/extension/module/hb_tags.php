<?php
class ModelExtensionModuleHbTags extends Model {
    public function run_batch(){
        $limit_start = 0;
        $limit_count = ($this->config->get('hb_tags_autolimit')) ? $this->config->get('hb_tags_autolimit') : 20;
        $products = $this->getEmptyTagsProducts($limit_start, $limit_count);

        foreach ($products as $product){
            $data['product_id'] = $product['product_id'];
            $data['language_id'] = $product['language_id'];

            $this->model_extension_module_hb_tags->generate_tags($data);
        }
    }
    
	public function generate_tags($data) {
        $product_id  = (int)$data['product_id'];
        $language_id = (int)$data['language_id'];
    
        // Initialize arrays
        $tags = $product_wild = $category_wild = $keyword_by_rules = [];
        
        // Rule flags
        $rule_a_enable = (bool)$this->config->get('hb_tags_rule_a');
        $rule_b_enable = (bool)$this->config->get('hb_tags_rule_b');
    
        // Get product details
        $product_info = $this->getProductInfo($product_id, $language_id);
    
        if (!$product_info) {
            $this->addlog("Product details not found for Product ID $product_id with language ID $language_id");
            return;
        }
    
        // Load configuration
        $template  = $this->config->get("hb_tags_pattern$language_id");
        $stopwords = $this->config->get("hb_tags_stopwords$language_id");
        $category  = $this->getCategoriesName($product_id, $language_id);
    
        // Compose template with product info
        $composed_template = $template;
        foreach ($product_info as $key => $value) {
            $value = trim($value ?? '');
            $composed_template = str_replace("{" . $key . "}", $value, $composed_template);
        }
        $composed_template = str_replace("{category}", $category, $composed_template);
    
        // Clean and extract keywords
        $composed_template = $this->cleanwords($composed_template);
        $composed_template = $this->extractKeyWords($composed_template, $stopwords);
    
        // Handle wildcards
        if (strpos($template, '{p*}') !== false) {
            $product_wild = $this->simpleExtract($product_info['name'], $stopwords);
        }
    
        if (strpos($template, '{c*}') !== false) {
            $category_wild[] = htmlspecialchars_decode($category);
        }
    
        // Process rules if enabled
        if ($rule_a_enable || $rule_b_enable) {
            $rule_a = $rule_b = [];
            if (!empty($category)) {
                $categories = array_map('trim', explode(',', $category));
                foreach ($categories as $cat) {
                    $rule_a[] = "$cat {$product_info['name']}";
                    $rule_b[] = "{$product_info['name']} $cat";
                }
            }
    
            if ($rule_a_enable && $rule_b_enable) {
                $keyword_by_rules = array_merge($rule_a, $rule_b);
            } elseif ($rule_a_enable) {
                $keyword_by_rules = $rule_a;
            } elseif ($rule_b_enable) {
                $keyword_by_rules = $rule_b;
            } else {
                $keyword_by_rules = [];
            }
        }
    
        // Merge all tags and remove duplicates
        $tags = array_unique(array_merge($composed_template, $tags, $product_wild, $category_wild, $keyword_by_rules));
    
        // Save tags
        $tags_string = implode(', ', $tags);
        $this->addtags($product_id, $language_id, $tags_string);
    
        // Log success
        $this->addlog("Tags Generated for Product ID $product_id with language ID $language_id");
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

    public function extractKeyWords($string, $stopwords) {
        $stopWords = array_map('strtolower', explode(',', $stopwords));
    
        // Clean and prepare the input string
        $string = strtolower(trim(preg_replace(['/[^a-z0-9 -]/', '/\s+/'], ['', ' '], $string)));
    
        // Extract words
        $words = array_filter(
            explode(' ', $string),
            fn($word) => $word !== '' 
                && !in_array($word, $stopWords) 
                && strlen($word) > 3 
                && !is_numeric($word)
        );
    
        // Count word occurrences
        $wordCounts = array_count_values($words);
    
        // Sort by frequency (descending) and limit to 20 words
        arsort($wordCounts);
        return array_keys(array_slice($wordCounts, 0, 20, true));
    }

    public function simpleExtract($string, $stopwords) {
        // Decode and clean the input string
        $string = strtolower(trim(htmlspecialchars_decode($string)));
        $string = preg_replace('/[!;:]/', '', $string);
    
        // Convert stopwords into a lowercase array for consistency
        $stopwords = array_map('strtolower', explode(',', $stopwords));
    
        // Extract and filter tags
        $tags = array_filter(
            explode(' ', $string),
            fn($tag) => strlen($tag) > 3 && !in_array($tag, $stopwords)
        );
    
        return array_values($tags); // Reset array keys
    }

    public function getProductInfo($product_id, $language_id) {
        $sql = "
            SELECT 
                p.product_id,
                pd.name,
                pd.tag,
                p.model,
                m.name AS brand,
                p.upc
            FROM 
                `" . DB_PREFIX . "product` p
            JOIN 
                `" . DB_PREFIX . "product_description` pd 
                ON p.product_id = pd.product_id
            LEFT JOIN 
                `" . DB_PREFIX . "manufacturer` m 
                ON p.manufacturer_id = m.manufacturer_id
            WHERE 
                p.product_id = '".(int)$product_id."' 
                AND pd.language_id = '".(int)$language_id."' 
            LIMIT 1
        ";
    
        $query = $this->db->query($sql);
        return $query->row;
    }    

    public function getCategoriesName($product_id, $language_id){
        $results = $this->db->query("SELECT group_concat((select name from " . DB_PREFIX . "category_description where category_id = a.category_id and language_id = ".$language_id.") separator ', ')as category FROM `" . DB_PREFIX . "product_to_category` a where product_id = '".$product_id."' group by product_id");
        if (isset($results->row['category'])){
            return $results->row['category'];
        }else{
            return $value = '';
        }
    }

    public function addtags($product_id, $language_id, $tags){
        $this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET tag = '".$this->db->escape($tags)."' WHERE product_id = '".(int)$product_id."' and language_id = '".(int)$language_id."'");
    }

    public function getTotalEmptyTagsProducts(){
        $sql = "SELECT count(*) as total FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_description pd ON (p.product_id = pd.product_id) WHERE trim(pd.tag) = ''";		
        $results = $this->db->query($sql);
        return $results->row['total'];
    }

    public function getEmptyTagsProducts($start, $end){
        $results = $this->db->query("SELECT p.product_id, pd.language_id FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_description pd ON (p.product_id = pd.product_id) WHERE trim(pd.tag) = '' LIMIT " . (int)$start . "," . (int)$end);		
        return $results->rows;
    }

    public function addlog($text = ''){
        if ($this->config->get('hb_tags_logs')) {
            if (!file_exists(DIR_LOGS)) {
                mkdir(DIR_LOGS, 0777, true);
            }
    
            $file = DIR_LOGS . 'hb_seo_product_tags.txt';
    
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
}