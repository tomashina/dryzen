<?php
class ModelExtensionMazaTfProduct extends Model {
	public function getProducts($data = array()) {
                // Get sort order list
                $sort = array();
                if(isset($data['sort_order'])){
                    $sort = array_column($data['sort_order'], 'sort');
                }
                if(isset($data['sort'])){
                    array_push($sort, $data['sort']);
                }
                
                $temp_table = false;
                
                // Create temporary table if require for some filters
                if(isset($data['filter_special']) || !empty($data['filter_min_special_perc']) || !empty($data['filter_rating']) || !empty($data['filter_min_rating']) || !empty($data['filter_max_rating']) || !empty($data['filter_min_price']) || !empty($data['filter_max_price'])){
                        $additional_field = array('p.sort_order');

                        if(in_array('pd.name', $sort)){
                            $additional_field[] = 'pd.name';
                        }

                        if(in_array('p.model', $sort)){
                            $additional_field[] = 'p.model';
                        }

                        if(in_array('p.quantity', $sort) || in_array('stock_status', $sort)){
			    $additional_field[] = 'p.quantity';
			}

			if (in_array('stock_status', $sort)) {
			    $additional_field[] = 'p.stock_status_id';
			}

                        if(in_array('p.date_added', $sort)){
                            $additional_field[] = 'p.date_added';
                        }

                        if(in_array('p.viewed', $sort)){
                            $additional_field[] = 'p.viewed';
                        }

                        if(in_array('p.price', $sort)){
                            $additional_field[] = 'price';
                        }

                        if(in_array('order_quantity', $sort)){
                            $additional_field[] = 'order_quantity';
                        }

                        if(in_array('rating', $sort)){
                            $additional_field[] = 'rating';
                        }
                        
                        $this->createTempTable(DB_PREFIX . 'tf_product_result', $additional_field, $data);
                        
                        $temp_table = true;
                        
                        $sql = 'SELECT product_id FROM ' . DB_PREFIX . 'tf_product_result';
                } else {
                        $sql = "SELECT p.product_id";

                        // Get total overall purchase quantity
                        if(in_array('order_quantity', $sort)){
                            $sql .= ", (SELECT SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) WHERE op.product_id = p.product_id AND o.order_status_id > '0' GROUP BY op.product_id LIMIT 1) AS order_quantity";
                        }

                        // Estimate average rating per product
                        if(in_array('rating', $sort)){
                            $sql .= ", (SELECT AVG(rating) FROM " . DB_PREFIX . "review WHERE product_id = p.product_id AND status = '1' GROUP BY product_id) AS rating";
                        }

                        // Estimate special and discounted lowest price based on priority per product
                        if(in_array('p.price', $sort)){
                            $sql .= ", (SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = p.product_id AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity = '1' AND (((CAST(date_start AS CHAR) = '0000-00-00') OR date_start < NOW()) AND ((CAST(date_end AS CHAR) = '0000-00-00') OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1) AS discount";
                            $sql .= ", (SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = p.product_id AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND (((CAST(date_start AS CHAR) = '0000-00-00') OR date_start < NOW()) AND ((CAST(date_end AS CHAR) = '0000-00-00') OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1) AS special";
                        }

                        if (!empty($data['filter_category_id'])) {
                                if (!empty($data['filter_sub_category'])) {
                                        $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                                } else {
                                        $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                                }

                                if (!empty($data['filter_filter'])) {
                                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
                                } else {
                                        $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
                                }
                        } else {
                                $sql .= " FROM " . DB_PREFIX . "product p";

                                if (!empty($data['filter_filter'])) {
                                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
                                }
                        }

                        // check product_description table is require to use
                        if (in_array('pd.name', $sort) || !empty($data['filter_name']) || !empty($data['filter_tag'])) {
                            $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.product_id = pd.product_id)";
                        }

                        if(!empty($data['filter_custom'])){
                            reset($data['filter_custom']);
                            foreach($data['filter_custom'] as $key => $custom_group){
                                $sql .= " INNER JOIN " . DB_PREFIX . "tf_filter_value_to_product f2p$key ON (f2p$key.value_id IN (" . implode(',',array_map('intval', $custom_group)) . ") AND f2p$key.product_id = p.product_id)";
                            }
                        }

                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

                        if (!empty($data['filter_category_id'])) {
                            if(is_array($data['filter_category_id'])){
                                $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);

                                if (!empty($data['filter_sub_category'])) {
//                                        if(is_array($data['filter_sub_category'])){
//                                            $sql .= " AND cp.path_id IN (" . implode(',', array_map('intval', $data['filter_sub_category'])) . ")";
//                                        } else {
                                            $sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
//                                        }
                                        
                                        if(!empty($data['filter_sub_category_depth'])){
                                            $sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
                                        }
                                } else {
                                        $sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
                                }
                            }else{
                                if (!empty($data['filter_sub_category'])) {
//                                        if(is_array($data['filter_sub_category'])){
//                                            $sql .= " AND cp.path_id IN (" . implode(',', array_map('intval', $data['filter_sub_category'])) . ")";
//                                        } else {
                                            $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
//                                        }
                                        
                                        if(!empty($data['filter_sub_category_depth'])){
                                            $sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
                                        }
                                } else {
                                        $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                                }
                            }
                        }

                        if (!empty($data['filter_filter'])) {
                                $implode = array();

                                $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                                foreach ($filters as $filter_id) {
                                        $implode[] = (int)$filter_id;
                                }

                                $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
                        }

                        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                                $sql .= " AND (";

                                if (!empty($data['filter_name'])) {
                                        $implode = array();

                                        $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                                        foreach ($words as $word) {
                                                $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                                        }

                                        if ($implode) {
                                                $sql .= " " . implode(" AND ", $implode) . "";
                                        }

                                        if (!empty($data['filter_description'])) {
                                                $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                                        }
                                }

                                if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                                        $sql .= " OR ";
                                }

                                if (!empty($data['filter_tag'])) {
                                        $implode = array();

                                        $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

                                        foreach ($words as $word) {
                                                $implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
                                        }

                                        if ($implode) {
                                                $sql .= " " . implode(" AND ", $implode) . "";
                                        }
                                }

                                if (!empty($data['filter_name'])) {
                                        $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                }

                                $sql .= ")";
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

                        if (!empty($data['filter_min_quantity'])) {
                                $sql .= " AND p.quantity >= '" . (int)$data['filter_min_quantity'] . "'";
                        }

                        if (!empty($data['filter_max_quantity'])) {
                                $sql .= " AND p.quantity <= '" . (int)$data['filter_max_quantity'] . "'";
                        }

                        if (!empty($data['filter_date_add_start'])) {
                                $sql .= " AND p.date_added >= '" . $this->db->escape($data['filter_date_add_start']) . "'";
                        }

                        if (!empty($data['filter_date_add_end'])) {
                                $sql .= " AND p.date_added <= '" . $this->db->escape($data['filter_date_add_end']) . "'";
                        }

                        $sql .= " GROUP BY p.product_id";
                }
                
                $sort_data = array(
			'pd.name'       => 'LCASE(name)',
			'p.model'       => 'LCASE(model)',
			'p.quantity' 	=> 'quantity',
			'stock_status' 	=> '(CASE WHEN quantity <= 0 THEN stock_status_id ELSE -1 END)',
			'p.price'       => '(CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)',
			'rating'        => 'rating',
			'order_quantity'=> 'order_quantity',
			'p.sort_order' 	=> 'sort_order',
			'p.date_added' 	=> 'date_added',
			'pd.date_added' 	=> 'date_added',
			'p.viewed'      => 'viewed',
			'random' 	=> 'RAND()'
		);
                
		// sort by multiple sort and order
		if(isset($data['sort_order'])){
			$sql_order_by = TRUE;
			
			foreach ($data['sort_order'] as $sort_order) {
				if (isset($sort_data[$sort_order['sort']])) {
					$sql .= (($sql_order_by)?' ORDER BY ':', ') . $sort_data[$sort_order['sort']];
				}

				if (isset($sort_order['order']) && ($sort_order['order'] == 'DESC')) {
					$sql .= ' DESC';
				} else {
					$sql .= ' ASC';
				}

				$sql_order_by = FALSE;
			}
		} elseif (isset($data['sort'])) {
			if (isset($sort_data[$data['sort']])) {
				$sql .= ' ORDER BY ' . $sort_data[$data['sort']];
			}
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
                                $sql .= ' DESC, product_id DESC';
			} else {
                                $sql .= ' ASC, product_id ASC';
			}
		} else {
			$sql .= ' ORDER BY sort_order';
						
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= ' DESC, product_id DESC';
			} else {
				$sql .= ' ASC, product_id ASC';
			}
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();
                
		$query = $this->db->query($sql);
                
                if($temp_table){
                    $this->dropTempTable(DB_PREFIX . 'tf_product_result');
                }

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}
                
		return $product_data;
	}
        
        public function getTotalProducts($data = array()) {
                // Create temporary table if require for some filters
                $temp_table = false;
                if(isset($data['filter_special']) || !empty($data['filter_min_special_perc']) || !empty($data['filter_rating']) || !empty($data['filter_min_rating']) || !empty($data['filter_max_rating']) || !empty($data['filter_min_price']) || !empty($data['filter_max_price'])){
                        $this->createTempTable(DB_PREFIX . 'tf_product_result', array(), $data);
                        $temp_table = true;

                        $sql = 'SELECT COUNT(DISTINCT product_id) AS total FROM ' . DB_PREFIX . 'tf_product_result';
                } else {
                        $sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

                        if (!empty($data['filter_category_id'])) {
                                if (!empty($data['filter_sub_category'])) {
                                        $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                                } else {
                                        $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                                }

                                if (!empty($data['filter_filter'])) {
                                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
                                } else {
                                        $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
                                }
                        } else {
                                $sql .= " FROM " . DB_PREFIX . "product p";

                                if (!empty($data['filter_filter'])) {
                                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
                                }
                        }

                        // check product_description table is require to use
                        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                            $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.product_id = pd.product_id)";
                        }

                        if(!empty($data['filter_custom'])){
                            reset($data['filter_custom']);
                            foreach($data['filter_custom'] as $key => $custom_group){
                                $sql .= " INNER JOIN " . DB_PREFIX . "tf_filter_value_to_product f2p$key ON (f2p$key.value_id IN (" . implode(',',array_map('intval', $custom_group)) . ") AND f2p$key.product_id = p.product_id)";
                            }
                        }

                        $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

                        if (!empty($data['filter_category_id'])) {
                            if(is_array($data['filter_category_id'])){
                                $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);

                                if (!empty($data['filter_sub_category'])) {
//                                        if(is_array($data['filter_sub_category'])){
//                                            $sql .= " AND cp.path_id IN (" . implode(',', array_map('intval', $data['filter_sub_category'])) . ")";
//                                        } else {
                                            $sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
//                                        }
                                        
                                        if(!empty($data['filter_sub_category_depth'])){
                                            $sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
                                        }
                                } else {
                                        $sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
                                }
                            }else{
                                if (!empty($data['filter_sub_category'])) {
//                                        if(is_array($data['filter_sub_category'])){
//                                            $sql .= " AND cp.path_id IN (" . implode(',', array_map('intval', $data['filter_sub_category'])) . ")";
//                                        } else {
                                            $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
//                                        }
                                        
                                        if(!empty($data['filter_sub_category_depth'])){
                                            $sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
                                        }
                                } else {
                                        $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                                }
                            }
                        }

                        if (!empty($data['filter_filter'])) {
                                $implode = array();

                                $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                                foreach ($filters as $filter_id) {
                                        $implode[] = (int)$filter_id;
                                }

                                $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
                        }

                        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                                $sql .= " AND (";

                                if (!empty($data['filter_name'])) {
                                        $implode = array();

                                        $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                                        foreach ($words as $word) {
                                                $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                                        }

                                        if ($implode) {
                                                $sql .= " " . implode(" AND ", $implode) . "";
                                        }

                                        if (!empty($data['filter_description'])) {
                                                $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                                        }
                                }

                                if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                                        $sql .= " OR ";
                                }

                                if (!empty($data['filter_tag'])) {
                                        $implode = array();

                                        $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

                                        foreach ($words as $word) {
                                                $implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
                                        }

                                        if ($implode) {
                                                $sql .= " " . implode(" AND ", $implode) . "";
                                        }
                                }

                                if (!empty($data['filter_name'])) {
                                        $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                        $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                                }

                                $sql .= ")";
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

                        if (!empty($data['filter_min_quantity'])) {
                                $sql .= " AND p.quantity >= '" . (int)$data['filter_min_quantity'] . "'";
                        }

                        if (!empty($data['filter_max_quantity'])) {
                                $sql .= " AND p.quantity <= '" . (int)$data['filter_max_quantity'] . "'";
                        }

                        if (!empty($data['filter_date_add_start'])) {
                                $sql .= " AND p.date_added >= '" . $this->db->escape($data['filter_date_add_start']) . "'";
                        }

                        if (!empty($data['filter_date_add_end'])) {
                                $sql .= " AND p.date_added <= '" . $this->db->escape($data['filter_date_add_end']) . "'";
                        }
                }

		$query = $this->db->query($sql);
                
                if($temp_table){
                    $this->dropTempTable(DB_PREFIX . 'tf_product_result');
                }
                
		return $query->row['total'];
	}
        
        
        /**
         * Create temporary table for product data
         * @param String $table_name
         * @param String $product_table temporary product table
         * @param Array $data filter products
         */
        public function createTempTable($table_name, $additional_field = array(), $data = array(), $product_table = null) {
                $query = $this->db->query("SELECT COUNT(*) total FROM information_schema.TABLES WHERE (TABLE_SCHEMA = '" . $this->db->escape(DB_DATABASE) . "') AND (TABLE_NAME = '" . $this->db->escape($table_name) . "')");
                
                if($query->row['total']){
                    throw new Exception("Can not create a temporary table with the same name of any permanent table in a database!");
                }
                
                // Product table
                if(!$product_table){
                    $product_table = DB_PREFIX . "product";
                }
            
		$sql = "CREATE TEMPORARY TABLE $table_name SELECT p.product_id";
                
                if(in_array('p.manufacturer_id', $additional_field)){
                    $sql .= ', p.manufacturer_id';
                }

                if(in_array('p.stock_status_id', $additional_field)){
		    $sql .= ', p.stock_status_id';
		}
                
                if(in_array('p.quantity', $additional_field)){
                    $sql .= ', p.quantity';
                }
                
                if(in_array('pd.name', $additional_field)){
                    $sql .= ", pd.name";
                }
                
                if(in_array('p.model', $additional_field)){
                    $sql .= ", p.model";
                }
                
                if(in_array('p.date_added', $additional_field)){
                    $sql .= ", p.date_added";
                }
                
                if(in_array('p.viewed', $additional_field)){
                    $sql .= ", p.viewed";
                }
                
                if(in_array('p.sort_order', $additional_field)){
                    $sql .= ", p.sort_order";
                }
                
                // Get total overall purchase quantity
                if($product_table == DB_PREFIX . 'product'){
                    if(in_array('order_quantity', $additional_field)){
                        $sql .= ", (SELECT SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) WHERE op.product_id = p.product_id AND o.order_status_id > '0' GROUP BY op.product_id LIMIT 1) AS order_quantity";
                    }

                    // Estimate average rating per product
                    if(in_array('rating', $additional_field) || !empty($data['filter_min_rating']) || !empty($data['filter_max_rating']) || !empty($data['filter_rating'])){
                        $sql .= ", (SELECT AVG(rating) FROM " . DB_PREFIX . "review WHERE product_id = p.product_id AND status = '1' GROUP BY product_id) AS rating";
                    }

                    // Estimate special and discounted lowest price based on priority per product
                    if(in_array('price', $additional_field) || !empty($data['filter_min_price']) || !empty($data['filter_max_price']) || isset($data['filter_special'])){
                        if($this->config->get('config_tax')){
                            $sql .= ", (p.price + IFNULL(ftax.total,0) + ((IFNULL(ptax.total,0) * p.price) / 100)) price";
                            $sql .= ", (SELECT price + IFNULL(ftax.total,0) + ((IFNULL(ptax.total,0) * price) / 100) FROM " . DB_PREFIX . "product_discount WHERE product_id = p.product_id AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity = '1' AND (((CAST(date_start AS CHAR) = '0000-00-00') OR date_start < NOW()) AND ((CAST(date_end AS CHAR) = '0000-00-00') OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1) AS discount";
                            $sql .= ", (SELECT price + IFNULL(ftax.total,0) + ((IFNULL(ptax.total,0) * price) / 100) FROM " . DB_PREFIX . "product_special WHERE product_id = p.product_id AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND (((CAST(date_start AS CHAR) = '0000-00-00') OR date_start < NOW()) AND ((CAST(date_end AS CHAR) = '0000-00-00') OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1) AS special";
                        } else {
                            $sql .= ", p.price";
                            $sql .= ", (SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = p.product_id AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity = '1' AND (((CAST(date_start AS CHAR) = '0000-00-00') OR date_start < NOW()) AND ((CAST(date_end AS CHAR) = '0000-00-00') OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1) AS discount";
                            $sql .= ", (SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = p.product_id AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND (((CAST(date_start AS CHAR) = '0000-00-00') OR date_start < NOW()) AND ((CAST(date_end AS CHAR) = '0000-00-00') OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1) AS special";
                        }
                    }
                    
                    // Add special discount percentage
                    if(in_array('special_perc', $additional_field) || !empty($data['filter_min_special_perc'])){
                        $sql .= ", (SELECT 100 - ((price * 100) / p.price) FROM " . DB_PREFIX . "product_special WHERE product_id = p.product_id AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND (((CAST(date_start AS CHAR) = '0000-00-00') OR date_start < NOW()) AND ((CAST(date_end AS CHAR) = '0000-00-00') OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1) AS special_perc";
                    }
                }
                
                
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN $product_table p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM $product_table p";
                        
                        if (!empty($data['filter_filter'])) {
                                $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";
                        }
		}
                
                // check product_description table is require to use
                if (in_array('pd.name', $additional_field) || !empty($data['filter_name']) || !empty($data['filter_tag'])) {
                    $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.product_id = pd.product_id)";
                }
                
                if($this->config->get('config_tax') && (!empty($data['filter_min_price']) || !empty($data['filter_max_price']) || isset($data['filter_special']) || in_array('price', $additional_field))){
                    $sql .= " LEFT JOIN (SELECT tax_class_id, SUM(`rate`) total FROM " . DB_PREFIX . "tf_user_ftax_rates GROUP BY tax_class_id) ftax ON (ftax.tax_class_id = p.tax_class_id)";
                    $sql .= " LEFT JOIN (SELECT tax_class_id, SUM(`rate`) total FROM " . DB_PREFIX . "tf_user_ptax_rates GROUP BY tax_class_id) ptax ON (ptax.tax_class_id = p.tax_class_id)";
                }
                
                if(!empty($data['filter_custom'])){
                    reset($data['filter_custom']);
                    foreach($data['filter_custom'] as $key => $custom_group){
                        $sql .= " INNER JOIN " . DB_PREFIX . "tf_filter_value_to_product f2p$key ON (f2p$key.value_id IN (" . implode(',',array_map('intval', $custom_group)) . ") AND f2p$key.product_id = p.product_id)";
                    }
                }

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                
		if (!empty($data['filter_category_id'])) {
                    if(is_array($data['filter_category_id'])){
                        $data['filter_category_id'] = array_map('intval', $data['filter_category_id']);
                        
                        if (!empty($data['filter_sub_category'])) {
//                                if(is_array($data['filter_sub_category'])){
//                                    $sql .= " AND cp.path_id IN (" . implode(',', array_map('intval', $data['filter_sub_category'])) . ")";
//                                } else {
                                    $sql .= " AND cp.path_id IN (" . implode(',', $data['filter_category_id']) . ")";
//                                }
                                        
                                if(!empty($data['filter_sub_category_depth'])){
                                    $sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
                                }
			} else {
				$sql .= " AND p2c.category_id IN (" . implode(',', $data['filter_category_id']) . ")";
			}
                    }else{
			if (!empty($data['filter_sub_category'])) {
//                                if(is_array($data['filter_sub_category'])){
//                                    $sql .= " AND cp.path_id IN (" . implode(',', array_map('intval', $data['filter_sub_category'])) . ")";
//                                } else {
                                    $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
//                                }
                                        
                                if(!empty($data['filter_sub_category_depth'])){
                                    $sql .= " AND cp.level <= '".(int)$data['filter_sub_category_depth']."'";
                                }
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
                    }
		}
                
                if (!empty($data['filter_filter'])) {
                        $implode = array();

                        $filters = is_array($data['filter_filter'])?$data['filter_filter']:explode(',', $data['filter_filter']);

                        foreach ($filters as $filter_id) {
                                $implode[] = (int)$filter_id;
                        }

                        $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
                }

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
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
                
                if (!empty($data['filter_min_quantity'])) {
			$sql .= " AND p.quantity >= '" . (int)$data['filter_min_quantity'] . "'";
		}
                
                if (!empty($data['filter_max_quantity'])) {
			$sql .= " AND p.quantity <= '" . (int)$data['filter_max_quantity'] . "'";
		}
                
                if (!empty($data['filter_date_add_start'])) {
			$sql .= " AND p.date_added >= '" . $this->db->escape($data['filter_date_add_start']) . "'";
		}
                
                if (!empty($data['filter_date_add_end'])) {
			$sql .= " AND p.date_added <= '" . $this->db->escape($data['filter_date_add_end']) . "'";
		}
                
                if($product_table !== DB_PREFIX . 'product'){
                        if(isset($data['filter_special'])){
                            $sql .= " AND special IS " . ($data['filter_special']?'NOT':'') . " NULL";
                        }
                        
                        if(!empty($data['filter_min_special_perc'])){
                            $sql .= " AND IFNULL(special_perc, 0) >= '" . (int)$data['filter_min_special_perc'] . "'";
                        }

                        if (!empty($data['filter_rating'])) {
                            if(is_array($data['filter_rating'])){
                                $sql .= " AND IFNULL(rating, 0) IN (" . implode(',', array_map('intval', $data['filter_rating'])) . ")";
                            } else {
                                $sql .= " AND IFNULL(rating, 0) = '" . (int)$data['filter_rating'] . "'";
                            }
                        }

                        if (!empty($data['filter_min_rating'])) {
                                $sql .= " AND IFNULL(rating, 0) >= '" . (int)$data['filter_min_rating'] . "'";
                        }

                        if (!empty($data['filter_max_rating'])) {
                                $sql .= " AND IFNULL(rating, 0) <= '" . (int)$data['filter_max_rating'] . "'";
                        }

                        if(!empty($data['filter_min_price'])){
                                $sql .= " AND (CASE WHEN special IS NOT NULL THEN special >= '" . (float)$data['filter_min_price'] . "' WHEN discount IS NOT NULL THEN discount >= '" . (float)$data['filter_min_price'] . "' ELSE price >= '" . (float)$data['filter_min_price'] . "' END)";
                        }

                        if(!empty($data['filter_max_price'])){
                                $sql .= " AND (CASE WHEN special IS NOT NULL THEN special <= '" . (float)$data['filter_max_price'] . "' WHEN discount IS NOT NULL THEN discount <= '" . (float)$data['filter_max_price'] . "' ELSE price <= '" . (float)$data['filter_max_price'] . "' END)";
                        }
                }
                
                $sql .= " GROUP BY p.product_id";
		
		$this->db->query($sql);
                
                if($product_table == DB_PREFIX . 'product'){
                    $delete_condition = '';
                
                    if(isset($data['filter_special'])){
                            $delete_condition .= " OR special IS " . ($data['filter_special']?'':'NOT') . " NULL";
                    }
                    
                    if(!empty($data['filter_min_special_perc'])){
                            $delete_condition .= " OR IFNULL(special_perc, 0) < '" . (int)$data['filter_min_special_perc'] . "'";
                    }

                    if (!empty($data['filter_rating'])) {
                        if(is_array($data['filter_rating'])){
                            $delete_condition .= " OR IFNULL(rating, 0) NOT IN (" . implode(',', array_map('intval', $data['filter_rating'])) . ")";
                        } else {
                            $delete_condition .= " OR IFNULL(rating, 0) != '" . (int)$data['filter_rating'] . "'";
                        }
                    }

                    if (!empty($data['filter_min_rating'])) {
                            $delete_condition .= " OR IFNULL(rating, 0) < '" . (int)$data['filter_min_rating'] . "'";
                    }

                    if (!empty($data['filter_max_rating'])) {
                            $delete_condition .= " OR IFNULL(rating, 0) > '" . (int)$data['filter_max_rating'] . "'";
                    }

                    if(!empty($data['filter_min_price'])){
                            $delete_condition .= " OR (CASE WHEN special IS NOT NULL THEN special < '" . (float)$data['filter_min_price'] . "' WHEN discount IS NOT NULL THEN discount < '" . (float)$data['filter_min_price'] . "' ELSE price < '" . (float)$data['filter_min_price'] . "' END)";
                    }

                    if(!empty($data['filter_max_price'])){
                            $delete_condition .= " OR (CASE WHEN special IS NOT NULL THEN special > '" . (float)$data['filter_max_price'] . "' WHEN discount IS NOT NULL THEN discount > '" . (float)$data['filter_max_price'] . "' ELSE price > '" . (float)$data['filter_max_price'] . "' END)";
                    }

                    if($delete_condition){
                        $this->db->query("DELETE FROM $table_name WHERE 0$delete_condition");
                    }
                }
                
	}
        
        /**
         * Delete temporary table to free memory
         * @param String $product_table table name
         */
        public function dropTempTable($product_table){
                $query = $this->db->query("SELECT COUNT(*) total FROM information_schema.TABLES WHERE (TABLE_SCHEMA = '" . $this->db->escape(DB_DATABASE) . "') AND (TABLE_NAME = '" . $this->db->escape($product_table) . "')");
                
                if($query->row['total']){
                    throw new Exception("Can not delete any permanent table in a database!");
                }
                
                $this->db->query("DROP TEMPORARY TABLE $product_table");
        }
}
