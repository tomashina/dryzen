<?php

//  Related Options / Связанные опции
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ModelExtensionModuleRelatedOptions extends \liveopencart\lib\v0023\Model {
	
	use \liveopencart\lib\v0023\traits\cache;
	
	protected $export_field_options                = 'options_values_ids';
	protected $export_field_product_name           = 'product_name_info_only';
	protected $export_field_description_of_options = 'description_of_options_info_only';
	protected $export_field_discounts              = 'discounts';
	protected $export_field_specials               = 'specials';
	
	public function __construct() {
		call_user_func_array( ['parent', '__construct'] , func_get_args());
		
		\liveopencart\ext\ro::getInstance($this->registry);
		
		$this->checkTables();
	}

	protected function decodeHTML($str) {
		return html_entity_decode($str, ENT_QUOTES, 'UTF-8');
	}
	
	public function getMaxProductIdWithRO() {
		$query = $this->db->query("SELECT MAX(product_id) product_id FROM " . DB_PREFIX . "relatedoptions ");
		if ( $query->num_rows ) {
			return $query->row['product_id'];
		} else {
			return 0;
		}
	}
	
	public function getMinProductIdWithRO() {
		$query = $this->db->query("SELECT MIN(product_id) product_id FROM " . DB_PREFIX . "relatedoptions ");
		if ( $query->num_rows ) {
			return $query->row['product_id'];
		} else {
			return 0;
		}
	}
	
	protected function getQueryROVP($product_id) {
		$query_rovp = $this->db->query("
			SELECT ROVP.*, ROV.relatedoptions_variant_name rov_name
			FROM `" . DB_PREFIX . "relatedoptions_variant_product` ROVP
				LEFT JOIN	`" . DB_PREFIX . "relatedoptions_variant` ROV ON (ROV.relatedoptions_variant_id = ROVP.relatedoptions_variant_id)
			WHERE ROVP.product_id = " . (int)$product_id . "
			ORDER BY ROV.sort_order, ROV.relatedoptions_variant_name, ROVP.relatedoptions_variant_id, ROVP.relatedoptions_variant_product_id
		");
		return $query_rovp;
	}
	
	protected function getAllCombinationsOfModels($rovps, $ro_models, $rovp_level = 0) {
		
		if ( count($rovps) > $rovp_level) {
			$next_models = $this->getAllCombinationsOfModels($rovps, $ro_models, $rovp_level + 1);
			$rovp_id     = $rovps[$rovp_level];
			$models      = [];
			
			if ( isset($ro_models[$rovp_id]) ) {
				foreach ($ro_models[$rovp_id] as $model_info) {
					if ($next_models) {
						foreach ($next_models as $next_model) {
							$ro_ids   = array_merge( [$model_info['relatedoptions_id']], $next_model['ro_ids']) ;
							$models[] = ['model' => $model_info['model'].$next_model['model'], 'ro_ids' => $ro_ids];
						}
					} else {
						if ( $model_info['model'] ) {
							$ro_ids   = [$model_info['relatedoptions_id']];
							$models[] = ['model' => $model_info['model'], 'ro_ids' => $ro_ids];
						}
					}
				}
			}
			return $models;
		}
		return false;
	}
	
	public function generateRelatedOptionsSearch($product_id) {
		
		$ro_settings = $this->config->get('related_options');
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "relatedoptions_search` WHERE product_id = ".(int)$product_id." ");
		
		if ( isset($ro_settings['spec_model']) && ($ro_settings['spec_model'] == 2 || $ro_settings['spec_model'] == 3) ) {
			
			$this->load->model('catalog/product');
			$product_info = $this->model_catalog_product->getProduct( (int)$product_id );
			
			$ro_models = [];
			
			$rovps = [];
			$query = $this->getQueryROVP($product_id);
			foreach ($query->rows as $row) {
				$rovps[] = $row['relatedoptions_variant_product_id'];
			}
			
			$query = $this->db->query("	SELECT `model`, `relatedoptions_variant_product_id`, `relatedoptions_id`
																	FROM `" . DB_PREFIX . "relatedoptions` 
																	WHERE product_id = ".(int)$product_id." ");
			foreach ( $query->rows as $row ) {
				if ( !isset($ro_models[$row['relatedoptions_variant_product_id']]) ) {
					$ro_models[$row['relatedoptions_variant_product_id']] = [];
				}
				$ro_models[$row['relatedoptions_variant_product_id']][] = $row;
			}
			
			$model_start = ($ro_settings['spec_model'] == 3) ? $product_info['model'] : '';
			
			$models = $this->getAllCombinationsOfModels($rovps, $ro_models);
			
			// << trying to increase many rows inserting speed
			
			$model_i     = 0;
			$insertLimit = 100;
			while ( $model_i < count($models)) {
				
				$sqlValues = '';
				for ($sub_i = 0;$sub_i < $insertLimit;$sub_i++) {
					
					if ( ($model_i + $sub_i) < count($models) ) {
						$model = $models[$model_i + $sub_i];
						
						$new_model = $model_start.$model['model'];
						
						$sqlValues .= ($sqlValues ? "," : "") . "('".(int)$product_id."','".$this->db->escape($new_model)."','".implode(',',$model['ro_ids'])."')";
					}
				}
				
				$this->db->query("INSERT INTO `" . DB_PREFIX . "relatedoptions_search` (`product_id`,`model`,`ro_ids`) VALUES ".$sqlValues." ");
				
				$model_i += $insertLimit;
			}
			
			// >> trying to increase many rows inserting speed
			
		}
	}
	
	public function getROVariantsHavingCombs($product_ids_start_end = [], $only_variant_id = false) {
		
		// to export only variants with data (two steps because of )
		// first all compatible options
		// others by sort order then name
		$query = $this->db->query("
			SELECT VP.relatedoptions_variant_id rov_id, V.relatedoptions_variant_name name, COUNT(RO.relatedoptions_id) ro_count
			FROM `".DB_PREFIX."relatedoptions_variant_product` VP
				LEFT JOIN `".DB_PREFIX."relatedoptions_variant` V ON ( VP.relatedoptions_variant_id = V.relatedoptions_variant_id )
				,`".DB_PREFIX."relatedoptions` RO
			WHERE RO.relatedoptions_variant_product_id = VP.relatedoptions_variant_product_id
				".( !empty($only_variant_id) ? " AND VP.relatedoptions_variant_id = ".(int)$only_variant_id." " : "" )."
				".( $product_ids_start_end ? " AND RO.product_id BETWEEN ".(int)$product_ids_start_end['start']." AND ".(int)$product_ids_start_end['end']." " : "" )."
			GROUP BY VP.relatedoptions_variant_id, V.relatedoptions_variant_name
			ORDER BY (CASE WHEN V.relatedoptions_variant_id IS NOT NULL THEN 0 ELSE 1 END) ASC
				,(CASE WHEN V.sort_order IS NOT NULL THEN V.sort_order ELSE 0 END) ASC
				,(CASE WHEN V.relatedoptions_variant_name IS NOT NULL THEN V.relatedoptions_variant_name ELSE '' END)
		");
		return $query->rows;
		
	}
	
	public function getProductsROVariantsByROVariantId($rov_id) {
		$query = $this->db->query("
			SELECT ROVP.*
			FROM `".DB_PREFIX."relatedoptions_variant_product` ROVP
				,`".DB_PREFIX."product` P
			WHERE P.product_id = ROVP.product_id
			  AND relatedoptions_variant_id = ".(int)$rov_id."
			ORDER BY P.sort_order ASC
		");
		return $query->rows;
	}
	
	protected function getROVariant($rov_id) {
		return $this->simple_db->selectRowById('relatedoptions_variant', $rov_id);
	}
	
	public function getProductsROVariantsByROVariantIdForExport($rov_id) {
		
		$products_variants      = $this->getProductsROVariantsByROVariantId($rov_id);
		$variant_option_ids     = $this->getVariantOptions($rov_id);
		$variant_option_ids_str = implode(',', $variant_option_ids);
		
		$rov      = $this->getROVariant($rov_id);
		$rov_name = !empty($rov['relatedoptions_variant_name']) ? $rov['relatedoptions_variant_name'] : '';
		
		array_walk($products_variants, function(&$elem, $key) use ($variant_option_ids_str, $rov_name) {
			$elem['options_ids']  = $variant_option_ids_str;
			$elem['variant_name'] = $rov_name;
			if ( $elem['allow_zero_select'] == 1 ) {
				$elem['allow_zero_select'] = 'no';
			} elseif ( $elem['allow_zero_select'] == 2 ) {
				$elem['allow_zero_select'] = 'yes';
			} else {
				$elem['allow_zero_select'] = '';
			}
		});
		
		return $products_variants;
	}
	
	public function getVariantROOptionsToExport($rov_id, $product_ids_start_end = []) {
		
		$language_id = $this->config->get('config_language_id');
		
		// get ro options
		$query = $this->db->query("
			SELECT ROO.*, IF(OD.name IS NULL, '?', OD.name) option_name, IF(OVD.name IS NULL, '?', OVD.name) value_name
			FROM `".DB_PREFIX."relatedoptions_option` ROO
				LEFT JOIN `".DB_PREFIX."option` O ON (O.option_id = ROO.option_id)
				LEFT JOIN `".DB_PREFIX."option_description` OD ON (OD.option_id = ROO.option_id AND OD.language_id = ".(int)$language_id.")
				LEFT JOIN `".DB_PREFIX."option_value_description` OVD ON (OVD.option_value_id = ROO.option_value_id)
			WHERE ROO.relatedoptions_id IN ( 	SELECT RO.relatedoptions_id
												FROM ".DB_PREFIX."relatedoptions RO
													,".DB_PREFIX."relatedoptions_variant_product ROVP
												WHERE ROVP.relatedoptions_variant_id = ".(int)$rov_id."
												AND ROVP.relatedoptions_variant_product_id = RO.relatedoptions_variant_product_id
												".( $product_ids_start_end ? " AND RO.product_id BETWEEN ".(int)$product_ids_start_end['start']." AND ".(int)$product_ids_start_end['end']." " : "" )."
											)
			ORDER BY ROO.relatedoptions_id ASC, O.sort_order ASC
		");
		
		$ro_options = [];
		foreach ( $query->rows as $row ) {
			$relatedoptions_id = $row['relatedoptions_id'];
			if ( !isset($ro_options[$relatedoptions_id]) ) {
				$ro_options[$relatedoptions_id] = ['option_ids' => [], 'description' => ''];
			}
			$ro_options[$relatedoptions_id]['option_ids'][$row['option_id']] = $row['option_value_id'];
			$ro_options[$relatedoptions_id]['description'] .= ''.$row['option_name'].': '.$row['value_name'].'; ';
		}
		
		return $ro_options;
	}
	
	public function getVariantROCombsToExport($rov_id, $product_ids_start_end = []) {
		
		// get data to export (ro combs)
		$query = $this->db->query("
			SELECT RO.*, P.model product_model, PD.name product_name_info_only
			FROM ".DB_PREFIX."relatedoptions RO
				,".DB_PREFIX."relatedoptions_variant_product ROVP
				,".DB_PREFIX."product P
				LEFT JOIN ".DB_PREFIX."product_description PD ON (PD.product_id = P.product_id)
			WHERE ROVP.relatedoptions_variant_id = ".(int)$rov_id."
			  AND ROVP.relatedoptions_variant_product_id = RO.relatedoptions_variant_product_id
			  AND RO.product_id = P.product_id
			  AND (PD.language_id IS NULL OR PD.language_id = ".(int)$this->config->get('config_language_id').")
				".( $product_ids_start_end ? " AND RO.product_id BETWEEN ".(int)$product_ids_start_end['start']." AND ".(int)$product_ids_start_end['end']." " : "" )."
			ORDER BY P.sort_order ASC, P.product_id ASC, RO.relatedoptions_id ASC
		");
		
		$ro_combs = $query->rows;
		
		$updated_ro_combs = $this->liveopencart_ext_ro->callPRO('addVariantFieldsToVariantROCombs', [$ro_combs, $rov_id]);
		if ( $updated_ro_combs ) {
			$ro_combs = $updated_ro_combs;
		}
		
		return $ro_combs;
	}
	
	public function getVariantRODiscountsToExport($rov_id, $product_ids_start_end = []) {
		
		$query = $this->db->query("
			SELECT RDS.*, RO.product_id
			FROM `".DB_PREFIX."relatedoptions_discount` RDS
				,`".DB_PREFIX."relatedoptions` RO
			WHERE RDS.relatedoptions_id IN	(	SELECT RO.relatedoptions_id
												FROM ".DB_PREFIX."relatedoptions RO
													,".DB_PREFIX."relatedoptions_variant_product ROVP
												WHERE ROVP.relatedoptions_variant_id = ".(int)$rov_id."
												  AND ROVP.relatedoptions_variant_product_id = RO.relatedoptions_variant_product_id
												".( $product_ids_start_end ? " AND RO.product_id BETWEEN ".(int)$product_ids_start_end['start']." AND ".(int)$product_ids_start_end['end']." " : "" )."
											)
			  AND RO.relatedoptions_id  = RDS.relatedoptions_id
			ORDER BY RDS.relatedoptions_id ASC
		");
		return $query->rows;
	}
	
	public function getVariantROSpecialsToExport($rov_id, $product_ids_start_end = []) {
		$query = $this->db->query("
			SELECT RDS.*, RO.product_id
			FROM `".DB_PREFIX."relatedoptions_special` RDS
				,`".DB_PREFIX."relatedoptions` RO
			WHERE RDS.relatedoptions_id IN (	SELECT RO.relatedoptions_id
												FROM ".DB_PREFIX."relatedoptions RO
													,".DB_PREFIX."relatedoptions_variant_product ROVP
												WHERE ROVP.relatedoptions_variant_id = ".(int)$rov_id."
												  AND ROVP.relatedoptions_variant_product_id = RO.relatedoptions_variant_product_id
													".( $product_ids_start_end ? " AND RO.product_id BETWEEN ".(int)$product_ids_start_end['start']." AND ".(int)$product_ids_start_end['end']." " : "" )."
											)
			  AND RO.relatedoptions_id  = RDS.relatedoptions_id
			ORDER BY RDS.relatedoptions_id ASC
		");
		return $query->rows;
	}
	
	public function getExportData() {

		$lang_id = $this->config->get('config_language_id');

		$data = [];

		$options_cnt = 0;
		$options     = [];

		$query_ro = $this->db->query('SELECT RO.*, P.model product_model FROM `' . DB_PREFIX . 'relatedoptions` RO, `' . DB_PREFIX . 'product` P WHERE P.product_id = RO.product_id ');
		foreach ($query_ro->rows as $row) {
			$data[$row['relatedoptions_id']] = [
				'relatedoptions_id'    => $row['relatedoptions_id'],
				'product_id'           => $row['product_id'],
				'product_model'        => $row['product_model'],
				'relatedoptions_model' => $row['model'],
				'relatedoptions_sku'   => $row['sku'],
				'relatedoptions_upc'   => $row['upc'],
				'relatedoptions_ean'   => $row['ean'],
				'in_stock_status_id'   => $row['in_stock_status_id'],
				'stock_status_id'      => $row['stock_status_id'],
				'weight_prefix'        => $row['weight_prefix'],
				'weight'               => $row['weight'],
				'quantity'             => $row['quantity'],
				'price_prefix'         => $row['price_prefix'],
				'price'                => $row['price'],
				
			];
		}
		unset($query_ro);

		// on first step let's select only names for all values of all options
		$query = $this->db->query("
			SELECT DISTINCT ROO.option_id, ROO.option_value_id, OD.name option_name, OVD.name option_value_name
			FROM `".DB_PREFIX."relatedoptions_option` ROO
				LEFT JOIN `".DB_PREFIX."option_value` OV ON (OV.option_value_id = ROO.option_value_id)
				LEFT JOIN `".DB_PREFIX."option_value_description` OVD ON (OVD.option_value_id = ROO.option_value_id	AND OVD.language_id = ".(int)$lang_id.")
			, `".DB_PREFIX."option` O
			LEFT JOIN `".DB_PREFIX."option_description` OD ON (O.option_id = OD.option_id AND OD.language_id = ".(int)$lang_id.")
			WHERE ROO.option_id = O.option_id
			ORDER BY O.sort_order	
		");
		
		$opts_names = [];
		foreach ($query->rows as $row) {
			if ( !isset($opts_names[$row['option_id']]) ) {
				$opts_names[$row['option_id']] = ['name' => $row['option_name'], 'values' => [0 => '']];
			}
			$opts_names[$row['option_id']]['values'][$row['option_value_id']] = $row['option_value_name'];
		}
		unset($query);

		$query = $this->db->query("
			SELECT ROO.*
			FROM `".DB_PREFIX."relatedoptions_option` ROO
				, `".DB_PREFIX."option` O
			WHERE ROO.option_id = O.option_id
			ORDER BY O.sort_order	
		");
		
		foreach ($query->rows as &$row) {
			if (!isset($options[$row['option_id']])) {
				$options_cnt++;
				$options[$row['option_id']] = $options_cnt;
			}
			
			$data[$row['relatedoptions_id']]['option_id'.$options[$row['option_id']]]         = $row['option_id'];
			$data[$row['relatedoptions_id']]['option_name'.$options[$row['option_id']]]       = isset($opts_names[$row['option_id']]['name']) ? $opts_names[$row['option_id']]['name'] : '';
			$data[$row['relatedoptions_id']]['option_value_id'.$options[$row['option_id']]]   = $row['option_value_id'];
			$data[$row['relatedoptions_id']]['option_value_name'.$options[$row['option_id']]] = $opts_names[$row['option_id']]['values'][$row['option_value_id']];

			$row = ""; // memory opt
		}
		
		unset($query);

		return $data;
	}
	
	// find relevant related options combination for options values array (product_option_id => product_option_value_id)
	public function getROCombinationsByPOIds($product_id, $options) {
		
		$variants = $this->getProductVariants($product_id);
		
		$ro_combinations = [];
		
		foreach ($variants as $variant) {
			$ro_combination = $this->getROCombinationsByPOIdsAndROVId($product_id, $variant['relatedoptions_variant_product_id'], $variant['relatedoptions_variant_id'], $options);
			if ($ro_combination) {
				$ro_combinations[] = $ro_combination;
			}
		}
		
		return $ro_combinations;
		
	}
	
	public function getROCombinationsByPOIdsAndROVId($product_id, $rovp_id, $rov_id, $options) {
		
		if (!is_array($options) || count($options) == 0 ) {
			return FALSE;
		}
		
		$str_opts = "";
		foreach ($options as $product_option_id => $option_value) {
			$str_opts .= ",".$product_option_id;
		}
		$str_opts = substr($str_opts, 1);
		
		// check only options used in relateted options
		$pvo = $this->getVariantOptions($rov_id); //returns option_ids
		
		if (count($pvo) > 0 && count($options) > 0) {
			
			$query = $this->db->query("
				SELECT PO.product_option_id, PO.option_id
				FROM " . DB_PREFIX . "product_option PO
				WHERE PO.product_id = ".(int)$product_id."
				  AND PO.product_option_id IN ( ".$str_opts.")
				  AND PO.option_id IN (".join(",",$pvo).")
			");
			
			$sql_from  = "";
			$sql_where = "";
			$sql_cnt   = 0;
			
			$povs = [];
			foreach ( $query->rows as $row ) {
				$povs[] = (int)$options[$row['product_option_id']];
			}
			if ( $povs ) {
				$query = $this->db->query("
					SELECT POV.option_value_id
					FROM  " . DB_PREFIX . "product_option_value POV
					WHERE POV.product_id = ".(int)$product_id."
					  AND POV.product_option_id IN ( ".$str_opts.")
					  AND POV.product_option_value_id IN (".implode(",",$povs).")
				");
				foreach ( $query->rows as $row ) {
					$sql_cnt++;
					$sql_from .= ", ( SELECT relatedoptions_id FROM ".DB_PREFIX."relatedoptions_option WHERE option_value_id = ".(int)$row['option_value_id']." ) ROO".$sql_cnt;
					$sql_where .= " AND ROO".$sql_cnt.".relatedoptions_id = RO.relatedoptions_id ";
				}
			}
			
			if ($sql_from != "") {
				
				$query = $this->db->query("
					SELECT RO.*
					FROM 	".DB_PREFIX."relatedoptions RO
						".$sql_from."
					WHERE RO.relatedoptions_variant_product_id = ".(int)$rovp_id."
						".$sql_where."
				");
				if ($query->num_rows) {
					return $query->row;
				}
				
			}
		}
		
		return FALSE;
		
	}
	
	public function getProductVariants($product_id) {
		
		if (!$this->installed()) return false;
		
		$query = $this->db->query("
			SELECT VP.*
			FROM 	`".DB_PREFIX."relatedoptions_variant_product` VP
			WHERE	VP.product_id = ".(int)$product_id."
		");
		if ($query->num_rows) {
			return $query->rows;
		} else {
			return false;
		}
	}
	
	// set product variant (one of variants)
	public function setProductVariant($product_id, $data, $ro_use) {
															
		if (!isset($data['rovp_id']) || !$data['rovp_id'] ) {
			$query = $this->db->query("
				INSERT INTO `".DB_PREFIX."relatedoptions_variant_product`
				SET product_id = ".(int)$product_id."
				  , relatedoptions_use = ".(int)$ro_use."
				  , relatedoptions_variant_id = ".(int)$data['rov_id']."
				  , allow_zero_select = ".(int)(isset($data['allow_zero_select']) ? $data['allow_zero_select'] : 0)."
			");
			
			return  $this->db->getLastId();
			
		} else {
			
			$query = $this->db->query("
				UPDATE `".DB_PREFIX."relatedoptions_variant_product`
				SET product_id = ".(int)$product_id."
				  , relatedoptions_use = ".(int)$ro_use."
				  , relatedoptions_variant_id = ".(int)$data['rov_id']."
				  , allow_zero_select = ".(int)(isset($data['allow_zero_select']) ? $data['allow_zero_select'] : 0)."
				WHERE relatedoptions_variant_product_id = ".(int)$data['rovp_id']."
			");
			return $data['rovp_id'];
		}
	}

	// get options that can be used in related options
	public function getCompatibleOptions($sort_by_name = false) {
		
		if (!$this->installed()) {
			return [];
		}
		
		$lang_id = $this->config->get('config_language_id');
		
		$query = $this->db->query("
			SELECT O.option_id, OD.name
			FROM `".DB_PREFIX."option` O
				,`".DB_PREFIX."option_description` OD
			WHERE O.option_id = OD.option_id
			  AND OD.language_id = ".$lang_id."
			  AND O.type IN (".$this->getOptionTypes().")
			ORDER BY ".($sort_by_name ? "OD.name ASC, " : "")." O.sort_order
		");
		
		return $query->rows;
	}
	
	public function getCompatibleOptionValues() {
		
		if (!$this->installed()) {
			return [];
		}
		
		$lang_id = $this->config->get('config_language_id');
		
		$optsv              = [];
		$compatible_options = $this->getCompatibleOptions();
		$str_opt            = "";
		foreach ($compatible_options as $option) {
			$optsv[$option['option_id']] = ['name' => $option['name'], 'values' => [] ];
			$str_opt .= ",".$option['option_id'];
		}
		if ($str_opt != "") {
			$str_opt = substr($str_opt, 1);
			$query   = $this->db->query("
				SELECT OV.option_id, OVD.name, OVD.option_value_id
				FROM `".DB_PREFIX."option_value` OV
					,`".DB_PREFIX."option_value_description` OVD 
				WHERE OV.option_id IN (".$str_opt.")
				  AND OVD.language_id = ".$lang_id."
				  AND OV.option_value_id = OVD.option_value_id
				ORDER BY OV.sort_order, OVD.name
			");
			foreach ($query->rows as $row) {
				$optsv[$row['option_id']]['values'][] = $row;
			}
		}
		
		return $optsv;
		
	}
	
	public function getVariantOptions($relatedoptions_variant_id) {
		
		$options = [];
		if ($relatedoptions_variant_id == 0) {
			$copts   = $this->getCompatibleOptions();
			$options = [];
			foreach ($copts as $option) {
				$options[] = $option['option_id'];
			}
		} else {
			$options = [];
			$query   = $this->db->query("
				SELECT VO.option_id
				FROM `".DB_PREFIX."relatedoptions_variant_option` VO
					,`".DB_PREFIX."option` O
				WHERE relatedoptions_variant_id = ".(int)$relatedoptions_variant_id."
				  AND VO.option_id = O.option_id
				ORDER BY O.sort_order
			");
			foreach ($query->rows as $row) {
				$options[] = $row['option_id'];
			}
		}
		
		return $options;
		
	}
	
	public function getVariant($rov_id) {
		$variants = $this->getVariants(false, false, $rov_id);
		if ($variants && isset($variants[$rov_id])) {
			return $variants[$rov_id];
		}
	}
	
	// returns array of all related options variants and relevant options
	// $add_all - add default variant "all avalable options"
	public function getVariants($add_all = false, $return_sorted = false, $rov_id = 0) {
		
		$lang_id = $this->config->get('config_language_id');
		
		$mod_settings = $this->config->get('related_options');
		
		$vopts = [];
		
		if ($this->installed()) {
			
			if ($add_all && empty($mod_settings['disable_all_options_variant']) ) {
				$comp_opts_order = [];
				$comp_opts       = $this->getCompatibleOptions($comp_opts_order);
				$vopts[0]        = ['options' => $comp_opts, 'sort_order' => $comp_opts_order, 'complete_sort_order' => 0, 'rov_id' => 0];
			}
			
			$query = $this->db->query("
				SELECT V.relatedoptions_variant_name, V.relatedoptions_variant_id, V.sort_order,
					(	SELECT COUNT(*)
						FROM `".DB_PREFIX."relatedoptions` RO
							,`".DB_PREFIX."relatedoptions_variant_product` ROVP
						WHERE RO.relatedoptions_variant_product_id = ROVP.relatedoptions_variant_product_id
						  AND ROVP.relatedoptions_variant_id = V.relatedoptions_variant_id
					) ro_combs_cnt
				FROM `".DB_PREFIX."relatedoptions_variant` V
				".($rov_id ? " WHERE V.relatedoptions_variant_id = ".(int)$rov_id." " : "")."
				ORDER BY V.sort_order ASC, V.relatedoptions_variant_name ASC, V.relatedoptions_variant_id ASC
			");
			$cnt = count($vopts);
			foreach ($query->rows as $row) {
				$vopts[$row['relatedoptions_variant_id']] = [
					'options'             => [],
					'name'                => $row['relatedoptions_variant_name'],
					'rov_id'              => $row['relatedoptions_variant_id'],
					'sort_order'          => $row['sort_order'],
					'ro_combs_cnt'        => $row['ro_combs_cnt'],
					'options_order'       => [],
					'complete_sort_order' => $cnt,
					'fields'              => $this->liveopencart_ext_ro->callPRO('getVariantFields', [$row['relatedoptions_variant_id']]),
				];
				$cnt++;
			}
			
			$query = $this->db->query("
				SELECT VO.relatedoptions_variant_id, VO.option_id, OD.name
				FROM `".DB_PREFIX."relatedoptions_variant_option` VO
					,`".DB_PREFIX."relatedoptions_variant` V
					,`".DB_PREFIX."option_description` OD
					,`".DB_PREFIX."option` O
				WHERE OD.option_id = VO.option_id
				  AND O.option_id = VO.option_id
				  AND OD.language_id = ".$lang_id."
				  AND V.relatedoptions_variant_id = VO.relatedoptions_variant_id
				  ".($rov_id ? " AND V.relatedoptions_variant_id = ".(int)$rov_id." " : "")."
				ORDER BY O.sort_order ASC, O.option_id ASC
			");
			
			foreach ($query->rows as $row) {
				$vopts[$row['relatedoptions_variant_id']]['options'][] = ['option_id' => $row['option_id'], 'name' => $row['name']];
			}
			
		}
		
		if ( $return_sorted ) {
			$sorted = [];
			foreach ($vopts as $vopt) {
				$sorted[$vopt['complete_sort_order']] = $vopt;
			}
			return ['sorted' => $sorted, 'vopts' => $vopts];
		}
			
		return $vopts;
		
	}
	
	public function getOptionsIdsOrderedCached() {
		if (!$this->hasCacheSimple(__METHOD__)) {
			
			$query = $this->db->query("SELECT option_id FROM `".DB_PREFIX."option` ORDER BY sort_order ASC, option_id ASC ");
			
			$this->setCacheSimple(__METHOD__, array_column($query->rows, 'option_id'));
		}
		return $this->getCacheSimple(__METHOD__);
	}
	
	public function updateSomeROVs($variants) {
		return $this->setVariantsOfRelatedOptions($variants, false);
	}
	
	// save related options variant with variant options
	// $clear_others - delete others variants
	public function setVariantsOfRelatedOptions($vo, $clear_others = true) {
		
		//if ($clear_others) {
		//	$query = $this->db->query("	DELETE FROM `".DB_PREFIX."relatedoptions_variant_option` ");
		//}
		$str_vo_id = "";
		
		$updated_vo = [];
		
		if (is_array($vo)) {
			
			$query                  = $this->db->query("	SELECT * FROM `".DB_PREFIX."relatedoptions_variant_option`");
			$all_vo_options_ids_old = [];
			foreach ($query->rows as $row) {
				$all_vo_options_ids_old[$row['relatedoptions_variant_id']][] = $row['option_id'];
			}
			
			foreach ($vo as $vo_arr) {
				
				if (is_array($vo_arr)) {
					
					$vo_id   = (int)(isset($vo_arr['id'])) ? $vo_arr['id'] : "";
					$vo_name = "";
					if (isset($vo_arr['name']) && $vo_arr['name'] != '' ) {
						$vo_name = $vo_arr['name'];
					} else {
						if (isset($vo_arr['options']) && is_array($vo_arr['options'])) {
							$lang_id    = $this->config->get('config_language_id');
							$options_in = implode(",", array_map('intval', array_values($vo_arr['options'])) );
							if ( $options_in ) {
								$query = $this->db->query("
									SELECT *
									FROM `".DB_PREFIX."option_description` OD
										,`".DB_PREFIX."option` O
									WHERE O.option_id IN (".$options_in.")
									  AND O.option_id = OD.option_id
									  AND OD.language_id = ".(int)$lang_id."
									ORDER BY O.sort_order ASC, OD.name ASC
									");
								if ($query->num_rows) {
									foreach ($query->rows as $row) {
										$vo_name .= " + ".$row['name'];
									}
									$vo_name = substr($vo_name, 3);
								}
							}
						}
					}
					
					if (!empty($vo_id)) {
						$query = $this->db->query("
							UPDATE `".DB_PREFIX."relatedoptions_variant`
							SET relatedoptions_variant_name='".$this->db->escape($vo_name)."'
							  , sort_order= ".(int)(isset($vo_arr['sort_order']) ? $vo_arr['sort_order'] : 0 )."
							WHERE relatedoptions_variant_id = ".$vo_id."
						");
					} else {
						$query = $this->db->query("
							INSERT INTO `".DB_PREFIX."relatedoptions_variant`
							SET relatedoptions_variant_name='".$this->db->escape($vo_name)."'
							  , sort_order= ".(int)(isset($vo_arr['sort_order']) ? $vo_arr['sort_order'] : 0 )."
						");
						$vo_id = $this->db->getLastId();
					}
					$str_vo_id .= ",".$vo_id;
					$updated_vo[] = $vo_id;
					
					if (isset($vo_arr['options'])) {
						$vo_options_ids = $vo_arr['options'];
						if (is_array($vo_options_ids)) {
							$vo_options_ids = array_unique(array_values($vo_options_ids));
							
							$vo_options_ids_old = (isset($all_vo_options_ids_old[$vo_id]) ? $all_vo_options_ids_old[$vo_id] : []);
							//$query = $this->db->query("	SELECT option_id FROM `".DB_PREFIX."relatedoptions_variant_option` WHERE relatedoptions_variant_id=".(int)$vo_id."");
							//$vo_options_ids_old = array_column($query->rows, 'option_id');
							
							$vo_options_ids_to_add    = array_diff($vo_options_ids, $vo_options_ids_old);
							$vo_options_ids_to_remove = array_diff($vo_options_ids_old, $vo_options_ids);
							if ($vo_options_ids_to_remove) {
								$query = $this->db->query("
									DELETE FROM `".DB_PREFIX."relatedoptions_variant_option`
									WHERE relatedoptions_variant_id=".(int)$vo_id."
									  AND option_id IN (".implode(',', array_map('intval', $vo_options_ids_to_remove)).")
								");
							}
							
							if ($vo_options_ids_to_add) {
								foreach ($vo_options_ids_to_add as $opts_key => $option_id) {
									
									// fix ro remove duplicates
									// $query = $this->db->query("	DELETE FROM `".DB_PREFIX."relatedoptions_variant_option` WHERE relatedoptions_variant_id=".(int)$vo_id." AND option_id = ".(int)$option_id." ");
									
									$query = $this->db->query("	INSERT INTO `".DB_PREFIX."relatedoptions_variant_option` SET relatedoptions_variant_id=".(int)$vo_id.", option_id = ".(int)$option_id." ");
								}
							}
						}
					}
					
					$this->liveopencart_ext_ro->callPRO('updateVariantFields', [$vo_id, !empty($vo_arr['fields']) ? $vo_arr['fields'] : []]);
				}
				
			}
		}
		
		if ($clear_others) {
			
			if ($clear_others) {
				$query = $this->db->query("	DELETE FROM `".DB_PREFIX."relatedoptions_variant_option` WHERE NOT relatedoptions_variant_id IN (0".$str_vo_id.") ");
			}
			
			$this->removeVariants(" NOT relatedoptions_variant_id IN (0".$str_vo_id.") ");
			//$query = $this->db->query("	DELETE FROM `".DB_PREFIX."relatedoptions_variant` WHERE NOT relatedoptions_variant_id IN (0".$str_vo_id.") ");
			//$query = $this->db->query("	DELETE FROM `".DB_PREFIX."relatedoptions_variant_product` WHERE NOT relatedoptions_variant_id IN (0".$str_vo_id.") ");
		}
		
		$this->liveopencart_ext_ro->callPRO('removeFieldsOfInexistentVariants');
		
		return $updated_vo; //rov_ids
		
	}
	
	protected function removeVariants($sql_filter = "") {
		
		$query = $this->db->query("
			SELECT *
			FROM `".DB_PREFIX."relatedoptions_variant`
			".($sql_filter ? " WHERE ".$sql_filter." " : "")."
		");
		foreach ( $query->rows as $variant ) {
			$this->removeVariant($variant['relatedoptions_variant_id']);
		}
		
	}
	
	public function removeVariant($variant_id) {
		
		$this->liveopencart_ext_ro->callPRO('removeVariantFields', [$variant_id]);
		
		$sql_filter = " relatedoptions_variant_product_id IN (
			SELECT ROVP_REMOVE.relatedoptions_variant_product_id 
			FROM `".DB_PREFIX."relatedoptions_variant_product` ROVP_REMOVE
			WHERE ROVP_REMOVE.relatedoptions_variant_id = ".(int)$variant_id."
		)";
		
		$this->removeROCombsByFilter($sql_filter);

		$this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE relatedoptions_variant_id = ".(int)$variant_id." ");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_variant WHERE relatedoptions_variant_id = ".(int)$variant_id." ");
		
	}
	
	protected function getProductRODiscountsGrouppedByROVPId($product_id) {
		$rovps_ros_discounts = [];
		$query               = $this->db->query("
			SELECT RD.*, RO.relatedoptions_variant_product_id
			FROM `" . DB_PREFIX . "relatedoptions` RO
				,`" . DB_PREFIX . "relatedoptions_discount` RD
			WHERE RO.product_id = " . (int)$product_id . "
			  AND RO.relatedoptions_id = RD.relatedoptions_id
			ORDER BY RD.relatedoptions_id, RD.customer_group_id, RD.quantity 
		");
		
		foreach ($query->rows as $row) {
			if (!isset($rovps_ros_discounts[$row['relatedoptions_variant_product_id']])) {
				$rovps_ros_discounts[$row['relatedoptions_variant_product_id']] = [];
			}
			$rovps_ros_discounts[$row['relatedoptions_variant_product_id']][] = $row;
		}
		
		return $rovps_ros_discounts;
	}
	
	protected function getProductROSpecialsGrouppedByROVPId($product_id) {
		$rovps_ros_specials = [];
		$query              = $this->db->query("
			SELECT RD.*, RO.relatedoptions_variant_product_id
			FROM `" . DB_PREFIX . "relatedoptions` RO
				,`" . DB_PREFIX . "relatedoptions_special` RD
			WHERE RO.product_id = " . (int)$product_id . "
			  AND RO.relatedoptions_id = RD.relatedoptions_id
			ORDER BY RD.relatedoptions_id, RD.customer_group_id
		");
		
		foreach ($query->rows as $row) {
			if (!isset($rovps_ros_specials[$row['relatedoptions_variant_product_id']])) {
				$rovps_ros_specials[$row['relatedoptions_variant_product_id']] = [];
			}
			$rovps_ros_specials[$row['relatedoptions_variant_product_id']][] = $row;
		}
		
		return $rovps_ros_specials;
	}
	
	protected function getProductROCompsGrouppedByROVPId($product_id) {
		$rovps_ros_ro_combs = [];
		$query              = $this->db->query("
			SELECT RO.*
			FROM 	`" . DB_PREFIX . "relatedoptions` RO
			WHERE RO.product_id = " . (int)$product_id . "
			ORDER BY RO.relatedoptions_id
		");
	
		foreach ($query->rows as $row) {
			if (!isset($rovps_ros_ro_combs[$row['relatedoptions_variant_product_id']])) {
				$rovps_ros_ro_combs[$row['relatedoptions_variant_product_id']] = [];
			}
			$rovps_ros_ro_combs[$row['relatedoptions_variant_product_id']][] = $row;
		}
		
		return $rovps_ros_ro_combs;
	}
	
	protected function getProductROOptionsGrouppedByROVPId($product_id) {
		$rovps_ros_options = [];
		$query             = $this->db->query("
				SELECT ROO.*, RO.relatedoptions_variant_product_id
				FROM `" . DB_PREFIX . "relatedoptions_option` ROO
					,`" . DB_PREFIX . "relatedoptions` RO
					,`" . DB_PREFIX . "option` O
					,`" . DB_PREFIX . "option_value` OV
				WHERE ROO.product_id = " . (int)$product_id . "
				  AND RO.relatedoptions_id = ROO.relatedoptions_id
				  AND O.option_id = ROO.option_id
				  AND OV.option_value_id = ROO.option_value_id
				ORDER BY ROO.relatedoptions_id, O.sort_order, OV.sort_order 
			");
		
		foreach ($query->rows as $row) {
			if (!isset($rovps_ros_options[$row['relatedoptions_variant_product_id']])) {
				$rovps_ros_options[$row['relatedoptions_variant_product_id']] = [];
			}
			$rovps_ros_options[$row['relatedoptions_variant_product_id']][] = $row;
		}
		
		return $rovps_ros_options;
	}
	
	protected function getOptionsIdsGrouppedByROVId() {
		$rovs_ros_options_ids = [];
		$query                = $this->db->query("
			SELECT ROVO.*
			FROM 	`" . DB_PREFIX . "relatedoptions_variant_option` ROVO
			ORDER BY ROVO.option_id
		");
		foreach ($query->rows as $row) {
			if (!isset($rovs_ros_options_ids[$row['relatedoptions_variant_id']])) {
				$rovs_ros_options_ids[$row['relatedoptions_variant_id']] = [];
			}
			$rovs_ros_options_ids[$row['relatedoptions_variant_id']][] = $row['option_id'];
		}
		
		return $rovs_ros_options_ids;
	}
	
	public function getRODataCached($product_id, $with_char_id = false) {
		$cache_name = 'ro_datas';
		$key        = json_encode([$product_id, $with_char_id]);
		if ( !$this->hasCache($cache_name, $key) ) {
			$ro_data = $this->getROData($product_id, $with_char_id);
			$this->setCache($cache_name, $key, $ro_data);
		}
		return $this->getCache($cache_name, $key);
	}
	
	public function getROData($product_id, $with_char_id = false) {
		
		if (!$this->installed()) {
			return [];
		}
		
		$ro_data = [];
		
		$query = $this->getQueryROVP($product_id);
		
		if ( $this->liveopencart_ext_ro->versionPRO() ) {
			$ros_cgs = $this->liveopencart_ext_relatedoptions_customer_group->getROCombsCustomerGroupsDataByProductId($product_id);
		}
		
		$rovp_rows = $query->rows;
		
		$rovs_ros_options_ids  = $this->getOptionsIdsGrouppedByROVId();
		$rovps_ros_discounts   = $this->getProductRODiscountsGrouppedByROVPId($product_id);
		$rovps_ros_specials    = $this->getProductROSpecialsGrouppedByROVPId($product_id);
		$rovps_ros_ro_combs    = $this->getProductROCompsGrouppedByROVPId($product_id);
		$rovps_ros_ros_options = $this->getProductROOptionsGrouppedByROVPId($product_id);
		
		foreach ($rovp_rows as $rovp_row) {
			
			$ro_data[] = [
				'rovp_id'           => $rovp_row['relatedoptions_variant_product_id'],
				'use'               => $rovp_row['relatedoptions_use'],
				'rov_id'            => $rovp_row['relatedoptions_variant_id'],
				'allow_zero_select' => $rovp_row['allow_zero_select'],
				'ro'                => [],
				'options_ids'       => [],
				'rov_name'          => $rovp_row['rov_name'],
			];
			$cnt     = count($ro_data) - 1;
			$rovp_id = (int)$rovp_row['relatedoptions_variant_product_id'];
			
			$ro_data[$cnt]['options_ids'] = (isset($rovs_ros_options_ids[$rovp_row['relatedoptions_variant_id']]) ? $rovs_ros_options_ids[$rovp_row['relatedoptions_variant_id']] : []);
			
			if (isset($rovps_ros_ro_combs[$rovp_id])) {
				foreach ($rovps_ros_ro_combs[$rovp_id] as $row) {
				//foreach ($query->rows as $row) {
					
					$ro_data[$cnt]['ro'][$row['relatedoptions_id']]              = $row;
					$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['options']   = [];
					$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['fields']    = [];
					$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['discounts'] = [];
					$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['specials']  = [];
					
					if ( $this->liveopencart_ext_ro->versionPRO() ) {
						$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['customer_groups'] = (isset($ros_cgs[$row['relatedoptions_id']]) ? $ros_cgs[$row['relatedoptions_id']] : []);
					}
				}
			}
			
			if (isset($rovps_ros_ros_options[$rovp_id])) {
				foreach ($rovps_ros_ros_options[$rovp_id] as $row) {
				//foreach ($query->rows as $row) {
					$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['options'][$row['option_id']] = $row['option_value_id'];
				}
			}
			
			$updated_ro_combs = $this->liveopencart_ext_ro->callPRO('addVariantFieldsToProductROCombsAssoc', [$ro_data[$cnt]['ro'], $product_id, $rovp_id]);
			if ( $updated_ro_combs ) {
				$ro_data[$cnt]['ro'] = $updated_ro_combs;
			}
			
			if (isset($rovps_ros_discounts[$rovp_id])) {
				foreach ($rovps_ros_discounts[$rovp_id] as $row) {
					$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['discounts'][] = $row;
				}
			}
			
			if (isset($rovps_ros_specials[$rovp_id])) {
				foreach ($rovps_ros_specials[$rovp_id] as $row) {
					$ro_data[$cnt]['ro'][$row['relatedoptions_id']]['specials'][] = $row;
				}
			}
			
		}
		
		$ro_data = $this->loadImagesToROData($product_id, $ro_data);
		
		$this->liveopencart_ext_ro->event->trigger('getROData_after', [$product_id, $ro_data]);
		
		return $ro_data;
	}
	
	public function getROCombName($ro_id, $include_option_names = false) {
		
		$query = $this->db->query("
			SELECT OD.name option_name, OVD.name option_value_name
			FROM ".DB_PREFIX."relatedoptions_option ROO
				,`".DB_PREFIX."option` O
				,`".DB_PREFIX."option_description` OD
				,`".DB_PREFIX."option_value_description` OVD
			WHERE ROO.relatedoptions_id = ".(int)$ro_id."
			  AND ROO.option_id = O.option_id
			  AND ROO.option_id = OD.option_id
			  AND ROO.option_value_id = OVD.option_value_id
			  AND OD.language_id = ".(int)$this->config->get('config_language_id')."
			  AND OVD.language_id = ".(int)$this->config->get('config_language_id')."
			ORDER BY O.sort_order ASC, OD.name ASC  
		");
		
		$names = [];
		foreach ( $query->rows as $row ) {
			$names[] = ($include_option_names ? $row['option_name'].': ' : '').$row['option_value_name'];
		}
		
		$name = implode(' + ', $names);
		
		$mod_settings = $this->config->get('related_options');
		if ( $this->liveopencart_ext_ro->versionPRO() && !empty($mod_settings['spec_customer_groups']) ) {
			$ro_cgs = $this->liveopencart_ext_relatedoptions_customer_group->getROCombsCustomerGroupsDataByROId($ro_id);
			if ( $ro_cgs ) {
				$name .= ' ('.implode(', ', array_column($ro_cgs, 'name')).')';
			}
		}
		
		return $name;
	}
	
	protected function loadImagesToROData($product_id, $ro_data) {
		if ( $ro_data ) {
			$result = $this->liveopencart_ext_ro->callPOIU('loadImagesToROData', [$product_id, $ro_data]);
			if ( $result ) {
				$ro_data = $result;
			}
		}
		return $ro_data;
	}
	
	protected function saveImagesForROCombs($product_id, $ro_combs, $option_data) {
		// call POIP, not POIU, to remove images in case of module downgrade
		$this->liveopencart_ext_ro->callPOIP('saveImagesForROCombs', [$product_id, $ro_combs, $option_data]);
	}
	
	protected function removeROImagesByProductId($product_id) {
		$this->liveopencart_ext_ro->callPOIU('removeROImagesByProductId', [$product_id] );
	}
	
	public function getOptionTypes() {
		return "'select', 'radio', 'image', 'block', 'color'";
	}
	
	protected function getROVariantProductId($product_id, $rov_id) {
		
		$query = $this->db->query("
			SELECT relatedoptions_variant_product_id
			FROM `".DB_PREFIX."relatedoptions_variant_product`
			WHERE product_id = ".(int)$product_id."
			  AND relatedoptions_variant_id = ".$rov_id."
		");
		if ( $query->num_rows ) {
			return $query->row['relatedoptions_variant_product_id'];
		}
		return 0;
		
	}
	
	// find relevant related option variant for product related options set, not found- create new
	protected function findOrCreateROVariant($data) {
		
		if (isset($data['ro']) && (is_array($data['ro']))) {
			
			$all_options = [];
			foreach ($data['ro'] as $relatedoptions) {
			
				if (isset($relatedoptions['options']) && is_array($relatedoptions['options'])) {
					$options = array_keys($relatedoptions['options']);
					$options = array_map('intval', $options);
					$options = array_filter($options);
					foreach ($options as $option_id) {
						if (!in_array($option_id, $all_options)) {
							$all_options[] = $option_id;
						}
					}
				}
			}
			
			if (count($all_options) > 0) {
				
				sort($all_options);
				
				$variants = $this->getVariants();
				
				foreach ($variants as $variant_id => $variant) {
					
					$vo_options = [];
					foreach ($variant['options'] as $option) {
						$vo_options[] = $option['option_id'];
					}
					//$vo_options = array_keys($variant['options']);
					sort($vo_options);
					if ($vo_options == $all_options) {
						return $variant_id;
					}
				}
			}
			
			// not found - create new
			$vo       = [];
			$vo[]     = ['options' => $all_options];
			$vo_added = $this->setVariantsOfRelatedOptions($vo, FALSE);
			if (is_array($vo_added) && count($vo_added) != 0) {
				return reset($vo_added);
			}
			
		}
		
		return 0;
	}
	
	protected function getOptionsUsedInRO($product_id) {
		$result = [];
		$query  = $this->db->query("
			SELECT DISTINCT ROO.option_id
			FROM ".DB_PREFIX."relatedoptions_option ROO
			WHERE ROO.product_id = ".(int)$product_id."
		");
		return array_column($query->rows, 'option_id');
	}
	
	protected function getProductOptionsUsedInRO($product_id) {
		$result = [];
		$query  = $this->db->query("
			SELECT DISTINCT PO.product_option_id
			FROM ".DB_PREFIX."relatedoptions_option ROO
				,".DB_PREFIX."product_option PO
			WHERE ROO.product_id = ".(int)$product_id."
			  AND ROO.option_id = PO.option_id
			  AND PO.product_id = ".(int)$product_id."
		");
		foreach ( $query->rows as $row ) {
			$result[] = $row['product_option_id'];
		}
		return $result;
	}
	
	public function getProductOptionsValuesUsedInRO($product_id) {
		$result = [];
		$query  = $this->db->query("
			SELECT DISTINCT POV.product_option_value_id
			FROM ".DB_PREFIX."relatedoptions_option ROO
				,".DB_PREFIX."product_option_value POV
			WHERE ROO.product_id = ".(int)$product_id."
			  AND ROO.option_value_id = POV.option_value_id
			  AND POV.product_id = ".(int)$product_id."
		");
		foreach ( $query->rows as $row ) {
			$result[] = $row['product_option_value_id'];
		}
		return $result;
	}
	
	public function setROData($product_id, $data) {
		
		if ( (isset($data['ro_data_included']) && $data['ro_data_included']) || (isset($data['ro_data']) && $data['ro_data']) ) {
			
			$mod_settings = $this->config->get('related_options');
			$ro_quantity  = false;
			$options      = [];
			$ro_combs     = [];
			
			$ro_use = false;
			
			if ( $mod_settings && !empty($mod_settings['update_options']) && !empty($mod_settings['update_options_remove']) ) {
				$product_options_used_in_ro_before = $this->getProductOptionsUsedInRO($product_id);
			}
			
			$this->removeROImagesByProductId($product_id);
			
			if ( !isset($data['ro_data']) || !$data['ro_data'] ) {
				// remove all product related options
				
				$this->editRelatedOptions($product_id, 0);
				
				return;
				
			} else {
				
				$used_rovp_ids = [];
				
				foreach ($data['ro_data'] as $ro_dt) {
					
					$edited_data = $this->editRelatedOptions($product_id, $ro_dt, $options, $ro_quantity);
					
					if ( $edited_data ) {
						$options     = $edited_data['product_options'];
						$ro_quantity = $edited_data['quantity_total'];
						
						$ro_combs = $ro_combs + $edited_data['ro_combs']; // saves keys
						if ( $edited_data['rovp_id'] ) {
							$used_rovp_ids[] = $edited_data['rovp_id'];
						}
					}
					
					$ro_use = $ro_use || (isset($ro_dt['use']) && $ro_dt['use']);
					
				}
				
				// remove not used ro variants from the product
				$rovp_rows = $this->getProductVariants($product_id);
				if ( $rovp_rows ) {
					foreach ( $rovp_rows as $rovp_row ) {
						if ( !in_array($rovp_row['relatedoptions_variant_product_id'], $used_rovp_ids) ) {
							$this->editRelatedOptions($product_id, ['rovp_id' => $rovp_row['relatedoptions_variant_product_id'], 'use' => false] );
						}
					}
				}
			}
			
			// update options and  quantity only if related options enabled
			if ($ro_use) {
				
				$this->updateStandardProductDataByRO($product_id, $options, !empty($product_options_used_in_ro_before) ? $product_options_used_in_ro_before : [], $ro_quantity);
				
				// it should work even if RO do not update options
				$this->saveImagesForROCombs($product_id, $ro_combs, $options);
				
			}
			
		}
		
	}
	
	protected function getProductSubtractStock($product_id) {
		$query = $this->db->query("SELECT subtract FROM `".DB_PREFIX."product` WHERE `product_id` = ".(int)$product_id." " );
		return $query->num_rows && $query->row['subtract'];
	}
	
	public function getProductIdByROId($ro_id) {
		$query = $this->db->query("SELECT product_id FROM ".DB_PREFIX."relatedoptions WHERE relatedoptions_id = ".(int)$ro_id." ");
		if ( $query->num_rows ) {
			return $query->row['product_id'];
		}
	}
	
	public function setROCombQuantity($ro_id, $quantity) {
		$this->db->query("UPDATE ".DB_PREFIX."relatedoptions SET quantity = ".(int)$quantity." WHERE relatedoptions_id = ".(int)$ro_id." ");
	}
	
	public function setROCombQuantityAndUpdateStandardQuantities($ro_id, $quantity) {
		$this->db->query("UPDATE ".DB_PREFIX."relatedoptions SET quantity = ".(int)$quantity." WHERE relatedoptions_id = ".(int)$ro_id." ");
		
		$product_id = $this->getProductIdByROId($ro_id);
		if ( $product_id ) {
			$this->updateStandardProductDataByRO($product_id);
		}
	}
	
	public function updateStandardProductDataByRO($product_id, $options = [], $product_options_used_in_ro_before = [], $ro_quantity = 0) {
		
		$mod_settings = $this->config->get('related_options');
		
		if ( !$options ) { // get from db
			$options       = [];
			$ro_data       = $this->getROData($product_id);
			$ro_quantities = [];
			foreach ( $ro_data as $ro_dt ) {
				$ro_quantities[] = array_sum(array_column($ro_dt['ro'], 'quantity'));
				
				$current_options = [];
				foreach ( $ro_dt['ro'] as $ro_comb ) {
					
					if ( $this->liveopencart_ext_ro->getStatusSeparatePOVForCustomerGroups() ) {
						$current_cg_key = (!empty($mod_settings['spec_customer_groups']) && !empty($ro_comb['customer_groups'])) ? implode('-', $ro_comb['customer_groups']) : '';
					} else {
						$current_cg_key = ''; // do not create separate option values for customer groups
					}
					
					foreach ( $ro_comb['options'] as $o_id => $ov_id ) {
						if ( !isset($current_options[$o_id]) ) {
							$current_options[$o_id] = [];
						}
						if ( !isset($current_options[$o_id][$ov_id]) ) {
							$current_options[$o_id][$ov_id] = [];
						}
						if ( !isset($current_options[$o_id][$ov_id][$current_cg_key]) ) {
							$current_options[$o_id][$ov_id][$current_cg_key] = ['quantity' => 0];
						}
						$current_options[$o_id][$ov_id][$current_cg_key]['quantity'] += $ro_comb['quantity'];
					}
				}
				foreach ($current_options as $o_id => $o_values) {
					foreach ( $o_values as $ov_id => $ov_cg_dts ) {
						
						foreach ($ov_cg_dts as $cg_key => $ov_cg_dt) {
							if ( !isset($options[$o_id]) ) {
								$options[$o_id] = [];
							}
							if ( !isset($options[$o_id][$ov_id]) ) {
								$options[$o_id][$ov_id] = [];
							};
							if ( !isset($options[$o_id][$ov_id][$cg_key]) ) {
								$options[$o_id][$ov_id][$cg_key] = $ov_cg_dt;
							} else {
								$options[$o_id][$ov_id]['quantity'] = min($options[$o_id][$ov_id][$cg_key]['quantity'], $ov_cg_dt['quantity']);
							}
						}
					}
				}
			}
			$ro_quantity = min($ro_quantities);
		}
		
		// update product quantity
		if ( $mod_settings && isset($mod_settings['update_quantity']) && $mod_settings['update_quantity'] ) {
			$this->db->query("UPDATE ".DB_PREFIX."product SET quantity = ".(int)$ro_quantity." WHERE product_id = ".(int)$product_id." ");
		}
		
		// update options
		if ( $mod_settings && isset($mod_settings['update_options']) && $mod_settings['update_options'] ) {
			
			if ( !empty($mod_settings['update_options_remove']) ) {
				$product_options_used_in_ro_now = $this->getProductOptionsUsedInRO($product_id);
				foreach ( $product_options_used_in_ro_before as $product_option_id ) {
					if ( !in_array($product_option_id, $product_options_used_in_ro_now) ) {
						$this->db->query("DELETE FROM ".DB_PREFIX."product_option_value WHERE product_option_id = ".(int)$product_option_id );
						$this->db->query("DELETE FROM ".DB_PREFIX."product_option WHERE product_option_id = ".(int)$product_option_id );
					}
				}
			}
			
			$product_subtract = $this->getProductSubtractStock($product_id);
			//$product_subtract = 0;
			//$query = $this->db->query("SELECT subtract FROM " . DB_PREFIX . "product WHERE product_id = ".(int)$product_id);
			//if ($query->num_rows) {
			//	$product_subtract = (int)$query->row['subtract'];
			//}
			
			$product_options_saved        = [];
			$product_options_values_saved = [];
			
			if (count($options)) {
				// update by options
				foreach ($options as $option_id => $option_values) {
					
					if ( isset($product_options_saved[$option_id]))  {
						$product_option_id = $product_options_saved[$option_id];
					} else {
						$product_option_id                 = $this->getUpdateProductOptionIdByOptionId($product_id, $option_id);
						$product_options_saved[$option_id] = $product_option_id;
					}
					
					if (!isset($product_options_values_saved[$product_option_id])) {
						$product_options_values_saved[$product_option_id] = [];
					}
					
					foreach ($option_values as $option_value_id => $option_cgs_datas) {
					//foreach ($option_values as $option_value_id => $option_data) {
						if ( $this->liveopencart_ext_ro->installedProductOptionValueByCustomerGroup() ) {
							$option_datas = $option_cgs_datas;
						} else {
							$option_datas = [['quantity' => array_sum(array_column($option_cgs_datas, 'quantity'))]];
						}
							
						foreach ( $option_datas as $cg_key => $option_data ) {
							if ( $option_value_id ) {
								$product_option_value_id = $this->getUpdateProductOptionValueIdByOptionValueId($product_id, $product_option_id, $option_id, $option_value_id, $product_subtract, $option_data['quantity'], $cg_key);
							} else {
								$product_option_value_id = 0;
							}
							
							$product_options_values_saved[$product_option_id][] = $product_option_value_id;
						}
					}
				}
				
				$sql_add = join(",", $product_options_saved);
				if ($sql_add != "") {
					$sql_add = "AND NOT product_option_id IN (".$sql_add.")";
				}
				
				$this->db->query("
					DELETE FROM " . DB_PREFIX . "product_option
					WHERE product_id = " . (int)$product_id . "
					  AND option_id IN (".join(",",array_keys($options)).")
						".$sql_add."
				");
				
				$sql_add = "";
				foreach ($product_options_values_saved as $product_option_id => $values) {
					if (count($values) != 0) {
						$sql_add .= ",".join(",",$values);
					}
				}
				if ($sql_add != "") {
					$sql_add = "AND NOT product_option_value_id IN (".substr($sql_add,1).")";
				}
				
				$this->db->query("
					DELETE FROM " . DB_PREFIX . "product_option_value
					WHERE product_id = " . (int)$product_id . "
					  AND option_id IN (".join(",",array_keys($options)).")
						".$sql_add."
				");
			}
		}
		
	}
	
	protected function getUpdateProductOptionIdByOptionId($product_id, $option_id) {
		
		$mod_settings = $this->config->get('related_options');
		
		$required_setting         = 1;
		$required_only_first_time = false;
		if ( isset($mod_settings['required']) ) {
			if ($mod_settings['required'] == 0) { //yes
				$required_setting = 1;
			} elseif ($mod_settings['required'] == 1) { // no
				$required_setting = 0;
			} elseif ($mod_settings['required'] == 2) { // yes only first time
				$required_setting         = 1;
				$required_only_first_time = true;
			}
		}
		
		$query = $this->db->query("
			SELECT product_option_id, required
			FROM " . DB_PREFIX . "product_option
			WHERE product_id = " . (int)$product_id . "
			  AND option_id = ".$option_id."
		");
		if ($query->num_rows) {
			$product_option_id = $query->row['product_option_id'];
			if ($query->row['required'] != $required_setting && !$required_only_first_time ) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_option SET required = ".(int)$required_setting." WHERE product_option_id = " . $product_option_id . " ");
			}
			
		} else {
			$query = $this->db->query("
				INSERT INTO " . DB_PREFIX . "product_option
				SET product_id = " . (int)$product_id . ", option_id = ".$option_id.", required = 1
			");
			$product_option_id = $this->db->getLastId();
		}
		return $product_option_id;
	}
	
	// $cg_key - for comp with Product Option Value By Customer Group (to create separate option values for different customer groups)
	protected function getUpdateProductOptionValueIdByOptionValueId($product_id, $product_option_id, $option_id, $option_value_id, $product_subtract, $quantity, $cg_key = '' ) {
		
		$mod_settings = $this->config->get('related_options');
		
		$subtract_stock                 = 0;
		$subtract_stock_only_first_time = false;
		if ( !isset($mod_settings['subtract_stock']) || $mod_settings['subtract_stock'] == 0 ) { // from product
			$subtract_stock = $product_subtract;
		} elseif ( $mod_settings['subtract_stock'] == 1 ) { // from product only first time
			$subtract_stock                 = $product_subtract;
			$subtract_stock_only_first_time = true;
		} elseif ( $mod_settings['subtract_stock'] == 2 ) { // yes
			$subtract_stock = 1;
		} elseif ( $mod_settings['subtract_stock'] == 3 ) { // no
			$subtract_stock = 0;
		}
		
		$sql_cg = '';
		if ( $this->liveopencart_ext_ro->installedProductOptionValueByCustomerGroup() ) {
			if ( $cg_key ) {
				
				$cg_ids = explode('-', $cg_key);
				array_map('intval', $cg_ids);
				sort($cg_ids);
				$cg_key_concat = implode(',', $cg_ids);
				
				$sql_cg = "AND '".$cg_key_concat."' = (
					SELECT GROUP_CONCAT(POVCG.customer_group_id ORDER BY POVCG.customer_group_id)
					FROM ".DB_PREFIX."product_option_value_group POVCG
					WHERE POVCG.product_option_value_id = POV.product_option_value_id
				) ";
				
			} else {
				$sql_cg = " 
					AND NOT (product_option_value_id IN (
						SELECT product_option_value_id
						FROM ".DB_PREFIX."product_option_value_group
						WHERE product_id = ".(int)$product_id."
					))
				";
			}
		}
		
		$query = $this->db->query("
			SELECT POV.product_option_value_id, POV.subtract
			FROM " . DB_PREFIX . "product_option_value POV
			WHERE POV.product_option_id = " . (int)$product_option_id . "
			  AND POV.option_value_id = ".(int)$option_value_id."
			  ".$sql_cg."
		");
		
		if ($query->num_rows) {
			
			$product_option_value_id = $query->row['product_option_value_id'];
			
			$this->db->query("
				UPDATE " . DB_PREFIX . "product_option_value
				SET quantity = ".(int)$quantity."
				WHERE product_option_value_id = ".(int)$product_option_value_id."	
			");
			
			if ($query->row['subtract'] != $subtract_stock && !$subtract_stock_only_first_time) {
				$this->db->query("
					UPDATE " . DB_PREFIX . "product_option_value
					SET subtract = ".(int)$subtract_stock."
					WHERE product_option_value_id = ".(int)$product_option_value_id."	
				");
			}
			
		} else {
			
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "product_option_value
				SET product_id = " . (int)$product_id . ", option_id = ".(int)$option_id."
				  , option_value_id = ".(int)$option_value_id.", quantity = ".(int)$quantity."
				  , product_option_id = ".(int)$product_option_id.", subtract = ".(int)$subtract_stock."
			");
			$product_option_value_id = $this->db->getLastId();
			
			if ( $cg_key && $this->liveopencart_ext_ro->installedProductOptionValueByCustomerGroup() ) {
				foreach ( explode('-', $cg_key) as $cg_id ) {
					$this->db->query("
						INSERT INTO ".DB_PREFIX."product_option_value_group
						SET product_id = ".(int)$product_id."
						  , product_option_value_id = ".(int)$product_option_value_id."
						  , customer_group_id = ".(int)$cg_id."
					");
				}
			}
			
		}
		
		return $product_option_value_id;
	}

	public function editRelatedOptions($product_id, $data, $param_product_options = false, $quantity_total = false) {
		
		if (!$this->installed() || (int)$product_id == 0) {
			return;
		}
		
		if ($data === 0) {
			$this->removeRelatedOptions($product_id);
			return;
		}
		
		$ro_use = (isset($data['use']) && $data['use']);
		
		// for importing
		if ($ro_use && isset($data['ro']) && is_array($data['ro']) && count($data['ro']) > 0 && isset($data['related_options_variant_search']) && $data['related_options_variant_search'] ) {
			$data['rov_id']  = $this->findOrCreateROVariant($data);
			$data['rovp_id'] = $this->getROVariantProductId($product_id, $data['rov_id']);
		}
		
		if ($ro_use) {
			$rovp_id = $this->setProductVariant($product_id, $data, $ro_use);
			
			$this->db->query("
				DELETE RD
				FROM " . DB_PREFIX . "relatedoptions_discount RD
				INNER JOIN " . DB_PREFIX . "relatedoptions RO ON(RO.relatedoptions_id = RD.relatedoptions_id )
				WHERE RO.relatedoptions_variant_product_id = ".(int)$rovp_id."
			");
		
			$this->db->query("
				DELETE RS
				FROM " . DB_PREFIX . "relatedoptions_special RS
				INNER JOIN " . DB_PREFIX . "relatedoptions RO ON(RO.relatedoptions_id = RS.relatedoptions_id )
				WHERE RO.relatedoptions_variant_product_id = ".(int)$rovp_id."
			");
			
		} else {
			
			if (isset($data['rovp_id']) && $data['rovp_id']) {
				$this->removeROCombsByFilter(" relatedoptions_variant_product_id = ".(int)$data['rovp_id']." ");

				$this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE relatedoptions_variant_product_id = ".(int)$data['rovp_id']." ");
			}
			return;
		}
		
		$ro_combs = [];
			
		if ($ro_use && isset($data['rov_id']))	{
			
			$mod_settings = $this->config->get('related_options');
			
			// get existing related options
			$query = $this->db->query("
				SELECT relatedoptions_id
				FROM " . DB_PREFIX . "relatedoptions
				WHERE product_id = " . (int)$product_id . "
				  AND relatedoptions_variant_product_id = ".(int)$rovp_id."
			");
			$rop_array = [];
			foreach ($query->rows as $row) {
				$rop_array[] = $row['relatedoptions_id'];
			}
			
			$ropupd_array = [];
			
			// to calculate options quantity
			$product_options = [];
			
			$options = $this->getVariantOptions($data['rov_id']);
			
			$ro_quantity = 0;
			
			if ( isset($data['ro']) && (is_array($data['ro']))  ) {
			
				if (count($options) != 0) {
					
					// remove links from related options to options
					$this->db->query("
						DELETE ROO
						FROM " . DB_PREFIX . "relatedoptions_option ROO
						INNER JOIN " . DB_PREFIX . "relatedoptions RO ON(RO.relatedoptions_id = ROO.relatedoptions_id )
						WHERE RO.relatedoptions_variant_product_id = ".(int)$rovp_id."
					");
					
					//$this->db->query("
					//	DELETE FROM " . DB_PREFIX . "relatedoptions_option
					//	WHERE relatedoptions_id IN (SELECT relatedoptions_id FROM ".DB_PREFIX."relatedoptions WHERE relatedoptions_variant_product_id = " . (int)$rovp_id . " )
					//");
					
					if ( $this->liveopencart_ext_ro->versionPRO() ) {
						$this->liveopencart_ext_relatedoptions_customer_group->delete(" relatedoptions_id IN ( SELECT relatedoptions_id FROM ".DB_PREFIX."relatedoptions WHERE relatedoptions_variant_product_id = ".(int)$rovp_id.") ");
					}
					
					$this->liveopencart_ext_ro->callPRO('removeVariantFieldValues', ["
						relatedoptions_id IN (SELECT relatedoptions_id FROM ".DB_PREFIX."relatedoptions WHERE relatedoptions_variant_product_id = ".(int)$rovp_id.")
					"]);
					
					$query = $this->db->query("
						SELECT relatedoptions_id
						FROM ".DB_PREFIX."relatedoptions
						WHERE relatedoptions_variant_product_id = " . (int)$rovp_id . "
					");
					$ro_ids_existing_before = array_map(function($row){
						return $row['relatedoptions_id'];
					}, $query->rows);
					
					foreach ($data['ro'] as $relatedoption) {
						
						$defaults_empty = ['model', 'sku', 'upc', 'ean', 'jan', 'location', 'stock_status_id', 'in_stock_status_id', 'weight_prefix', 'weight', 'price', 'defaultselect', 'defaultselectpriority', 'disabled'];
						
						foreach ($defaults_empty as $column_name) {
							if (!isset($relatedoption[$column_name])) $relatedoption[$column_name] = '';
						}
						
						if (!isset($relatedoption['price_prefix'])) $relatedoption['price_prefix'] = '=';
						
						$relatedoption['quantity'] = (int)$relatedoption['quantity'];
						
						$relatedoption['customer_groups'] = isset($relatedoption['customer_groups']) ? array_map('intval', $relatedoption['customer_groups']) : [];
						sort($relatedoption['customer_groups']);
						
						$relatedoptions_id = '';
						// if this related options combnation exists, let it be, otherwise we will add new
						if ( !empty($relatedoption['relatedoptions_id']) ) {

							if ( in_array((int)$relatedoption['relatedoptions_id'], $ro_ids_existing_before) ) {
								$relatedoptions_id = (int)$relatedoption['relatedoptions_id'];
								$ropupd_array[]    = $relatedoptions_id;
							}
						}
						
						$set_sql = "
							SET	product_id = " . (int)$product_id . "
							  , relatedoptions_variant_product_id = ".(int)$rovp_id."
							  , quantity = ".(int)$relatedoption['quantity']."
							  , model = '".$this->db->escape((string)$relatedoption['model'])."'
							  , sku = '".$this->db->escape((string)$relatedoption['sku'])."'
							  , upc = '".$this->db->escape((string)$relatedoption['upc'])."'
							  , ean = '".$this->db->escape((string)$relatedoption['ean'])."'
							  , jan = '".$this->db->escape((string)$relatedoption['jan'])."'
							  , location = '".$this->db->escape((string)$relatedoption['location'])."'
							  , stock_status_id = ".(int)$relatedoption['stock_status_id']."
							  , in_stock_status_id = ".(int)$relatedoption['in_stock_status_id']."
							  , weight_prefix = '".$this->db->escape((string)$relatedoption['weight_prefix'])."'
							  , weight = ".(float)$relatedoption['weight']."
							  , price = ".(float)$relatedoption['price']."
							  , price_prefix = '".(string)$relatedoption['price_prefix']."'
							  , defaultselect = ".(int)$relatedoption['defaultselect']."
							  , defaultselectpriority = ".(float)$relatedoption['defaultselectpriority']."
							  , disabled = ".(int)$relatedoption['disabled']."
						";
						
						if ( !$relatedoptions_id ) {
							$this->db->query("
								INSERT INTO " . DB_PREFIX . "relatedoptions
								".$set_sql."
							");
							$relatedoptions_id = $this->db->getLastId();
						} else {
							$this->db->query("
								UPDATE ".DB_PREFIX."relatedoptions
								".$set_sql."
								WHERE relatedoptions_id = ".(int)$relatedoptions_id."
							");
						}

						if ( $this->liveopencart_ext_ro->versionPRO() ) {
							foreach ( $relatedoption['customer_groups'] as $cg_id ) {
								$this->liveopencart_ext_relatedoptions_customer_group->insert([
									'relatedoptions_id' => $relatedoptions_id,
									'customer_group_id' => $cg_id,
								]);
							}
						}
						
						if ( isset($relatedoption['options']) && is_array($relatedoption['options']) ) {
							
							if ( $this->liveopencart_ext_ro->getStatusSeparatePOVForCustomerGroups() ) {
								$cg_key = implode('-', $relatedoption['customer_groups']);
							} else {
								$cg_key = ''; // do not create separate option values for different customer groups
							}
							$options_sets = [];
							foreach ($relatedoption['options'] as $option_id => $option_value_id) {
								
								if ( in_array($option_id, $options)) {
									
									$options_sets[] = "('".(int)$product_id."','".(int)$relatedoptions_id."','".(int)$option_id."','".(int)$option_value_id."')";
									
									// total for product options quantity
									if ( !isset($product_options[$option_id])) {
										$product_options[$option_id] = [];
									}
									if ( !isset($product_options[$option_id][$option_value_id])) {
										$product_options[$option_id][$option_value_id] = [];
										//$product_options[$option_id][$option_value_id] = array('ro_ids'=>array(), 'quantity'=>0);
									}
									if ( !isset($product_options[$option_id][$option_value_id][$cg_key])) {
										$product_options[$option_id][$option_value_id][$cg_key] = ['ro_ids' => [], 'quantity' => 0];
									}
									$product_options[$option_id][$option_value_id][$cg_key]['ro_ids'][] = $relatedoptions_id;
									$product_options[$option_id][$option_value_id][$cg_key]['quantity'] += (int)$relatedoption['quantity'];
								}
							}
							
							if ($options_sets) {
								$this->db->query("
									INSERT INTO " . DB_PREFIX . "relatedoptions_option
									(product_id, relatedoptions_id, option_id, option_value_id)
									VALUES ".implode(',', $options_sets)."
								");
							}
						}
						
						if ( !empty($relatedoption['fields']) ) {
							$this->liveopencart_ext_ro->callPRO('addVariantFieldValues', [$data['rov_id'], $relatedoptions_id, $relatedoption['fields']]);
						}
						
						// deleting should be already done above
						//$this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_discount WHERE relatedoptions_id = " . (int)$relatedoptions_id . " ");
						if (isset($relatedoption['discounts']) && is_array($relatedoption['discounts'])) {
							$discounts_sets = [];
							foreach ($relatedoption['discounts'] as $ro_discount) {
								$discounts_sets[] = "(".
									"'".(int)$relatedoptions_id."',".
									"'".(int)$ro_discount['customer_group_id']."',".
									"'".(int)$ro_discount['quantity']."',".
									"'".(int) ( isset($ro_discount['priority']) ? $ro_discount['priority'] : 0 )."',".
									"'".(float)$ro_discount['price']."'".
								")";
							}
							if ($discounts_sets) {
								$this->db->query("
									INSERT INTO " . DB_PREFIX . "relatedoptions_discount
									(relatedoptions_id, customer_group_id, quantity, priority, price)
									VALUES ".implode(',', $discounts_sets)."
								");
							}
						}
						
						// deleting should be already done above
						//$this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_special WHERE relatedoptions_id = " . (int)$relatedoptions_id . " ");
						if (isset($relatedoption['specials']) && is_array($relatedoption['specials'])) {
							foreach ($relatedoption['specials'] as $ro_special) {
								$this->db->query("
									INSERT INTO " . DB_PREFIX . "relatedoptions_special
									SET relatedoptions_id 	= " . (int)$relatedoptions_id . "
									  , customer_group_id 	= " . (int)$ro_special['customer_group_id'] . "
									  , priority 			= " . (int) ( isset($ro_special['priority']) ? $ro_special['priority'] : 0 ) . "
									  , price 				= " . (float)$ro_special['price'] . "
								");
							}
						}
						
						$ro_quantity += $relatedoption['quantity'];
						$ro_combs[$relatedoptions_id] = $relatedoption;
					}
					
				}
			}
			
			$str_del = '';
			foreach ($rop_array as $relatedoptions_id) {
				if ( !in_array($relatedoptions_id, $ropupd_array )) {
					$str_del .= (($str_del == '')?(''):(',')).$relatedoptions_id;
				}
			}
			
			if ($str_del != '') {
				$this->removeROCombsByFilter(" relatedoptions_variant_product_id = " . (int)$rovp_id . " AND relatedoptions_id IN (".$str_del.") ");
			}
			
			$quantity_total = $quantity_total === false ? (int)$ro_quantity : MIN($quantity_total, (int)$ro_quantity);
			
			// save options quantities in common data
			if ($product_options) {
				foreach($product_options as $option_id => $option_values) {
					
					if (!isset($param_product_options[$option_id])) {
						$param_product_options[$option_id] = $option_values;
					} else {
					 
						foreach ($option_values as $option_value_id => $option_cg_data) {
							if ($option_cg_data) {
								if ( !isset($param_product_options[$option_id][$option_value_id]) ) {
									$param_product_options[$option_id][$option_value_id] = $option_cg_data;
								} else {
									foreach ($option_cg_data as $cg_key => $option_data) {
										$param_product_options[$option_id][$option_value_id][$cg_key]['quantity'] = MIN($param_product_options[$option_id][$option_value_id][$cg_key]['quantity'], 		$option_data['quantity']);
										$param_product_options[$option_id][$option_value_id][$cg_key]['ro_ids']   = array_merge($param_product_options[$option_id][$option_value_id][$cg_key]['ro_ids'], $option_data['ro_ids']);
									}
								}
							}
						}
						
					}
				}
			}
			
		}
		
		$this->generateRelatedOptionsSearch($product_id);
		
		return ['product_options' => $param_product_options, 'quantity_total' => $quantity_total, 'rovp_id' => isset($rovp_id) ? $rovp_id : false, 'ro_combs' => $ro_combs ];
	}
	
	public function removeRelatedOptions($product_id = false) {
		if ($this->installed()) {
			if ($product_id === false) {
				$this->removeROCombsByFilter();
				$this->db->query("TRUNCATE TABLE ".DB_PREFIX."relatedoptions_variant_product ");
				$this->db->query("TRUNCATE TABLE ".DB_PREFIX."relatedoptions_search ");
			} else {
				$this->removeROCombsByProductId($product_id);
				$this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = " . (int)$product_id . "");
				$this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_search WHERE product_id = " . (int)$product_id . "");
			}
		}
	}
	
	protected function removeROCombsByProductId($product_id) {
		$this->removeROCombsByFilter(" product_id = ".(int)$product_id." ");
	}
	
	protected function removeROCombsByFilter($sql_where = "") {
		if ( !$sql_where ) { // remove all combinations of related options
			$this->db->query("TRUNCATE TABLE ".DB_PREFIX."relatedoptions ");
			$this->db->query("TRUNCATE TABLE ".DB_PREFIX."relatedoptions_option ");
			$this->db->query("TRUNCATE TABLE ".DB_PREFIX."relatedoptions_discount ");
			$this->db->query("TRUNCATE TABLE ".DB_PREFIX."relatedoptions_special ");
			$this->liveopencart_ext_ro->callPRO('removeVariantFieldValues');
			$this->liveopencart_ext_ro->callPOIU('removeROImagesByFilter');
			
			if ( $this->liveopencart_ext_ro->versionPRO() ) {
				$this->liveopencart_ext_relatedoptions_customer_group->delete("");
			}
		} else {
			
			$ro_comb_sql = "SELECT relatedoptions_id FROM ".DB_PREFIX."relatedoptions WHERE ".$sql_where;
			
			$this->liveopencart_ext_ro->callPRO('removeVariantFieldValues', [" relatedoptions_id IN ( ".$ro_comb_sql." ) "]);
			$this->liveopencart_ext_ro->callPOIU('removeROImagesByFilter', [$sql_where]);
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_discount WHERE relatedoptions_id IN ( ".$ro_comb_sql." )");
			$this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_special WHERE relatedoptions_id IN ( ".$ro_comb_sql." )");
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_option WHERE relatedoptions_id IN ( ".$ro_comb_sql." )");
			
			if ( $this->liveopencart_ext_ro->versionPRO() ) {
				$this->liveopencart_ext_relatedoptions_customer_group->delete(" relatedoptions_id IN ( ".$ro_comb_sql." ) ");
			}
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions WHERE ".$sql_where);
			
		}
	}
	
	public function productSaveValidate() {

		if ( $this->installed() && isset($this->request->post['ro_data']) && is_array($this->request->post['ro_data']) ) {
			
			$ro_data = $this->request->post['ro_data'];
			
			$ro_data_cnt = 0;
			foreach ($ro_data as $ro_dataset) {
				
				if (isset($ro_dataset['ro']) && !empty($ro_dataset['use']) ) {
					$ro_combs = $ro_dataset['ro'];
					
					$ro_data_cnt++;
					
					if (is_array($ro_combs)) {
					
						// there's shouldn't be options not relevant to selected variant
						// some extra options - not a problem, any missing - bad
						
						$voptions = $this->getVariantOptions($ro_dataset['rov_id']);
						
						$enough_options = true;
						foreach ($ro_combs as $ro_comb) {
							foreach ($voptions as $option_id) {
								if (!isset($ro_comb['options'][$option_id])) {
									$enough_options = false;
								}
							}
						}
						
						if (!$enough_options) {
							return $this->language->get('error_not_enough_options');
						}
				
						//$ro_keys = array_keys($ro_combs);
				
						// there are should not be equal option combinations
						if ($enough_options) {
							
							$prev_option_combs = [];
							$ro_cnt            = 0;
							$prev_keys         = [];
							foreach ( $ro_combs as $ro_comb  ) {
								$ro_cnt++;
								$ro_comb_key = json_encode([$ro_comb['options'], (isset($ro_comb['customer_groups']) ? $ro_comb['customer_groups'] : [])]);
								if ( !in_array($ro_comb_key, $prev_keys) ) {
									$prev_keys[] = $ro_comb_key;
								} else {
									return $this->language->get('error_equal_options').' #'.$ro_data_cnt.'-#'.$ro_cnt.'';
								}
							}
						}
					}
				}
			}
		}
	}
	
	public function getOrderInfoPageData($data) {
		
		$ro_settings = $this->config->get('related_options');
		
		$data['ro_installed'] = $this->installed();
		
		if ($data['ro_installed'] && $ro_settings)  {
			
			// appropriate language file should be loaded on previous steps
			$data['column_sku']      = $this->language->get('entry_sku');
			$data['column_upc']      = $this->language->get('entry_upc');
			$data['column_ean']      = $this->language->get('entry_ean');
			$data['column_jan']      = $this->language->get('entry_jan');
			$data['column_location'] = $this->language->get('entry_location');
		
			$data['ro_fields'] = [];
			$ro_fields         = ['sku', 'upc', 'ean', 'jan', 'location'];
			foreach ($ro_fields as $ro_field) {
				if (isset($ro_settings['spec_'.$ro_field]) && $ro_settings['spec_'.$ro_field]) {
					$data['ro_fields'][] = $ro_field;
				}
			}
		}
		
		return $data;
	}
	
	public function installed() {
		return \liveopencart\ext\ro::getInstance($this->registry)->installed();
	}
	
	public function install() {
		$this->uninstall();
	
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."relatedoptions` (
					`relatedoptions_id` int(11) NOT NULL AUTO_INCREMENT,
					`relatedoptions_variant_product_id` int(11) NOT NULL,
					`product_id` int(11) NOT NULL,
					`quantity` int(4) NOT NULL,
					`model` varchar(64) NOT NULL,
					`sku` varchar(64) NOT NULL,
					`upc` varchar(12) NOT NULL,
					`ean` VARCHAR(14) NOT NULL,
					`jan` VARCHAR(13) NOT NULL,
					`location` varchar(128) NOT NULL,
					`in_stock_status_id` int(11) NOT NULL,
					`stock_status_id` int(11) NOT NULL,
					`weight_prefix` varchar(1) NOT NULL,
					`weight` decimal(15,8) NOT NULL,
					`price_prefix` VARCHAR(2) NOT NULL,
					`price` decimal(15,4) NOT NULL,
					`defaultselect` tinyint(11) NOT NULL,
					`defaultselectpriority` int(11) NOT NULL,
					PRIMARY KEY (`relatedoptions_id`),
					KEY (`relatedoptions_variant_product_id`),
					FOREIGN KEY (product_id) REFERENCES ".DB_PREFIX."product(product_id) ON DELETE CASCADE,
					FOREIGN KEY (relatedoptions_variant_product_id) REFERENCES ".DB_PREFIX."relatedoptions_variant_product(relatedoptions_variant_product_id) ON DELETE CASCADE,
					KEY `quantity` (`quantity`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");
			
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."relatedoptions_option` (
					`relatedoptions_id` int(11) NOT NULL,
					`product_id` int(11) NOT NULL,
					`option_id` int(11) NOT NULL,
					`option_value_id` int(11) NOT NULL,
					FOREIGN KEY (`relatedoptions_id`) 	REFERENCES `".DB_PREFIX."relatedoptions`(`relatedoptions_id`) ON DELETE CASCADE,
					FOREIGN KEY (`option_value_id`) 	REFERENCES `".DB_PREFIX."option_value`(`option_value_id`) ON DELETE CASCADE,
					FOREIGN KEY (`option_id`) 			REFERENCES `".DB_PREFIX."option`(`option_id`) 			ON DELETE CASCADE,
					FOREIGN KEY (`product_id`) 			REFERENCES `".DB_PREFIX."product`(`product_id`) 			ON DELETE CASCADE
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");
			
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."relatedoptions_variant` (
					`relatedoptions_variant_id` int(11) NOT NULL AUTO_INCREMENT,
					`relatedoptions_variant_name` char(255) NOT NULL,
					`sort_order` int(3) NOT NULL,
					PRIMARY KEY (`relatedoptions_variant_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");
			
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."relatedoptions_variant_option` (
					`relatedoptions_variant_id` int(11) NOT NULL,
					`option_id` int(11) NOT NULL,
					FOREIGN KEY (`option_id`) 			REFERENCES `".DB_PREFIX."option`(`option_id`) 			ON DELETE CASCADE,
					FOREIGN KEY (`relatedoptions_variant_id`) 			REFERENCES `".DB_PREFIX."relatedoptions_variant`(`relatedoptions_variant_id`) 			ON DELETE CASCADE
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");
			
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."relatedoptions_variant_product` (
					`relatedoptions_variant_product_id` int(11) NOT NULL AUTO_INCREMENT,
					`relatedoptions_variant_id` int(11) NOT NULL,
					`product_id` int(11) NOT NULL,
					`relatedoptions_use` tinyint(1) NOT NULL,
					PRIMARY KEY (`relatedoptions_variant_product_id`),
					FOREIGN KEY (`product_id`) 			REFERENCES `".DB_PREFIX."product`(`product_id`) 			ON DELETE CASCADE,
								FOREIGN KEY (`relatedoptions_variant_id`) 			REFERENCES `".DB_PREFIX."relatedoptions_variant`(`relatedoptions_variant_id`) 			ON DELETE CASCADE
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."relatedoptions_discount` (
					`relatedoptions_id` int(11) NOT NULL,
					`customer_group_id` int(11) NOT NULL,
					`quantity` int(4) NOT NULL,
					`priority` int(5) NOT NULL,
					`price` decimal(15,4) NOT NULL,
					KEY (`relatedoptions_id`),
					KEY (`customer_group_id`),
					KEY (`quantity`),
					FOREIGN KEY (relatedoptions_id) REFERENCES ".DB_PREFIX."relatedoptions(relatedoptions_id) ON DELETE CASCADE,
					FOREIGN KEY (customer_group_id) REFERENCES ".DB_PREFIX."customer_group(customer_group_id) ON DELETE CASCADE
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."relatedoptions_special` (
					`relatedoptions_id` int(11) NOT NULL,
					`customer_group_id` int(11) NOT NULL,
					`priority` int(5) NOT NULL,
					`price` decimal(15,4) NOT NULL,
					KEY (`relatedoptions_id`),
					KEY (`customer_group_id`),
					FOREIGN KEY (relatedoptions_id) REFERENCES ".DB_PREFIX."relatedoptions(relatedoptions_id) ON DELETE CASCADE,
					FOREIGN KEY (customer_group_id) REFERENCES ".DB_PREFIX."customer_group(customer_group_id) ON DELETE CASCADE
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."relatedoptions_search` (
					`product_id` int(11) NOT NULL,
					`ro_ids` varchar(255) NOT NULL,
					`model` varchar(64) NOT NULL,
					`sku` varchar(64) NOT NULL,
					FOREIGN KEY (product_id) REFERENCES ".DB_PREFIX."product(product_id) ON DELETE CASCADE
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");
		
		$this->checkTables(true);
		
	}
	
	protected function checkTables($install = false) {
		
		$query = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."relatedoptions' ");
		
		if ( $query->num_rows ) {
			$query = $this->db->query("DESCRIBE `".DB_PREFIX."relatedoptions` 'price_prefix' ");
			if ( $query->num_rows && strtolower($query->row['Type']) == 'varchar(1)' ) {
				$this->db->query("ALTER TABLE `".DB_PREFIX."relatedoptions` MODIFY `price_prefix` varchar(2) NOT NULL");
			}
		
			if ( $this->installed() || $install ) {
				$this->simple_db->addTableColumnIfNotExists('relatedoptions_variant_product', 'allow_zero_select', 'TINYINT(1) NOT NULL');
				$this->simple_db->addTableColumnIfNotExists('relatedoptions', 'in_stock_status_id', 'INT(11) NOT NULL');
				$this->simple_db->addTableColumnIfNotExists('relatedoptions', 'disabled', 'TINYINT(1) NOT NULL');
				$this->simple_db->addTableColumnIfNotExists('relatedoptions', 'jan', 'VARCHAR(13) NOT NULL');
				$this->simple_db->addTableIndexIfNotExists('relatedoptions', 'disabled');
				
			}
			
			$this->liveopencart_ext_ro->callPRO('checkTables', [$install]);
		}
		
	}
	
	public function uninstall() {
		$this->db->query("
			DROP TABLE IF EXISTS 
				`" . DB_PREFIX . "relatedoptions`,
				`" . DB_PREFIX . "relatedoptions_variant`,
				`" . DB_PREFIX . "relatedoptions_variant_option`,
				`" . DB_PREFIX . "relatedoptions_variant_product`,
				`" . DB_PREFIX . "relatedoptions_discount`,
				`" . DB_PREFIX . "relatedoptions_special`,
				`" . DB_PREFIX . "relatedoptions_option`,
				`" . DB_PREFIX . "relatedoptions_search`
		;");
		
		$this->liveopencart_ext_ro->callPRO('removeTables');
		
	}
}
