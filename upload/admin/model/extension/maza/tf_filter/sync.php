<?php
class ModelExtensionMazaTfFilterSync extends Model {
        /**
         * Get filter values
         * @param Array $attributes
         * @param Int $filter_language_id
         * @return Generator
         */
        public function getAttributeValues($attributes, $filter_language_id){
                $query = $this->tf_db->query("SELECT * FROM `" . DB_PREFIX . "product_attribute` WHERE attribute_id IN (" . implode(",", array_map('intval', $attributes)) . ") AND language_id = '" . (int)$filter_language_id . "' GROUP BY `text`", true);
                
                foreach($query->rows as $row){
                    $data_attribute_value = array();
                    
                    $query2 = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_attribute` WHERE product_id = '" . $row['product_id'] . "' AND attribute_id = '" . $row['attribute_id'] . "'");
                    
                    foreach($query2->rows as $row2){
                        $data_attribute_value['name'][$row2['language_id']] = $row2['text'];
                    }
                    
                    yield $data_attribute_value;
                }
                
                
        }
        
        /**
         * Get options values
         * @param Array $options
         * @return Generator
         */
        public function getOptionsValues($options){
                $query = $this->tf_db->query("SELECT * FROM " . DB_PREFIX . "option_value WHERE option_id IN (" . implode(",", array_map('intval', $options)) . ")", true);
                
                foreach($query->rows as $option_value){
                    $data_option_value = array();
                    $data_option_value['image'] = $option_value['image'];
                    $data_option_value['sort_order'] = $option_value['sort_order'];
                    
                    $query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '" . $option_value['option_value_id'] . "'");
                    
                    foreach($query2->rows as $option_value_description){
                        $data_option_value['name'][$option_value_description['language_id']] = $option_value_description['name'];
                    }
                    
                    yield $data_option_value;
                }
        }
        
        /**
         * Get filter group values
         * @param Array $filter_groups
         * @return Generator
         */
        public function getFilterGroupsValues($filter_groups){
                $query = $this->tf_db->query("SELECT f.filter_id, f.sort_order, fd.language_id, fd.name FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_group_id IN (" . implode(",", array_map('intval', $filter_groups)) . ") ORDER BY f.filter_id", true);
                
                $filter_last_id = 0;
                $data_filter_value = array();
                
                foreach($query->rows as $filter_value){
                    if($filter_last_id && $filter_last_id != $filter_value['filter_id']){ // Return value data
                        yield $data_filter_value;
                    } 
                    
                    if(!$filter_last_id || $filter_last_id != $filter_value['filter_id']){ // Initialise value data
                        $data_filter_value = array();
                    }
                    
                    $data_filter_value['name'][$filter_value['language_id']] = $filter_value['name'];
                    $data_filter_value['sort_order'] = $filter_value['sort_order'];
                    
                    $filter_last_id = $filter_value['filter_id'];
                }
                
                // Return last value
                if($data_filter_value){
                    yield $data_filter_value;
                }
        }
        
        /**
         * Add unique values to filter
         * @param Int $filter_id
         * @param Int $filter_language_id
         * @param Array $data values
         */
        public function addValues($filter_id, $filter_language_id, $data){
                foreach($data as $value){
                    
                    if(empty($value['name'][$filter_language_id])){
                        continue; // Skip current value to add
                    }
                    
                    // Skip to add value if already exist
                    $query = $this->db->query("SELECT COUNT(*) total FROM " . DB_PREFIX . "tf_filter_value WHERE filter_id = '" . (int)$filter_id . "' AND ((`regex` = 1 AND '" . $this->db->escape($value['name'][$filter_language_id]) . "' REGEXP `value`) OR (`regex` = 0 AND '" . $this->db->escape($value['name'][$filter_language_id]) . "' LIKE `value`))");
                    
                    if($query->row['total']){
                        continue; // Skip current value to add
                    }
                    
                    // Add value
                    $this->db->query("INSERT INTO " . DB_PREFIX . "tf_filter_value SET filter_id = '" . (int)$filter_id . "', `regex` = 0, `value` = '" . $this->db->escape($value['name'][$filter_language_id]) . "', `image` = '" . $this->db->escape(isset($value['image'])?$value['image']:'') . "', sort_order = '" . (int)(isset($value['sort_order'])?$value['sort_order']:0) . "', status = '1'");
                    
                    $value_id = $this->db->getLastId();
                    
                    foreach ($value['name'] as $language_id => $name) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "tf_filter_value_description SET value_id = '" . (int)$value_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($name) . "'");
                    }
                }
        }
        
        public function addProductsToValue($filter_id, $filter_language_id, $setting = array()){
                // Add to trash before to update
                $this->db->query("UPDATE " . DB_PREFIX . "tf_filter_value_to_product fv2p LEFT JOIN " . DB_PREFIX . "tf_filter_value fv ON (fv.value_id = fv2p.value_id) SET fv2p.trash = '1' WHERE fv.filter_id = '" . (int)$filter_id . "' AND fv.status = '1'");
            
                // Attribute
                if(!empty($setting['attributes'])){
//                    $this->db->query("REPLACE INTO " . DB_PREFIX . "tf_filter_value_to_product (value_id, product_id) SELECT fv.value_id, pa.product_id FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "product p ON (pa.attribute_id IN ('" . implode("','", array_map('intval', $setting['attributes'])) . "') AND pa.language_id = '" . (int)$filter_language_id . "' AND p.product_id = pa.product_id) LEFT JOIN " . DB_PREFIX . "tf_filter_value fv ON (p.product_id IS NOT NULL AND fv.filter_id = '" . (int)$filter_id . "' AND (fv.last_sync < '" . $filter_date_modified . "' OR p.date_modified > fv.last_sync) AND pa.text REGEXP fv.value) WHERE fv.value_id IS NOT NULL GROUP BY fv.value_id, pa.product_id");
                    $this->db->query("REPLACE INTO " . DB_PREFIX . "tf_filter_value_to_product (value_id, product_id, trash) SELECT fv.value_id, pa.product_id, '0' FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "product p ON (pa.attribute_id IN (" . implode(",", array_map('intval', $setting['attributes'])) . ") AND pa.language_id = '" . (int)$filter_language_id . "' AND p.product_id = pa.product_id) LEFT JOIN " . DB_PREFIX . "tf_filter_value fv ON (p.product_id IS NOT NULL AND fv.filter_id = '" . (int)$filter_id . "' AND fv.status = '1' AND ((fv.regex = 1 AND pa.text REGEXP fv.value) OR (fv.regex = 0 AND pa.text LIKE fv.value))) WHERE fv.value_id IS NOT NULL GROUP BY fv.value_id, pa.product_id");
                }
                
                // Options
                if(!empty($setting['options'])){
                    $this->db->query("REPLACE INTO " . DB_PREFIX . "tf_filter_value_to_product (value_id, product_id, trash) SELECT fv.value_id, pov.product_id, '0' FROM `" . DB_PREFIX . "option_value_description` ovd LEFT JOIN " . DB_PREFIX . "tf_filter_value fv ON (ovd.option_id IN (" . implode(",", array_map('intval', $setting['options'])) . ") AND ovd.language_id = '" . (int)$filter_language_id . "' AND fv.filter_id = '" . (int)$filter_id . "' AND fv.status = '1' AND ((fv.regex = 1 AND ovd.name REGEXP fv.value) OR (fv.regex = 0 AND ovd.name LIKE fv.value))) LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (fv.value_id IS NOT NULL AND pov.option_value_id = ovd.option_value_id) WHERE pov.product_id IS NOT NULL AND pov.quantity > 0 GROUP BY fv.value_id, pov.product_id");
//                    $this->db->query("REPLACE INTO " . DB_PREFIX . "tf_filter_value_to_product (value_id, product_id, trash) SELECT fv.value_id, pov.product_id, '0' FROM `" . DB_PREFIX . "option_value_description` ovd LEFT JOIN " . DB_PREFIX . "tf_filter_value fv ON (ovd.option_id IN (" . implode(",", array_map('intval', $setting['options'])) . ") AND ovd.language_id = '" . (int)$filter_language_id . "' AND fv.filter_id = '" . (int)$filter_id . "' AND fv.status = '1' AND ((fv.regex = 1 AND ovd.name REGEXP fv.value) OR (fv.regex = 0 AND ovd.name LIKE fv.value))) LEFT JOIN " . DB_PREFIX . "combination_option_value pov ON (fv.value_id IS NOT NULL AND (pov.option_value1 = ovd.option_value_id OR pov.option_value2 = ovd.option_value_id)) WHERE pov.product_id IS NOT NULL GROUP BY fv.value_id, pov.product_id");
                }
                
                // Filter group
                if(!empty($setting['filter_groups'])){
                    $this->db->query("REPLACE INTO " . DB_PREFIX . "tf_filter_value_to_product (value_id, product_id, trash) SELECT fv.value_id, pf.product_id, '0' FROM `" . DB_PREFIX . "filter_description` fd LEFT JOIN " . DB_PREFIX . "tf_filter_value fv ON (fd.filter_group_id IN (" . implode(",", array_map('intval', $setting['filter_groups'])) . ") AND fd.language_id = '" . (int)$filter_language_id . "' AND fv.filter_id = '" . (int)$filter_id . "' AND fv.status = '1' AND ((fv.regex = 1 AND fd.name REGEXP fv.value) OR (fv.regex = 0 AND fd.name LIKE fv.value))) LEFT JOIN " . DB_PREFIX . "product_filter pf ON (fv.value_id IS NOT NULL AND fd.filter_id = pf.filter_id) WHERE pf.product_id IS NOT NULL GROUP BY fv.value_id, pf.product_id");
                }
                
                // Product name
                if(!empty($setting['product_name'])){
                    $this->db->query("REPLACE INTO " . DB_PREFIX . "tf_filter_value_to_product (value_id, product_id, trash) SELECT fv.value_id, pd.product_id, '0' FROM `" . DB_PREFIX . "product_description` pd LEFT JOIN " . DB_PREFIX . "tf_filter_value fv ON (pd.language_id = '" . (int)$filter_language_id . "' AND fv.filter_id = '" . (int)$filter_id . "' AND fv.status = '1' AND ((fv.regex = 1 AND pd.name REGEXP fv.value) OR (fv.regex = 0 AND pd.name LIKE fv.value))) WHERE fv.value_id IS NOT NULL GROUP BY fv.value_id, pd.product_id");
                }
                
                // Product description
                if(!empty($setting['product_description'])){
                    $this->db->query("REPLACE INTO " . DB_PREFIX . "tf_filter_value_to_product (value_id, product_id, trash) SELECT fv.value_id, pd.product_id, '0' FROM `" . DB_PREFIX . "product_description` pd LEFT JOIN " . DB_PREFIX . "tf_filter_value fv ON (pd.language_id = '" . (int)$filter_language_id . "' AND fv.filter_id = '" . (int)$filter_id . "' AND fv.status = '1' AND ((fv.regex = 1 AND pd.description REGEXP fv.value) OR (fv.regex = 0 AND pd.description LIKE fv.value))) WHERE fv.value_id IS NOT NULL GROUP BY fv.value_id, pd.product_id");
                }
                
                // Product tags
                if(!empty($setting['product_tags'])){
                    $this->db->query("REPLACE INTO " . DB_PREFIX . "tf_filter_value_to_product (value_id, product_id, trash) SELECT fv.value_id, pd.product_id, '0' FROM `" . DB_PREFIX . "product_description` pd LEFT JOIN " . DB_PREFIX . "tf_filter_value fv ON (pd.language_id = '" . (int)$filter_language_id . "' AND fv.filter_id = '" . (int)$filter_id . "' AND fv.status = '1' AND ((fv.regex = 1 AND pd.tag REGEXP fv.value) OR (fv.regex = 0 AND pd.tag LIKE fv.value))) WHERE fv.value_id IS NOT NULL GROUP BY fv.value_id, pd.product_id");
                }
                
                // Empty Trash
                $this->db->query("DELETE FROM " . DB_PREFIX . "tf_filter_value_to_product WHERE trash = '1'");
                
                // Set last sync
//                $this->db->query("UPDATE " . DB_PREFIX . "tf_filter_value SET last_sync = NOW() WHERE filter_id = '" . (int)$filter_id . "'");
        }
}