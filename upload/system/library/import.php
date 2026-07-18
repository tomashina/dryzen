<?php

//Copyright (c) 2019 Dalibor. All Rights Reserved

class Import {

	private $json_array = array();
	private $product_links = array();
	private $settings;

	public function __construct($registry, $settings) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->language = $registry->get('language');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->settings = $settings;
	}
	
	public function getAllData($path, $test = false){
		$fields = $this->settings['import_api_field'];
		
		$id_parts = explode('->', $path);
		
		array_shift($id_parts);
		
		foreach($fields as $field => $value){
			if(!$value) continue;
			$field_paths = $value ? html_entity_decode($value, ENT_QUOTES, 'UTF-8') : '';
			$field_parts = explode('->', $field_paths);
			
			$nested_data[$field] = $this->getFieldData($this->json_array, $field_parts, $id_parts);	
		}
		
		if($test == 'view-raw'){
			return $nested_data;
		}

		$nested_data = $this->checkCombinations($nested_data);
		
		if($test == 'view-split'){
			return $nested_data;
		}

		$original_product = $this->resolveAllNestedData($nested_data);
		
		if($test == 'view-grouping'){
			return $original_product;
		}
		
		$modified = $this->changeOriginal($original_product);
		
		return $modified;
	}
	
	public function resolveAllNestedData($nested_data) {
		if(!empty($nested_data['attribute_value'])){
			
			if(!isset($nested_data['attribute_group'])){
				$nested_data['attribute_group'] = $this->settings['import_api_attribute_group'];
			}
			
			if(!isset($nested_data['attribute_name'])){
				$nested_data['attribute_name'] = $this->settings['import_api_default_attribute'];
			}
			
			$attribute_text = $this->resolveNested($nested_data['attribute_value'], $nested_data['attribute_name']);
			$attribute_map = $this->resolveNested($nested_data['attribute_name'], $nested_data['attribute_group']);
			$nested_data['attributes'] = array();
			
			foreach($attribute_text as $text){
				foreach($attribute_map as $map){
					if($map['value'] == $text['parent']){
						$nested_data['attributes'][] = array(
							'group' => $map['parent'],
							'name' => $map['value'],
							'text' => $text['value'],
						);
						break;
					}
				}
			}
		}
		
		if(!empty($nested_data['category'])){
			
			if(!isset($nested_data['category_parent'])){
				$nested_data['category_parent'] = 0; // it is not good idea to put default top category here
			}
			
			if($this->settings['import_api_category_path'] && is_array($nested_data['category'])){
				$nested_data['category'] = implode('->', $nested_data['category']);
			}
			
			$nested_data['category_path'] = $this->resolveNested($nested_data['category'], $nested_data['category_parent']);
		}
		
		$nested_data['options'] = array();
		if(!empty($nested_data['option_value'])){
			
			if(!isset($nested_data['option'])){
				$nested_data['option'] = $this->settings['import_api_default_option'];
			}
			
			if(!isset($nested_data['option_price'])){
				$nested_data['option_price'] = 0;
			}
			
			if(!isset($nested_data['option_weight'])){
				$nested_data['option_weight'] = 0;
			}
			
			$option_map= $this->resolveNested($nested_data['option_value'], $nested_data['option']);
			$option_price_map = $this->resolveNested($nested_data['option_price'], $nested_data['option_value']);
			$option_weight_map = $this->resolveNested($nested_data['option_weight'], $nested_data['option_value']);
			foreach($option_map as $key => $map){
				
				if(is_array($nested_data['option_price'])){
					foreach($option_price_map as $price_map){
						if($map['value'] == $price_map['parent']){
							$nested_data['options'][$key] = array(
								'option' => $map['parent'],
								'option_value' => $map['value'],
								'price' => $price_map['value']
							);
							break;
						}
					}
				} else {
					$nested_data['options'][$key] = array(
						'option' => $map['parent'],
						'option_value' => $map['value'],
						'price' => $nested_data['option_price']
					);
				}
				
				if(is_array($nested_data['option_weight'])){
					foreach($option_weight_map as $weight_map){
						if($map['value'] == $weight_map['parent']){
							$nested_data['options'][$key]['weight'] = $weight_map['value'];
							break;
						}
					}
				} else {
					$nested_data['options'][$key]['weight'] = $nested_data['option_weight'];
				}
			}
		
		}
		
		return $nested_data;
	}
		
	public function getFieldData($tree, $field_parts, $id_parts){	
		foreach($field_parts as $key => $part){

			if (preg_match("/array/", $part) === 1) {
				$tree = $tree[$id_parts[$key]];
			} else {
				if($part == $id_parts[$key]){
					$tree = $tree[$part];
				} else { 
					return $this->followDifferentPath($field_parts, $tree);
				}			
			}
			array_shift($field_parts);
		}
	}
	
	public function followDifferentPath($own_parts, $tree){

		foreach($own_parts as $part){
			array_shift($own_parts);
			if (preg_match("/array/", $part) === 1 && is_array($tree)) {
				$ret = array();
				foreach($tree as $t){
					$ret[] = $this->followDifferentPath($own_parts, $t);
				}
				return $ret;
			} else {
				$tree = isset($tree[$part]) ? $tree[$part] : '';							
			}		
		}
		
		if(!is_array($tree)){
			return trim($tree);
		}
		
		return $tree;		
	}
	
	public function findUniqueProductsIdentifier($tree, $parts, $identifier, $path = ''){
		
		$tmp_parts = $parts;
		
		foreach($parts as $part){
			array_shift($tmp_parts);
			if ($part == $identifier) {
				$path .= '->'. $tree[$part];
				$this->product_links[] = $path;
				
			} elseif (preg_match("/array/", $part) === 1) {
			
				foreach($tree as $key => $t){
					$local_path = $path. '->' . $key;
					$this->findUniqueProductsIdentifier($t, $tmp_parts, $identifier, $local_path);
				}
				return;
			} else {
				$tree = $tree[$part];
				$path .= '->'. $part;
			}
		}
	}
	
	public function resolveNested($child_data, $parent_data, $default_parent = 'Value'){
		
		$child_indexes = array();
		
		if(!is_array($child_data)){			
			$child_data = array($child_data);
		}
		
		$this->setIndexPath($child_data, $child_indexes);
		
		
		$parent_indexes = array();
		
		if(is_array($parent_data)){			
			$this->setIndexPath($parent_data, $parent_indexes);
		} else {
			$map = array();
			foreach($child_indexes as $child){
				
				$map[] = array(
					'value' => $child['value'],
					'parent' => $parent_data
				);
			}
			
			return $map;
		}
		
		return $this->addParent($child_indexes, $parent_indexes);
		
	}
	
	public function addParent($children, $parents){
		$map = array();
		foreach($parents as $parent){
			foreach($children as $key => $child){
				if(substr($child['key_path'], 0, strlen($parent['key_path'])) === $parent['key_path']){
					$map[] = array(
						'value' => $child['value'],
						'parent' => $parent['value']
					);
					
					unset($children[$key]);
				}
			}
		}
		
		return $map;
	}
	
	/**
	 * Takes nested array with unknown nesting levels and return one level array with indexes to values.
	 *
	 * @param array   $array  array with unknown nesting levels
	 * @param string   $key_path  string made from array keys
	 * @return array with values and string from value indexes
	*/
	
	public function setIndexPath($array, &$return_values = array(), $key_path = ''){
		foreach($array as $key => $array_part){

			if(is_array($array_part)){
				$this->setIndexPath($array_part, $return_values, $key_path . $key);
			} else {				
				$return_values[] = array(
					'key_path' => $key_path . $key,
					'value' => $array_part
				);
			}
		}		
	}
	
	public function changeOriginal($product){
			
		$modifications = $this->settings['import_api_modification'];
		
		foreach($modifications as $field => $modification){
			if(!$modification){
				continue;
			}
			
			$modification = html_entity_decode($modification, ENT_QUOTES, 'UTF-8');
			
			$modified = $this->check_string($modification, $product);
			$product[$field] = $modified;
			
			if(in_array($field, ['price', 'quantity', 'option_price','option_quantity', 'special'])){				
				$modified = $this->calculateMath($modified);
				$product[$field] = $modified;
			}
		}
		
		return $product;
	}
	
	public function checkCombinations($product){
		
		$combinations = $this->settings['import_api_combination'];
		
		foreach($combinations as $field => $combination){
			if(!$combination){
				continue;
			} else {
				$position = 'all';
				$parts = explode('##', $combination);
				if(isset($parts[1])){
					$position = $parts[1];
				}
			}
			
			$exploded = $this->explodeCombinations($product[$field], $parts[0], $position);
			$product[$field] = $exploded;
			
		}
		return $product;
	}
	
	public function explodeCombinations($value, $separator, $position = 'all'){
		if(!is_array($value)){
			$parts = explode($separator, $value);
			if($position == 'all'){
				return $parts;
			}
			
			if($position == 'last'){
				return end($parts);
			}
			
			if(isset($parts[$position-1])){
				return $parts[$position-1];
			}
			
			return $value;
		} else {
			$ret = array();
			foreach($value as $key => $v){
				$ret[] = $this->explodeCombinations($v, $separator, $position);
			}

			return $ret;		
		}	
	}
	
	public function getProductLinks(){		
		
		return $this->product_links;
	}
	
	function check_string($string, $prod){
		if($string){
			preg_match_all('/\[\[([a-z_]*\#?[^\]]*)\]\]/', $string , $match);
			if($match){
				$string = $this->replace_string($string, $match[1], $prod);
			}
		}		
		return $string;
	}
	
	function replace_string($string, $keys, $d_array){
		foreach($keys as $key){
			$exploded_key = explode('#', $key);
			$separator = false;
			if(isset($exploded_key[1])){
				$string = str_replace($key, $exploded_key[0], $string);
				$key = $exploded_key[0];
				$separator = $exploded_key[1];
			}
			if(isset($d_array[$key])){
				if(is_array($d_array[$key])){
					$string = $this->replace_all_array_strings($d_array[$key], $key, $string, $separator);
				} else {
					$string = str_replace('[['. $key .']]', $d_array[$key], $string);
				}			
			}
		}
		return $string;
	}
	
	function replace_all_array_strings($array, $key, $string, $separator = false){
		$s = array();
		foreach($array as $d_part){
			$s[] = str_replace('[['. $key .']]', $d_part, $string);
		}
		
		if($separator !== false){
			$s = implode($separator, $s);
		}
		
		return $s;
	}
	
	function calculateMath($expression){
		$expression = str_replace(',', '.', $expression);
		$expression = str_replace(' ', '', $expression);
		$expression = preg_replace('/[^0-9\.\+\-\*\/\(\)]/', '', $expression);
		
		$expression = $this->replaceMathExpession($expression, '*');
		$expression = $this->replaceMathExpession($expression, '/');
		
		$expression = $this->replaceMathExpession($expression, '+');
		$expression = $this->replaceMathExpession($expression, '-');
		return $expression;		
	}
	
	function replaceMathExpession($expression, $sign){
		$patern = '\\' . $sign;
		$expression = preg_replace_callback(
        '/[0-9]*\.?[0-9]+' . $patern . '[0-9]*\.?[0-9]+(' . $patern . '[0-9]*\.?[0-9]+)*/',
        function ($matches) use ($sign) {
            if(!empty($matches[0])){			   
			   return $this->calculate_string($matches[0], $sign);			  
		    }
        }, $expression);
		return $expression;
	}
	
	function calculate_string($string, $sign){
		$parts = explode($sign, $string);
		$final = $parts[0];
	
		for($i = 1; $i < count($parts); $i++){
			if($sign == '*'){
				$final = $final * $parts[$i];
			} elseif($sign == '/'){
				$final = $final / $parts[$i];
			} elseif($sign == '+'){
				$final = $final + $parts[$i];
			} elseif($sign == '-'){
				$final = $final - $parts[$i];
			}
		}		
		return $final;		
	}
	
	public function jsonToArray($file) {
		$external_string = file_get_contents($json_url);
		$ob = @simplexml_load_string($external_string);
		$json_string = @json_encode($ob);
		$json_array = json_decode($json_string, true);
		return $json_array;
	}

	public function setJsonArray($json_array){		
		$this->json_array = $json_array;
	}
}
