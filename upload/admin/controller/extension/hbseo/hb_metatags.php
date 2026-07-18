<?php
class ControllerExtensionHbseoHbMetatags extends Controller {
	
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
		
		$this->hb_extension_version	= '3.2.2';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/seo-meta-tags/';

		$this->load->model('extension/hbseo/hb_metatags');		
		$this->load->language($this->hb_extension_route.'/hb_metatags');
	}
	
	public function index() {   
		$data['extension_version'] =  $this->hb_extension_version;
		
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		$store_id = $data['store_id'];
		
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_metatags', $this->request->post, $data['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_metatags', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		
		$text_strings = array(
				'heading_title','text_extension',
				'tab_home','tab_pagination','tab_search','tab_tag','tab_route',
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
			'href' => $this->url->link('marketplace/extension', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->hb_extension_route.'/hb_metatags', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
   		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		
		$data['action'] 	= $this->url->link($this->hb_extension_route.'/hb_metatags', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		
		$data['cancel'] 	= $this->url->link('marketplace/extension', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		
		$data['base_route'] = $this->hb_extension_route;

		$data['doc_link']	= $this->doc_link;
		
		$extn_info = $this->model_setting_setting->getSetting('hb_metatags', $data['store_id']);
		$config_info = $this->model_setting_setting->getSetting('config', $data['store_id']);

		//settings
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		foreach ($data['languages'] as $language){
	 		$language_id = $language['language_id'];	
			$data['hb_metatags_hmtitle'][$language_id] 		=  isset($extn_info['hb_metatags_hmtitle'.$language_id])?$extn_info['hb_metatags_hmtitle'.$language_id] : $config_info['config_meta_title'];
			$data['hb_metatags_hmdesc'][$language_id] 		=  isset($extn_info['hb_metatags_hmdesc'.$language_id])?$extn_info['hb_metatags_hmdesc'.$language_id] : $config_info['config_meta_description'];
			$data['hb_metatags_hmkeyword'][$language_id] 	=  isset($extn_info['hb_metatags_hmkeyword'.$language_id])?$extn_info['hb_metatags_hmkeyword'.$language_id] : $config_info['config_meta_keyword'];
		
			$data['hb_metatags_pgtitle'][$language_id] 		=  isset($extn_info['hb_metatags_pgtitle'.$language_id])?$extn_info['hb_metatags_pgtitle'.$language_id] : 'Page {page} - {meta}';
			$data['hb_metatags_pgdesc'][$language_id] 		=  isset($extn_info['hb_metatags_pgdesc'.$language_id])?$extn_info['hb_metatags_pgdesc'.$language_id] : 'Showing Page {page}. {meta}';
			
			$data['hb_metatags_srtitle'][$language_id] 		=  isset($extn_info['hb_metatags_srtitle'.$language_id])?$extn_info['hb_metatags_srtitle'.$language_id] : '{total} products for your search {tag} | Page {page} | '.$config_info['config_name'];
			$data['hb_metatags_srdesc'][$language_id] 		=  isset($extn_info['hb_metatags_srdesc'.$language_id])?$extn_info['hb_metatags_srdesc'.$language_id] : 'Browse {total} products for {tag} in '.$config_info['config_name'].' Page {page}';
			$data['hb_metatags_srkeyword'][$language_id] 	=  isset($extn_info['hb_metatags_srkeyword'.$language_id])?$extn_info['hb_metatags_srkeyword'.$language_id] : '{products}';
			
			$data['hb_metatags_tgtitle'][$language_id] 		=  isset($extn_info['hb_metatags_tgtitle'.$language_id])?$extn_info['hb_metatags_tgtitle'.$language_id] : '{total} products tagged {tag} | Page {page} | '.$config_info['config_name'];
			$data['hb_metatags_tgdesc'][$language_id] 		=  isset($extn_info['hb_metatags_tgdesc'.$language_id])?$extn_info['hb_metatags_tgdesc'.$language_id] : 'Browse {total} products tagged {tag} in '.$config_info['config_name'].' Page {page}';
			$data['hb_metatags_tgkeyword'][$language_id] 	=  isset($extn_info['hb_metatags_tgkeyword'.$language_id])?$extn_info['hb_metatags_tgkeyword'.$language_id] : '{products}';
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_metatags'.$this->hb_template_extension, $data));
	}
	
	public function routes() {		
		$store_id 		= (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';
		$language_id 	= (int)$this->request->get['language_id'];

		$data['language_id'] = $language_id;

		$records = $this->model_extension_hbseo_hb_metatags->getRoutes($store_id, $language_id);

		$data['records'] = array();
		if ($records) {
			foreach ($records as $record) {
				$data['records'][] = array(
					'id' 				=> $record['id'],
					'route' 			=> $record['route'],
					'meta_title' 		=> $record['meta_title'],
					'meta_description' 	=> $record['meta_description'],
					'meta_keyword' 		=> $record['meta_keyword'],
					'date_added'		=> date($this->language->get('date_format_short'), strtotime($record['date_added']))
				);
			}
		}
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_metatags_routes'.$this->hb_template_extension, $data));
	}
	
	public function add_meta(){
		$json = [];
		
		$data['store_id']			= (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';
		$data['route'] 				= trim($this->request->post['route']);
		$data['language_id'] 		= (int)$this->request->post['language_id'];
		$data['meta_title'] 		= trim($this->request->post['meta_title']);
		$data['meta_description'] 	= trim($this->request->post['meta_description']);
		$data['meta_keyword'] 		= trim($this->request->post['meta_keyword']);

		if (!$this->validate()){
			$json['error'] = $this->error['warning'];
		}
		
		if (empty($data['route']) || empty($data['meta_title']) || empty($data['meta_description']) || empty($data['meta_keyword'])){
			$json['error'] = $this->language->get('error_data');
		}
		
		if (!$json){
			if ($this->model_extension_hbseo_hb_metatags->isMetaExists($data)) {
				$json['error'] = $this->language->get('error_route_exists');
			}else{
				$this->model_extension_hbseo_hb_metatags->addMeta($data);
				$json['success'] = $this->language->get('success_meta_added');
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function delete_meta(){
		$json = [];
		
		$id = (int)$this->request->post['id'];

		if (!$this->validate()){
			$json['error'] = $this->error['warning'];
		}

		if (!$json){
			$this->model_extension_hbseo_hb_metatags->deleteMeta($id);
			$json['success'] = $this->language->get('success_meta_deleted');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function install(){
		$this->model_extension_hbseo_hb_metatags->install();
	}
	
	public function uninstall(){
		$this->model_extension_hbseo_hb_metatags->uninstall();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_metatags')) {
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