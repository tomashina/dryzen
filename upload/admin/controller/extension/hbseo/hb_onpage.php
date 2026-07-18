<?php
class ControllerExtensionHbseoHbOnpage extends Controller {	
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
		
		$this->hb_extension_version	= '3.9.0';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/seo-on-page-tags-generator/';

		$this->load->model('extension/hbseo/hb_onpage');		
		$this->load->language($this->hb_extension_route.'/hb_onpage');
	}
	
	public function index() {   
		$data['extension_version'] = $this->hb_extension_version;
		
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		} else {
			$data['store_id'] = 0;
		}
		
		$store_id = $data['store_id'];
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_onpage', $this->request->post, $this->request->get['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_onpage', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
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
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->hb_extension_route.'/hb_onpage', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_onpage', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		
		$data['clear'] = $this->url->link($this->hb_extension_route.'/hb_onpage/clear_logs', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;

		$data['doc_link'] = $this->doc_link;
		
		$store_info = $this->model_setting_setting->getSetting('hb_onpage', $this->request->get['store_id']);

		//settings
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		$first_language = reset($data['languages']);
		$data['first_language'] = $first_language['language_id'];
	
		$data['hb_onpage_logs'] = isset($store_info['hb_onpage_logs']) ? $store_info['hb_onpage_logs'] : '';
		$data['hb_onpage_auto'] = isset($store_info['hb_onpage_auto']) ? $store_info['hb_onpage_auto'] : '';
		$data['hb_onpage_autolimit'] = isset($store_info['hb_onpage_autolimit']) ? $store_info['hb_onpage_autolimit'] : '500';
		$data['hb_onpage_authkey'] = isset($store_info['hb_onpage_authkey']) ? $store_info['hb_onpage_authkey'] : md5(rand());
		
		$this->load->model('setting/store');
		if ($data['store_id'] == 0){ 
			$store_url = HTTP_CATALOG;
		} else {
			 $results = $this->model_setting_store->getStore($data['store_id']);
			 $store_url = $results['url'];
		}

		$data['hb_onpage_cron'] = 'wget --quiet --delete-after "'.$store_url.'index.php?route=extension/module/hb_onpage/auto&authkey='.$data['hb_onpage_authkey'].'"';
		
		$data['pageTypes'] = [
			'product' => $this->language->get('text_page_type_product'),
			'category' => $this->language->get('text_page_type_category'),
			'manufacturer' => $this->language->get('text_page_type_manufacturer'),
			'information' => $this->language->get('text_page_type_information'),
		];

		$data['elementTypes'] = [
			'meta_title' => $this->language->get('text_element_type_meta_title'),
			'meta_description' => $this->language->get('text_element_type_meta_description'),
			'meta_keyword' => $this->language->get('text_element_type_meta_keyword'),
			'h1' => $this->language->get('text_element_type_h1'),
			'h2' => $this->language->get('text_element_type_h2'),
			'image_alt' => $this->language->get('text_element_type_image_alt'),
			'image_title' => $this->language->get('text_element_type_image_title'),
		];

		$data['elements'] = [
			'product' => ['meta_title', 'meta_description', 'meta_keyword', 'h1', 'h2', 'image_alt', 'image_title'],
			'category' => ['meta_title', 'meta_description', 'meta_keyword', 'h1', 'h2', 'image_alt', 'image_title'],
			'manufacturer' => ['meta_title', 'meta_description', 'meta_keyword', 'h1', 'h2', 'image_alt', 'image_title'],
			'information' => ['meta_title', 'meta_description', 'meta_keyword']
		];
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_onpage'.$this->hb_template_extension, $data));
	}
	
	public function dashboard() { 		
		$store_id = (int)$this->request->get['store_id'];
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		$elements = [
			'product' => [
				'meta_title'       => 'Meta Title',
				'meta_description' => 'Meta Description',
				'meta_keyword'     => 'Meta Keyword',
				'h1'               => 'H1 Tag',
				'h2'               => 'H2 Tag',
				'image_alt'        => 'Image Alt Tag',
				'image_title'      => 'Image Title Tag'
			],
			'category' => [
				'meta_title'       => 'Meta Title',
				'meta_description' => 'Meta Description',
				'meta_keyword'     => 'Meta Keyword',
				'h1'               => 'H1 Tag',
				'h2'               => 'H2 Tag',
				'image_alt'        => 'Image Alt Tag',
				'image_title'      => 'Image Title Tag'
			],
			'manufacturer' => [
				'meta_title'       => 'Meta Title',
				'meta_description' => 'Meta Description',
				'meta_keyword'     => 'Meta Keyword',
				'h1'               => 'H1 Tag',
				'h2'               => 'H2 Tag',
				'image_alt'        => 'Image Alt Tag',
				'image_title'      => 'Image Title Tag'
			],
			'information' => [
				'meta_title'       => 'Meta Title',
				'meta_description' => 'Meta Description',
				'meta_keyword'     => 'Meta Keyword'
			]
		];
		
		foreach ($data['languages'] as $language) {
			$language_id = $language['language_id'];
			foreach ($elements as $page_type => $element_types) {
				foreach ($element_types as $key => $value) {
					$data[$page_type][$key][$language_id] = $this->model_extension_hbseo_hb_onpage->getCount($page_type, $key, $language_id);
				}
			}
		}
		
		$data['totals'] = [
			'product'     => $this->model_extension_hbseo_hb_onpage->getTotalItems('product'),
			'category'    => $this->model_extension_hbseo_hb_onpage->getTotalItems('category'),
			'manufacturer'       => $this->model_extension_hbseo_hb_onpage->getTotalItems('manufacturer'),
			'information' => $this->model_extension_hbseo_hb_onpage->getTotalItems('information')
		];
		
		foreach ($elements as $page_type => $element_types) {
			$data['pages'][] = [
				'title'       => ucfirst($page_type) . ' Pages',
				'code'        => $page_type,
				'total_items' => $data['totals'][$page_type],
				'items'       => $element_types,
				'counts'      => $data[$page_type]
			];
		}
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_onpage_dashboard'.$this->hb_template_extension, $data));
	}

	public function get_element_total() {
		$page_type    = $this->request->get['page_type'];
		$element_type = $this->request->get['element_type'];
		$language_id  = (int)$this->request->get['language_id'];

		$total = $this->model_extension_hbseo_hb_onpage->getCount($page_type, $element_type, $language_id);
		echo $total;
	}
	
	public function logs() {
		if (!file_exists(DIR_LOGS)) {
			mkdir(DIR_LOGS, 0777, true);
		}

		$file = DIR_LOGS . 'huntbee_seo_onpage_elements.txt';
		if (file_exists($file)) {
			$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		} else {
			$data['log'] = '';
		}

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_onpage_logs', $data));
	}

	public function clear_logs() {
		$store_id = (int)$this->request->get['store_id'];
		if (!$this->validate()) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = DIR_LOGS . 'huntbee_seo_onpage_elements.txt';

			$handle = fopen($file, 'w+');

			fclose($handle);

			$this->session->data['success'] = $this->language->get('text_success_logs');
		}

		$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_onpage', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$store_id, true));
	}
	
	public function clear_tags() {
		$json = [];

		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}

		$store_id  = (int)$this->request->get['store_id'];
		$page_type = $this->request->post['page_type'];
		$element   = $this->request->post['element'];
			
		if (!$json) {
			$this->model_extension_hbseo_hb_onpage->clearTags($page_type, $element, $store_id);
			$json['success'] = sprintf($this->language->get('success_element_data_deleted'), ucfirst(str_replace('_', ' ', $element)), $page_type);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}
	
	public function setsamples() {
		$store_id = (int)$this->request->get['store_id'];		
		
		$this->load->model('setting/store');
		if ($store_id == 0) { 
			$store_name = $this->config->get('config_name');
		} else {
			$results = $this->model_setting_store->getStore($store_id);
			$store_name = $results['name'];
		}
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		foreach ($data['languages'] as $language) {
			$language_id = $language['language_id'];
			$this->model_extension_hbseo_hb_onpage->sampletemplates($language_id, $store_id, $store_name);
		}
		$json['success'] = 'Sample Templates has been loaded. Please note these are only sample templates; For better SEO, you need to understand your website and use templates that will better suit your website content.';
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function clear_all_templates() {
		$this->model_extension_hbseo_hb_onpage->clearAllTemplates();
		
		$json['success'] = $this->language->get('success_template_cleared');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function clear_templates_by_element() {
		$json = [];

		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}

		$store_id = (int)$this->request->get['store_id'];
		$element_type = $this->request->get['element_type'];

		if (!$json) {
			$this->model_extension_hbseo_hb_onpage->clearTemplatesByElement($element_type, $store_id);
			$json['success'] = $this->language->get('success_element_templates_cleared');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function loadblock() {		
		$data['store_id']      = (int)$this->request->get['store_id'];
		$data['language_id']   = (int)$this->request->get['language_id'];
		$data['page_type']     = $this->request->get['page_type'];
		$data['element_type']  = $this->request->get['element_type'];
		
		$data['block']         = $data['page_type'] . '_' . $data['element_type'] . '_block' . $data['language_id'];
		$data['refreshlink']   = $this->url->link($this->hb_extension_route . '/hb_onpage/loadblock', $this->hb_token_name . '=' . $this->session->data[$this->hb_token_name] . '&store_id=' . $data['store_id'] . '&page_type=' . $data['page_type'] . '&element_type=' . $data['element_type'] . '&language_id=' . $data['language_id'], true);
		
		$data['templates']     = $this->model_extension_hbseo_hb_onpage->getTemplates($data);
		$data['table_heading'] = strtoupper(str_replace('_', ' ', $data['element_type']) . ' ' . $this->language->get('text_templates'));
		
		$this->response->setOutput($this->load->view('extension/hbseo/' . $this->hb_template_folder . '/hb_onpage_templates' . $this->hb_template_extension, $data));
	}
	
	public function tools(){
		$data = array();
	
		$data['invalid_language_checks'] = array(
			'product'      => $this->model_extension_hbseo_hb_onpage->invalidLanguageEntries('product'),
			'category'     => $this->model_extension_hbseo_hb_onpage->invalidLanguageEntries('category'),
			'manufacturer'        => $this->model_extension_hbseo_hb_onpage->invalidLanguageEntries('manufacturer'),
			'information'  => $this->model_extension_hbseo_hb_onpage->invalidLanguageEntries('information')
		);
		
		$data['language_check_fine'] = !(array_sum($data['invalid_language_checks']) > 0);
	
		$data['meta_title_checks'] = array(
			'product'      => $this->model_extension_hbseo_hb_onpage->titleLengthIssues('product'),
			'category'     => $this->model_extension_hbseo_hb_onpage->titleLengthIssues('category'),
			'manufacturer'        => $this->model_extension_hbseo_hb_onpage->titleLengthIssues('manufacturer'),
			'information'  => $this->model_extension_hbseo_hb_onpage->titleLengthIssues('information')
		);
	
		$data['meta_description_checks'] = array(
			'product'      => $this->model_extension_hbseo_hb_onpage->mdLengthIssues('product'),
			'category'     => $this->model_extension_hbseo_hb_onpage->mdLengthIssues('category'),
			'manufacturer' => $this->model_extension_hbseo_hb_onpage->mdLengthIssues('manufacturer'),
			'information'  => $this->model_extension_hbseo_hb_onpage->mdLengthIssues('information')
		);
	
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_onpage_tools'.$this->hb_template_extension, $data));
	}
	
	
	public function fixlanguageentries() {
		$json = [];

		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}
		
		if (!$json) {
			$this->model_extension_hbseo_hb_onpage->fixLanguageEntries();
			$json['success'] = $this->language->get('success_invalid_rows_deleted');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}
	
	public function clearmetatitleissues() {
		$json = [];

		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}

		$page_type = $this->request->get['page_type'];

		if (!$json) {
			$this->model_extension_hbseo_hb_onpage->deleteLengthIssues($page_type);
			$json['success'] = $this->language->get('success_meta_titles_cleared');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}
	
	public function clearmetadescissues() {
		$json = [];

		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}

		$page_type = $this->request->get['page_type'];
				
		if (!$json) {
			$this->model_extension_hbseo_hb_onpage->deletemdLengthIssues($page_type);
			$json['success'] = $this->language->get('success_meta_descriptions_cleared');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}
	
	public function add_template() {
		$json = [];

		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}

		$data = $this->request->post;
		$data['store_id'] = (int)$this->request->get['store_id'];

		$template = $data['template'];
		foreach ($template as $language_id => $template_data) {
			if (empty($template_data)) {
				$json['error'] = 'Template data cannot be empty for any language!';
				break;
			}
		}
		
		if (!$json) {
			$this->model_extension_hbseo_hb_onpage->addTemplate($data);
			$json['success'] = $this->language->get('success_template_added');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function delete_template() {
		$json = [];

		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}

		$id = (int)$this->request->post['id'];

		if (!$json) {
			$this->model_extension_hbseo_hb_onpage->deleteTemplate($id);
			$json['success'] = $this->language->get('success_template_deleted');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function install() {
		$this->model_extension_hbseo_hb_onpage->install();
	}
	
	public function uninstall() {
		$this->model_extension_hbseo_hb_onpage->uninstall();
	}

	public function update(){
		$this->model_extension_hbseo_hb_onpage->update();
		return true;
	}
	
	private function validate() {
		/*if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_onpage')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	*/
		return TRUE;
	}	
}
?>