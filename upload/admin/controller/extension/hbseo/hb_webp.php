<?php
require DIR_SYSTEM.'library/vendor/huntbee-webp/autoload.php';
use WebPConvert\WebPConvert;

class ControllerExtensionHbseoHbWebp extends Controller {	
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
		
		$this->hb_extension_version	= '3.2.0';
		$this->doc_link = 'https://www.huntbee.com/resources/docs/opencart-webp-compression/';

		$this->load->model('extension/hbseo/hb_webp');
		$this->load->language($this->hb_extension_route.'/hb_webp');
	}
	
	public function index() {   
		$data['extension_version'] = $this->hb_extension_version;;
		
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_webp', $this->request->post, $this->request->get['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_webp', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$text_strings = array(
				'heading_title','text_extension',
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
			'href'      => $this->url->link($this->hb_extension_route.'/hb_webp', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_webp', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		$data['clear']	= $this->url->link('extension/hbseo/hb_webp/clear_logs', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true);
		$data['doc_link']	= $this->doc_link;

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;
		
		$store_info = $this->model_setting_setting->getSetting('hb_webp', $data['store_id']);
		
		$data['hb_webp_cron_limit'] 	= isset($store_info['hb_webp_cron_limit']) ? $store_info['hb_webp_cron_limit'] : 10;
		$data['hb_webp_cron_key'] 		= isset($store_info['hb_webp_cron_key']) ? $store_info['hb_webp_cron_key'] : md5(rand());
		
		$data['hb_webp_cron'] = 'wget --quiet --delete-after "'.HTTPS_CATALOG.'index.php?route=extension/module/hb_webp/cron&authkey='.$data['hb_webp_cron_key'].'"';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_webp'.$this->hb_template_extension, $data));
	}
	
	public function dashboard() { 		
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;
		
		$data['files'] = array(); 
		$data['oc_cache_files'] = array(); 
		$data['gauge'] = 0;
		$image_cache_folder = DIR_IMAGE.'cache/';
		
		$data['image_cache_directory'] = false;
		
		if(is_dir($image_cache_folder)) {
			$data['image_cache_directory'] = true;
			
			$data['files'] = $this->getDirContents($image_cache_folder);
			$data['oc_cache_files'] = $this->getoccacheimages($image_cache_folder);
			
			$data['uncompress_count'] = count($data['files']);
			$data['oc_cache_count'] = count($data['oc_cache_files']);
			$data['compress_count'] = $data['oc_cache_count'] - $data['uncompress_count'];
			
			if ($data['oc_cache_count'] <> 0) {
				$data['gauge'] = ($data['uncompress_count'] / $data['oc_cache_count']) * 100;
				$data['gauge'] =  100 - (round($data['gauge']));
			}else{
				$data['gauge'] = 0;
			}
			
			$records_total = $this->model_extension_hbseo_hb_webp->getTotalrecords();			
		}
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_webp_dashboard'.$this->hb_template_extension, $data));
	}
	
	public function records(){		
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
			'limit' => $this->config->get('config_limit_admin')
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total = $this->model_extension_hbseo_hb_webp->getTotalrecords(); 		
		$records = $this->model_extension_hbseo_hb_webp->getrecords($data);
		
		$data['records'] = array();
		foreach ($records as $record) {
			$data['records'][] = array(
				'id'		=> $record['id'],
				'path' 		=> $record['path']
			);
		}
		
		$pagination 			= new Pagination();
		$pagination->total 		= $reports_total;
		$pagination->page 		= $page;
		$pagination->limit 		= $this->config->get('config_limit_admin');
		$pagination->url 		= $this->url->link($this->hb_extension_route.'/hb_webp/records', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_webp_records'.$this->hb_template_extension, $data));
	}
	
	public function getcachedimages(){
		$json = array();
		
		$this->model_extension_hbseo_hb_webp->deleteall();
		
		$image_cache_folder = DIR_IMAGE.'cache/';
		
		if(is_dir($image_cache_folder)) {
			$files = $this->getDirContents($image_cache_folder);
			
			foreach ($files as $file) {
				if (!empty($file)) {
					$this->model_extension_hbseo_hb_webp->insertpath($file);
				}
			}
			
			$json['success'] = 'Uncompressed WebP images logged to database!';
		}else{
			$json['success'] = 'No Image Cache folder found!';
		}
		
		$this->response->setOutput(json_encode($json));	
	}
	
	public function generate() {  
		error_reporting(0);
		
		if (!file_exists(DIR_IMAGE . 'webp')) {
			mkdir(DIR_IMAGE . 'webp', 0777, true);
		}
		
		
		if (isset($this->request->get['oc_count'])){
			$oc_count = (int)$this->request->get['oc_count'];
		}else{
			$oc_count = 0;
		}
		
		if (isset($this->request->get['prev_total'])){
			$prev_total = (int)$this->request->get['prev_total'];
		}else{
			$prev_total = 0;
		}
		
		$limit_start = 0;
		$limit_count = 5;
								
		$total 		= $this->model_extension_hbseo_hb_webp->getTotalrecords();
		
		$paths 		= $this->model_extension_hbseo_hb_webp->allrecords($limit_count);
		
		if ($total > 0){
			if ($total == $prev_total) {
				$json['error'] = 'Operation Stopped. Refer to your error log.';
			}else{
				if ($total > $limit_count) {
					$json['next'] 		= str_replace('&amp;', '&', $this->url->link($this->hb_extension_route.'/hb_webp/generate', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&prev_total='.$total.'&oc_count='.$oc_count, true));
					$json['success'] 	= '<i class="fa fa-spinner fa-pulse fa-fw"></i> '.$total.' Remaining';
					if ($oc_count > 0) {
						$json['percentage']	=  round((($oc_count - $total)/$oc_count)  * 100);
					}
				} else {
					$json['next'] 		= '';
					$json['success'] 	= '<i class="fa fa-check-circle"></i> Completed';
					$json['over']		= true;
				}
				
				foreach ($paths as $path) {
					$id 				= $path['id'];
					$webp_source 		= $path['path'];
					
					list($width_orig, $height_orig, $image_type) = getimagesize($webp_source);
					if (in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG))) {
						$webp_source_without_extension 		= utf8_substr($webp_source, 0, utf8_strrpos($webp_source, "."));
						$webp_source_without_extension		= str_replace('/cache/','/webp/',$webp_source_without_extension);
						$webp_source_without_extension		= str_replace('\\cache\\','\\webp\\',$webp_source_without_extension); //localhost windows testing
						$webp_destination 	= $webp_source_without_extension.'.webp';
						$options = [];
						try{
							WebPConvert::convert($webp_source, $webp_destination, $options);
							$this->model_extension_hbseo_hb_webp->deleteid($id);
						}catch (Exception $e){
							$this->log->write('Extension - Huntbee WebP Compression: Issue while processing (image) path '.$webp_source.'. Issue: '.$e->getMessage());
						}
					}
				}
			}
		}else {
			$json['success'] 	= 'All Images Compressed / No Image path found';
			$json['over']		= true;
		}
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function clearcache(){
		$image_cache_folder = DIR_IMAGE.'cache/';
		
		if(is_dir($image_cache_folder)) {
			$this->delete_files($image_cache_folder);
			$json['success'] = 'Image caches deleted successfully!';
		}else{
			$json['success'] = 'No Image Cache folder found!';
		}
		
		$this->response->setOutput(json_encode($json));	
	}
	
	public function getDirContents($dir, &$results = array()) {		
		$files = scandir($dir);
	
		foreach ($files as $key => $value) {
			$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
			if (!is_dir($path)) {
				if ((strpos($path, '.html') === false) and (strpos($path, '.gif') === false) and (strpos($path, '.webp') === false)) {
					$wp_path 		= utf8_substr($path, 0, utf8_strrpos($path, "."));
					$wp_path		= str_replace('/cache/','/webp/',$wp_path);
					$wp_path		= str_replace('\\cache\\','\\webp\\',$wp_path); //localhost windows testing
					if (!file_exists($wp_path.'.webp') and strpos($wp_path, '.webp') === false) {
						$results[] = $path;
					}
				}
			} else if ($value != "." && $value != "..") {
				$this->getDirContents($path, $results );
				//$results[] = $path; //stores the directory name
			}
		}
	
		return $results;
	}
	
	public function getoccacheimages($dir, &$results = array()) {
		$files = scandir($dir);
	
		foreach ($files as $key => $value) {
			$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
			if (!is_dir($path)) {
				if (strpos($path, '.html') === false) {
					if (strpos($path, '.webp') === false) {
						$results[] = $path;
					}
				}
			} else if ($value != "." && $value != "..") {
				$this->getoccacheimages($path, $results);
				//$results[] = $path; //stores the directory name
			}
		}
	
		return $results;
	}
		
	private function delete_files($target) {
		if(is_dir($target)){
			$files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
	
			foreach($files as $file){
				$this->delete_files( $file );      
			}
	
			rmdir($target);
		} elseif(is_file($target)) {
			unlink($target);  
		}
	}
	
	public function logs(){
		if (!file_exists(DIR_LOGS)) {
			mkdir(DIR_LOGS, 0777, true);
		}

		$file = DIR_LOGS . 'hb_webp.txt';
		if (file_exists($file)) {
			$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		}else{
			$data['log'] = '';
		}

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_webp_logs', $data));
	}

	public function clear_logs() {
		if (!$this->validate()) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = DIR_LOGS . 'hb_webp.txt';

			$handle = fopen($file, 'w+');

			fclose($handle);

			$this->session->data['success'] =  $this->language->get('text_success_logs');
		}

		$this->response->redirect($this->url->link('extension/hbseo/hb_webp', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function install() {	
		$this->model_extension_hbseo_hb_webp->install();
	}
	
	public function uninstall() {		
		$this->model_extension_hbseo_hb_webp->uninstall();
	}

	public function update(){
		$this->model_extension_hbseo_hb_webp->update();
		return true;
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_webp')) {
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