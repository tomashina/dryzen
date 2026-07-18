<?php
class ModelExtensionModuleHbTranslate extends Model {
	public function translate_content($data) {
        $apiKey = $this->config->get('hb_translate_api');
        $url = "https://translation.googleapis.com/language/translate/v2";

        // Build request data with necessary parameters
        $request_data = [
            'q' => $data['source_text'],
            'target' => $data['target_language_code'],
            'format' => 'html',  // This preserves HTML formatting in the translation
            'source' => $data['source_language_code'],
            'key' => $apiKey
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode response
        $result = json_decode($response, true);

        // Check for errors
        if (isset($result['error'])) {
            $this->addlog("Error: " . $result['error']['message']);
            return null;
        }

        // Return translated text
        return $result['data']['translations'][0]['translatedText'] ?? null;
        //return 'Translated Text'.$data['source_text'].' to '.$data['target_language_code'];
	}

    public function fix_empty_fields(){
        $this->db->query("UPDATE `".DB_PREFIX."product_description` SET description = '' WHERE description = '&lt;p&gt;&lt;br&gt;&lt;/p&gt;'");
        $this->db->query("UPDATE `".DB_PREFIX."category_description` SET description = '' WHERE description = '&lt;p&gt;&lt;br&gt;&lt;/p&gt;'");
        $this->db->query("UPDATE `".DB_PREFIX."information_description` SET description = '' WHERE description = '&lt;p&gt;&lt;br&gt;&lt;/p&gt;'");
        $this->db->query("UPDATE `".DB_PREFIX."manufacturer_description` SET description = '' WHERE description = '&lt;p&gt;&lt;br&gt;&lt;/p&gt;'");
    }

    public function getColumns($type){
        $columns = [];
        switch ($type) {
            case 'product':
                $columns = $this->config->get('hb_translate_fields_product');
                break;
            
            case 'category':
                $columns = $this->config->get('hb_translate_fields_category');
                break;

            case 'information':
                $columns = $this->config->get('hb_translate_fields_information');
                break;
            
            case 'manufacturer':
                $columns = $this->config->get('hb_translate_fields_manufacturer');
                break;
        }

        return $columns;
    }

    public function getTotalEmpty($data){
        $type   = $data['type'];
        $column = $data['column'];
        $master_language_id =  $data['master_language_id'];

        switch ($type) {
            case 'product':
                if ($column == 'attribute'){
                    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_attribute` pa WHERE TRIM(pa.`text`) = '' AND pa.language_id != '" . (int)$master_language_id . "' AND EXISTS (SELECT 1 FROM `" . DB_PREFIX . "product_attribute` pa_master WHERE pa_master.product_id = pa.product_id AND pa_master.attribute_id = pa.attribute_id AND pa_master.language_id = '" . (int)$master_language_id . "' AND TRIM(pa_master.`text`) != '')");
                }else{
                    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) WHERE TRIM(pd.`" . $column . "`) = '' AND pd.language_id != '" . (int)$master_language_id . "' AND EXISTS (SELECT 1 FROM `" . DB_PREFIX . "product_description` pd_master WHERE pd_master.product_id = p.product_id AND pd_master.language_id = '" . (int)$master_language_id . "' AND TRIM(pd_master.`" . $column . "`) != '')");
                }
                break;
            
            case 'category':
                $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (c.category_id = cd.category_id) WHERE TRIM(cd.`" . $column . "`) = '' AND cd.language_id != '" . (int)$master_language_id . "' AND EXISTS (SELECT 1 FROM `" . DB_PREFIX . "category_description` cd_master WHERE cd_master.category_id = c.category_id AND cd_master.language_id = '" . (int)$master_language_id . "' AND TRIM(cd_master.`" . $column . "`) != '')");
                break;

            case 'information':
                $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "information` i LEFT JOIN `" . DB_PREFIX . "information_description` id ON (i.information_id = id.information_id) WHERE TRIM(id.`" . $column . "`) = '' AND id.language_id != '" . (int)$master_language_id . "' AND EXISTS (SELECT 1 FROM `" . DB_PREFIX . "information_description` id_master WHERE id_master.information_id = i.information_id AND id_master.language_id = '" . (int)$master_language_id . "' AND TRIM(id_master.`" . $column . "`) != '')");
                break;   
            
            case 'manufacturer':
                $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "manufacturer` m LEFT JOIN `" . DB_PREFIX . "manufacturer_description` md ON (m.information_id = md.manufacturer_id) WHERE TRIM(md.`" . $column . "`) = '' AND md.language_id != '" . (int)$master_language_id . "' AND EXISTS (SELECT 1 FROM `" . DB_PREFIX . "manufacturer_description` md_master WHERE md_master.manufacturer_id = m.manufacturer_id AND md_master.language_id = '" . (int)$master_language_id . "' AND TRIM(md_master.`" . $column . "`) != '')");
                break; 
        }

        return (int)$query->row['total'];
    }

    public function getEmpty($data, $limit){
        $type   = $data['type'];
        $column = $data['column'];
        $master_language_id =  $data['master_language_id'];

        switch ($type) {
            case 'product':
                if ($column == 'attribute'){
                    $query = $this->db->query("SELECT product_id AS id, attribute_id, language_id FROM `" . DB_PREFIX . "product_attribute` WHERE TRIM(`text`) = '' AND language_id != '" . (int)$master_language_id . "' AND EXISTS (SELECT 1 FROM `" . DB_PREFIX . "product_attribute` pa_master WHERE pa_master.product_id = product_id AND pa_master.attribute_id = attribute_id AND pa_master.language_id = '" . (int)$master_language_id . "' AND TRIM(pa_master.`text`) != '') LIMIT " . (int)$limit);
                }else{
                    $query = $this->db->query("SELECT p.product_id AS id, pd.language_id FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) WHERE TRIM(pd.`" . $column . "`) = '' AND pd.language_id != '" . (int)$master_language_id . "' AND EXISTS (SELECT 1 FROM `" . DB_PREFIX . "product_description` pd_master WHERE pd_master.product_id = p.product_id AND pd_master.language_id = '" . (int)$master_language_id . "' AND TRIM(pd_master.`" . $column . "`) != '') LIMIT " . (int)$limit);
                }
                break;
            
            case 'category':
                $query = $this->db->query("SELECT c.category_id as id, cd.language_id FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) WHERE (trim(cd.`".$column."`) = '') AND cd.language_id != '".(int)$master_language_id."' AND EXISTS (SELECT 1 FROM `" . DB_PREFIX . "category_description` cd_master WHERE cd_master.category_id = c.category_id AND cd_master.language_id = '" . (int)$master_language_id . "' AND TRIM(cd_master.`" . $column . "`) != '') LIMIT ".$limit);
                break;

            case 'information':
                $query = $this->db->query("SELECT i.information_id AS id, id.language_id FROM `" . DB_PREFIX . "information` i LEFT JOIN `" . DB_PREFIX . "information_description` id ON (i.information_id = id.information_id) WHERE TRIM(id.`" . $column . "`) = '' AND id.language_id != '" . (int)$master_language_id . "' AND EXISTS (SELECT 1 FROM `" . DB_PREFIX . "information_description` id_master WHERE id_master.information_id = i.information_id AND id_master.language_id = '" . (int)$master_language_id . "' AND TRIM(id_master.`" . $column . "`) != '') LIMIT " . (int)$limit);
                break;         
            
            case 'manufacturer':
                $query = $this->db->query("SELECT m.manufacturer_id AS id, md.language_id FROM `" . DB_PREFIX . "manufacturer` m LEFT JOIN `" . DB_PREFIX . "manufacturer_description` md ON (m.manufacturer_id = md.manufacturer_id) WHERE TRIM(md.`" . $column . "`) = '' AND md.language_id != '" . (int)$master_language_id . "' AND EXISTS (SELECT 1 FROM `" . DB_PREFIX . "manufacturer_description` md_master WHERE md_master.manufacturer_id = m.manufacturer_id AND md_master.language_id = '" . (int)$master_language_id . "' AND TRIM(md_master.`" . $column . "`) != '') LIMIT " . (int)$limit);
                break;
        }

        return $query->rows;
    }

    public function translate_column_data($data){
        $type                   = $data['type'];
        $column                 = $data['column'];
        $master_language_id     = $data['master_language_id'];
        $id                     = $data['id'];
        $target_language_id     = $data['target_language_id'];
        $target_language_code   = $data['target_language_code'];
        $source_language_code   = $data['source_language_code'];       

        //source text >> translate >> update text
        $source_text = $this->getOriginalContent($data);
        if (!empty($source_text)){
            if ($target_language_id != $master_language_id){
                $source_text = html_entity_decode($source_text, ENT_QUOTES, 'UTF-8');
                
                $api_send_data = [
                    'source_text' => $source_text,
                    'source_language_code' => $source_language_code,
                    'target_language_code' => $target_language_code
                ];

                $translated_text = $this->translate_content($api_send_data);
                if (!empty($translated_text)){
                    switch ($type) {
                        case 'product':
                            if ($column == 'attribute'){
                                if (isset($data['attribute_id']) && $data['attribute_id'] > 0) {
                                    $this->db->query("UPDATE `".DB_PREFIX."product_attribute` SET `text` = '".$this->db->escape($translated_text)."' WHERE product_id = '".(int)$id."' AND attribute_id = '".(int)$data['attribute_id']."' AND language_id = '".(int)$target_language_id."'");
                                    $this->addlog('Product Attribute Updated for attribute ID '. $data['attribute_id'] . '. Product ID: '. $id . '. Language: '.$target_language_code);
                                }else{
                                    $this->addlog('Invalid Attribute ID '. $data['attribute_id']);
                                }
                            }else{
                                $this->db->query("UPDATE `".DB_PREFIX."product_description` SET `".$column."` = '".$this->db->escape($translated_text)."' WHERE product_id = '".(int)$id."' AND language_id = '".(int)$target_language_id."'");
                                $this->addlog('Product Updated for Column '. $column . '. Product ID: '. $id . '. Language: '.$target_language_code);
                            }
                            break;
                        
                        case 'category':
                            $this->db->query("UPDATE `".DB_PREFIX."category_description` SET `".$column."` = '".$this->db->escape($translated_text)."' WHERE category_id = '".(int)$id."' AND language_id = '".(int)$target_language_id."'");
                            $this->addlog('Category Updated for Column '. $column . '. Product ID: '. $id . '. Language: '.$target_language_code);
                            break;
            
                        case 'information':
                            $this->db->query("UPDATE `".DB_PREFIX."information_description` SET `".$column."` = '".$this->db->escape($translated_text)."' WHERE information_id = '".(int)$id."' AND language_id = '".(int)$target_language_id."'");
                            $this->addlog('Information Updated for Column '. $column . '. Product ID: '. $id . '. Language: '.$target_language_code);
                            break; 

                        case 'manufacturer':
                            $this->db->query("UPDATE `".DB_PREFIX."manufacturer_description` SET `".$column."` = '".$this->db->escape($translated_text)."' WHERE manufacturer_id = '".(int)$id."' AND language_id = '".(int)$target_language_id."'");
                            $this->addlog('Manufacturer Updated for Column '. $column . '. Product ID: '. $id . '. Language: '.$target_language_code);
                            break;
                    }  
                    return true;                  
                }else{
                    return false;
                }
            }                                        
        }else{
            $this->addlog('Source Text is empty for '.$type.' ID '. $id . ' Column: '.$column);
        }
    }

    public function getOriginalContent($data){
        $type                   = $data['type'];
        $column                 = $data['column'];
        $master_language_id     = $data['master_language_id'];
        $id                     = $data['id'];

        switch ($type) {
            case 'product':
                if ($column == 'attribute') {
                    $query = $this->db->query("SELECT `text` as attribute FROM `".DB_PREFIX."product_attribute` WHERE product_id = '".(int)$id."' AND attribute_id = '".(int)$data['attribute_id']."' AND language_id = '".(int)$master_language_id."' LIMIT 1");
                }else{
                    $query = $this->db->query("SELECT `$column` FROM `".DB_PREFIX."product_description` WHERE product_id = '".(int)$id."' AND language_id = '".(int)$master_language_id."' LIMIT 1");
                }
                break;
            
            case 'category':
                $query = $this->db->query("SELECT `$column` FROM `".DB_PREFIX."category_description` WHERE category_id = '".(int)$id."' AND language_id = '".(int)$master_language_id."' LIMIT 1");
                break;
            
            case 'information':
                $query = $this->db->query("SELECT `$column` FROM `".DB_PREFIX."information_description` WHERE information_id = '".(int)$id."' AND language_id = '".(int)$master_language_id."' LIMIT 1");
                break;
            
            case 'manufacturer':
                $query = $this->db->query("SELECT `$column` FROM `".DB_PREFIX."manufacturer_description` WHERE manufacturer_id = '".(int)$id."' AND language_id = '".(int)$master_language_id."' LIMIT 1");
                break;
        }
        
        if ($query->row){
            return $query->row[$column];
        }else{
            return '';
        }
    }

    public function addlog($text = ''){
        if (!file_exists(DIR_LOGS)) {
            mkdir(DIR_LOGS, 0777, true);
        }

        $file = DIR_LOGS . 'huntbee_translatePro_logs.txt';

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