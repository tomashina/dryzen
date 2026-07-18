<?php

class Msmart_Search {
	
	private $_ctrl;
	
	private $_settings;
	
	private $_config;
	
	private $_name = 'msmart_search';
	
	private $_customer_group_id;
	
	private $_language_id;
	
	private $_store_id;
	
	private $_data = array();
	
	private $_sqls = array();
	
	private $_limit;
	
	private $_results;
	
	private $_mfp = false;
	
	private $_cache = array();
	
	private $_extra_fields = array();
	
	private $_joins = array();
	
	private $_table_aliases = array(
		'product' => 'p',
		'product_description' => 'pd',
		
		'manufacturer' => 'm',
		
		'attribute' => 'a',
		'attribute_description' => 'ad',
		'attribute_group' => 'ag',
		'attribute_group_description' => 'agd',
		
		'option' => 'o',
		'option_description' => 'od',
		'option_value' => 'ov',
		'option_value_description' => 'ovd',
	);
	
	private static $_instance;
	
	public static function make( & $ctrl ) {
		if( ! self::$_instance ) {
			self::$_instance = new self( $ctrl );
		}
		
		return self::$_instance;
	}
	
	public function reset() {
		$this->_results = null;
		
		return $this;
	}
	
	
	private function __construct( & $ctrl ) {
		$this->_ctrl = & $ctrl;
		$this->_config = (array) $ctrl->config->get( $this->_name );
		$this->_settings = (array) $ctrl->config->get( $this->_name . '_s' );
		$this->_customer_group_id = $ctrl->customer->isLogged() ? $ctrl->customer->getGroupId() : (int) $ctrl->config->get('config_customer_group_id');
		$this->_language_id = (int) $ctrl->config->get('config_language_id');
		$this->_store_id = (int) $ctrl->config->get('config_store_id');
		
		$this->initExtraFields();
	}
	
	private function initExtraFields() {
		/* @var $extra_field_ids array */
		$extra_field_ids = array();
		
		foreach( array( 'fields', 'fields_categories' ) as $type ) {
			if( empty( $this->_config[$type] ) ) continue;
			
			foreach( $this->_config[$type] as $name => $params ) {
				if( strpos( $name, 'extra_field_' ) === 0 && isset( $params['sort_order'] ) ) {
					$extra_field_ids[] = (int) str_replace( 'extra_field_', '', $name );
				}
			}
		}
		
		if( $extra_field_ids ) {
			foreach( $this->_ctrl->db->query( "SELECT * FROM `" . DB_PREFIX . "msmart_search_extra_field` WHERE `id` IN(" . implode( ',', $extra_field_ids ) . ")")->rows as $extra_field ) {
				/* @var $name string */
				$name = 'extra_field_' . $extra_field['id'];
				
				/* @var $config array */
				$config = json_decode( $extra_field['config'], true );
				
				$this->_extra_fields[$name] = array_replace( $extra_field, array(
					'config' => $config,
				));
				
				if( ! empty( $config['joins'] ) ) {
					foreach( $config['joins'] as $join ) {
						$this->_joins[$join['table']] = "
							LEFT JOIN 
								`" . DB_PREFIX . $join['table'] . "` AS `" . $this->tableAlias( $join['table'] ) . "`
							ON
								`" . $this->tableAlias( $join['table'] ) . "`.`" . $join['column'] . "` = `" . $this->tableAlias( $join['on_table'] ) . "`.`" . $join['on_column'] . "`";
						
						if( ! empty( $join['table_lc'] ) ) {
							$this->_joins[$join['table']] .= " AND `" . $this->tableAlias( $join['table'] ) . "`.`language_id` = " . $this->_language_id;
						}
					}
				}
			}
		}
	}
	
	public function filterData( $data ) {
		/* @var $is_autocomplete bool */
		$is_autocomplete = false;
		
		if( isset( $data['is_autocomplete'] ) ) {
			$is_autocomplete = $data['is_autocomplete'];
			
			unset( $data['is_autocomplete'] );
		}
		
		$this->_data = $data;
		
		/* @var $settings array */
		$settings = (array) $this->_ctrl->config->get( $this->_name . '_s' );

		if( ! empty( $settings['history_enabled'] ) && ! $is_autocomplete ) {
			/* @var $phrase string */
			$phrase = null;

			if( isset( $this->_data['filter_name'] ) ) {
				$phrase = $this->_data['filter_name'];
			} else if( isset( $this->_data['filter_tag'] ) ) {
				$phrase = $this->_data['filter_tag'];
			}

			$this->_ctrl->load->model('extension/module/msmart_search');
			$this->_ctrl->model_extension_module_msmart_search->addToDatabase( $phrase );
		}
		
		if( isset( $this->_data['filter_name'] ) ) {
			$this->_ctrl->load->model('extension/module/msmart_search');
			
			if(($replacedPhrase = $this->_ctrl->model_extension_module_msmart_search->checkPhrase($this->_data['filter_name'])) !== false){
				$this->_data['filter_name'] = $replacedPhrase;
			}
		}
		
		if( isset( $this->_data['filter_tag'] ) ) {
			$this->_ctrl->load->model('extension/module/msmart_search');
			
			if(($replacedPhrase = $this->_ctrl->model_extension_module_msmart_search->checkPhrase($this->_data['filter_tag'])) !== false){
				$this->_data['filter_tag'] = $replacedPhrase;
			}
		}
		
		if( isset( $data['limit'] ) ) {
			$this->_limit = (int) $data['limit'];
			
			if( $this->_limit < 1 ) {
				$this->_limit = 20;
			}
		}
		
		return $this;
	}
	
	public function getTotalProducts() {
		$this->prepare();
		
		if( ! $this->_results ) {
			return 0;
		}
		
		/* @var $count int */
		$count = 0;
		
		foreach( $this->_sqls as $sql ) {
			if( is_int( $sql ) ) {
				$count += $sql;
			} else {
				$sql = preg_replace( '/\s+LIMIT\s+[0-9]+(,\s*[0-9]+)?$/i', '', trim( $sql ) );
				$count += $this->cacheQuery( "SELECT COUNT( DISTINCT `product_id` ) AS `total` FROM(" . $sql . ") AS `tmp`" )->row['total'];
			}
		}
		
		return $count;
	}
	
	public function getProducts() {
		$this->prepare();
		
		if( empty( $this->_results ) ) {
			return array();
		}
		
		$this->load->model('catalog/product');
		
		/* @var $results array */
		$results = array();
		
		foreach( $this->_results as $result ) {
			$results[] = $this->model_catalog_product->getProduct( $result['product_id'] );
			
			if( $this->_limit !== null && count( $results ) >= $this->_limit ) break;
		}
		
		if( isset( $this->_data['sort'] ) && $this->_data['order'] ) {
			usort( $results, array( $this, '__sortResults' ) );
		}
		
		return $results;
	}
	
	public function __sortResults( $a, $b ) {
		foreach( array( 'special', 'discount', 'model', 'name', 'rating', 'sort_order' ) as $n ) {
			if( ! isset( $a[$n] ) ) $a[$n] = null;
			if( ! isset( $b[$n] ) ) $b[$n] = null;
		}
		
		switch( $this->_data['sort'] ) {
			case 'p.price' :
				$x = $a['price'];
				$y = $b['price'];
				
				if( ! empty( $a['special'] ) ) {
					$x = $a['special'];
				} else if( ! empty( $a['discount'] ) ) {
					$x = $a['discount'];
				}
				
				if( ! empty( $b['special'] ) ) {
					$y = $b['special'];
				} else if( ! empty( $b['discount'] ) ) {
					$y = $b['discount'];
				}
				
				return $this->_data['order'] == 'ASC' ? $x - $y : $y - $x;
			
			case 'p.model' :
				$key = 'model';
				
			case 'pd.name' :
				if( empty( $key ) ) {
					$key = 'name';
				}
				
				if( $a[$key] == $b[$key] ) return 0;
				
				$sort = array( $a[$key], $b[$key] );
				
				sort( $sort, SORT_STRING );
				
				if( $a[$key] == $sort[0] ) return $this->_data['order'] == 'ASC' ? -1 : 1;
				
				return $this->_data['order'] == 'ASC' ? 1 : -1;
				
			case 'rating' :
				return $this->_data['order'] == 'ASC' ? $a['rating'] - $b['rating'] : $b['rating'] - $a['rating'];
				
			default: 
				return $this->_data['order'] == 'ASC' ? $a['sort_order'] - $b['sort_order'] : $b['sort_order'] - $a['sort_order'];
		}
	}
	
	private function sortFields( $cfields, $products = false ) {
		/* @var $fields array */
		$fields = array();
		
		/* @var $max int */
		$max = 0;
		
		/* @var $min int */
		$min = 0;
		
		foreach( $cfields as $options ) {
			if( (int) $options['sort_order'] > $max ) {
				$max = (int) $options['sort_order'];
			}
			
			if( (int) $options['sort_order'] < $min ) {
				$min = (int) $options['sort_order'];
			}
		}
		
		foreach( $cfields as $name => $options ) {
			if( $options['sort_order'] !== '' ) {
				$fields[$name] = $options['sort_order'] === '' ? $max+1 : (int) $options['sort_order'];
			}
		}
		
		if( $products ) {
			if( ! isset( $fields['description'] ) && ! empty( $this->_data['filter_description'] ) && in_array( $this->_data['filter_description'], array( 'true', '1' ) ) ) {
				$fields['description'] = $min;
			}
		}
		
		asort( $fields );
		
		return $fields;
	}
	
	private function tableAlias( $table ) {
		if( isset( $this->_table_aliases[$table] ) ) {
			return $this->_table_aliases[$table];
		}
		
		return $table;
	}
	
	private function fields() {
		/* @var $fields array */
		$fields = $this->sortFields( $this->_config['fields'], true );
		
		/* @var $map array */
		$map = array(
			'name' => '`pd`.`name`',
			'manufacturer' => '`m`.`name`',
			'description' => '`pd`.`description`',
			'model' => '`p`.`model`',
			'sku' => '`p`.`sku`',
			'upc' => '`p`.`upc`',
			'ean' => '`p`.`ean`',
			'jan' => '`p`.`jan`',
			'isbn' => '`p`.`isbn`',
			'mpn' => '`p`.`mpn`',
			'location' => '`p`.`location`',
			'meta_title' => '`pd`.`meta_title`',
			'meta_description' => '`pd`.`meta_description`',
			'meta_keyword' => '`pd`.`meta_keyword`',
			'attribute_value' => '`pa`.`text`',
			'attribute_name' => '`ad`.`name`',
			'attribute_group' => '`agd`.`name`',
			'option_value' => '`ovd`.`name`',
			'option_name' => '`od`.`name`',
			'tag' => '`pd`.`tag`',
		);
		
		foreach( $this->_extra_fields as $name => $extra_field ) {			
			$map[$name] = '`' . $this->tableAlias( $extra_field['config']['condition']['table'] ) . '`.`' . $extra_field['config']['condition']['column'] . '`';
		}
		
		/* @var $groups array */
		$groups = array();
		
		foreach( $fields as $k => $v ) {
			$groups[$v][$k] = $map[$k];
			$fields[$k] = $map[$k];
		}
		
		return array( $groups, $fields );
	}
	
	private function prepareValue( $values, $operator ) {
		if( $operator != 'in' ) {
			return "'" . $this->_ctrl->db->escape( $values ) . "'";
		}
		
		$values = explode( ',', $values );
		
		foreach( $values as $k => $v ) {
			$values[$k] = "'" . $this->_ctrl->db->escape( $v ) . "'";
		}
		
		return implode( ',', $values );
	}
	
	private function prepare() {
		if( $this->_results !== null ) return;
		
		/** 
		 * @var $groups array
		 * @var $fields array 
		 */
		list( $groups, $fields ) = $this->fields();
		
		/* @var $columns array */
		$columns = array(
			"`p`.`product_id`",
		);
		
		if( isset( $this->_data['sort'] ) ) {
			switch( $this->_data['sort'] ) {
				case 'rating' : {
					$columns[] = "(SELECT AVG(`rating`) AS `total` FROM `" . DB_PREFIX . "review` `r1` WHERE `r1`.`product_id` = `p`.`product_id` AND `r1`.`status` = '1' GROUP BY `r1`.`product_id`) AS `rating`";
					
					break;
				}
				case 'p.price' : {
					$columns[] = "`p`.`price`";
					$columns[] = "(SELECT `price` FROM `" . DB_PREFIX . "product_discount` AS `pd2` WHERE `pd2`.`product_id` = `p`.`product_id` AND `pd2`.`customer_group_id` = '" . $this->_customer_group_id . "' AND `pd2`.`quantity` = '1' AND ((`pd2`.`date_start` = '0000-00-00' OR `pd2`.`date_start` < NOW()) AND (`pd2`.`date_end` = '0000-00-00' OR `pd2`.`date_end` > NOW())) ORDER BY `pd2`.`priority` ASC, `pd2`.`price` ASC LIMIT 1) AS `discount`";
					$columns[] = "(SELECT `price` FROM `" . DB_PREFIX . "product_special` AS `ps` WHERE `ps`.`product_id` = `p`.`product_id` AND `ps`.`customer_group_id` = '" . $this->_customer_group_id . "' AND ((`ps`.`date_start` = '0000-00-00' OR `ps`.`date_start` < NOW()) AND (`ps`.`date_end` = '0000-00-00' OR `ps`.`date_end` > NOW())) ORDER BY `ps`.`priority` ASC, `ps`.`price` ASC LIMIT 1) AS `special`";
					
					break;
				}
			}
		}
		
		/* @var $conditions array */
		$conditions = array(
			'`p`.`date_available` <= NOW()',
			'`p`.`status` = 1',
		);
		
		/** Support for Customer Group Restrict */
		if( null != ( $cgr = $this->_ctrl->config->get( 'customer_group_restrict' ) ) ) {
			/* @var $customer_group_id int */
			$customer_group_id = $this->_ctrl->customer->isLogged() ? (int) $this->_ctrl->customer->getGroupId() : (int) $this->_ctrl->config->get('config_customer_group_id');
			
			$conditions[] = '( 
				`p`.`mod_customer_group_restrict` IS NULL 
					OR 
				' . ( $cgr['mode_product'] == 'unavailable' ? 'NOT' : '' ) . ' FIND_IN_SET( ' . $customer_group_id . ', `p`.`mod_customer_group_restrict` )
			)';
		}
		
		/* @var $join_categories_table bool */
		$join_categories_table = false;
		
		if( ! empty( $this->_settings['exclude_products_rules'] ) ) {
			foreach( $this->_settings['exclude_products_rules'] as $rule ) {
				/* @var $column string */
				$column = null;
				
				switch( $rule['type'] ) {
					case 'category_id' : $column = '`p2c`.`category_id`'; break;
					case 'stock_status_id' :
					case 'product_id' :
					case 'quantity' : $column = '`p`.`'.$rule['type'].'`'; break;
					case 'product_status' : $column = '`p`.`status`'; break;
				}
				
				/* @var $operator string */
				$operator = null;
				
				switch( $rule['operator'] ) {
					case 'more' : $operator = '> %s'; break;
					case 'less' : $operator = '< %s'; break;
					case 'is' : $operator = '= %s'; break;
					case 'not' : $operator = '!= %s'; break;
					case 'in' : $operator = 'IN(%s)'; break;
				}
				
				if( $column && $operator ) {
					$conditions[] = $column . ' ' . sprintf( $operator, $this->prepareValue( $rule['value'], $rule['operator'] ) );
					
					if( $column == '`p2c`.`category_id`' ) {
						$join_categories_table = true;
					}
				}
			}
		}
		
		/* @var $show_products_from_subcategories bool */
		$show_products_from_subcategories = isset( $this->_ctrl->request->get[$this->_ctrl->config->get('msmart_url_param')?$this->_ctrl->config->get('msmart_url_param'):'mfp'] ) && $this->showProductsFromSubcategoriesMfp();
		
		if( ! empty( $this->_data['filter_category_id'] ) || $show_products_from_subcategories ) {
			if( isset( $this->_data['filter_category_id'] ) ) {
				if( ! empty( $this->_data['filter_sub_category'] ) || $show_products_from_subcategories ) {
					if( ! empty( $this->_data['filter_category_id'] ) ) {
						$conditions[] = "`cp`.`path_id` = '" . (int) $this->_data['filter_category_id'] . "'";
					}
				} else if( ! empty( $this->_data['filter_category_id'] ) ) {
					$conditions[] = "`p2c`.`category_id` = '" . (int) $this->_data['filter_category_id'] . "'";
				}
			}
			
			if( ! empty( $this->_data['filter_filter'] ) ) {
				/* @var $implode array */
				$implode = array();
				
				/* @var $filters array */
				$filters = explode( ',', $this->_data['filter_filter'] );
				
				/* @var $filter_id int */
				foreach( $filters as $filter_id ) {
					$implode[] = (int) $filter_id;
				}
				
				$conditions[] = "`pf`.`filter_id` IN(" . implode( ', ', $implode ) . ")";
			}
		}
		
		/* @var $from string */
		$from = "FROM `" . DB_PREFIX . "product` AS `p`";
		
		if( ! empty( $this->_data['filter_category_id'] ) || $show_products_from_subcategories || $join_categories_table ) {
			if( ! empty( $this->_data['filter_sub_category'] ) || $show_products_from_subcategories ) {
				$from = "
					FROM
						`" . DB_PREFIX . "category_path` AS `cp`
					LEFT JOIN
						`" . DB_PREFIX . "product_to_category` AS `p2c`
					ON
						`cp`.`category_id` = `p2c`.`category_id`
				";
			} else {
				$from = "
					FROM
						`" . DB_PREFIX . "product_to_category` AS `p2c`
				";
			}
			
			if( ! empty( $this->_data['filter_filter'] ) ) {
				$from .= "
					LEFT JOIN
						`" . DB_PREFIX . "product_filter` AS `pf`
					ON
						`p2c`.`product_id` = `pf`.`product_id`
					LEFT JOIN
						`" . DB_PREFIX . "product` AS `p`
					ON
						`pf`.`product_id` = `p`.`product_id`
				";
			} else {
				$from .= "
					LEFT JOIN
						`" . DB_PREFIX . "product` AS `p`
					ON
						`p2c`.`product_id` = `p`.`product_id`
				";
			}
		}
		
		/* @var $sql string */
		$sql = "SELECT {columns} {from}";
		
		/* @var $joins array */
		$joins = array(
			'product_description' => "
				INNER JOIN
					`" . DB_PREFIX . "product_description` AS `pd`
				ON
					`p`.`product_id` = `pd`.`product_id` AND `pd`.`language_id` = " . $this->_language_id,
			'product_to_store' => "
				INNER JOIN
					`" . DB_PREFIX . "product_to_store` AS `p2s`
				ON
					`p`.`product_id` = `p2s`.`product_id` AND `p2s`.`store_id` = " . $this->_store_id
		);
		
		if( isset( $fields['manufacturer'] ) ) {			
			$joins['manufacturer'] = "
				LEFT JOIN
					`" . DB_PREFIX . "manufacturer` AS `m`
				ON
					`p`.`manufacturer_id` = `m`.`manufacturer_id`
			";
		}
		
		if( isset( $fields['attribute_name'] ) || isset( $fields['attribute_value'] ) || isset( $fields['attribute_group'] ) ) {
			$joins['product_attribute'] = "
				LEFT JOIN
					`" . DB_PREFIX . "product_attribute` AS `pa`
				ON
					`p`.`product_id` = `pa`.`product_id`
			";
			
			$joins['attribute'] = "
				LEFT JOIN
					`" . DB_PREFIX . "attribute` AS `a`
				ON
					`a`.`attribute_id` = `pa`.`attribute_id`
			";
			
			$joins['attribute_description'] = "
				LEFT JOIN
					`" . DB_PREFIX . "attribute_description` AS `ad`
				ON
					`a`.`attribute_id` = `ad`.`attribute_id` AND `ad`.`language_id` = " . $this->_language_id;
			
			$joins['attribute_group_description'] = "
				LEFT JOIN
					`" . DB_PREFIX . "attribute_group_description` AS `agd`
				ON
					`a`.`attribute_group_id` = `agd`.`attribute_group_id` AND `agd`.`language_id` = " . $this->_language_id;
		}
		
		if( isset( $fields['option_name'] ) || isset( $fields['option_value'] ) ) {
			$joins['product_option_value'] = "
				LEFT JOIN
					`" . DB_PREFIX . "product_option_value` AS `pov`
				ON
					`p`.`product_id` = `pov`.`product_id`
			";
			
			$joins['option_description'] = "
				LEFT JOIN
					`" . DB_PREFIX . "option_description` AS `od`
				ON
					`od`.`option_id` = `pov`.`option_id` AND `od`.`language_id` = " . $this->_language_id;
			
			$joins['option_value_description'] = "
				LEFT JOIN
					`" . DB_PREFIX . "option_value_description` AS `ovd`
				ON
					`ovd`.`option_value_id` = `pov`.`option_value_id` AND `ovd`.`language_id` = " . $this->_language_id;
		}
		
		$sql .= implode( ' ', array_replace( $this->_joins, $joins ) ) . "
				{joins}
			WHERE 
				{conditions}
			GROUP BY
				`p`.`product_id`
			ORDER BY
				{order_by}
			{limit}
		";
		
		/* @var $order_by string */
		$order_by = '';
		
		if( ! isset( $this->_data['sort'] ) ) {
			$this->_data['sort'] = '';
		}
		
		if( ! isset( $this->_data['order'] ) ) {
			$this->_data['order'] = '';
		}
		
		if( in_array( $this->_data['sort'], array( 'pd.name', 'p.model' ) ) ) {
			$order_by = 'LCASE(' . $this->_data['sort'] . ')';
		} else if( $this->_data['sort'] == 'p.price' ) {
			$order_by = '(CASE WHEN `special` IS NOT NULL THEN `special` WHEN `discount` IS NOT NULL THEN `discount` ELSE `p`.`price` END)';
		} else if( in_array( $this->_data['sort'], array( 'p.quantity', 'rating', 'p.sort_order', 'p.date_added' ) ) ) {
			$order_by = $this->_data['sort'];
		} else {
			$order_by = 'p.sort_order';
		}
		
		if( $this->_data['order'] == 'DESC' ) {
			$order_by .= ' DESC, LCASE(`pd`.`name`) DESC';
		} else {
			$order_by .= ' ASC, LCASE(`pd`.`name`) ASC';
		}
		
		$sql = str_replace(array(
			'{from}',
			'{order_by}',
			'{limit}',
		), array(
			$from,
			$order_by,
			$this->sqlLimit(),
		), $sql);
		
		$this->findResults( $groups, $sql, $columns, $conditions );
	}
	
	private function sqlLimit() {
		/* @var $limit string */
		$limit = '';
		
		if( isset( $this->_data['start'] ) && $this->_limit !== null ) {
			if( $this->_data['start'] < 0 ) {
				$this->_data['start'] = 0;
			}
			
			$limit = "LIMIT " . (int) $this->_data['start'] . ", " . $this->_limit;
		}
		
		return $limit;
	}
	
	public function prepareConditionsForCategories( $phrase ) {
		/* @var $conditions array */
		$conditions = array();
		
		/* @var $fields array */
		$fields = $this->sortFields( isset( $this->_config['fields_categories'] ) ? $this->_config['fields_categories'] : array( 'name' => array( 'sort_order' => 1 ) ) );
		$fields = array_keys( $fields );
		
		/* @var $map array */
		$map = array(
			'name' => '`cd`.`name`',
			'description' => '`cd`.`description`',
			'meta_title' => '`cd`.`meta_title`',
			'meta_description' => '`cd`.`meta_description`',
			'meta_keyword' => '`cd`.`meta_keyword`',
			'tag' => '`cd`.`tag`',
		);
		
		/* @var $word_groups array */
		$word_groups = array( array( array( $phrase ) ) );
			
		if( empty( $this->_settings['strict_search'] ) ) {
			/* @var $words array */
			$words = explode( ' ', trim( preg_replace( '/\s+/', ' ', $phrase ) ) );

			if( count( $words ) > 1 ) {
				foreach( $words as $k => $v ) {
					$words[$k] = array( $v );
				}

				$word_groups[] = $words;
			}

			$this->addSingularisation( $word_groups );
		}
		
		/* @var $words array */
		foreach( $word_groups as $words ) {
			/* @var $conditions2 array */
			$conditions2 = array();
			
			/* @var @word string */
			foreach( $words as $word ) {
				/* @var $query array */
				$query = array();
				
				/* @var $column string */
				foreach( $fields as $column ) {
					/* @var $variant string */
					foreach( $word as $variant ) {
						$query[] = $this->prepareQuery( $map[$column], $variant );
					}
				}
				
				if( $query ) {
					$conditions2[] = '(' . implode( "\n\t OR ", $query ) . ')';
				}
			}
			
			if( $conditions2 ) {
				$conditions[] = '( ' . implode( ' AND ', array_unique( $conditions2 ) ) . ' )';
			}
		}
		
		return implode( ' OR ', array_unique( $conditions ) );
	}
	
	private function prepareQuery( $column, $phrase ) {
		if( ! empty( $this->_settings['fix_polish_l'] ) ) {
			$column = "REPLACE(" . $column . ", 'ł', 'l')";
			$column = "REPLACE(" . $column . ", 'Ł', 'l')";
			$phrase = preg_replace( '/[\x{0142}\x{0141}]/u', 'l', $phrase );
		}
		
		return "LCASE(" . $column . ") LIKE '%" . $this->db->escape( $phrase ) . "%'";
	}
	
	private function showProductsFromSubcategoriesMfp() {
		$settings = $this->_ctrl->config->get('mega_filter_settings');
		
		if( empty( $settings['show_products_from_subcategories'] ) ) {
			return false;
		}
		
		if( ! empty( $settings['level_products_from_subcategories'] ) ) {
			$level = (int) $settings['level_products_from_subcategories'];
			$path = explode( '_', empty( $this->_ctrl->request->get['path'] ) ? '' : $this->_ctrl->request->get['path'] );
			
			if( $path && count( $path ) < $level ) {
				return false;
			}
		}
		
		return true;
	}
	
	public function mfp() {		
		/* @var $start int|null */
		$start = isset( $this->_data['start'] ) ? $this->_data['start'] : null;
		
		/* @var $limit int|null */
		$limit = $this->_limit;
		
		/* @var $results array|null */
		$results = $this->_results;
		
		// reset settings
		$this->_data['start'] = 0;
		$this->_limit = 5000;
		$this->_mfp = true;
		$this->_results = null;
		
		$this->prepare();
		
		$this->_mfp = false;
		$this->_limit = $limit;
		
		if( ! is_null( $start ) ) {
			$this->_data['start'] = $start;
		} else {
			unset( $this->_data['start'] );
		}
		
		if( $this->_results ) {
			/* @var $keys array */
			$keys = array_keys( $this->_results );
			
			$this->_results = $results;
			
			return $keys;
		}
		
		$this->_results = $results;
		
		return array();
	}
	
	private function addSingularisation( & $word_groups ) {
		if( empty( $this->_settings['singularisation'] ) ) {
			return;
		}
		
		require_once DIR_SYSTEM . 'library/msmart_search_singularisation.php';
		
		/* @var $words array */
		foreach( $word_groups as $key => $word_groups_2 ) {
			/* @var $new_words array */
			$new_words = array();
			
			foreach( $word_groups_2 as $key2 => $words ) {
				/* @var $word string */
				foreach( $words as $word ) {
					/* @var $singular string */
					$singular = Msmart_Search_Singularisation::singularize( $word );

					/* @var $plural string */
					$plular = Msmart_Search_Singularisation::pluralize( $word );

					$word = mb_strtolower( $word, 'utf8' );

					if( $word != mb_strtolower( $singular, 'utf8' ) ) {
						$new_words[] = $singular;
					}

					if( $word != mb_strtolower( $plular, 'utf8' ) ) {
						$new_words[] = $plular;
					}
				}

				if( $new_words ) {
					$word_groups[$key][$key2] = array_merge( $words, $new_words );
				}
			}
		}
	}
	
	private function findResults( $groups, $sql, $columns, $originalConditions ) {
		if( $this->_results !== null ) return;
		
		if( ! empty( $this->_data['filter_name'] ) ) {
			/* @var $word_groups array */
			$word_groups = array( array( array( $this->_data['filter_name'] ) ) );
			
			if( empty( $this->_settings['strict_search'] ) ) {
				/* @var $words array */
				$words = explode( ' ', trim( preg_replace( '/\s+/', ' ', $this->_data['filter_name'] ) ) );
				
				if( count( $words ) > 1 ) {
					foreach( $words as $k => $v ) {
						$words[$k] = array( $v );
					}
					
					$word_groups[] = $words;
				}
				
				$this->addSingularisation( $word_groups );
			}
			
			foreach( $groups as $fields ) {
				/* @var $words array */
				foreach( $word_groups as $words ) {
					/* @var $conditions array */
					$conditions = $originalConditions;
				
					/* @var $word string */
					foreach( $words as $word ) {
						/* @var $query array */
						$query = array();

						foreach( $fields as $column ) {
							/* @var $variant string */
							foreach( $word as $variant ) {
								$query[] = $this->prepareQuery( $column, $variant );
							}
						}

						$conditions[] = "(" . implode( "\n\t OR ", $query ) . ")";
					}
					
					if( $this->query( $columns, $conditions, $sql ) ) {
						return;
					}
				}
			}
			
			if( empty( $this->_settings['strict_search'] ) && ! empty( $this->_settings['any_keyword'] ) ) {
				foreach( $groups as $fields ) {
					/* @var $conditions array */
					$conditions = array();

					/* @var $words array */
					foreach( $word_groups as $words ) {
						/* @var $word string */
						foreach( $words as $word ) {
							/* @var $query array */
							$query = array();

							foreach( $fields as $column ) {
								/* @var $variant string */
								foreach( $word as $variant ) {
									$query[] = $this->prepareQuery( $column, $variant );
								}
							}

							$conditions[] = "(" . implode( "\n\t OR ", $query ) . ")";
						}
						
						if( $this->query( $columns, array_merge( $originalConditions, array( '(' . implode( ' OR ', $conditions ) . ')' ) ), $sql ) ) {
							return;
						}
					}
				}
			}
		} else if( ! empty( $this->_data['filter_tag'] ) ) {
			/* @var $conditions array */
			$conditions = $originalConditions;
			$conditions[] = $this->prepareQuery( '`pd`.`tag`', $this->_data['filter_tag'] );
			
			if( $this->query( $columns, $conditions, $sql ) ) {
				return;
			}
		}
		
		return false;
	}
	
	private function cacheQuery( $sql ) {
		/* @var $key string */
		$key = md5( $sql );
		
		if( isset( $this->_cache['db'][$key] ) ) {
			return $this->_cache['db'][$key];
		}
		
		$this->_cache['db'][$key] = $this->_ctrl->db->query( $sql );
		
		return $this->_cache['db'][$key];
	}
	
	private function query( $columns, $conditions, $sql ) {
		if( ! $this->_results ) {
			$this->_results = array();
		}
		
		if( $this->_results ) {
			$conditions[] = "`p`.`product_id` NOT IN(" . implode(',', array_keys($this->_results)) . ")";
		}
		
		/* @var $conditionsOut array */
		$conditionsOut = array();
		
		/* @var $joins array */
		$joins = array();
		
		if( ! empty( $this->_ctrl->request->get[$this->_ctrl->config->get('msmart_url_param')?$this->_ctrl->config->get('msmart_url_param'):'mfp'] ) || ( NULL != ( $mfSettings = $this->_ctrl->config->get('mega_filter_settings') ) && ! empty( $mfSettings['in_stock_default_selected'] ) ) ) {
			if( ! $this->_mfp ) {
				$this->load->model( 'extension/module/mega_filter' );

				if( class_exists( 'MegaFilterCore' ) ) {
					$mfc = MegaFilterCore::newInstance( $this->_ctrl, $sql, $this->_data );

					list( $conditionsIn, $conditionsOut ) = $mfc->getConditions();
					$conditionsIn = $conditionsIn['in'];

					$conditions = array_merge( $conditions, $conditionsIn );

					$columns = array_merge( $columns, $mfc->getColumns() );
					
					$params = $mfc->getParseParams();
					
					if( ! empty( $params['vehicle_make_id'] ) || ! empty( $params['vehicle_model_id'] ) || ! empty( $params['vehicle_engine_id'] ) || ! empty( $params['vehicle_year'] ) ) {
						$joins = $mfc->_joinVehicle();
					}
					
					if( ! empty( $params['levels'] ) ) {
						$joins[] = $mfc->_joinLevel();
					}
				}
			}
		}
		
		$sql = str_replace(array(
			'{columns}',
			'{conditions}',
			'{joins}',
		), array(
			implode( ', ', $columns ),
			implode( "\n AND ", $conditions ),
			implode( ' ', $joins ),
		), $sql );
		
		if( $conditionsOut ) {
			$uid = '{' . md5( uniqid() ) . '}';
			$sql = preg_replace( '/\s+LIMIT\s+[0-9]+(,\s*[0-9]+)?$/i', '', trim( $sql ) );
			$sql = sprintf( "SELECT * FROM(%s) AS `tmp` WHERE ", $sql ) . implode( ' AND ', $conditionsOut ) . ' ' . $uid;
			$sql = str_replace( $uid, $this->sqlLimit(), $sql );
		}
		
		/* @var $rows array */
		$rows = $this->db->query( $sql )->rows;
		
		$this->_sqls[] = ! count( $rows ) ? count( $rows ) : $sql;
		
		foreach( $rows as $row ) {
			$this->_results[$row['product_id']] = $row;
		}
		
		return count( $this->_results ) >= $this->_settings['required_number_of_results'] ? true : false;
	}
	
	public function __get( $name ) {
		return $this->_ctrl->{$name};
	}
}