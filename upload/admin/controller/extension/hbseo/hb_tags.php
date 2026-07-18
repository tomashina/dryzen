<?php
class ControllerExtensionHbseoHbTags extends Controller {
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
		
		$this->hb_extension_version	= '3.8.1';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/seo-product-tags-generator/';

		$this->load->model('extension/hbseo/hb_tags');		
		$this->load->language($this->hb_extension_route.'/hb_tags');
	} 
	
	public function index() {   
		$data['extension_version'] =  $this->hb_extension_version;
		
		$data['store_id'] = 0;		

		$data['language_id'] = (isset($this->request->get['language_id']))? (int)$this->request->get['language_id'] : (int)$this->config->get('config_language_id');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/hbseo/hb_tags');
		$this->load->model('setting/setting');
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_tags', $this->request->post, $data['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/hbseo/hb_tags', 'user_token=' . $this->session->data['user_token'], true));
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
			'href'      => $this->url->link('extension/hbseo/hb_tags', 'user_token=' . $this->session->data['user_token'], true)
   		);
		
		$data['action'] = $this->url->link('extension/hbseo/hb_tags', 'user_token=' . $this->session->data['user_token'], true);		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=hbseo', true);
		$data['clear']	= $this->url->link('extension/hbseo/hb_tags/clear_logs', 'user_token=' . $this->session->data['user_token'], true);

		$data['doc_link']	= $this->doc_link;

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$store_info = $this->model_setting_setting->getSetting('hb_tags', $data['store_id']);

		//settings
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		foreach ($data['languages'] as $language){
	 		$language_id = $language['language_id'];	
			$data['hb_tags_stopwords'][$language_id] =  isset($store_info['hb_tags_stopwords'.$language_id])?$store_info['hb_tags_stopwords'.$language_id]:'i,is,was,there,where,for,from,to,become,www,the,of';
			$data['hb_tags_pattern'][$language_id] =  isset($store_info['hb_tags_pattern'.$language_id])?$store_info['hb_tags_pattern'.$language_id]:'{p*} , {c*}, {name}, {category}, {brand}';
		}
	
		$data['hb_tags_logs'] = isset($store_info['hb_tags_logs'])?$store_info['hb_tags_logs']:'';
		$data['hb_tags_auto'] = isset($store_info['hb_tags_auto'])?$store_info['hb_tags_auto']:'';
		$data['hb_tags_autolimit'] = isset($store_info['hb_tags_autolimit'])?$store_info['hb_tags_autolimit']:'20';
		$data['hb_tags_rule_a'] = isset($store_info['hb_tags_rule_a'])?$store_info['hb_tags_rule_a']:'';
		$data['hb_tags_rule_b'] = isset($store_info['hb_tags_rule_b'])?$store_info['hb_tags_rule_b']:'';
		$data['hb_tags_authkey'] = isset($store_info['hb_tags_authkey'])?$store_info['hb_tags_authkey']:md5(rand());
		
		$store_url = HTTPS_CATALOG;
		
		$data['hb_tags_cron'] =  'wget --quiet --delete-after "'.$store_url.'index.php?route=extension/module/hb_tags/cron&key='.$data['hb_tags_authkey'].'"';
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_tags', $data));
	}
	
	public function stats() {
		$data['total_products'] = $this->model_extension_hbseo_hb_tags->getTotalProducts();
		$data['empty_tags'] = $this->model_extension_hbseo_hb_tags->getTotalEmptyTagsProducts();

		$data['filled_tags'] = $data['total_products'] - $data['empty_tags'];
		$data['filled_tags_percent'] = ($data['filled_tags']/$data['total_products']) * 100;
		$data['empty_tags_percent'] = ($data['empty_tags']/$data['total_products']) * 100;

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_tags_stats', $data));
	}

	public function products() {  
		$language_id = (isset($this->request->get['language_id']))? (int)$this->request->get['language_id'] : (int)$this->config->get('config_language_id');

		$page 	= (isset($this->request->get['page']))? $this->request->get['page'] : 1;
		$search = (isset($this->request->get['search']))? $this->request->get['search'] : '';

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['search'])) {
			$url .= '&search=' . $this->request->get['search'];
		}
		
		$limit = ($this->config->get('config_limit_admin')) ? $this->config->get('config_limit_admin') : 50;
		$data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'language_id'	=> $language_id,
			'search'		=> strtolower($search),
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$records_total = $this->model_extension_hbseo_hb_tags->getTotalRecords($data); 		
		$records = $this->model_extension_hbseo_hb_tags->getRecords($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'product_id'		=> $record['product_id'],
				'name' 				=> html_entity_decode($record['name'], ENT_QUOTES, 'UTF-8'),
				'model' 			=> html_entity_decode($record['model'], ENT_QUOTES, 'UTF-8'),
				'tag'				=> html_entity_decode($record['tag'], ENT_QUOTES, 'UTF-8'),
				'href'				=> $this->url->link('catalog/product/edit', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&product_id='.$record['product_id'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_tags/products', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] .'&language_id=' . $language_id . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_tags_items'.$this->hb_template_extension, $data));
	}

	public function delete_tags(){
		$json = [];
		
		$selected = (isset($this->request->post['selected']))? $this->request->post['selected'] : [];

		if (empty($selected)){
			$json['error'] = $this->language->get('error_no_record_selected');
		}

		if (!$json){
			foreach ($selected as $id) {
				$this->model_extension_hbseo_hb_tags->deleteTags($id);
			}

			$json['success'] = $this->language->get('success_deleted');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function clear_tags(){	
		if ($this->validate()){
			$this->model_extension_hbseo_hb_tags->clearTags();
			$json['success'] = $this->language->get('text_all_tags_deleted');
		}else{
			$json['error'] = $this->error['warning'];
		}
		
		$this->response->setOutput(json_encode($json));	
	}
	
	public function logs(){
		if (!file_exists(DIR_LOGS)) {
			mkdir(DIR_LOGS, 0777, true);
		}

		$file = DIR_LOGS . 'hb_seo_product_tags.txt';
		if (file_exists($file)) {
			$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		}else{
			$data['log'] = '';
		}

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_tags_logs', $data));
	}

	public function clear_logs() {
		if (!$this->validate()) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = DIR_LOGS . 'hb_seo_product_tags.txt';

			$handle = fopen($file, 'w+');

			fclose($handle);

			$this->session->data['success'] =  $this->language->get('text_success_logs');
		}

		$this->response->redirect($this->url->link('extension/hbseo/hb_tags', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/hbseo/hb_tags')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}

	public function event_add_product_tag(&$route, &$args, &$output){
        if ($this->config->get('hb_tags_auto')){
            file_get_contents(HTTPS_CATALOG.'index.php?route=extension/module/hb_tags/cron&key='.$this->config->get('hb_tags_authkey'));
        }
    }

	public function install() { 
		$this->model_extension_hbseo_hb_tags->install();
	}
	
	public function uninstall() { 
		$this->model_extension_hbseo_hb_tags->uninstall();
	}
	
}
?>