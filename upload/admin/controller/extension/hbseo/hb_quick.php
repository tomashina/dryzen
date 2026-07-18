<?php
class ControllerExtensionHbseoHbQuick extends Controller {
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
		
		$this->hb_extension_version	= '3.1.2';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/seo-quick-editor/';

		$this->load->model('extension/hbseo/hb_quick');		
		$this->load->language($this->hb_extension_route.'/hb_quick');
	}
	
	public function index() {   
		$data['extension_version'] =  $this->hb_extension_version;
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		$data['language_id'] = (isset($this->request->get['language_id']))? (int)$this->request->get['language_id'] : (int)$this->config->get('config_language_id');
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_quick', $this->request->post);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_quick', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&language_id=' . $data['language_id'] , true));
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
			'href'      => $this->url->link('common/dashboard', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->hb_extension_route.'/hb_quick', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&language_id=' . $data['language_id'], true)
   		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_quick', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&language_id=' . $data['language_id'], true);
		
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;

		$data['clear']	= $this->url->link($this->hb_extension_route.'/hb_quick/clear_logs', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&language_id=' . $data['language_id'], true);

		$data['doc_link']	= $this->doc_link;
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['total_languages'] = count($data['languages']);
		
		$store_info = $this->model_setting_setting->getSetting('hb_quick');
		
		//settings
		$data['hb_quick_count']			= isset($store_info['hb_quick_count'])? $store_info['hb_quick_count'] : '50';
		$data['hb_quick_sort_field']	= isset($store_info['hb_quick_sort_field'])? $store_info['hb_quick_sort_field'] : 'date_modified';
		$data['hb_quick_sort_order']	= isset($store_info['hb_quick_sort_order'])? $store_info['hb_quick_sort_order'] : 'DESC';

		//columns to show
		$data['columns'] = $this->model_extension_hbseo_hb_quick->getProductColumns();
		
		foreach ($data['columns'] as $key => $value){
			$data['hb_quick_field'][$key]		= isset($store_info['hb_quick_field_'.$key])? $store_info['hb_quick_field_'.$key] : '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_quick'.$this->hb_template_extension, $data));
	}
	
	public function product() {  
		$language_id = (isset($this->request->get['language_id']))? (int)$this->request->get['language_id'] : (int)$this->config->get('config_language_id');

		$page 	= (isset($this->request->get['page']))? $this->request->get['page'] : 1;
		$search = (isset($this->request->get['search']))? $this->request->get['search'] : '';
		
		$limit = ($this->config->get('hb_quick_count')) ? $this->config->get('hb_quick_count') : 50;
		$data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'language_id'	=> $language_id,
			'sort_field'	=> ($this->config->get('hb_quick_sort_field')) ? $this->config->get('hb_quick_sort_field') : 'date_modified',
			'sort_order'	=> ($this->config->get('hb_quick_sort_order')) ? $this->config->get('hb_quick_sort_order') : 'DESC',
			'search'		=> strtolower($search),
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$records_total = $this->model_extension_hbseo_hb_quick->getTotalProducts($data); 		
		$records = $this->model_extension_hbseo_hb_quick->getProducts($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$h1 = (!empty($record['h1'])) ? html_entity_decode($record['h1'], ENT_QUOTES, 'UTF-8') : '';
			$h2 = (!empty($record['h2'])) ? html_entity_decode($record['h2'], ENT_QUOTES, 'UTF-8') : '';
			$image_alt = (!empty($record['image_alt'])) ? html_entity_decode($record['image_alt'], ENT_QUOTES, 'UTF-8') : '';
			$image_title = (!empty($record['image_title'])) ? html_entity_decode($record['image_title'], ENT_QUOTES, 'UTF-8') : '';
			$keyword = (!empty($record['seo_keyword'])) ? html_entity_decode($record['seo_keyword'], ENT_QUOTES, 'UTF-8') : '';

			// Generate short and full descriptions
			$description = (!empty($record['description'])) ? html_entity_decode($record['description'], ENT_QUOTES, 'UTF-8') : '';
			$shortDescription = mb_substr(strip_tags($description), 0, 200, 'UTF-8'); // First 200 characters

			$data['records'][] = array(
				'item_id'			=> $record['item_id'],
				'name' 				=> !empty($record['name']) ? html_entity_decode($record['name'], ENT_QUOTES, 'UTF-8') : '',
				'model' 			=> !empty($record['model']) ? html_entity_decode($record['model'], ENT_QUOTES, 'UTF-8') : '',
				'description'       => $description,
				'short_description' => $shortDescription,
				'meta_title'		=> !empty($record['meta_title']) ? html_entity_decode($record['meta_title'], ENT_QUOTES, 'UTF-8') : '',
				'meta_description'	=> !empty($record['meta_description']) ? html_entity_decode($record['meta_description'], ENT_QUOTES, 'UTF-8') : '',
				'meta_keyword'		=> !empty($record['meta_keyword']) ? html_entity_decode($record['meta_keyword'], ENT_QUOTES, 'UTF-8') : '',
				'h1'				=> $h1,
				'h2'				=> $h2,
				'image_alt'			=> $image_alt,
				'image_title'		=> $image_title,
				'tag'				=> !empty($record['tag']) ? html_entity_decode($record['tag'], ENT_QUOTES, 'UTF-8') : '',
				'keyword'			=> $keyword,
				'href'				=> $this->url->link('catalog/product/edit', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&product_id='.$record['item_id'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_quick/product', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] .'&language_id=' . $language_id . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['columns'] = $this->model_extension_hbseo_hb_quick->getProductColumns();

		$data['type'] = 'product';

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_quick_items'.$this->hb_template_extension, $data));
	}	

	public function category() {  		
		$language_id = (isset($this->request->get['language_id']))? (int)$this->request->get['language_id'] : (int)$this->config->get('config_language_id');

		$page 	= (isset($this->request->get['page']))? $this->request->get['page'] : 1;
		$search = (isset($this->request->get['search']))? $this->request->get['search'] : '';

		$limit = ($this->config->get('hb_quick_count')) ? $this->config->get('hb_quick_count') : 50;
		$data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'language_id'	=> $language_id,
			'sort_field'	=> ($this->config->get('hb_quick_sort_field')) ? $this->config->get('hb_quick_sort_field') : 'date_modified',
			'sort_order'	=> ($this->config->get('hb_quick_sort_order')) ? $this->config->get('hb_quick_sort_order') : 'DESC',
			'search'		=> strtolower($search),
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$records_total = $this->model_extension_hbseo_hb_quick->getTotalCategories($data); 		
		$records = $this->model_extension_hbseo_hb_quick->getCategories($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$h1 = (!empty($record['h1'])) ? html_entity_decode($record['h1'], ENT_QUOTES, 'UTF-8') : '';
			$h2 = (!empty($record['h2'])) ? html_entity_decode($record['h2'], ENT_QUOTES, 'UTF-8') : '';
			$image_alt = (!empty($record['image_alt'])) ? html_entity_decode($record['image_alt'], ENT_QUOTES, 'UTF-8') : '';
			$image_title = (!empty($record['image_title'])) ? html_entity_decode($record['image_title'], ENT_QUOTES, 'UTF-8') : '';
			$keyword = (!empty($record['seo_keyword'])) ? html_entity_decode($record['seo_keyword'], ENT_QUOTES, 'UTF-8') : '';

			// Generate short and full descriptions
			$description = (!empty($record['description'])) ? html_entity_decode($record['description'], ENT_QUOTES, 'UTF-8') : '';
			$shortDescription = mb_substr(strip_tags($description), 0, 200, 'UTF-8'); // First 200 characters

			$data['records'][] = array(
				'item_id'			=> $record['item_id'],
				'name' 				=> !empty($record['name']) ? html_entity_decode($record['name'], ENT_QUOTES, 'UTF-8') : '',
				'description'       => $description,
				'short_description' => $shortDescription,
				'meta_title'		=> !empty($record['meta_title']) ? html_entity_decode($record['meta_title'], ENT_QUOTES, 'UTF-8') : '',
				'meta_description'	=> !empty($record['meta_description']) ? html_entity_decode($record['meta_description'], ENT_QUOTES, 'UTF-8') : '',
				'meta_keyword'		=> !empty($record['meta_keyword']) ? html_entity_decode($record['meta_keyword'], ENT_QUOTES, 'UTF-8') : '',
				'h1'				=> $h1,
				'h2'				=> $h2,
				'image_alt'			=> $image_alt,
				'image_title'		=> $image_title,
				'keyword'			=> $keyword,
				'href'				=> $this->url->link('catalog/category/edit', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&category_id='.$record['item_id'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_quick/category', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] .'&language_id=' . $language_id . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['columns'] = $this->model_extension_hbseo_hb_quick->getCategoryColumns();

		$data['type'] = 'category';

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_quick_items'.$this->hb_template_extension, $data));
	}

	public function information() {  		
		$language_id = (isset($this->request->get['language_id']))? (int)$this->request->get['language_id'] : (int)$this->config->get('config_language_id');

		$page 	= (isset($this->request->get['page']))? $this->request->get['page'] : 1;
		$search = (isset($this->request->get['search']))? $this->request->get['search'] : '';
		
		$limit = ($this->config->get('hb_quick_count')) ? $this->config->get('hb_quick_count') : 50;
		$data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'language_id'	=> $language_id,
			'sort_field'	=> ($this->config->get('hb_quick_sort_field')) ? $this->config->get('hb_quick_sort_field') : 'date_modified',
			'sort_order'	=> ($this->config->get('hb_quick_sort_order')) ? $this->config->get('hb_quick_sort_order') : 'DESC',
			'search'		=> strtolower($search),
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$records_total = $this->model_extension_hbseo_hb_quick->getTotalInformations($data); 		
		$records = $this->model_extension_hbseo_hb_quick->getInformations($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$keyword = (!empty($record['seo_keyword'])) ? html_entity_decode($record['seo_keyword'], ENT_QUOTES, 'UTF-8') : '';

			// Generate short and full descriptions
			$description = (!empty($record['description'])) ? html_entity_decode($record['description'], ENT_QUOTES, 'UTF-8') : '';
			$shortDescription = mb_substr(strip_tags($description), 0, 200, 'UTF-8'); // First 200 characters

			$data['records'][] = array(
				'item_id'			=> $record['item_id'],
				'title' 			=> !empty($record['name']) ? html_entity_decode($record['name'], ENT_QUOTES, 'UTF-8') : '',
				'description'       => $description,
				'short_description' => $shortDescription,
				'meta_title'		=> !empty($record['meta_title']) ? html_entity_decode($record['meta_title'], ENT_QUOTES, 'UTF-8') : '',
				'meta_description'	=> !empty($record['meta_description']) ? html_entity_decode($record['meta_description'], ENT_QUOTES, 'UTF-8') : '',
				'meta_keyword'		=> !empty($record['meta_keyword']) ? html_entity_decode($record['meta_keyword'], ENT_QUOTES, 'UTF-8') : '',
				'keyword'			=> $keyword,
				'href'				=> $this->url->link('catalog/information/edit', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&information_id='.$record['item_id'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_quick/information', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] .'&language_id=' . $language_id . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['columns'] = $this->model_extension_hbseo_hb_quick->getInformationColumns();

		$data['type'] = 'information';

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_quick_items'.$this->hb_template_extension, $data));
	}

	public function manufacturer() {  		
		$language_id = (isset($this->request->get['language_id']))? (int)$this->request->get['language_id'] : (int)$this->config->get('config_language_id');

		$page 	= (isset($this->request->get['page']))? $this->request->get['page'] : 1;
		$search = (isset($this->request->get['search']))? $this->request->get['search'] : '';
		
		$limit = ($this->config->get('hb_quick_count')) ? $this->config->get('hb_quick_count') : 50;
		$data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'language_id'	=> $language_id,
			'sort_field'	=> ($this->config->get('hb_quick_sort_field')) ? $this->config->get('hb_quick_sort_field') : 'date_modified',
			'sort_order'	=> ($this->config->get('hb_quick_sort_order')) ? $this->config->get('hb_quick_sort_order') : 'DESC',
			'search'		=> strtolower($search),
		);

		$data['is_language'] = $this->model_extension_hbseo_hb_quick->isManufacturerDescTable();

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		if ($data['is_language']) {
			$records_total = $this->model_extension_hbseo_hb_quick->getTotalMLManufacturers($data); 		
			$records = $this->model_extension_hbseo_hb_quick->getMLManufacturers($data);
		}else{
			$records_total = $this->model_extension_hbseo_hb_quick->getTotalManufacturers($data); 		
			$records = $this->model_extension_hbseo_hb_quick->getManufacturers($data);
		}

		$data['records'] = [];

		foreach ($records as $record) {
			$meta_title = (!empty($record['meta_title'])) ? html_entity_decode($record['meta_title'], ENT_QUOTES, 'UTF-8') : '';
			$meta_description = (!empty($record['meta_description'])) ? html_entity_decode($record['meta_description'], ENT_QUOTES, 'UTF-8') : '';
			$meta_keyword = (!empty($record['meta_keyword'])) ? html_entity_decode($record['meta_keyword'], ENT_QUOTES, 'UTF-8') : '';
			$h1 = (!empty($record['h1'])) ? html_entity_decode($record['h1'], ENT_QUOTES, 'UTF-8') : '';
			$h2 = (!empty($record['h2'])) ? html_entity_decode($record['h2'], ENT_QUOTES, 'UTF-8') : '';
			$image_alt = (!empty($record['image_alt'])) ? html_entity_decode($record['image_alt'], ENT_QUOTES, 'UTF-8') : '';
			$image_title = (!empty($record['image_title'])) ? html_entity_decode($record['image_title'], ENT_QUOTES, 'UTF-8') : '';
			$keyword = (!empty($record['seo_keyword'])) ? html_entity_decode($record['seo_keyword'], ENT_QUOTES, 'UTF-8') : '';

			// Generate short and full descriptions
			$description = (!empty($record['description'])) ? html_entity_decode($record['description'], ENT_QUOTES, 'UTF-8') : '';
			$shortDescription = mb_substr(strip_tags($description), 0, 200, 'UTF-8'); // First 200 characters
			
			$data['records'][] = array(
				'item_id'			=> $record['manufacturer_id'],
				'name' 				=> !empty($record['name']) ? html_entity_decode($record['name'], ENT_QUOTES, 'UTF-8') : '',
				'description'		=> $description,
				'short_description' => $shortDescription,
				'meta_title'		=> $meta_title,
				'meta_description'	=> $meta_description,
				'meta_keyword'		=> $meta_keyword,
				'h1'				=> $h1,
				'h2'				=> $h2,
				'image_alt'			=> $image_alt,
				'image_title'		=> $image_title,
				'keyword'			=> $keyword,
				'href'				=> $this->url->link('catalog/manufacturer/edit', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name]. '&manufacturer_id='.$record['manufacturer_id'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_quick/manufacturer', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] .'&language_id=' . $language_id . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['columns'] = $this->model_extension_hbseo_hb_quick->getManufacturerColumns();

		$data['type'] = 'manufacturer';

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_quick_items'.$this->hb_template_extension, $data));
	}

	public function desc_editor(){
		$data['type'] = (isset($this->request->get['type'])) ? $this->request->get['type'] : '';

		$data['column'] = 'description';

		$data['language_id'] = (isset($this->request->get['language_id']))? (int)$this->request->get['language_id'] : (int)$this->config->get('config_language_id');
		$data['item_id'] 	= (isset($this->request->get['item_id']))? (int)$this->request->get['item_id'] : '0';
		$data['description'] = $this->model_extension_hbseo_hb_quick->getDescription($data);

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_quick_desc_editor'.$this->hb_template_extension, $data));
	}

	public function item_name(){
		$data['type'] = (isset($this->request->get['type'])) ? $this->request->get['type'] : '';
		$data['language_id'] = (isset($this->request->get['language_id']))? (int)$this->request->get['language_id'] : (int)$this->config->get('config_language_id');
		$data['item_id'] 	= (isset($this->request->get['item_id']))? (int)$this->request->get['item_id'] : '0';

		echo $this->model_extension_hbseo_hb_quick->getName($data);
	}

	public function save_changes(){
		$json = [];

		if (!$this->validate()){
			$json['error'] = $this->error['warning'];
		}

		if (isset($this->request->get['language_id'])){
			$data['language_id'] = (int)$this->request->get['language_id'];
		}else{
			$json['error'] = $this->language->get('error_language_id');
		}

		$data['type'] = isset($this->request->post['type']) ? $this->request->post['type'] : '';
		$data['id'] = isset($this->request->post['id']) ? (int)$this->request->post['id'] : 0;
		$data['column'] = isset($this->request->post['key']) ? $this->request->post['key'] : '';
		$data['updated_value'] = isset($this->request->post['value']) ? trim($this->request->post['value']) : '';


		if ($data['type'] == '' || $data['id'] == 0 || $data['column'] == ''){
			$json['error'] = sprintf($this->language->get('error_invalid_data'), $data['column'], $data['type'], $data['id']);
		}

		if ($data['column'] == 'keyword'){
			$data['updated_value'] = preg_replace('/\s+/', '-', $data['updated_value']);
			if ($this->model_extension_hbseo_hb_quick->is_keyword_exists($data['updated_value'])){
				$json['error'] = $this->language->get('error_keyword');
			}
		}

		if (!$json){
			if ($data['column'] != 'description'){
				$data['updated_value'] = strip_tags($data['updated_value']);
			}
			
			$this->model_extension_hbseo_hb_quick->update_value($data);
			$json['success'] = ucfirst(sprintf($this->language->get('success_cell_updated'), $data['column'], $data['type'], $data['id']));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function install() { 
		$this->model_extension_hbseo_hb_quick->install();
	}
	
	public function uninstall() { 
		$this->model_extension_hbseo_hb_quick->uninstall();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_quick')) {
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