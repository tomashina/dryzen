<?php
class ControllerExtensionHbseoHbSitemap extends Controller {
	protected $registry;
	private $error = array(); 
	
	public function __construct($registry) {
		$this->registry = $registry;
		$this->hb_extension_version	= '3.5.4';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/seo-xml-sitemap-generator-pro/';

		$this->load->model('extension/hbseo/hb_sitemap');		
		$this->load->language('extension/hbseo/hb_sitemap');
	}
	
	public function index() {
		$data['extension_version'] =  $this->hb_extension_version;
		
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_sitemap', $this->request->post, $this->request->get['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/hbseo/hb_sitemap', 'user_token=' . $this->session->data['user_token'].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
	
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=hbseo', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/hbseo/hb_sitemap', 'user_token=' . $this->session->data['user_token'].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link('extension/hbseo/hb_sitemap', 'user_token=' . $this->session->data['user_token'].'&store_id='.$data['store_id'], true);
		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=hbseo', true);
		
		$data['user_token'] = $this->session->data['user_token'];

		$data['doc_link']	= $this->doc_link;
		
		$store_info = $this->model_setting_setting->getSetting('hb_sitemap', $this->request->get['store_id']);

		$search = 'extension/hbseo/sitemap';
		$file = '../.htaccess';
		
		$data['htaccess_enabled'] = false;
		if (file_exists($file)) {
			$lines = file($file);
			foreach($lines as $line) {
			  if(strpos($line, $search) !== false) {
				$data['htaccess_enabled'] = true;
			  }
			}
		}else{
			$data['htaccess_enabled'] = false;
		}
		
		$data['htaccess_code'] = $this->htaccesscode();
		
		//dashboard
		$this->load->model('setting/store');
		
		$data['stores'] = $this->model_setting_store->getStores();

		if ($data['store_id'] == 0){ 
			$data['store_url'] = HTTPS_CATALOG;
			$store_folder_name = 'default';
		}else{
			$results = $this->model_setting_store->getStore($data['store_id']);
			$data['store_url'] = $results['url'];
			$store_folder_name = 'store-'.$data['store_id'];
		}
		
		$data['google_index_link'] = 'https://www.google.co.in/search?q=site%3A'.urlencode($data['store_url']);
				
		if ($data['htaccess_enabled'] ==  true) {
			$data['sitemap_index_link'] = $data['store_url']."sitemap_index.xml";
		}else{
			$data['sitemap_index_link'] = $data['store_url']."index.php?route=extension/hbseo/sitemap/index";
		}

		$data['sitemaps'] = [];

		try {
			$data['sitemaps'] = $this->model_extension_hbseo_hb_sitemap->getSitemapLinks($data['sitemap_index_link']);
		} catch (Exception $e) {
			$this->log->write('Error: ' . $e->getMessage());
		}

		$data['real_sitemap_files'] = [];
		$real_sitemap_file = '../sitemaps/'.$store_folder_name.'/sitemap_index.xml';

		if (file_exists($real_sitemap_file)) {
			$real_sitemap_metadata = ['loc' => $data['store_url'].'sitemaps/'.$store_folder_name.'/sitemap_index.xml','lastmod' => date('Y-m-d H:i:s', filemtime($real_sitemap_file))];

			$real_sitemap_links = $this->model_extension_hbseo_hb_sitemap->getSitemapLinks($real_sitemap_file);
			$data['real_sitemap_files'] = array_merge([$real_sitemap_metadata], $real_sitemap_links);
		}	

		//settings
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		foreach ($data['languages'] as $language){
			$language_id = $language['language_id'];	
			$data['hb_sitemap_caption'][$language_id] 	= isset($store_info['hb_sitemap_caption'.$language_id]) ? $store_info['hb_sitemap_caption'.$language_id] : '{p} Main Image';
			$data['hb_sitemap_title'][$language_id] 	= isset($store_info['hb_sitemap_title'.$language_id]) ? $store_info['hb_sitemap_title'.$language_id] : '{p} Image';
			$data['hb_sitemap_a_caption'][$language_id] = isset($store_info['hb_sitemap_a_caption'.$language_id]) ? $store_info['hb_sitemap_a_caption'.$language_id] : 'Showing Additional Image for {p}';
			$data['hb_sitemap_a_title'][$language_id] 	= isset($store_info['hb_sitemap_a_title'.$language_id]) ? $store_info['hb_sitemap_a_title'.$language_id] : 'More {p} Images';
		}
	
		$data['hb_sitemap_enable']             = isset($store_info['hb_sitemap_enable']) ? $store_info['hb_sitemap_enable'] : '';
		$data['hb_sitemap_beautify']           = isset($store_info['hb_sitemap_beautify']) ? $store_info['hb_sitemap_beautify'] : '';
		$data['hb_sitemap_product']            = isset($store_info['hb_sitemap_product']) ? $store_info['hb_sitemap_product'] : '';
		$data['hb_sitemap_product_tags']       = isset($store_info['hb_sitemap_product_tags']) ? $store_info['hb_sitemap_product_tags'] : '';
		$data['hb_sitemap_category']           = isset($store_info['hb_sitemap_category']) ? $store_info['hb_sitemap_category'] : '';
		$data['hb_sitemap_brand']              = isset($store_info['hb_sitemap_brand']) ? $store_info['hb_sitemap_brand'] : '';
		$data['hb_sitemap_information']        = isset($store_info['hb_sitemap_information']) ? $store_info['hb_sitemap_information'] : '';
		$data['hb_sitemap_category_to_product'] = isset($store_info['hb_sitemap_category_to_product']) ? $store_info['hb_sitemap_category_to_product'] : '';
		$data['hb_sitemap_brand_to_product']   = isset($store_info['hb_sitemap_brand_to_product']) ? $store_info['hb_sitemap_brand_to_product'] : '';
		$data['hb_sitemap_misc']               = isset($store_info['hb_sitemap_misc']) ? $store_info['hb_sitemap_misc'] : '';
		$data['hb_sitemap_journal3blog']       = isset($store_info['hb_sitemap_journal3blog']) ? $store_info['hb_sitemap_journal3blog'] : '';
		
		$data['hb_sitemap_limit']              = isset($store_info['hb_sitemap_limit']) ? $store_info['hb_sitemap_limit'] : '3000';
		$data['hb_sitemap_width']              = isset($store_info['hb_sitemap_width']) ? $store_info['hb_sitemap_width'] : '500';
		$data['hb_sitemap_height']             = isset($store_info['hb_sitemap_height']) ? $store_info['hb_sitemap_height'] : '500';

		//LINKS
		$data['freq_options'] = [
			'monthly' 	=> $this->language->get('text_freq_monthly'),
			'weekly' 	=> $this->language->get('text_freq_weekly'),
			'yearly'	=> $this->language->get('text_freq_yearly'),
			'daily' 	=> $this->language->get('text_freq_daily'),
			'hourly' 	=> $this->language->get('text_freq_hourly'),
			'always' 	=> $this->language->get('text_freq_always'),
			'never' 	=> $this->language->get('text_freq_never')
		];
		
		$data['priority_options'] = [
			'1' => $this->language->get('text_priority_100'),
			'0.9' => $this->language->get('text_priority_90'),
			'0.8' => $this->language->get('text_priority_80'),
			'0.7' => $this->language->get('text_priority_70'),
			'0.6' => $this->language->get('text_priority_60'),
			'0.5' => $this->language->get('text_priority_50'),
			'0.4' => $this->language->get('text_priority_40'),
			'0.3' => $this->language->get('text_priority_30'),
			'0.2' => $this->language->get('text_priority_20'),
			'0.1' => $this->language->get('text_priority_10')
		];
		
		$product_invalid_date = $this->model_extension_hbseo_hb_sitemap->checkInvalidDate('product');
		$category_invalid_date = $this->model_extension_hbseo_hb_sitemap->checkInvalidDate('category');

		$data['invalid_date'] = [];

		if ($product_invalid_date > 0) {
			$data['invalid_date'][] = sprintf($this->language->get('text_invalid_date_product'), $product_invalid_date);
		}
		
		if ($category_invalid_date > 0) {
			$data['invalid_date'][] = sprintf($this->language->get('text_invalid_date_category'), $category_invalid_date);
		}

		
				
		$data['header'] 		= $this->load->controller('common/header');
		$data['column_left'] 	= $this->load->controller('common/column_left');
		$data['footer'] 		= $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_sitemap', $data));
	}
	
	public function links() {  
		$store_id = (int)$this->request->get['store_id'];
		
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$search = isset($this->request->get['search']) ? $this->request->get['search'] : '';

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		if (isset($this->request->get['search'])) {
			$url .= '&search=' . $this->request->get['search'];
		}

		$data = array(
			'start' 	=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 	=> $this->config->get('config_limit_admin'),
			'search'	=> strtolower($search),
			'store_id'	=> $store_id
		);

		$data['user_token'] = $this->session->data['user_token'];	
		
		$reports_total = $this->model_extension_hbseo_hb_sitemap->getTotalLinks($data); 		
		$links = $this->model_extension_hbseo_hb_sitemap->getLinks($data);

		$data['links'] = [];

		foreach ($links as $link) {
			$data['links'][] = array(
				'id' 			=> $link['id'],
				'link' 			=> urldecode($link['link']),
				'frequency'		=> $link['freq'],
				'priority' 		=> $link['priority'],
				'date_added'	=> date($this->language->get('date_format_short'), strtotime($link['date_added']))
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/hbseo/hb_sitemap/links', 'user_token=' . $this->session->data['user_token'] . '&store_id='.$store_id.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_sitemap_links', $data));
	}	
	
	public function add_link(){
		$json = [];
		
		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}

		$data['store_id'] 	= isset($this->request->get['store_id']) ? (int)$this->request->get['store_id'] : 0;
		$data['link'] 		= isset($this->request->post['link']) ? trim($this->request->post['link']) : '';
		$data['freq'] 		= isset($this->request->post['freq']) ? $this->request->post['freq'] : 'monthly';
		$data['priority'] 	= isset($this->request->post['priority']) ? $this->request->post['priority'] : '1';

		if (!filter_var($data['link'], FILTER_VALIDATE_URL)) {
			$json['error'] = $this->language->get('error_link');
		} else {
			$scheme = parse_url($data['link'], PHP_URL_SCHEME);
			if (!in_array($scheme, ['http', 'https'])) {
				$json['error'] = $this->language->get('error_link');
			}
		}		
		
		if (!$json) {
			if ($this->model_extension_hbseo_hb_sitemap->isLinkExists($data) == false){
				$this->model_extension_hbseo_hb_sitemap->addLink($data);
				$json['success'] = $this->language->get('success_add');
			}else{
				$json['error'] = $this->language->get('error_link_exists');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}
	
	public function delete_link(){
		$json = [];

		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}
		
		if (!isset($this->request->post['selected'])){
			$json['error'] = $this->language->get('error_no_record_selected');
		}
		
		if (!$json) {
			$count = 0;
			foreach ($this->request->post['selected'] as $id) {
				$this->model_extension_hbseo_hb_sitemap->deleteLink($id);
				$count++;
			}
			$json['success'] = sprintf($this->language->get('success_delete'), $count);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function fix_dates(){
		$this->model_extension_hbseo_hb_sitemap->updateInvalidDate();
		$json['success'] = $this->language->get('success_fixed_dates');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function import_bulk() {
		$json = [];
	
		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}
	
		$store_id = $this->request->get['store_id'] ?? 0;
	
		$links = isset($this->request->post['links']) ? trim($this->request->post['links']) : '';
		$links = preg_split('/\s+/', str_replace(' ', '', $links), -1, PREG_SPLIT_NO_EMPTY);
	
		if (empty($links)) {
			$json['error'] = $this->language->get('error_no_links');
		}
	
		if (empty($json)) {
			$data = [
				'store_id' => (int)$store_id,
				'freq' => 'daily',
				'priority' => '0.8',
			];
	
			$invalid_links = [];
			foreach ($links as $link) {
				$link = trim($link);
	
				if (!filter_var($link, FILTER_VALIDATE_URL)) {
					$invalid_links[] = $link;
					continue;
				}
	
				$data['link'] = $link;
				$this->model_extension_hbseo_hb_sitemap->addLink($data);
			}
	
			if (!empty($invalid_links)) {
				$json['error'] = sprintf($this->language->get('error_invalid_links'),count($invalid_links));
			} else {
				$json['success'] = $this->language->get('success_links_added');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}	

	public function generateSitemaps() {
		$json = [];

		$store_id = $this->request->get['store_id'] ?? 0;
	
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['sitemapUrl'])) {
			try {
				$sitemapUrl = $this->request->post['sitemapUrl'];
				$this->model_extension_hbseo_hb_sitemap->generateSitemaps($sitemapUrl, $store_id);
				$json['success'] = $this->language->get('success_sitemaps_generated');
			} catch (Exception $e) {
				$json['error'] = $e->getMessage();
			}
		} else {
			$json['error'] = $this->language->get('error_invalid_request');
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function add_htaccess(){
		$json = [];

		$data['htaccess_code'] = $this->htaccesscode();
		$file = '../.htaccess';
		$backupfile = '../.htaccess.sitemap.BACKUP';
		
		if (!file_exists($file)){
			$json['error'] = '.htaccess file not found in your server';
		} 
		
		if (!$json) {
			copy($file, $backupfile);
			
			$f = fopen($file, "r+");
			
			$oldstr = file_get_contents($file);
			
			$referenceLines = [
				"RewriteRule ^system/storage/(.*) index.php?route=error/not_found [L]",
				"RewriteRule ^download/(.*) /index.php?route=error/not_found [L]",
				"RewriteRule ^system/download/(.*) index.php?route=error/not_found [L]"
			];
			
			$json['error'] = 'Reference Line not found in .htaccess file';
			while (($buffer = fgets($f)) !== false) {
				foreach ($referenceLines as $specificLine) {
					if (strpos($buffer, $specificLine) !== false) {
						$pos = ftell($f); 
						$newstr = substr_replace($oldstr, $data['htaccess_code'], $pos, 0);
						file_put_contents($file, $newstr);
						$json['success'] = 'Code Added to .htaccess file';
						break 2;
					}
				}
			}
			fclose($f); 
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}
	
	private function htaccesscode(){
		return 'RewriteRule ^sitemap_index.xml$ index.php?route=extension/hbseo/sitemap [L,QSA] 
RewriteRule ^sitemaps/([^?]*)/product_sitemap_([0-9]+).xml$ index.php?route=extension/hbseo/sitemap/product&hbxmllang=$1&page=$2 [L,QSA] 
RewriteRule ^sitemaps/([^?]*)/product_tags_sitemap_([0-9]+).xml$ index.php?route=extension/hbseo/sitemap/product_tags&hbxmllang=$1&page=$2 [L,QSA] 
RewriteRule ^sitemaps/([^?]*)/category_sitemap.xml$ index.php?route=extension/hbseo/sitemap/category&hbxmllang=$1 [L,QSA] 
RewriteRule ^sitemaps/([^?]*)/brand_sitemap.xml$ index.php?route=extension/hbseo/sitemap/brand&hbxmllang=$1 [L,QSA] 
RewriteRule ^sitemaps/([^?]*)/information_sitemap.xml$ index.php?route=extension/hbseo/sitemap/information&hbxmllang=$1 [L,QSA] 
RewriteRule ^sitemaps/([^?]*)/category_to_product_sitemap.xml$ index.php?route=extension/hbseo/sitemap/category_to_product&hbxmllang=$1 [L,QSA] 
RewriteRule ^sitemaps/([^?]*)/brand_to_product_sitemap.xml$ index.php?route=extension/hbseo/sitemap/brand_to_product&hbxmllang=$1 [L,QSA] 
RewriteRule ^sitemaps/misc_sitemap.xml$ index.php?route=extension/hbseo/sitemap/misc [L,QSA] 
RewriteRule ^sitemaps/([^?]*)/journal3blog_sitemap.xml$ index.php?route=extension/hbseo/sitemap/journal3blog&hbxmllang=$1 [L,QSA] 
';
	}		
			
	public function install(){
		$this->model_extension_hbseo_hb_sitemap->install();
	}
	
	public function uninstall(){
		$this->model_extension_hbseo_hb_sitemap->uninstall();
	}
		
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/hbseo/hb_sitemap')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}	
	
}
?>