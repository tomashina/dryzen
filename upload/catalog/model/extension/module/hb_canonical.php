<?php
class ModelExtensionModuleHbCanonical extends Model {	
	public function getCategoryCanonical($category_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(c1.category_id ORDER BY level SEPARATOR '_') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.path_id = c1.category_id AND cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id GROUP BY cp.category_id) AS path FROM " . DB_PREFIX . "category c WHERE c.category_id = '" . (int)$category_id . "'");
		return $query->row;
	}
	
	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}
	
	public function getManualPath($type, $id){
		$path = false;
		if ($type == 'product') {
			$query = $this->db->query("SELECT path FROM " . DB_PREFIX . "product_canonical WHERE product_id = '".(int)$id."' LIMIT 1");
		}
		
		if ($type == 'category') {
			$query = $this->db->query("SELECT path FROM " . DB_PREFIX . "category_canonical WHERE category_id = '".(int)$id."' LIMIT 1");
		}
		
		if ($query->row) {
			$path = $query->row['path'];
		}
		
		return $path;
	}
	
	public function product_canonical($product_id) {
		$categories = array();
		$canonical_path = array();
		
		$manual_path = $this->getManualPath('product', $product_id);
		
		if ($manual_path) {
			if ($manual_path != 'E') {
				$url = $this->url->link('product/product','path='.$manual_path.'&product_id='.$product_id);
			}else{
				$url = $this->url->link('product/product','product_id='.$product_id);
			}
		}else { //auto
			$canonical_path[] = array('path_array_count' => 0, 'path' => false, 'url' => $this->url->link('product/product', 'product_id='.$product_id) );
			
			$categories = $this->getProductCategories($product_id);
	
			foreach ($categories as $category_id) {
				$canonical_paths = $this->getCategoryCanonical($category_id);
				if ($canonical_paths) {
					if ($canonical_paths['path']) {
						$paths = $canonical_paths['path'].'_'.$canonical_paths['category_id'];
						$paths_array = explode('_',$paths);
						$paths_array_count = count($paths_array);
					}else{
						$paths = $canonical_paths['category_id'];
						$paths_array = array($canonical_paths['category_id']);
						$paths_array_count = 1;
					}
					$canonical_path[] = array('path_array_count' => $paths_array_count,'path' => $paths, 'url' => $this->url->link('product/product', 'path='.$paths.'&product_id='.$product_id) );
				}
			}
			
			if (!empty($canonical_path)) {
				$array_count  = array_column($canonical_path, 'path_array_count');
				
				if ($this->config->get('hb_canonical_type') == 0) { //short URL
					array_multisort($array_count, SORT_ASC, $canonical_path);
				}else{ //longest path
					array_multisort($array_count, SORT_DESC, $canonical_path);
				}
				
				$url = $canonical_path[0]['url'];
				
				if ($this->config->get('hb_canonical_type') == 2) {  //level selection
					$level = $this->config->get('hb_canonical_level');
					$key = array_search($level, array_column($canonical_path, 'path_array_count'));
					if ($key) {
						$url = $canonical_path[$key]['url'];
					}
				} 
			}else{
				$url = $this->url->link('product/product', 'product_id='.$product_id);
			}
			
		}
		$this->document->addLink($url, 'canonical');
		 
		return $url;
	}
	
	
	public function category_canonical($category_id, $page, $limit, $total) {
		$categories = array();
		$canonical_path = array();
		
		$manual_path = $this->getManualPath('category', $category_id);
		
		if ($manual_path) {
			if ($manual_path != 'E') {
				$path = $manual_path;
			}else{
				$path = $category_id;
			}
		}else { //auto
			$canonical_path[] = array('path_array_count' => 0, 'path' => $category_id, 'url' => $this->url->link('product/category', 'path='.$category_id) );

			$canonical_paths = $this->getCategoryCanonical($category_id);
			if ($canonical_paths) {
				if ($canonical_paths['path']) {
					$paths = $canonical_paths['path'].'_'.$canonical_paths['category_id'];
					$paths_array = explode('_',$paths);
					$paths_array_count = count($paths_array);
				}else{
					$paths = $canonical_paths['category_id'];
					$paths_array = array($canonical_paths['category_id']);
					$paths_array_count = 1;
				}
				$canonical_path[] = array('path_array_count' => $paths_array_count,'path' => $paths, 'url' => $this->url->link('product/category', 'path='.$paths) );
			}

			if (!empty($canonical_path)) {
				$array_count  = array_column($canonical_path, 'path_array_count');
				
				if ($this->config->get('hb_canonical_type_c') == 0) { //short URL
					array_multisort($array_count, SORT_ASC, $canonical_path);
				}else{ //longest path
					array_multisort($array_count, SORT_DESC, $canonical_path);
				}
				
				$path = $canonical_path[0]['path'];
				//print_r($canonical_path);
				/*if ($this->config->get('hb_canonical_type') == 2) {  //level selection
					$level = $this->config->get('hb_canonical_level');
					$key = array_search($level, array_column($canonical_path, 'path_array_count'));
					if ($key) {
						$url = $canonical_path[$key]['url'];
					}
				}*/ 
			}else{
				$path = $category_id;
			}
			
		}
	
		if ($page == 1){
			$this->document->addLink($this->url->link('product/category', 'path=' . $path), 'canonical');
		}else{
			$this->document->addLink($this->url->link('product/category', 'path=' . $path . '&page=' . $page), 'canonical');
		}
		if ($limit && ceil($total / $limit) > $page) {
			$this->document->addLink($this->url->link('product/category', 'path=' . $path . '&page=' . ($page + 1)), 'next');
		}
		if ($page > 1) {
			if ($page == 2) {
				$this->document->addLink($this->url->link('product/category', 'path=' . $path), 'prev');
			}else{
				$this->document->addLink($this->url->link('product/category', 'path=' . $path . '&page=' . ($page - 1)), 'prev');
			}
		}
					
		return $path;
	}
}
?>