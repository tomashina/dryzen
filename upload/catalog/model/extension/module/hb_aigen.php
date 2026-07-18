<?php
class ModelExtensionModuleHbAigen extends Model {
    public function generateForLanguage($language_id){
        if ($this->isExtensionInstalled('hb_onpage')){
            $page_type = array('product', 'category', 'manufacturer', 'information');
        }else{
            $page_type = array('product', 'category', 'information');
        }

        $limit = $this->config->get('hb_aigen_cron_limit') ? $this->config->get('hb_aigen_cron_limit') : 10;

        $enabled_fields = $this->config->get('hb_aigen_sections') ? $this->config->get('hb_aigen_sections') : [];

        if (empty($enabled_fields)) {
            $this->addlog('No sections are enabled');
            return;
        }

        foreach ($page_type as $type) {
            switch ($type) {
                case 'product':
                    $items = $this->getProducts($language_id, $enabled_fields, $limit);
                    break;
                case 'category':
                    $remove_fields = ['tag'];
                    $fields = array_diff($enabled_fields, $remove_fields);

                    $items = $this->getCategories($language_id, $fields, $limit);
                    break;
                case 'manufacturer':
                    $remove_fields = ['description','tag'];
                    $fields = array_diff($enabled_fields, $remove_fields);

                    $items = $this->getManufacturers($language_id, $fields, $limit);
                    break;
                case 'information':
                    $remove_fields = ['h1', 'h2', 'tag'];
                    $fields = array_diff($enabled_fields, $remove_fields);

                    $items = $this->getInformations($language_id, $fields, $limit);
                    break;
            }

            if ($items) {
                foreach ($items as $item) {
                    $this->generateContent($type, $item['item_id'], $item['language_id']);
                }
            }
        }
    }

    public function callChatGPTAPI($prompt) {
        $api_key = $this->config->get('hb_aigen_api');
        $gpt_model = $this->config->get('hb_aigen_gpt_model') ?: 'gpt-3.5-turbo';

        $url = 'https://api.openai.com/v1/chat/completions';

        $post_data = [
            'model' => $gpt_model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            //'max_tokens' => 200,
            'temperature' => 0.7,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));

        $response = curl_exec($ch);
        curl_close($ch); 

        //TEST RESPONSE
        /*$response = '{"description": "Test The Canon EOS 5D is a high-performance DSLR camera designed for professional photographers and enthusiasts. It features a full-frame CMOS sensor, delivering exceptional image quality and low-light performance. With advanced autofocus capabilities, 4K video recording, and a durable magnesium alloy body, this camera is perfect for capturing stunning photos and videos in any environment.","meta_title": "Test Canon EOS 5D - Professional DSLR Camera"}';*/

        $this->addlog('API Response: ' . $response);

        return json_decode($response, true);
    }

    public function generate_prompt($type, $item_id, $language_id) {        
        $prompt = '';
    
        $data = [
            "description" => $this->language->get('prompt_text_'.$type.'_description'),
            "meta_title" => $this->language->get('prompt_text_'.$type.'_meta_title'),
            "meta_description" => $this->language->get('prompt_text_'.$type.'_meta_description'),
            "meta_keyword" => $this->language->get('prompt_text_'.$type.'_meta_keyword'),
            "h1" => $this->language->get('prompt_text_'.$type.'_h1'),
            "h2" => $this->language->get('prompt_text_'.$type.'_h2'),
            "tag" => $this->language->get('prompt_text_product_tags'),
        ];
    
        $sections = $this->config->get('hb_aigen_sections') ? $this->config->get('hb_aigen_sections') : [];
        $description_max_length = $this->config->get('hb_aigen_description_max_length') ?: 500;
    
        if (!empty($sections)) {
            if ($type == 'category') {
                $sections = array_diff($sections, ['tag']);
            }

            if ($type == 'information') {
                $sections = array_diff($sections, ['h1', 'h2', 'tag']);
            }

            if ($type == 'manufacturer') {
                $sections = array_diff($sections, ['tag']);
            }
            
            $filtered_data = array_filter(
                $data,
                function ($key) use ($sections) {
                    return in_array($key, $sections);
                },
                ARRAY_FILTER_USE_KEY
            );
    
            switch ($type) {
                case 'product':
                    $template = $this->config->get('hb_aigen_product_template' . $language_id) ?: '';
                    $product_info = $this->getProduct($item_id, $language_id);
                    $template = str_replace('{name}', $product_info['name'], $template);
                    $template = str_replace('{model}', $product_info['model'], $template);
                    $template = str_replace('{description}', (strlen($product_info['description']) > $description_max_length) ? substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $description_max_length) . '...' : strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), $template); 
                    $prompt .= $template;
                    break;
    
                case 'category':
                    $template = $this->config->get('hb_aigen_category_template' . $language_id) ?: '';
                    $category_info = $this->getCategory($item_id, $language_id);
                    $template = str_replace('{name}', $category_info['name'], $template);
                    $template = str_replace('{description}', (strlen($category_info['description']) > $description_max_length) ? substr(strip_tags(html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8')), 0, $description_max_length) . '...' : strip_tags(html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8')), $template);   
                    $prompt .= $template;
                    break;
    
                case 'manufacturer':
                    $template = $this->config->get('hb_aigen_manufacturer_template' . $language_id) ?: '';
                    $manufacturer_info = $this->getManufacturer($item_id, $language_id);
                    $template = str_replace('{name}', $manufacturer_info['name'], $template);
                    $template = str_replace('{description}', (strlen($manufacturer_info['description']) > $description_max_length) ? substr(strip_tags(html_entity_decode($manufacturer_info['description'], ENT_QUOTES, 'UTF-8')), 0, $description_max_length) . '...' : strip_tags(html_entity_decode($manufacturer_info['description'], ENT_QUOTES, 'UTF-8')), $template);
                    $prompt .= $template;
                    break;
    
                case 'information':
                    $template = $this->config->get('hb_aigen_information_template' . $language_id) ?: '';
                    $information_info = $this->getInformation($item_id, $language_id);
                    $template = str_replace('{title}', $information_info['title'], $template);
                    $template = str_replace('{description}', (strlen($information_info['description']) > $description_max_length) ? substr(strip_tags(html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8')), 0, $description_max_length) . '...' : strip_tags(html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8')), $template);
                    
                    $prompt .= $template;
                    break;
            }
            
            if (!empty(trim($template))) {
                $prompt .= "\n\n" . $this->language->get('prompt_text_generate_sections');
        
                /*foreach ($filtered_data as $key => $value) {
                    $prompt .= "\n - " . sprintf($this->language->get('prompt_text_section_format'), $key, $value);
                }*/

                $prompt .= "\n" . implode(", ", $sections);
        
                $prompt .= "\n\n" . $this->language->get('prompt_text_json_format') . ' : ';

                $prompt .= "\n\n" . json_encode($filtered_data);
        
                $prompt .= "\n\n" . $this->language->get('prompt_text_generate_content');

                $prompt = mb_convert_encoding($prompt, 'UTF-8', 'UTF-8');
            }
        }
    
        return $prompt;
    } 

    public function isExtensionInstalled($code) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `code` = '" . $this->db->escape($code) . "'");

        return $query->num_rows;
    }

    public function getProduct($product_id, $langauge_id = 1) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$langauge_id . "' LIMIT 1");

        return $query->row;
    }

    public function getCategory($category_id, $langauge_id = 1) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$langauge_id . "' LIMIT 1");

        return $query->row;
    }

    public function getManufacturer($manufacturer_id, $langauge_id = 1) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "' AND md.language_id = '" . (int)$langauge_id . "' LIMIT 1");

        return $query->row;
    }

    public function getInformation($information_id, $langauge_id = 1) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$langauge_id . "' LIMIT 1");

        return $query->row;
    }

    public function getProducts($language_id, $fields, $limit) {
        $sql = "SELECT product_id as item_id, language_id FROM " . DB_PREFIX . "product_description WHERE language_id = '" . (int)$language_id . "'";
    
        if (!empty($fields) && is_array($fields)) {
            $conditions = array_map(function($field) {
                return "`" . $this->db->escape($field) . "` IS NULL OR `" . $this->db->escape($field) . "` = ''";
            }, $fields);
            $sql .= " AND (" . implode(" OR ", $conditions) . ")";
        }
    
        $sql .= " LIMIT " . (int)$limit;
    
        $query = $this->db->query($sql);
    
        return $query->rows;
    }

    public function getCategories($language_id, $fields, $limit) {
        $sql = "SELECT category_id as item_id, language_id FROM " . DB_PREFIX . "category_description WHERE language_id = '" . (int)$language_id . "'";
    
        if (!empty($fields) && is_array($fields)) {
            $conditions = array_map(function($field) {
                return "`" . $this->db->escape($field) . "` IS NULL OR `" . $this->db->escape($field) . "` = ''";
            }, $fields);
            $sql .= " AND (" . implode(" OR ", $conditions) . ")";
        }
    
        $sql .= " LIMIT " . (int)$limit;
    
        $query = $this->db->query($sql);
    
        return $query->rows;
    }

    public function getManufacturers($language_id, $fields, $limit) {
        $sql = "SELECT manufacturer_id as item_id, language_id FROM " . DB_PREFIX . "manufacturer_description WHERE language_id = '" . (int)$language_id . "'";
    
        if (!empty($fields) && is_array($fields)) {
            $conditions = array_map(function($field) {
                return "`" . $this->db->escape($field) . "` IS NULL OR `" . $this->db->escape($field) . "` = ''";
            }, $fields);
            $sql .= " AND (" . implode(" OR ", $conditions) . ")";
        }
    
        $sql .= " LIMIT " . (int)$limit;
    
        $query = $this->db->query($sql);
    
        return $query->rows;
    }

    public function getInformations($language_id, $fields, $limit) {
        $sql = "SELECT information_id as item_id, language_id FROM " . DB_PREFIX . "information_description WHERE language_id = '" . (int)$language_id . "'";
    
        if (!empty($fields) && is_array($fields)) {
            $conditions = array_map(function($field) {
                return "`" . $this->db->escape($field) . "` IS NULL OR `" . $this->db->escape($field) . "` = ''";
            }, $fields);
            $sql .= " AND (" . implode(" OR ", $conditions) . ")";
        }
    
        $sql .= " LIMIT " . (int)$limit;
    
        $query = $this->db->query($sql);
    
        return $query->rows;
    }    

    public function generateContent($type, $item_id, $language_id) {
        $sections = ($this->config->get('hb_aigen_sections'))? $this->config->get('hb_aigen_sections') : [];

        if ($this->config->get('hb_aigen_one_language') && $language_id != $this->config->get('hb_aigen_language_id')) {
            $this->addlog('Language is not the default language');
            return false;
        }

        $prompt = $this->generate_prompt($type, $item_id, $language_id);

        if (empty($prompt)) {
            $this->addlog('Prompt is empty');
            return false;
        }

        $response = $this->callChatGPTAPI($prompt);

        if (!is_array($response)) {
            $this->addlog('API Response is not an array');
            return false;			
        }

        // Extract JSON content if it's wrapped in a Markdown block
        if (isset($response['choices'][0]['message']['content'])) {
            $content = $response['choices'][0]['message']['content'];
            
            // Remove Markdown code block if present
            $content = trim($content);
            if (strpos($content, '```json') === 0) {
                $content = preg_replace('/^```json|```$/', '', $content);
            }

            // Decode JSON content
            $responseData = json_decode(trim($content), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addlog('Failed to decode JSON: ' . json_last_error_msg());
                return false;
            }
        } else {
            $this->addlog('Invalid API response format');
            return false;
        }

        // Validate sections
        foreach ($responseData as $key => $value) {
            if (in_array($key, $sections)) {
                $this->saveGeneratedData($type, $item_id, $language_id, $key, $value);
            }else{
                $this->addlog('Section ' . $key . ' is not in the sections list');
                return false;
            }						
        }	

        return true;        
    }

    public function getPreviousValue($type, $item_id, $language_id, $element) {
        $previous_value = '';
        switch ($type) {
            case 'product':
                $product_info = $this->getProduct($item_id, $language_id);
                $previous_value = $product_info[$element];
                break;

            case 'category':
                $category_info = $this->getCategory($item_id, $language_id);
                $previous_value = $category_info[$element];
                break;

            case 'manufacturer':
                $manufacturer_info = $this->getManufacturer($item_id, $language_id);
                $previous_value = $manufacturer_info[$element];
                break;

            case 'information':
                $information_info = $this->getInformation($item_id, $language_id);
                $previous_value = $information_info[$element];
                break;
        }

        return trim($previous_value);
    }


    public function saveGeneratedData($type, $item_id, $language_id, $element, $value) {
        $this->addlog('Saving data for ' . $type . ' with ID ' . $item_id . ' and element ' . $element . ' and language ' . $language_id);
        $previous_value = $this->getPreviousValue($type, $item_id, $language_id, $element);
        //$this->addlog('Previous Value: ' . $previous_value);
        //$this->addlog('New Value: ' . $value);

        $this->db->query("DELETE FROM `" . DB_PREFIX . "aigen` WHERE type = '" . $this->db->escape($type) . "' AND item_id = '" . (int)$item_id . "' AND element = '" . $this->db->escape($element) . "' AND language_id = '". (int)$language_id ."'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "aigen` SET type = '" . $this->db->escape($type) . "', item_id = '" . (int)$item_id . "', element = '" . $this->db->escape($element) . "', language_id = '". (int)$language_id  ."' , value = '" . $this->db->escape($value) . "', previous_value = '" . $this->db->escape($previous_value) . "', date_added = NOW()");

        if ($this->config->get('hb_aigen_simulate')) {
            $this->addlog('Simulation Mode is Enabled. Data is not saved in the main tables.');
            return;
        }

        $overwrite = ($this->config->get('hb_aigen_overwrite')) ? true : false;

        switch ($type) {
            case 'product':
                if ($overwrite || empty($previous_value)) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET " . $element . " = '" . $this->db->escape($value) . "' WHERE product_id = '" . (int)$item_id . "' AND language_id = '" . (int)$language_id . "'");
                }
                break;
            
            case 'category':
                if ($overwrite || empty($previous_value)) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "category_description` SET " . $element . " = '" . $this->db->escape($value) . "' WHERE category_id = '" . (int)$item_id . "' AND language_id = '" . (int)$language_id . "'");
                }
                break;

            case 'manufacturer':
                if ($overwrite || empty($previous_value)) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "manufacturer_description` SET " . $element . " = '" . $this->db->escape($value) . "' WHERE manufacturer_id = '" . (int)$item_id . "' AND language_id = '" . (int)$language_id . "'");
                }
                break;

            case 'information':
                if ($overwrite || empty($previous_value)) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "information_description` SET " . $element . " = '" . $this->db->escape($value) . "' WHERE information_id = '" . (int)$item_id . "' AND language_id = '" . (int)$language_id . "'");
                }
                break;

        }
    }

    public function addlog($text = ''){
        if ($this->config->get('hb_aigen_logs')) {
            if (!file_exists(DIR_LOGS)) {
                mkdir(DIR_LOGS, 0777, true);
            }
    
            $file = DIR_LOGS . 'hb_aigen.txt';
    
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