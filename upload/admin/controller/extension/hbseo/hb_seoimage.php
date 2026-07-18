<?php
if (!defined('HTTPS_CATALOG')){
	define('HTTPS_CATALOG', HTTP_CATALOG);
}
class ControllerExtensionHbseoHbSeoimage extends Controller {
	protected $registry;
	private $error = array(); 
	
	public function __construct($registry) {
		$this->registry = $registry;
		if (version_compare(VERSION,'3.0.0.0','>=' )) {
			$this->hb_template_folder 		= 'oc3';
			$this->hb_extension_base 		= 'marketplace/extension';
			$this->hb_token_name 			= 'user_token';
			$this->hb_template_extension 	= '';
			$this->hb_extension_route 		= 'extension/hbseo';
		}else if (version_compare(VERSION,'2.2.0.0','<=' )) {
			$this->hb_template_folder 		= 'oc2';
			$this->hb_extension_base 		= 'extension/hbseo';
			$this->hb_token_name 			= 'token';
			$this->hb_template_extension 	= '.tpl';
			$this->hb_extension_route 		= 'hbseo';
		}else{
			$this->hb_template_folder 		= 'oc2';
			$this->hb_extension_base 		= 'extension/extension';
			$this->hb_token_name 			= 'token';
			$this->hb_template_extension 	= '';
			$this->hb_extension_route 		= 'extension/hbseo';
		}
		
		$this->hb_extension_version	= '9.0.7';
		
		if (!isset($_SESSION))  { 
			session_start(); 
		} 
		$_SESSION["hbfm_access_key"]  	= $this->session->data[$this->hb_token_name];
		$_SESSION["hbfm_store_url"]		= HTTPS_CATALOG;
	}
	
	public function index() {   
		$data['extension_version'] =  $this->hb_extension_version;
		
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		$this->load->language($this->hb_extension_route.'/hb_seoimage');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/hbseo/hb_seoimage');
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_seoimage', $this->request->post, $this->request->get['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_seoimage', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		
		$text_strings = array(
				'heading_title','text_extension',
				'tab_dashboard','tab_setting','tab_target_directory','tab_logs',
				'text_loading','button_save','button_cancel'
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}
	
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->hb_extension_route.'/hb_seoimage', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_seoimage', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		$store_info = $this->model_setting_setting->getSetting('hb_seoimage', $this->request->get['store_id']);
		
		//settings
		$data['dir_image'] = DIR_IMAGE ;
		
		$data['hb_seoimage_status'] 			= isset($store_info['hb_seoimage_status'])?$store_info['hb_seoimage_status']:'';
		$data['hb_seoimage_language'] 			= isset($store_info['hb_seoimage_language'])?$store_info['hb_seoimage_language']:'';
		$data['hb_seoimage_pattern_p'] 			= isset($store_info['hb_seoimage_pattern_p'])?$store_info['hb_seoimage_pattern_p']:'{p}-{m}';
		$data['hb_seoimage_pattern_pa'] 			= isset($store_info['hb_seoimage_pattern_pa'])?$store_info['hb_seoimage_pattern_pa']:'{p}-additional-image';
		$data['hb_seoimage_pattern_po'] 			= isset($store_info['hb_seoimage_pattern_po'])?$store_info['hb_seoimage_pattern_po']:'option-{name}';
		
		$data['hb_seoimage_tgf_product'] 		= isset($store_info['hb_seoimage_tgf_product'])?$store_info['hb_seoimage_tgf_product']:'catalog/products/';
		$data['hb_seoimage_tgf_option'] 		= isset($store_info['hb_seoimage_tgf_option'])?$store_info['hb_seoimage_tgf_option']:'catalog/options/';
		$data['hb_seoimage_tgf_category'] 		= isset($store_info['hb_seoimage_tgf_category'])?$store_info['hb_seoimage_tgf_category']:'catalog/categories/';
		$data['hb_seoimage_tgf_brand']			= isset($store_info['hb_seoimage_tgf_brand'])?$store_info['hb_seoimage_tgf_brand']:'catalog/brands/';
		$data['hb_seoimage_tgf_unassigned'] 	= isset($store_info['hb_seoimage_tgf_unassigned'])?$store_info['hb_seoimage_tgf_unassigned']:'others/';
		
		$data['hb_seoimage_jpg_convert'] 		= isset($store_info['hb_seoimage_jpg_convert'])?$store_info['hb_seoimage_jpg_convert']:'';
		$data['hb_seoimage_delete_original'] 				= isset($store_info['hb_seoimage_delete_original'])?$store_info['hb_seoimage_delete_original']:'';
		$data['hb_seoimage_auto'] 				= isset($store_info['hb_seoimage_auto'])?$store_info['hb_seoimage_auto']:'';

		//Target Directory
		$directory = DIR_IMAGE.$data['hb_seoimage_tgf_product'];
		$files = glob($directory . "*");
		$data['directories'] = array();
		foreach($files as $file) {
			 if(is_dir($file))
			 {
				$data['directories'][] = array(
					'directory_name' => $file 
					);
			 }
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seoimage'.$this->hb_template_extension, $data));

	}
	
	public function logs() {  
		$store_id = (int)$this->request->get['store_id'];		
		$this->load->model('extension/hbseo/hb_seoimage');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'store_id'=> $store_id
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total = $this->model_extension_hbseo_hb_seoimage->getTotalrecords($data); 		
		$records = $this->model_extension_hbseo_hb_seoimage->getrecords($data);
		$data['records'] = array();
		foreach ($records as $record) {
			$data['records'][] = array(
				'id' 			=> $record['id'],
				'old_path' 		=> $record['old_path'],
				'new_path' 		=> $record['new_path'],
				'status'	 	=> $this->model_extension_hbseo_hb_seoimage->status_text($record['status']),
				'date_added' 	=> $record['date_added']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_seoimage/logs', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&store_id='.$store_id.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seoimage_records'.$this->hb_template_extension, $data));
	}
	
	public function missing() {  
		$store_id = (int)$this->request->get['store_id'];		
		$this->load->model('extension/hbseo/hb_seoimage');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data = array(
			'start' 	=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 	=> $this->config->get('config_limit_admin'),
			'store_id'	=> $store_id,
			'missing'	=> true
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total 	= $this->model_extension_hbseo_hb_seoimage->getTotalrecords($data); 		
		$records 		= $this->model_extension_hbseo_hb_seoimage->getrecords($data);
		$data['records'] = array();
		foreach ($records as $record) {
			$data['records'][] = array(
				'id' 			=> $record['id'],
				'old_path' 		=> $record['old_path'],
				'new_path' 		=> $record['new_path'],
				'status'	 	=> $this->model_extension_hbseo_hb_seoimage->status_text($record['status']),
				'date_added' 	=> $record['date_added']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_seoimage/missing', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&store_id='.$store_id.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seoimage_records'.$this->hb_template_extension, $data));
	}
	
	public function clear_logs() {
		$this->db->query("TRUNCATE TABLE `".DB_PREFIX."image_rename_logs`");
		$json['success'] = 'Logs Cleared!';
		$this->response->setOutput(json_encode($json));
	}

	public function dashboard() { 
		$store_id = (int)$this->request->get['store_id'];
		$this->load->model('extension/hbseo/hb_seoimage');
		$this->load->model('setting/setting');
		
	    $data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;
		$data['store_id'] 	= $store_id;
		
		$store_info = $this->model_setting_setting->getSetting('hb_seoimage', $this->request->get['store_id']);
		
		$product_target_folder 				= isset($store_info['hb_seoimage_tgf_product'])?$store_info['hb_seoimage_tgf_product']:'catalog/products/';
		$option_target_folder 				= isset($store_info['hb_seoimage_tgf_option'])?$store_info['hb_seoimage_tgf_option']:'catalog/options/';
		$category_target_folder 			= isset($store_info['hb_seoimage_tgf_category'])?$store_info['hb_seoimage_tgf_category']:'catalog/categories/';
		$brand_target_folder 				= isset($store_info['hb_seoimage_tgf_brand'])?$store_info['hb_seoimage_tgf_brand']:'catalog/brands/';
		
		$data['hb_seoimage_status'] 				= isset($store_info['hb_seoimage_status'])? true:false;
		
		$target_folders = array($product_target_folder,$option_target_folder,$category_target_folder,$brand_target_folder);
		
		foreach ($target_folders as $val) {
			$query_parts[] = "'%".$this->db->escape($val)."%'";
		}
		
		$target_folders = implode(' AND image NOT LIKE ', $query_parts);
		
		$data['unorg_products_main_total'] 		= $this->model_extension_hbseo_hb_seoimage->getTotalUnorganizedImage('product',$target_folders);
		$data['unorg_products_additional_total']= $this->model_extension_hbseo_hb_seoimage->getTotalUnorganizedImage('product_image',$target_folders);
		$data['unorg_category_total'] 			= $this->model_extension_hbseo_hb_seoimage->getTotalUnorganizedImage('category',$target_folders);
		$data['unorg_brand_total'] 				= $this->model_extension_hbseo_hb_seoimage->getTotalUnorganizedImage('manufacturer',$target_folders);
		$data['unorg_option_total'] 			= $this->model_extension_hbseo_hb_seoimage->getTotalUnorganizedImage('option_value',$target_folders);
		
		$data['org_products_main_total'] 		= $this->model_extension_hbseo_hb_seoimage->getTotalOrganizedImage('product',$product_target_folder);
		$data['org_products_additional_total'] 	= $this->model_extension_hbseo_hb_seoimage->getTotalOrganizedImage('product_image',$product_target_folder);
		$data['org_category_total'] 			= $this->model_extension_hbseo_hb_seoimage->getTotalOrganizedImage('category',$category_target_folder);
		$data['org_brand_total'] 				= $this->model_extension_hbseo_hb_seoimage->getTotalOrganizedImage('manufacturer',$brand_target_folder);
		$data['org_option_total'] 				= $this->model_extension_hbseo_hb_seoimage->getTotalOrganizedImage('option_value',$option_target_folder);
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seoimage_dashboard'.$this->hb_template_extension, $data));
		
	}
	
	public function generate() {  
		$store_id = (isset($this->request->get['store_id'])) ?  (int)$this->request->get['store_id'] : 0;
		
		$prev_product_total = (isset($this->request->get['prev_product_total'])) ?  (int)$this->request->get['prev_product_total'] : 0;
		
		$limit_start = 0;
		$limit_count = 10;
						
		$this->load->model('extension/hbseo/hb_seoimage');
		$this->load->model('setting/setting');
		
		$store_info 		= $this->model_setting_setting->getSetting('hb_seoimage', $store_id);
		$target_folder 		= isset($store_info['hb_seoimage_tgf_product'])? $this->sanitizepath($store_info['hb_seoimage_tgf_product']) :'catalog/products/';
		
		$products_total 	= $this->model_extension_hbseo_hb_seoimage->getTotalProductsImage($target_folder);
		$products 			= $this->model_extension_hbseo_hb_seoimage->getProductsImage($target_folder,$limit_start,$limit_count);
		
		if ($products_total > 0){					
			if ($products_total == $prev_product_total) {
				$json['error']['products'] = 'Operation Stopped. Refer to your error log.';
			}else{
				if ($products_total > $limit_count) {
					$json['next'] = str_replace('&amp;', '&', $this->url->link($this->hb_extension_route.'/hb_seoimage/generate', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='. $store_id.'&prev_product_total='.$products_total, true));
					$json['success'] = $products_total.' product yet to be processed for Image Renaming Process';
				} else {
					$json['next'] = '';
					$json['success'] = 'Completed: Product Images copied to target folder';
				}
				
				foreach ($products as $product) {
					$this->renameproductimage($product['product_id']);
				}
			}
		}else {
			$json['error']['products'] = 'All Images are already within the target folder.';
		}
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function renameproductimage($product_id = 0){
		if (isset($this->request->get['store_id'])){
			$store_id = (int)$this->request->get['store_id'];
		}else{
			$store_id = 0;
		}

		if (isset($this->request->get['product_id'])){
			$product_id = (int)$this->request->get['product_id'];
		}
		
		$this->load->model('extension/hbseo/hb_seoimage');
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('hb_seoimage', $store_id);
		
		$language_id 			= (int)$store_info['hb_seoimage_language'];
		$target_folder 			= isset($store_info['hb_seoimage_tgf_product'])?$store_info['hb_seoimage_tgf_product']:'catalog/products/';
		$jpg_convert 			= isset($store_info['hb_seoimage_jpg_convert'])?$store_info['hb_seoimage_jpg_convert']:false;
		$unassigned_folder 		= isset($store_info['hb_seoimage_tgf_unassigned'])?$store_info['hb_seoimage_tgf_unassigned']:'others';
		
		//GET PRODUCT DETAILS
		$product_info 	= $this->db->query("SELECT a.image, b.name, a.model, a.sku, a.upc FROM  `" . DB_PREFIX . "product` a, `" . DB_PREFIX . "product_description` b WHERE a.product_id = b.product_id AND b.language_id = '".(int)$language_id."' AND a.product_id = '".(int)$product_id."' ORDER BY RAND()");
		$product_name 	= $product_info->row['name'];
		$product_image 	= $product_info->row['image'];
		$product_model 	= $product_info->row['model'];
		$product_sku 	= $product_info->row['sku'];
		$product_upc 	= $product_info->row['upc'];
		
		$product_image = mb_convert_encoding($product_image, 'ISO-8859-1', 'UTF-8');
		
		$category = $this->model_extension_hbseo_hb_seoimage->getCategoriesName($product_id,$language_id);
		
		$category_foldername 	= ($category == '') ? $unassigned_folder : $category ;
		//$category_foldername 	= $this->model_extension_hbseo_hb_seoimage->urlslug($category_foldername);
		$target_folder			= $target_folder.$category_foldername;
		
		$pattern_p = isset($store_info['hb_seoimage_pattern_p'])?$store_info['hb_seoimage_pattern_p']:'{p}-{c}-{m}-{upc}-{sku}';
		$pattern_pa = isset($store_info['hb_seoimage_pattern_pa'])?$store_info['hb_seoimage_pattern_pa']:'{p}-{c}-{m}-{upc}-{sku}-additional-image';
		
		$pattern_p = str_replace('{product_id}',$product_id,$pattern_p);
		$pattern_p = str_replace('{p}',$product_name,$pattern_p);
		$pattern_p = str_replace('{m}',$product_model,$pattern_p);
		$pattern_p = str_replace('{sku}',$product_sku,$pattern_p);
		$pattern_p = str_replace('{upc}',$product_upc,$pattern_p);
		$pattern_p = str_replace('{c}',$category,$pattern_p);
	
		$options = array();
		$options = array(
			'type'				=>	'product',
			'store_id'			=>	$store_id,
			'id'				=>	$product_id,
			'image' 			=> 	$product_image,
			'name'				=>  $pattern_p,
			'jpg_convert'		=>	$jpg_convert,
			'target_folder'		=>  $target_folder
		);
		$this->model_extension_hbseo_hb_seoimage->coreFunction($options);
		
		//ADDITIONAL IMAGE
		$options = array();
		$product_additional_images = $this->db->query("SELECT * FROM  `" . DB_PREFIX . "product_image` WHERE product_id = '".(int)$product_id."'");
		if ($product_additional_images->num_rows > 0){
			$pattern_pa = str_replace('{product_id}',$product_id,$pattern_pa);
			$pattern_pa = str_replace('{p}',$product_name,$pattern_pa);
			$pattern_pa = str_replace('{m}',$product_model,$pattern_pa);
			$pattern_pa = str_replace('{sku}',$product_sku,$pattern_pa);
			$pattern_pa = str_replace('{upc}',$product_upc,$pattern_pa);
			$pattern_pa = str_replace('{c}',$category,$pattern_pa);
			
			$product_additional_images = $product_additional_images->rows;
			foreach ($product_additional_images as $image) {
				$product_image = $image['image'];
				$product_image = mb_convert_encoding($product_image, 'ISO-8859-1', 'UTF-8');
				$product_image_id = $image['product_image_id'];
				$options = array(
					'type'				=>	'product_image',
					'store_id'			=>	$store_id,
					'id'				=>	$product_image_id,
					'image' 			=> 	$product_image,
					'name'				=>  $pattern_pa.'-'.$product_image_id,
					'jpg_convert'		=>	$jpg_convert,
					'target_folder'		=>  $target_folder
				);
				$this->model_extension_hbseo_hb_seoimage->coreFunction($options);
			}
		}		
	}
	
	public function renameoptionimages(){
		$store_id = (isset($this->request->get['store_id'])) ?  (int)$this->request->get['store_id'] : 0;
		
		$this->load->model('extension/hbseo/hb_seoimage');
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('hb_seoimage', $store_id);
		
		$language_id 	= (int)$store_info['hb_seoimage_language'];
		$target_folder 	= isset($store_info['hb_seoimage_tgf_option'])? $store_info['hb_seoimage_tgf_option']:'catalog/options/';
		$pattern_po 	= isset($store_info['hb_seoimage_pattern_po'])?$store_info['hb_seoimage_pattern_po']:'option-{name}';
		$jpg_convert 	= isset($store_info['hb_seoimage_jpg_convert'])? $store_info['hb_seoimage_jpg_convert']:false;
		//GET OPTIONS DETAILS
		$options = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option_value` a, " . DB_PREFIX . "option_value_description b WHERE a.option_value_id = b.option_value_id AND b.language_id = '".(int)$language_id."' AND a.image NOT LIKE '%".$this->db->escape($target_folder)."%' AND trim(a.image) <> '' ORDER BY RAND()");
		if ($options->rows){
			foreach ($options->rows as $info) {
				$id 	= $info['option_value_id'];
				$name 	= $info['name'];
				$image 	= trim($info['image']);
				$image = mb_convert_encoding($image, 'ISO-8859-1', 'UTF-8');
				
				$pattern_po = str_replace('{name}',$name,$pattern_po);
				
				$options = array(
					'type'				=>	'option_value',
					'store_id'			=>	$store_id,
					'id'				=>	$id,
					'image' 			=> 	$image,
					'name'				=>  $pattern_po.'-'.$id,
					'jpg_convert'		=>	$jpg_convert,
					'target_folder'		=>  $target_folder
				);
				$this->model_extension_hbseo_hb_seoimage->coreFunction($options);
			}
			$json['success'] = 'Option Images copied to target folder';
		} else {
			$json['success'] = 'No pending image found. Images may have been already copied to target folder!';
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function renamecategoryimages(){
		$store_id = (isset($this->request->get['store_id'])) ?  (int)$this->request->get['store_id'] : 0;
				
		$this->load->model('extension/hbseo/hb_seoimage');
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('hb_seoimage', $store_id);
		
		$language_id 	= $store_info['hb_seoimage_language'];
		$target_folder 	= isset($store_info['hb_seoimage_tgf_category'])?$store_info['hb_seoimage_tgf_category']:'catalog/categories/';
		$jpg_convert 	= isset($store_info['hb_seoimage_jpg_convert'])?$store_info['hb_seoimage_jpg_convert']:false;
		//GET CATEGORY DETAILS
		$categories = $this->db->query("SELECT a.category_id, a.image, b.name FROM  `" . DB_PREFIX . "category` a, `" . DB_PREFIX . "category_description` b WHERE a.category_id = b.category_id AND b.language_id = '".(int)$language_id."' AND a.image NOT LIKE '%".$this->db->escape($target_folder)."%' AND trim(a.image) <> '' ORDER BY RAND()");
		if ($categories->rows){
			foreach ($categories->rows as $info) {
				$id 	= $info['category_id'];
				$name 	= $info['name'];
				$image 	= trim($info['image']);
				$image = mb_convert_encoding($image, 'ISO-8859-1', 'UTF-8');
				$options = array(
					'type'				=>	'category',
					'store_id'			=>	$store_id,
					'id'				=>	$id,
					'image' 			=> 	$image,
					'name'				=>  $name,
					'jpg_convert'		=>	$jpg_convert,
					'target_folder'		=>  $target_folder
				);
				$this->model_extension_hbseo_hb_seoimage->coreFunction($options);
			}
			$json['success'] = 'Category Images copied to target folder';
		} else {
			$json['success'] = 'Images already copied to target folder';
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	
	
	public function renamebrandimages(){
		if (isset($this->request->get['store_id'])){
			$store_id = (int)$this->request->get['store_id'];
		}else{
			$store_id = 0;
		}
		
		$this->load->model('extension/hbseo/hb_seoimage');
		$this->load->model('setting/setting');
		
		$store_info = $this->model_setting_setting->getSetting('hb_seoimage', $store_id);
		
		$language_id 		= $store_info['hb_seoimage_language'];
		$target_folder 		= isset($store_info['hb_seoimage_tgf_brand'])?$store_info['hb_seoimage_tgf_brand']:'catalog/brands/';
		$jpg_convert 		= isset($store_info['hb_seoimage_jpg_convert'])?$store_info['hb_seoimage_jpg_convert']:false;

		$brands = $this->db->query("SELECT manufacturer_id, image, name FROM  `" . DB_PREFIX . "manufacturer` WHERE image NOT LIKE '%".$this->db->escape($target_folder)."%' AND trim(image) <> '' ORDER BY RAND()");
		if ($brands->rows){
			foreach ($brands->rows as $info) {
				$id 	= $info['manufacturer_id'];
				$name 	= $info['name'];
				$image 	= trim($info['image']);
				$image = mb_convert_encoding($image, 'ISO-8859-1', 'UTF-8');
				$options = array(
					'type'				=>	'manufacturer',
					'store_id'			=>	$store_id,
					'id'				=>	$id,
					'image' 			=> 	$image,
					'name'				=>  $name,
					'jpg_convert'		=>	$jpg_convert,
					'target_folder'		=>  $target_folder
				);
				$this->model_extension_hbseo_hb_seoimage->coreFunction($options);
			}
			$json['success'] = 'Brand Images copied to target folder';
		} else {
			$json['success'] = 'Images already copied to target folder';
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function image_list() { 
		if (isset($this->request->get['store_id'])){
			$store_id = (int)$this->request->get['store_id'];
		}else{
			$store_id = 0;
		}

		$this->load->model('extension/hbseo/hb_seoimage');
		$this->load->model('setting/setting');
		
		$store_info = $this->model_setting_setting->getSetting('hb_seoimage', $store_id);
		
		$product_target_folder 				= isset($store_info['hb_seoimage_tgf_product'])?$store_info['hb_seoimage_tgf_product']:'catalog/products/';
		$option_target_folder 				= isset($store_info['hb_seoimage_tgf_option'])?$store_info['hb_seoimage_tgf_option']:'catalog/options/';
		$category_target_folder 			= isset($store_info['hb_seoimage_tgf_category'])?$store_info['hb_seoimage_tgf_category']:'catalog/categories/';
		$brand_target_folder 				= isset($store_info['hb_seoimage_tgf_brand'])?$store_info['hb_seoimage_tgf_brand']:'catalog/brands/';
		
		$target_folders = array($product_target_folder,$option_target_folder,$category_target_folder,$brand_target_folder);
		
		foreach ($target_folders as $val) {
			$query_parts[] = "'%".$this->db->escape($val)."%'";
		}
		
		$target_folders = implode(' AND image NOT LIKE ', $query_parts);
		
		$table = $this->request->get['table'];
		$type = $this->request->get['type'];

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		

		
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;
		
		if ($type == 'o') {
			if ($table == 'product') {
				$target_folder = $product_target_folder; 
			}elseif ($table == 'product_image') {
				$target_folder = $product_target_folder; 
			}elseif ($table == 'option_value') {
				$target_folder = $option_target_folder; 
			}elseif ($table == 'category') {
				$target_folder = $category_target_folder; 
			}elseif ($table == 'manufacturer') {
				$target_folder = $brand_target_folder; 
			}
			
			$data = array(
				'start' 	=> ($page - 1) * 20,
				'limit' 	=> 20,
				'table'		=> $table,
				'target_folder'	=> $target_folder
			);
			
			$reports_total = $this->model_extension_hbseo_hb_seoimage->getTotalOrgList($data); 		
			$records = $this->model_extension_hbseo_hb_seoimage->getOrgList($data);
		}else{
			$data = array(
				'start' 	=> ($page - 1) * 20,
				'limit' 	=> 20,
				'table'		=> $table,
				'folders'	=> $target_folders
			);
		
			$reports_total = $this->model_extension_hbseo_hb_seoimage->getTotalDisputes($data); 		
			$records = $this->model_extension_hbseo_hb_seoimage->getDisputes($data);
		}
		
		$data['records'] = array();
		
		foreach ($records as $record) {
			if (($table == 'product') || ($table == 'product_image')) {
				$data['records'][] = array(
					'id' 		=> $record['product_id'],
					'image' 	=> $record['image'],
					'edit'		=> $this->url->link('catalog/product/edit', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&product_id='.$record['product_id'], true)
				);
			}
			
			if ($table == 'category') {
				$data['records'][] = array(
					'id' 		=> $record['category_id'],
					'image' 	=> $record['image'],
					'edit'		=> $this->url->link('catalog/category/edit', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&category_id='.$record['category_id'], true)
				);
			}
			
			if ($table == 'manufacturer') {
				$data['records'][] = array(
					'id' 		=> $record['manufacturer_id'],
					'image' 	=> $record['image'],
					'edit'		=> $this->url->link('catalog/manufacturer/edit', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&manufacturer_id='.$record['manufacturer_id'], true)
				);
			}
			
			if ($table == 'option_value') {
				$data['records'][] = array(
					'id' 		=> $record['option_id'],
					'image' 	=> $record['image'],
					'edit'		=> $this->url->link('catalog/option/edit', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&option_id='.$record['option_id'], true)
				);
			}
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = 20;
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_seoimage/image_list', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&table='.$table.'&type='.$type.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = 20;

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seoimage_list'.$this->hb_template_extension, $data));
	}
	
	public function install() { 
		$this->load->model('extension/hbseo/hb_seoimage');
		$this->model_extension_hbseo_hb_seoimage->install();
	}
	
	public function uninstall() { 
		$this->load->model('extension/hbseo/hb_seoimage');
		$this->model_extension_hbseo_hb_seoimage->uninstall();
	}
	
	private function sanitizepath($path = "") {
		$path = rtrim($path);		// Removes white space at end of string
		$path = rtrim($path, "/");	// Removes all trailing slashes after removing white space
		$path = $path . "/";		// Adds one slash to the end of the string
		return $path;
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_seoimage')) {
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