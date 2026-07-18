<?php
//set_time_limit(0);

class ControllerExtensionFeedImportApi extends Controller {
	private $settings = array();
	private $importer;
	private $languages;

    public function convertToOcProduct($product){

		$languages = $this->languages;

		$product_related = array();
		
		if(!empty($product['price'])){
			if(is_array($product['price'])){
				$product['price'] = current($product['price']);
			}
			
			if($this->config->get('import_api_multiplier')){
				$product['price'] *= $this->config->get('import_api_multiplier');
			}
			
			$price = $product['price'];
			
		} else {
			$price = 0;
		}
		
		if(!empty($product['quantity'])){
			if(is_array($product['quantity'])){
				$quantity = current($product['quantity']);
			} else {
				$quantity = $product['quantity'];
			}

		} else {
			$quantity = 0;
		}
		
		if(!empty($product['minimum'])){
			if(is_array($product['minimum'])){
				$minimum = current($product['minimum']);
			} else {
				$minimum= $product['minimum'];
			}

		} else {
			$minimum = 0;
		}
		
		if(!empty($product['weight'])){
			if(is_array($product['weight'])){
				$weight = current($product['weight']);
			} else {
				$weight = $product['weight'];
			}

		} else {
			$weight = 0;
		}
		
		$main_image_path = '';
		if(!empty($product['image'])){
			if(is_array($product['image'])){
				$product['image'] = current($product['image']);
			}
			
			$main_image_path = $this->getImagePath($product['image']);
		}
		
		$product_images = array();
		
		if(!empty($product['images'])){
			if(!is_array($product['images'])){
				$product['images'] = array($product['images']);
			}

			foreach($product['images'] as $image){
				$oc_path = $this->getImagePath($image);
				$product_images[] = array('image' => $oc_path, 'sort_order' => 0);
			}
		}
		
		$product_special = array();
		if(!empty($product['special'])){			
			if(is_array($product['special'])){
				$product['special'] = current($product['special']);
			}
			
			$product_special[0] = array (
				'customer_group_id' => (int)$this->config->get('config_customer_group_id'),
				'priority' => '',
				'price' => $this->config->get('import_api_multiplier') ? $this->config->get('import_api_multiplier') * $product['special'] :  $product['special'],
				'date_start' => '',
				'date_end' => '',
			);
		}
		
		if(!empty($product['name'])){			
			if(is_array($product['name'])){
				$product['name'] = current($product['name']);
			}			
			$name = $this->request->clean($product['name']);
		} else {
			$name = $this->request->clean($product['unique']);
		}
		
		if(!empty($product['location'])){			
			if(is_array($product['location'])){
				$product['location'] = current($product['location']);
			}			
			$location = $this->request->clean($product['location']);
		} else {
			$location = '';
		}

		if(!empty($product['description'])){			
			if(is_array($product['description'])){
				$product['description'] = current($product['description']);
			}			
			$description = $this->request->clean($product['description']);
		} else {
			$description = '';
		}

		$product_description = $this->generateProductDescriptions($name, $description, $languages);

		$product_attribute = array();
		
		if(!empty($product['attributes'])){
			$this->request->clean($product['attributes']);
			foreach($product['attributes'] as $attribute) {
				$product_attribute[] = $this->generateAttributeData($attribute, $languages);
			}
		}
		
		$product_option = array();
		
		if(!empty($product['options'])){
			$this->request->clean($product['options']);
			$product_option = $this->generateOptionData($product['options'], $languages, $price, $quantity, $weight);
		}
		
		$product_category = array();
		if(!empty($product['category_path'])){

			foreach($product['category_path'] as $path) {
				
				if($path['parent']){
					$path['parent'] = $this->request->clean($path['parent']);
					$parent_id = $this->model_module_oc_model->getCategoryId($path['parent'], $languages, $this->settings['top_category_id']);
				} else {
					$parent_id = $this->settings['top_category_id'];
				}
				
				if($this->settings['import_api_category_path']){
					$categories = explode('>', $path['value']);
				} else {
					$categories = array($path['value']);
				}
				
				foreach($categories as $category){
					if(!$category) continue;
					$category_name = $this->request->clean($category);
					$parent_id = $category_id = $this->model_module_oc_model->getCategoryId($category_name, $languages, $parent_id);
					$product_category[] = $category_id;
				}
			}			
		}

		$product_category = array_unique($product_category);
		
		if(!$product_category && $this->settings['top_category_id']){
			$product_category[] = $this->settings['top_category_id'];
		}
		
		if(!$product_category && $this->settings['default_category_id']){
			$product_category[] = $this->settings['default_category_id'];
		}
		
		if(empty($product['brand'])){
			$product['brand'] = $this->config->get('import_api_default_brand');
		}
		
		if(!empty($product['brand'])){
			$product['brand'] = $this->request->clean($product['brand']);
			$manufacturer_id = $this->model_module_oc_model->getManufacturerId($product['brand']);
		} else {
			$manufacturer_id = $this->settings['default_manufacturer_id'];
		}

		$oc_product = array(
			'product_description' => $product_description,
			'price' => $price,
			'tax_class_id' => $this->config->get('import_api_tax'),
			'quantity' => $quantity,
			'minimum' => $minimum,
			'subtract' => '1',
			'stock_status_id' => $this->config->get('import_api_stock_status_id'),
			'shipping' => '1',
			'date_available' => date('Y-m-d'),
			'length' => '',
			'width' => '',
			'height' => '',
			'length_class_id' => '1',
			'weight' => $weight,
			'weight_class_id' => $this->config->get('import_api_weight_class_id'),
			'status' => '1',
			'sort_order' => '1',
			'manufacturer_id' => $manufacturer_id,
			'product_category' => $product_category,
			'product_related' => $product_related,
			'product_store' => array (0 => '0'),		
			'product_attribute' => $product_attribute,		
			'product_option' => $product_option,		
			'product_special' => $product_special,		
			'image' => $main_image_path,
			'product_image' => $product_images,
			'points' => '',
			'location' => $location,
			//'keyword' => '',
		);
		
		$product_data_fields = ['model', 'sku', 'upc', 'ean', 'jan', 'isbn', 'mpn', 'location', 'points', 'minimum'];
		
		foreach($product_data_fields as $field){
			if(isset($product[$field]) && is_array($product[$field])){
				$product[$field] = current($product[$field]);
			}
			
			$oc_product[$field] = isset($product[$field]) ? $this->request->clean($product[$field]) : '';
		}
		
		return $oc_product;
	}
	
	public function generateProductDescriptions($name, $description, $languages){
		$product_description = array();
		foreach($languages as $language_id){
			$product_description[$language_id] = array (			 		
				'name' => $name,
				'description' => $description,
				'meta_title' => $name,
				'meta_description' => $name,
				'meta_keyword' => '',
				'tag' => ''
			);
		}
		
		return $product_description;
	}
	
	public function generateAttributeData($attribute, $languages){
		$attribute_id = $this->model_module_oc_model->getAttributeId($attribute['name'], $languages, $attribute['group']);
		
		foreach($languages as $language_id){
			$product_attribute_description[$language_id] = array(
				'text' => $attribute['text']
			);
		}
		
		$product_attribute = array(
		    'name' => $attribute['name'],
		    'attribute_id' => $attribute_id,
		    'product_attribute_description' => $product_attribute_description,
		);
		
		return $product_attribute;
	}
	
	public function generateOptionData($options, $languages, $price, $quantity, $weight){
		$product_option = array();
		foreach($options as $option){
			$option_id = $this->model_module_oc_model->getOptionId($option['option'], $languages);
			$option_value_id = $this->model_module_oc_model->getOptionValueId($option['option_value'], $languages, $option_id);
			
			$product_option_value = array(
				'option_value_id' => $option_value_id,
				'product_option_value_id' => '',
				'quantity' => $quantity,
				'subtract' => 1,
				'price_prefix' => '+',
				'price' =>  $this->config->get('import_api_multiplier') ?  $this->config->get('import_api_multiplier') * $option['price'] : $option['price'],
				'points_prefix' => '+',
				'points' => '',
				'weight_prefix' => '+',
				'weight' => $option['weight']
			);
			
			if(isset($product_option[$option_id])){
				$product_option[$option_id]['product_option_value'][] = $product_option_value;
			} else {
				$product_option[$option_id] = array(
					'product_option_id' => '',
					'name' => $option['option'],
					'option_id' => $option_id,
					'type' => 'select',
					'required' => 1,
					'product_option_value' => array($product_option_value)
				);
			}	
		}
		
		return $product_option;
	}
	
	public function generateAttributeGroup($value, $parent, $languages){		
		$attribute_id = $this->model_extension_module_oc_model->getAttributeId($value, $languages, $attribute_group_id);
	}
	
	public function getProductId($value, $table, $identifier = 'name'){		
		$query = $this->db->query("SELECT  product_id FROM `" . DB_PREFIX . $table . "` WHERE `". $identifier ."` = '" . $this->db->escape($value) . "'");

		if($query->num_rows){
			return $query->row['product_id'];
		} else {
			return 0;
		}	
	}
	
	public function getLanguages() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1'");
		foreach($query->rows as $row){
			$laguanges[] = $row['language_id'];
		}
		return $laguanges;
	}
	
	function URL_exists($uri){
		if(@getimagesize($uri)){
			return true;
		} else {
			return false;
		}
	}
	
	function getImagePath($url){
		$parts = parse_url($url);
		$name = basename($parts["path"]);
		$name = str_replace('%20', ' ', $name);

		if(is_file('image/api/' . $name)){
			return 'api/' . $name;
		}
		
		if(!$this->URL_exists($url)){
			return '';
		}
		
		copy($url, 'image/api/' . $name);

		return 'api/' . $name;
	}

	public function test(){
		
		$json = array();
		
		$view = isset($this->request->get['id']) ? $this->request->get['id'] : '';
	
		$product_links = $this->getProductLinks('test');
		
		foreach($product_links as $link){
			$json[] = $this->importer->getAllData($link, $view);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}
	
	public function import(){		
	    $product_links = $this->getProductLinks();

		$products_created = 0;
		$products_updated = 0;
		
		$json['notice'] = '';
		$json['total'] = count($product_links);

		foreach($product_links as $link){
			$original_product = $this->importer->getAllData($link);
			$oc_product = $this->convertToOcProduct($original_product);
			
		    $equivalent = $this->settings['unique_equivalent'];
			if($equivalent == 'name'){
				$dsc = current($oc_product['product_description']);
				$unique = $this->request->clean($dsc['name']);
			} else {
				$unique = $this->request->clean($original_product[$equivalent]);
			}

			$unique = is_array($unique) ? current($unique) : $unique;
			
			$product_id = $this->getProductId($unique, $this->settings['table_equivalent'], $equivalent);
			if($product_id){
				$this->model_module_oc_model->editProduct($product_id, $oc_product);
				$products_updated++;
			} else {
				$product_id = $this->model_module_oc_model->addProduct($oc_product);
				$products_created++;
			}

			if(isset($_SERVER["REQUEST_TIME"])){
				if(microtime(true) - $_SERVER["REQUEST_TIME"] > ini_get('max_execution_time') - 10){
					$json['notice'] = 'time_out';
					break;
				}
			}
		    //$product_related[] = $product_id;		
		}
		
		$this->cache->delete('product');
		$this->cache->delete('manufacturer');		
		$this->cache->delete('category');
		
		if(isset($this->request->get['from']) && $this->request->get['from'] == 'admin'){
			$json['products_created'] = $products_created;
			$json['products_updated'] = $products_updated;
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));	
		}
	
	}
	
	function getProductLinks($action = 'import'){
		
		$begin_character = 'FEED';
		
		$settings = $this->getModuleSettings();
		$settings['import_api_link'] = html_entity_decode($settings['import_api_link'], ENT_QUOTES, "UTF-8");
		$external_string = file_get_contents($settings['import_api_link']);	
		$ob = simplexml_load_string($external_string, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE);
		
		//$ob = @simplexml_load_string($external_string);
		$json_string = @json_encode($ob);

		$json_array[$begin_character] = json_decode($json_string, true);

		$parts = explode('->', $settings['unique_field']);
		
		$identifier_field = $parts[count($parts) - 1];
		
	
		$importer = new Import($this->registry, $settings);
	  
		$importer->setJsonArray($json_array);
		$importer->findUniqueProductsIdentifier($json_array, $parts, $identifier_field);
		
		
		$product_links = $importer->getProductLinks();
		
		$this->importer = $importer;
		
		$start = $limit = 0;
		
		if($action == 'test'){
			$start = $settings['start'];
			$limit = 20;
		}
		
		if(isset($this->request->get['start'])){
			$start = $this->request->get['start'];
		}
		
		if(isset($this->request->get['limit'])){
			$limit = $this->request->get['limit'];
		}
		
		if($start && !$limit){
			$limit = count($product_links) - $start;
		}
		
		if($limit){
			$product_links = array_slice($product_links, $start, $limit);
		}
		
		return $product_links;
	}
	
	function getModuleSettings(){
		
		$this->load->model('catalog/product');
		$this->load->model('module/oc_model');
		
		$languages = $this->languages = $this->getLanguages();
		
		$settings_fields = array('link', 'attribute_group', 'default_brand', 'default_category', 'top_category', 'weight_class_id', 'stock_status_id', 'tax', 'default_option', 'category_path', 'multiplier');
		
		if(isset($this->request->post['import_api_field'])){
			
			$settings = array(
				'import_api_field' => $this->request->post['import_api_field'],
				'import_api_modification' =>  $this->request->post['import_api_modification'],
				'import_api_combination' =>  $this->request->post['import_api_combination'],
			);
			
			foreach($settings_fields as $field){
				$settings['import_api_'. $field] = $this->request->post['import_api_'. $field];
			}
			
		} else {
			
			$settings = array(
				'import_api_field' => $this->config->get('import_api_field'),
				'import_api_modification' =>  $this->config->get('import_api_modification'),
				'import_api_combination' =>  $this->config->get('import_api_combination'),
			);
			
			foreach($settings_fields as $field){
				$settings['import_api_'. $field] = $this->config->get('import_api_'. $field);
			}			
		}
		
		if(!empty($settings['import_api_field']['unique'])){
			$settings['unique_field'] = html_entity_decode($settings['import_api_field']['unique'], ENT_QUOTES, 'UTF-8');
		} else {
			exit('you need to set unique field');
		}
		
		if (isset($this->request->post['import_api_start_index'])){
			$settings['start'] = $this->request->post['import_api_start_index'];
		} elseif ($this->config->get('import_api_start_index')){
			$settings['start'] = $this->config->get('import_api_start_index');
		} else {
			$settings['start'] = 0;
		}
		
		if($settings['import_api_top_category']){
			$settings['top_category_id'] = $this->model_module_oc_model->getCategoryId($settings['import_api_top_category'], $languages, 0);
		} else {
			$settings['top_category_id'] = 0;
		}
		
		if($settings['import_api_default_category']){
			$settings['default_category_id'] = $this->model_module_oc_model->getCategoryId($this->config->get('import_api_default_category'), $languages, 0);
		} else {
			$settings['default_category_id'] = 0;
		}
		
		if($settings['import_api_default_brand']){
			$settings['default_manufacturer_id'] = $this->model_module_oc_model->getManufacturerId($this->config->get('import_api_default_brand'));
		} else {
			$settings['default_manufacturer_id'] = 0;
		}
		
		$possible_eq = ['model', 'sku', 'ean', 'mpn', 'upc', 'jan', 'isbn', 'location'];
		
		$settings['unique_equivalent'] = 'name';
		$settings['table_equivalent'] = 'product_description';
		
		foreach($possible_eq as $f){
			if(!empty($settings['import_api_field'][$f]) && $settings['import_api_field'][$f] == $settings['import_api_field']['unique']){
				$settings['unique_equivalent'] = $f;
				$settings['table_equivalent'] = 'product';
				break;
			}
		}

		$this->settings = $settings;
		
		return $settings;		
	}
	
	public function printSettings(){
		$settings = $this->getModuleSettings();
		var_dump($settings);
	}
	
	public function product_links(){
		$product_links = $this->getProductLinks();
		
		foreach($product_links as $key => $link){
			echo $key . ', Unique: ' . $link. '</br>';
			/*$original_product = $this->importer->getAllData($link);
			//$oc_product = $this->convertToOcProduct($original_product);
			$unique = $this->request->clean($original_product['unique']);
		    $equivalent = $this->request->clean($this->settings['unique_equivalent']);
			if($this->getProductId($unique, $this->settings['table_equivalent'], $equivalent)){
				echo '<p style="color:red">Exists: Yes<p>';
			} else {
				echo '<p style="color:blue">Exists: No<p>';
			}
			*/
		}
	}
}