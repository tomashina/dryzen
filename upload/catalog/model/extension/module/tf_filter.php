<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2017, TemplateMaza
 * @license		One domain license
 * @link		http://www.templatemaza.com
 */
class ModelExtensionModuleTfFilter extends model {
        /**
         * Get minimum price
         * @param String $product_table product table 
         * @return Int
         */
        public function getMinimumPrice($product_table){
            $query = $this->db->query("SELECT MIN(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END) total FROM $product_table");
            
            return $query->row['total'];
        }
        
        /**
         * Get maximum price
         * @param String $product_table product table 
         * @return Int
         */
        public function getMaximumPrice($product_table){
            $query = $this->db->query("SELECT MAX(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE price END) total FROM $product_table");
            
            return $query->row['total'];
        }
        
        /**
         * Get sub categories for filter
         * @param String $product_table product table 
         * @param Int $category_id
         * @param Array $data
         * @return Array
         */
        public function getSubCategories($product_table, $category_id, $data = array()){
                $sql = 'SELECT c.category_id, cd.name, c.image, c.sort_order';
                
                if(!empty($data['field_total'])){
                    $sql .= ', COUNT(DISTINCT p.product_id) total';
                }
                
                $sql .= " FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "') LEFT JOIN " . DB_PREFIX . "category_path cp ON (c.category_id = cp.path_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.category_id = cp.category_id) LEFT JOIN $product_table p ON (p.product_id = p2c.product_id)";
                
                if (!empty($data['filter_filter'])) {
                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
                }
                
                if(!empty($data['filter_custom'])){
                    reset($data['filter_custom']);
                    
                    foreach($data['filter_custom'] as $key => $custom_group){
                        $sql .= " INNER JOIN " . DB_PREFIX . "tf_filter_value_to_product f2p$key ON (f2p$key.value_id IN (" . implode(',',array_map('intval', $custom_group)) . ") AND f2p$key.product_id = p.product_id)";
                    }
                }
                
                $sql .= "LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$category_id . "' AND c.status = '1' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                
                if (!empty($data['filter_manufacturer_id'])) {
                    if(is_array($data['filter_manufacturer_id'])){
                        $sql .= " AND p.manufacturer_id IN (" . implode(',', array_map('intval', $data['filter_manufacturer_id'])) . ")";
                    } else {
                        $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
                    }
		        }
                
                if (!empty($data['filter_min_rating'])) {
                        $sql .= " AND IFNULL(p.rating, 0) >= '" . (int)$data['filter_min_rating'] . "'";
                }
                
                if(!empty($data['filter_min_special_perc'])){
                        $sql .= " AND IFNULL(p.special_perc, 0) >= '" . (int)$data['filter_min_special_perc'] . "'";
                }
                
                if (isset($data['filter_in_stock']) && $data['filter_in_stock'] !== '') {
                    if($data['filter_in_stock']){
                        $sql .= " AND p.quantity > 0";
                    } else {
                        $sql .= " AND p.quantity <= 0";
                    }
                } elseif (!empty($data['filter_stock_status'])) {
                    if (in_array(-1, $data['filter_stock_status'])) {
                        if (count($data['filter_stock_status']) > 1) {
                            $sql .= " AND (p.quantity > 0 OR (p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")))";
                        } else {
                            $sql .= " AND p.quantity > 0";
                        }
                    } else {
                        $sql .= " AND p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")";
                    }
                }
                
                if (!empty($data['filter_filter'])) {
                    $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                    $sql .= " AND pf.filter_id IN (" . implode(',', array_map('intval', $filters)) . ")";
                }
                
                $sql .= " GROUP BY c.category_id";
                
                /*if(!empty($data['field_total'])){
                    $sql .= " ORDER BY total DESC, c.sort_order ASC";
                } else {
                    $sql .= " ORDER BY c.sort_order ASC";
                }*/
                $query = $this->db->query($sql);
                
                return $query->rows;
        }
        
        /**
         * Get manufacturers for filter
         * @param String $product_table product table 
         * @param Array $data
         * @return Array
         */
        public function getManufacturers($product_table, $data = array()){
                $sql = 'SELECT m.manufacturer_id, m.name, m.image, m.sort_order';
                
                if(!empty($data['field_total'])){
                    $sql .= ', COUNT(DISTINCT p.product_id) total';
                }
                
                if (!empty($data['filter_category_id'])) {
                        if (!empty($data['filter_sub_category'])) {
                                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                        } else {
                                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                        }

                        $sql .= " LEFT JOIN $product_table p ON (p2c.product_id = p.product_id)";
                } else {
                        $sql .= " FROM $product_table p";
                }
                
                $sql .= " LEFT JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id = p.manufacturer_id)";
                
                if (!empty($data['filter_filter'])) {
                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
                }
                
                if(!empty($data['filter_custom'])){
                    reset($data['filter_custom']);
                    
                    foreach($data['filter_custom'] as $key => $custom_group){
                        $sql .= " INNER JOIN " . DB_PREFIX . "tf_filter_value_to_product f2p$key ON (f2p$key.value_id IN (" . implode(',',array_map('intval', $custom_group)) . ") AND f2p$key.product_id = p.product_id)";
                    }
                }
                
                $sql .= " WHERE m.manufacturer_id IS NOT NULL";
                
                if (!empty($data['filter_category_id'])) {
                    if(is_array($data['filter_category_id'])){
                        $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);

                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        } else {
                            $sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        }
                    }else{
                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                        } else {
                            $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                        }
                    }
                }
                
                if (!empty($data['filter_min_rating'])) {
                        $sql .= " AND IFNULL(p.rating, 0) >= '" . (int)$data['filter_min_rating'] . "'";
                }
                
                if(!empty($data['filter_min_special_perc'])){
                        $sql .= " AND IFNULL(p.special_perc, 0) >= '" . (int)$data['filter_min_special_perc'] . "'";
                }
                
                if (isset($data['filter_in_stock']) && $data['filter_in_stock'] !== '') {
                    if($data['filter_in_stock']){
                        $sql .= " AND p.quantity > 0";
                    } else {
                        $sql .= " AND p.quantity <= 0";
                    }
                } elseif (!empty($data['filter_stock_status'])) {
                    if (in_array(-1, $data['filter_stock_status'])) {
                        if (count($data['filter_stock_status']) > 1) {
                            $sql .= " AND (p.quantity > 0 OR (p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")))";
                        } else {
                            $sql .= " AND p.quantity > 0";
                        }
                    } else {
                        $sql .= " AND p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")";
                    }
                }
                
                if (!empty($data['filter_filter'])) {
                        $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                        $sql .= " AND pf.filter_id IN (" . implode(',', array_map('intval', $filters)) . ")";
                }
                
                $sql .= " GROUP BY m.manufacturer_id";
                
                $query = $this->db->query($sql);
                
                return $query->rows;
        }

        /**
         * Get Stock status for filter
         * @param String $product_table product table 
         * @param Array $data
         * @return Array
         */
        public function getStockStatuses(string $product_table, array $data = array()): array {
            $sql = 'SELECT ss.stock_status_id, ss.name';
            
            if(!empty($data['field_total'])){
                $sql .= ', COUNT(DISTINCT p.product_id) total';
            }
            
            if (!empty($data['filter_category_id'])) {
                if (!empty($data['filter_sub_category'])) {
                        $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                } else {
                        $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                }

                $sql .= " LEFT JOIN $product_table p ON (p2c.product_id = p.product_id)";
            } else {
                $sql .= " FROM $product_table p";
            }
            
            $sql .= " LEFT JOIN " . DB_PREFIX . "stock_status ss ON (ss.stock_status_id = p.stock_status_id)";
            
            if (!empty($data['filter_filter'])) {
                    $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
            }
            
            if(!empty($data['filter_custom'])){
                reset($data['filter_custom']);
                
                foreach($data['filter_custom'] as $key => $custom_group){
                    $sql .= " INNER JOIN " . DB_PREFIX . "tf_filter_value_to_product f2p$key ON (f2p$key.value_id IN (" . implode(',',array_map('intval', $custom_group)) . ") AND f2p$key.product_id = p.product_id)";
                }
            }
            
            $sql .= " WHERE ss.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.quantity <= 0";
            
            if (!empty($data['filter_category_id'])) {
                if(is_array($data['filter_category_id'])){
                    $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);

                    if (!empty($data['filter_sub_category'])) {
                        $sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
                    } else {
                        $sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
                    }
                }else{
                    if (!empty($data['filter_sub_category'])) {
                        $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                    } else {
                        $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                    }
                }
            }
            
            if (!empty($data['filter_min_rating'])) {
                    $sql .= " AND IFNULL(p.rating, 0) >= '" . (int)$data['filter_min_rating'] . "'";
            }
            
            if(!empty($data['filter_min_special_perc'])){
                    $sql .= " AND IFNULL(p.special_perc, 0) >= '" . (int)$data['filter_min_special_perc'] . "'";
            }

            if (!empty($data['filter_manufacturer_id'])) {
                if(is_array($data['filter_manufacturer_id'])){
                    $sql .= " AND p.manufacturer_id IN (" . implode(',', array_map('intval', $data['filter_manufacturer_id'])) . ")";
                } else {
                    $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
                }
            }
                
            if (!empty($data['filter_filter'])) {
                $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                $sql .= " AND pf.filter_id IN (" . implode(',', array_map('intval', $filters)) . ")";
            }
            
            $sql .= " GROUP BY ss.stock_status_id";
            
            $query = $this->db->query($sql);
            
            return $query->rows;
        }
        
        /**
         * Get total products by stock
         * @param String $product_table product table 
         * @param Bool $stock_status
         * @return Int
         */
        public function getTotalProductsByStock($product_table, $stock_status, $data = array()){
                $sql = "SELECT COUNT(DISTINCT p.product_id) total";
                
                if (!empty($data['filter_category_id'])) {
                        if (!empty($data['filter_sub_category'])) {
                                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                        } else {
                                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                        }

                        $sql .= " LEFT JOIN $product_table p ON (p2c.product_id = p.product_id)";
                } else {
                        $sql .= " FROM $product_table p";
                }
                
                if (!empty($data['filter_filter'])) {
                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
                }
                
                if(!empty($data['filter_custom'])){
                    reset($data['filter_custom']);
                    
                    foreach($data['filter_custom'] as $key => $custom_group){
                        $sql .= " INNER JOIN " . DB_PREFIX . "tf_filter_value_to_product f2p$key ON (f2p$key.value_id IN (" . implode(',',array_map('intval', $custom_group)) . ") AND f2p$key.product_id = p.product_id)";
                    }
                }
                
                if($stock_status){
                    $sql .= " WHERE p.quantity > 0";
                } else {
                    $sql .= " WHERE p.quantity <= 0";
                }
                
                if (!empty($data['filter_category_id'])) {
                    if(is_array($data['filter_category_id'])){
                        $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);

                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        } else {
                            $sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        }
                    }else{
                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                        } else {
                            $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                        }
                    }
                }
                
                if (!empty($data['filter_min_rating'])) {
                        $sql .= " AND IFNULL(p.rating, 0) >= '" . (int)$data['filter_min_rating'] . "'";
                }
                
                if(!empty($data['filter_min_special_perc'])){
                        $sql .= " AND IFNULL(p.special_perc, 0) >= '" . (int)$data['filter_min_special_perc'] . "'";
                }
                
                if (!empty($data['filter_manufacturer_id'])) {
                    if(is_array($data['filter_manufacturer_id'])){
                        $sql .= " AND p.manufacturer_id IN (" . implode(',', array_map('intval', $data['filter_manufacturer_id'])) . ")";
                    } else {
                        $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
                    }
		        }
                
                if (!empty($data['filter_filter'])) {
                        $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                        $sql .= " AND pf.filter_id IN (" . implode(',', array_map('intval', $filters)) . ")";
                }
                
                $query = $this->db->query($sql);
                
                return $query->row['total'];
        }
        
        /**
         * Get opencart filters for filter
         * @param String $product_table product table 
         * @param Array $data 
         * @return Array
         */
        public function getFilters($product_table, $category_id = 0, $data = array()){
                $sql = "SELECT f.filter_id, f.filter_group_id, f.sort_order, fd.name";
                
                if(!empty($data['field_total'])){
                    $sql .= ', COUNT(DISTINCT p.product_id) total';
                }
                
                if (!empty($data['filter_category_id'])) {
                        if (!empty($data['filter_sub_category'])) {
                                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                        } else {
                                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                        }

                        $sql .= " LEFT JOIN $product_table p ON (p2c.product_id = p.product_id)";
                } else {
                        $sql .= " FROM $product_table p";
                }
                
                if(!empty($data['filter_custom'])){
                    reset($data['filter_custom']);
                    
                    foreach($data['filter_custom'] as $key => $custom_group){
                        $sql .= " INNER JOIN " . DB_PREFIX . "tf_filter_value_to_product f2p$key ON (f2p$key.value_id IN (" . implode(',',array_map('intval', $custom_group)) . ") AND f2p$key.product_id = p.product_id)";
                    }
                }
                
                $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "filter f ON (f.filter_id = pf.filter_id) LEFT JOIN " . DB_PREFIX . "filter_description fd ON (fd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND f.filter_id = fd.filter_id)";
                
                if($category_id){
                    if(!empty($data['filter_sub_category'])){
                        $sql .= " LEFT JOIN " . DB_PREFIX . "category_filter cf ON (cf.filter_id = f.filter_id) LEFT JOIN " . DB_PREFIX . "category_path cp2 ON (cp2.category_id = cf.category_id)";
                    } else {
                        $sql .= " LEFT JOIN " . DB_PREFIX . "category_filter cf ON (cf.filter_id = f.filter_id)";
                    }
                }
                
                $sql .= " WHERE f.filter_id IS NOT NULL";
                
                if($category_id){
                    if(!empty($data['filter_sub_category'])){
                        $sql .= " AND cp2.path_id = '" . (int)$category_id . "'";
                    } else {
                        $sql .= " AND cf.category_id = '" . (int)$category_id . "'";
                    }
                }
                
                if (!empty($data['filter_category_id'])) {
                    if(is_array($data['filter_category_id'])){
                        $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);

                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        } else {
                            $sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        }
                    }else{
                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                        } else {
                            $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                        }
                    }
                }
                
                if (!empty($data['filter_min_rating'])) {
                        $sql .= " AND IFNULL(p.rating, 0) >= '" . (int)$data['filter_min_rating'] . "'";
                }
                
                if(!empty($data['filter_min_special_perc'])){
                        $sql .= " AND IFNULL(p.special_perc, 0) >= '" . (int)$data['filter_min_special_perc'] . "'";
                }
                
                if (!empty($data['filter_manufacturer_id'])) {
                    if(is_array($data['filter_manufacturer_id'])){
                        $sql .= " AND p.manufacturer_id IN (" . implode(',', array_map('intval', $data['filter_manufacturer_id'])) . ")";
                    } else {
                        $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
                    }
		        }
                
                if (isset($data['filter_in_stock']) && $data['filter_in_stock'] !== '') {
                    if($data['filter_in_stock']){
                        $sql .= " AND p.quantity > 0";
                    } else {
                        $sql .= " AND p.quantity <= 0";
                    }
                } elseif (!empty($data['filter_stock_status'])) {
                    if (in_array(-1, $data['filter_stock_status'])) {
                        if (count($data['filter_stock_status']) > 1) {
                            $sql .= " AND (p.quantity > 0 OR (p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")))";
                        } else {
                            $sql .= " AND p.quantity > 0";
                        }
                    } else {
                        $sql .= " AND p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")";
                    }
                }
                
                $sql .= " GROUP BY f.filter_id";
                
                $query = $this->db->query($sql);
                
                return $query->rows;
        }
        
        /**
         * Get list of filter group detail by ids
         * @param String $product_table product table 
         * @param Array $filter_group_ids
         * @return Array
         */
        public function getFilterGroups($filter_group_ids){
                if($filter_group_ids){
                    $query = $this->db->query("SELECT fg.filter_group_id, fg.sort_order, fgd.name FROM " . DB_PREFIX . "filter_group fg LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fgd.language_id = '" . $this->config->get('config_language_id') . "' AND fg.filter_group_id = fgd.filter_group_id) WHERE fg.filter_group_id IN (" . implode(",", array_map('intval', $filter_group_ids)) . ")");
                
                    return $query->rows;
                } else {
                    return array();
                }
        }
        
        /**
         * Get custom filter values for filter
         * @param String $product_table product table 
         * @param Int $category_id
         * @param Array $data 
         * @return Array
         */
        public function getCustomFilterValues($product_table, $category_id = 0, $data = array()){
                $sql = "SELECT fv.value_id, fv.filter_id, fv.image, fvd.name, fv.sort_order";
                
                if(!empty($data['field_total'])){
                    $sql .= ', COUNT(DISTINCT p.product_id) total';
                }
                
                if (!empty($data['filter_category_id'])) {
                        if (!empty($data['filter_sub_category'])) {
                                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                        } else {
                                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                        }

                        $sql .= " LEFT JOIN $product_table p ON (p2c.product_id = p.product_id)";
                } else {
                        $sql .= " FROM $product_table p";
                }
                
                if (!empty($data['filter_filter'])) {
                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
                }
                
                if(!empty($data['filter_custom'])){
                    reset($data['filter_custom']);
                    
                    foreach($data['filter_custom'] as $key => $custom_group){
                        $sql .= " INNER JOIN " . DB_PREFIX . "tf_filter_value_to_product f2p$key ON (f2p$key.value_id IN (" . implode(',',array_map('intval', $custom_group)) . ") AND f2p$key.product_id = p.product_id)";
                    }
                }
                
                $sql .= " LEFT JOIN " . DB_PREFIX . "tf_filter_value_to_product fv2p ON (p.product_id = fv2p.product_id) LEFT JOIN " . DB_PREFIX . "tf_filter_value fv ON (fv.value_id = fv2p.value_id) LEFT JOIN " . DB_PREFIX . "tf_filter f ON (f.filter_id = fv.filter_id) LEFT JOIN " . DB_PREFIX . "tf_filter_value_description fvd ON (fvd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND fv.value_id = fvd.value_id)";
                
                if($category_id){
                    if(!empty($data['filter_sub_category'])){
                        $sql .= " LEFT JOIN " . DB_PREFIX . "tf_filter_to_category f2c ON (f2c.filter_id = f.filter_id) LEFT JOIN " . DB_PREFIX . "category_path cp2 ON (cp2.category_id = f2c.category_id)";
                    } else {
                        $sql .= " LEFT JOIN " . DB_PREFIX . "tf_filter_to_category f2c ON (f2c.filter_id = f.filter_id)";
                    }
                }
                
                $sql .= " WHERE f.status = '1' AND fv.status = '1'";
                
                if (!empty($data['filter_filter_id'])) {
                        $sql .= " AND fv.filter_id = '" . (int)$data['filter_filter_id'] . "'";
                }
                
                if (!empty($data['filter_category_id'])) {
                    if(is_array($data['filter_category_id'])){
                        $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);

                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        } else {
                            $sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        }
                    }else{
                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                        } else {
                            $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                        }
                    }
                }
                
                if($category_id){
                    if(!empty($data['filter_sub_category'])){
                        $sql .= " AND cp2.path_id = '" . (int)$category_id . "'";
                    } else {
                        $sql .= " AND f2c.category_id = '" . (int)$category_id . "'";
                    }
                }
                
                if (!empty($data['filter_min_rating'])) {
                        $sql .= " AND IFNULL(p.rating, 0) >= '" . (int)$data['filter_min_rating'] . "'";
                }
                
                if(!empty($data['filter_min_special_perc'])){
                        $sql .= " AND IFNULL(p.special_perc, 0) >= '" . (int)$data['filter_min_special_perc'] . "'";
                }
                
                if (isset($data['filter_in_stock']) && $data['filter_in_stock'] !== '') {
                    if($data['filter_in_stock']){
                        $sql .= " AND p.quantity > 0";
                    } else {
                        $sql .= " AND p.quantity <= 0";
                    }
                } elseif (!empty($data['filter_stock_status'])) {
                    if (in_array(-1, $data['filter_stock_status'])) {
                        if (count($data['filter_stock_status']) > 1) {
                            $sql .= " AND (p.quantity > 0 OR (p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")))";
                        } else {
                            $sql .= " AND p.quantity > 0";
                        }
                    } else {
                        $sql .= " AND p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")";
                    }
                }
                
                if (!empty($data['filter_manufacturer_id'])) {
                    if(is_array($data['filter_manufacturer_id'])){
                        $sql .= " AND p.manufacturer_id IN (" . implode(',', array_map('intval', $data['filter_manufacturer_id'])) . ")";
                    } else {
                        $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
                    }
		        }
                
                if (!empty($data['filter_filter'])) {
                        $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                        $sql .= " AND pf.filter_id IN (" . implode(',', array_map('intval', $filters)) . ")";
                }
                
                $sql .= " GROUP BY fv.value_id";
                
                $query = $this->db->query($sql);

                return $query->rows;
        }
        
        /**
         * Get custom filters
         * @param String $product_table product table 
         * @param Array $data 
         * @return Array
         */
        public function getCustomFilters($product_table, $category_id = 0, $data = array()){
                $sql = "SELECT f.filter_id";
                
                if (!empty($data['filter_category_id'])) {
                        if (!empty($data['filter_sub_category'])) {
                                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                        } else {
                                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                        }

                        $sql .= " LEFT JOIN $product_table p ON (p2c.product_id = p.product_id)";
                } else {
                        $sql .= " FROM $product_table p";
                }
                
                if (!empty($data['filter_filter'])) {
                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
                }
                
                $sql .= " LEFT JOIN " . DB_PREFIX . "tf_filter_value_to_product fv2p ON (p.product_id = fv2p.product_id) LEFT JOIN " . DB_PREFIX . "tf_filter_value fv ON (fv.value_id = fv2p.value_id) LEFT JOIN " . DB_PREFIX . "tf_filter f ON (f.filter_id = fv.filter_id)";
                
                if($category_id){
                    if(!empty($data['filter_sub_category'])){
                        $sql .= " LEFT JOIN " . DB_PREFIX . "tf_filter_to_category f2c ON (f2c.filter_id = f.filter_id) LEFT JOIN " . DB_PREFIX . "category_path cp2 ON (cp2.category_id = f2c.category_id)";
                    } else {
                        $sql .= " LEFT JOIN " . DB_PREFIX . "tf_filter_to_category f2c ON (f2c.filter_id = f.filter_id)";
                    }
                }
                
                $sql .= " WHERE f.status = '1' AND fv.status = '1'";
                
                if (!empty($data['filter_category_id'])) {
                    if(is_array($data['filter_category_id'])){
                        $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);

                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        } else {
                            $sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        }
                    }else{
                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                        } else {
                            $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                        }
                    }
                }
                
                if($category_id){
                    if(!empty($data['filter_sub_category'])){
                        $sql .= " AND cp2.path_id = '" . (int)$category_id . "'";
                    } else {
                        $sql .= " AND f2c.category_id = '" . (int)$category_id . "'";
                    }
                }
                
                if (!empty($data['filter_min_rating'])) {
                        $sql .= " AND IFNULL(p.rating, 0) >= '" . (int)$data['filter_min_rating'] . "'";
                }
                
                if(!empty($data['filter_min_special_perc'])){
                        $sql .= " AND IFNULL(p.special_perc, 0) >= '" . (int)$data['filter_min_special_perc'] . "'";
                }
                
                if (isset($data['filter_in_stock']) && $data['filter_in_stock'] !== '') {
                    if($data['filter_in_stock']){
                        $sql .= " AND p.quantity > 0";
                    } else {
                        $sql .= " AND p.quantity <= 0";
                    }
                } elseif (!empty($data['filter_stock_status'])) {
                    if (in_array(-1, $data['filter_stock_status'])) {
                        if (count($data['filter_stock_status']) > 1) {
                            $sql .= " AND (p.quantity > 0 OR (p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")))";
                        } else {
                            $sql .= " AND p.quantity > 0";
                        }
                    } else {
                        $sql .= " AND p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")";
                    }
                }
                
                if (!empty($data['filter_manufacturer_id'])) {
                    if(is_array($data['filter_manufacturer_id'])){
                        $sql .= " AND p.manufacturer_id IN (" . implode(',', array_map('intval', $data['filter_manufacturer_id'])) . ")";
                    } else {
                        $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
                    }
		        }
                
                if (!empty($data['filter_filter'])) {
                        $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                        $sql .= " AND pf.filter_id IN (" . implode(',', array_map('intval', $filters)) . ")";
                }
                
                $sql .= " GROUP BY f.filter_id";
                
                $query = $this->db->query($sql);
                
                return $query->rows;
        }
        
        
        /**
         * Get list of custom filters detail by ids
         * @param Array $filter_ids
         * @return Array
         */
        public function getCustomFiltersByIds($filter_ids){
                if(empty($filter_ids)){
                    return array();
                }
                
                $query = $this->db->query("SELECT f.filter_id, f.sort_order, f.setting, fd.name FROM " . DB_PREFIX . "tf_filter f LEFT JOIN " . DB_PREFIX . "tf_filter_description fd ON (fd.language_id = '" . $this->config->get('config_language_id') . "' AND f.filter_id = fd.filter_id) WHERE f.status = '1' AND f.filter_id IN (" . implode(",", array_map('intval', $filter_ids)) . ")");

                foreach($query->rows as &$row){
                    $row['setting'] = json_decode($row['setting'], true);
                }

                return $query->rows;
        }
        
        /**
         * Get custom filter detail by value id
         * @param Array $value_ids
         * @return Array
         */
        public function getCustomFiltersIdByValue($value_ids){
                $query = $this->db->query("SELECT fv.filter_id, fv.value_id FROM " . DB_PREFIX . "tf_filter_value fv WHERE fv.status = '1' AND fv.value_id IN (" . implode(",", array_map('intval', $value_ids)) . ") GROUP BY fv.filter_id");

                return $query->rows;
        }
        
        
        /**
         * Get Ratings for filter
         * @param String $product_table product table
         * @param Array $data
         * @return Array
         */
        public function getRatings($product_table, $data = array()){
                $sql = 'SELECT FLOOR(p.rating) rating';
                
                if(!empty($data['field_total'])){
                    $sql .= ', COUNT(DISTINCT p.product_id) total';
                }
                
                if (!empty($data['filter_category_id'])) {
                        if (!empty($data['filter_sub_category'])) {
                                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                        } else {
                                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                        }

                        $sql .= " LEFT JOIN $product_table p ON (p2c.product_id = p.product_id)";
                } else {
                        $sql .= " FROM $product_table p";
                }
                
                if(!empty($data['filter_custom'])){
                    reset($data['filter_custom']);
                    
                    foreach($data['filter_custom'] as $key => $custom_group){
                        $sql .= " INNER JOIN " . DB_PREFIX . "tf_filter_value_to_product f2p$key ON (f2p$key.value_id IN (" . implode(',',array_map('intval', $custom_group)) . ") AND f2p$key.product_id = p.product_id)";
                    }
                }
                
                if (!empty($data['filter_filter'])) {
                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
                }
                
                $sql .= " WHERE p.rating IS NOT NULL";
                
                if (!empty($data['filter_category_id'])) {
                    if(is_array($data['filter_category_id'])){
                        $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);

                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        } else {
                            $sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        }
                    }else{
                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                        } else {
                            $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                        }
                    }
                }
                
                if (!empty($data['filter_manufacturer_id'])) {
                    if(is_array($data['filter_manufacturer_id'])){
                        $sql .= " AND p.manufacturer_id IN (" . implode(',', array_map('intval', $data['filter_manufacturer_id'])) . ")";
                    } else {
                        $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
                    }
		        }
                
                if (isset($data['filter_in_stock']) && $data['filter_in_stock'] !== '') {
                    if($data['filter_in_stock']){
                        $sql .= " AND p.quantity > 0";
                    } else {
                        $sql .= " AND p.quantity <= 0";
                    }
                } elseif (!empty($data['filter_stock_status'])) {
                    if (in_array(-1, $data['filter_stock_status'])) {
                        if (count($data['filter_stock_status']) > 1) {
                            $sql .= " AND (p.quantity > 0 OR (p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")))";
                        } else {
                            $sql .= " AND p.quantity > 0";
                        }
                    } else {
                        $sql .= " AND p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")";
                    }
                }
                
                if(!empty($data['filter_min_special_perc'])){
                        $sql .= " AND IFNULL(p.special_perc, 0) >= '" . (int)$data['filter_min_special_perc'] . "'";
                }
                
                if (!empty($data['filter_filter'])) {
                        $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                        $sql .= " AND pf.filter_id IN (" . implode(',', array_map('intval', $filters)) . ")";
                }
                
                $sql .= " GROUP BY FLOOR(p.rating)";
                
                $query = $this->db->query($sql);
                
                return $query->rows;
        }
        
        /**
         * Get discount list for filter
         * @param String $product_table product table
         * @param Array $data
         * @return Array
         */
        public function getDiscounts($product_table, $data = array()){
                $sql = 'SELECT';
                
                $discount_group_condition = array();
                
                foreach($data['discount_group'] as $dicount_rate){
                    $discount_group_condition[] = "WHEN p.special_perc >= '" . (int)$dicount_rate . "' THEN '" . (int)$dicount_rate . "'";
                }
                
                if($discount_group_condition){
                    $sql .= ' (CASE ' . implode(' ', $discount_group_condition) . ' END) discount';
                }
                
                if(!empty($data['field_total'])){
                    $sql .= ', COUNT(DISTINCT p.product_id) total';
                }
                
                if (!empty($data['filter_category_id'])) {
                        if (!empty($data['filter_sub_category'])) {
                                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                        } else {
                                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                        }

                        $sql .= " LEFT JOIN $product_table p ON (p2c.product_id = p.product_id)";
                } else {
                        $sql .= " FROM $product_table p";
                }
                
                if(!empty($data['filter_custom'])){
                    reset($data['filter_custom']);
                    
                    foreach($data['filter_custom'] as $key => $custom_group){
                        $sql .= " INNER JOIN " . DB_PREFIX . "tf_filter_value_to_product f2p$key ON (f2p$key.value_id IN (" . implode(',',array_map('intval', $custom_group)) . ") AND f2p$key.product_id = p.product_id)";
                    }
                }
                
                if (!empty($data['filter_filter'])) {
                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
                }
                
                $sql .= " WHERE p.special_perc IS NOT NULL";
                
                if (!empty($data['filter_category_id'])) {
                    if(is_array($data['filter_category_id'])){
                        $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);

                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        } else {
                            $sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
                        }
                    }else{
                        if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                        } else {
                            $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                        }
                    }
                }
                
                if (!empty($data['filter_manufacturer_id'])) {
                    if(is_array($data['filter_manufacturer_id'])){
                        $sql .= " AND p.manufacturer_id IN (" . implode(',', array_map('intval', $data['filter_manufacturer_id'])) . ")";
                    } else {
                        $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
                    }
		        }
                
                if (!empty($data['filter_min_rating'])) {
                        $sql .= " AND IFNULL(p.rating, 0) >= '" . (int)$data['filter_min_rating'] . "'";
                }
                
                if (isset($data['filter_in_stock']) && $data['filter_in_stock'] !== '') {
                    if($data['filter_in_stock']){
                        $sql .= " AND p.quantity > 0";
                    } else {
                        $sql .= " AND p.quantity <= 0";
                    }
                } elseif (!empty($data['filter_stock_status'])) {
                    if (in_array(-1, $data['filter_stock_status'])) {
                        if (count($data['filter_stock_status']) > 1) {
                            $sql .= " AND (p.quantity > 0 OR (p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")))";
                        } else {
                            $sql .= " AND p.quantity > 0";
                        }
                    } else {
                        $sql .= " AND p.quantity <= 0 AND p.stock_status_id IN (" . implode(',', array_map('intval', $data['filter_stock_status'])) . ")";
                    }
                }
                
                if (!empty($data['filter_filter'])) {
                        $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                        $sql .= " AND pf.filter_id IN (" . implode(',', array_map('intval', $filters)) . ")";
                }
                
                $sql .= ' GROUP BY (CASE ' . implode(' ', $discount_group_condition) . ' END)';
                
                $query = $this->db->query($sql);
                
                return $query->rows;
        }
}
