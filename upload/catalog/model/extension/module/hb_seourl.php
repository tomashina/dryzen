<?php
class ModelExtensionModuleHbSeourl extends Model {	
	public function hreflang(string $route, array $url_parameters): array{
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

		$hreflang = [];
		$hreflang_default = '';

		if (!$this->config->get('hb_seourl_hreflang')){
		 	return $hreflang;
		}
		
		$default_language_query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `code` = 'config' AND `key` = 'config_language' AND store_id = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1");
					
		if (isset($default_language_query->row['value'])) {
			$default_language = $default_language_query->row['value'];
		}else{
			$default_language = $this->config->get('config_language');
		}

		switch ($route) {
			case 'product/product':
				if (empty($url_parameters['product_id'])) {
					break;
				}

				$product_id = (int)$url_parameters['product_id'];
				foreach($languages as $lang) {
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `query` = 'product_id=" . $product_id . "' AND language_id = '" . (int)$lang['language_id'] . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1");
					if (isset($query->row['keyword'])) {
						if ($lang['code'] != $default_language) {
							$final_keyword = substr($lang['code'],0,2).'/'.$query->row['keyword'];
						}else{
							$final_keyword = $query->row['keyword'];
						}						
					}else{
						$final_keyword = '&product_id='.$product_id;
					}

					$href = $this->config->get('config_url').$final_keyword;
					$hreflang[] = '<link rel="alternate" hreflang="'.$lang['code'].'" href="'.$href.'" />';

					if ($lang['code'] == $default_language) {
						$href = $this->config->get('config_url').$final_keyword;
						$hreflang_default = '<link rel="alternate" hreflang="x-default" href="'.$href.'" />';
					}
				}
				$hreflang[] = $hreflang_default;
			break;
			
			case 'product/category':
				if (empty($url_parameters['path'])) {
					break;
				}

				$split_path = explode('_', $url_parameters['path']);
				$catrgy_id = end($split_path);
				$catrgy_id = (int)$catrgy_id;

				foreach($languages as $lang) {
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `query` = 'category_id=" . $catrgy_id . "' AND language_id = '" . (int)$lang['language_id'] . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1");
					if (isset($query->row['keyword'])) {
						if ($lang['code'] != $default_language) {
							$final_keyword = substr($lang['code'],0,2).'/'.$query->row['keyword'];
						}else{
							$final_keyword = $query->row['keyword'];
						}
					}else{
						$final_keyword = '&path='.$url_parameters['path'];
					}

					$href = $this->config->get('config_url').$final_keyword;
					$hreflang[] = '<link rel="alternate" hreflang="'.$lang['code'].'" href="'.$href.'" />';

					if ($lang['code'] == $default_language) {
						$href = $this->config->get('config_url').$final_keyword;
						$hreflang_default = '<link rel="alternate" hreflang="x-default" href="'.$href.'" />';
					}
				}	

				$hreflang[] = $hreflang_default;
			break;

			case 'product/manufacturer/info':
				if (empty($url_parameters['manufacturer_id'])) {
					break;
				}

				$manufacturer_id = (int)$url_parameters['manufacturer_id'];
				foreach($languages as $lang) {
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `query` = 'manufacturer_id=" . $manufacturer_id . "' AND language_id = '" . (int)$lang['language_id'] . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1");
					if (isset($query->row['keyword'])) {
						if ($lang['code'] != $default_language) {
							$final_keyword = substr($lang['code'],0,2).'/'.$query->row['keyword'];
						}else{
							$final_keyword = $query->row['keyword'];
						}
					}else{
						$final_keyword = '&manufacturer_id='.$manufacturer_id;
					}

					$href = $this->config->get('config_url').$final_keyword;
	
					$hreflang[] = '<link rel="alternate" hreflang="'.$lang['code'].'" href="'.$href.'" />';

					if ($lang['code'] == $default_language) {
						$href = $this->config->get('config_url').$final_keyword;
						$hreflang_default = '<link rel="alternate" hreflang="x-default" href="'.$href.'" />';
					}
				}	

				$hreflang[] = $hreflang_default;
			break;
			
			case 'information/information':
				if (empty($url_parameters['information_id'])) {
					break;
				}

				$information_id = (int)$url_parameters['information_id'];
				foreach($languages as $lang) {
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `query` = 'information_id=" . $information_id . "' AND language_id = '" . (int)$lang['language_id'] . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1");
					if (isset($query->row['keyword'])) {
						if ($lang['code'] != $default_language) {
							$final_keyword = substr($lang['code'],0,2).'/'.$query->row['keyword'];
						}else{
							$final_keyword = $query->row['keyword'];
						}
					}else{
						$final_keyword = '&information_id='.$information_id;
					}

					$href = $this->config->get('config_url').$final_keyword;

					$hreflang[] = '<link rel="alternate" hreflang="'.$lang['code'].'" href="'.$href.'" />';

					if ($lang['code'] == $default_language) {
						$href = $this->config->get('config_url').$final_keyword;
						$hreflang_default = '<link rel="alternate" hreflang="x-default" href="'.$href.'" />';
					}
				}	

				$hreflang[] = $hreflang_default;
			break;

			case 'common/home':
				foreach($languages as $lang) {
					$home_langcode = substr($lang['code'],0,2);
					$default_language_code = substr($this->config->get('config_language'),0,2);
					if ($home_langcode == $default_language_code){
						$home_langcode = '';
					}else{
						$home_langcode = $home_langcode.'/';
					}

					$href = $this->config->get('config_url').$home_langcode;

					$hreflang[] = '<link rel="alternate" hreflang="'.$lang['code'].'" href="'.$href.'" />';

					if ($lang['code'] == $default_language) {
						$href = $this->config->get('config_url');
						$hreflang_default = '<link rel="alternate" hreflang="x-default" href="'.$href.'" />';
					}
				}	

				$hreflang[] = $hreflang_default;
			break;

			default:
				foreach($languages as $lang) {
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` where `query` = '".$this->db->escape($route)."' and language_id = '".(int)$lang['language_id']."' AND store_id = '" . (int)$this->config->get('config_store_id') . "'  LIMIT 1");
					if (isset($query->row['keyword'])) {
						if ($lang['code'] != $default_language) {
							$final_keyword = substr($lang['code'],0,2).'/'.$query->row['keyword'];
						}else{
							$final_keyword = $query->row['keyword'];
						}
					}else{
						$final_keyword = 'route='.$route;
					}

					$href = $this->config->get('config_url').$final_keyword;

					$hreflang[] = '<link rel="alternate" hreflang="'.$lang['code'].'" href="'.$href.'" />';

					if ($lang['code'] == $default_language) {
						$href = $this->config->get('config_url').$final_keyword;
						$hreflang_default = '<link rel="alternate" hreflang="x-default" href="'.$href.'" />';
					}
				}	

				$hreflang[] = $hreflang_default;
			break;
		}

		return $hreflang;
	}

	public function hreflang_home(): array{
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

		$hreflang = [];
		$hreflang_default = '';

		 if (!$this->config->get('hb_seourl_hreflang')){
		 	return $hreflang;
		}
		
		$default_language_query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `code` = 'config' AND `key` = 'config_language' AND store_id = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1");
					
		if (isset($default_language_query->row['value'])) {
			$default_language = $default_language_query->row['value'];
		}else{
			$default_language = $this->config->get('config_language');
		}
		
		foreach($languages as $lang) {
			$home_langcode = substr($lang['code'],0,2);
			$default_language_code = substr($this->config->get('config_language'),0,2);
			if ($home_langcode == $default_language_code){
				$home_langcode = '';
			}else{
				$home_langcode = $home_langcode.'/';
			}

			$href = $this->config->get('config_url').$home_langcode;

			$hreflang[] = '<link rel="alternate" hreflang="'.$lang['code'].'" href="'.$href.'" />';

			if ($lang['code'] == $default_language) {
				$href = $this->config->get('config_url');
				$hreflang_default = '<link rel="alternate" hreflang="x-default" href="'.$href.'" />';
			}			
		}	

		$hreflang[] = $hreflang_default;
			

		return $hreflang;
	}
}
