<?php
class ControllerExtensionHbseoRedirectDisabled extends Controller {
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
		
		$this->hb_extension_version	= '3.0.0';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/redirect-disabled-products-pages/';

		$this->load->model('extension/hbseo/redirect_disabled');		
		$this->load->language($this->hb_extension_route.'/redirect_disabled');
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
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('redirect_disabled', $this->request->post, $this->request->get['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/redirect_disabled', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
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
			'href'      => $this->url->link($this->hb_extension_route.'/redirect_disabled', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/redirect_disabled', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;

		$data['doc_link']	= $this->doc_link;
		
		$store_info = $this->model_setting_setting->getSetting('redirect_disabled', $this->request->get['store_id']);
		
		//settings
		//$data['redirect_disabled_status'] 			= isset($store_info['redirect_disabled_status'])?$store_info['redirect_disabled_status']:'';		

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/redirect_disabled'.$this->hb_template_extension, $data));
	}
	
	public function products() {  
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['search'])) {
			$url .= '&search=' . $this->request->get['search'];
		}
		
		$data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'search'	=> strtolower($search),
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total = $this->model_extension_hbseo_redirect_disabled->getTotalDisabledProduct($data); 		
		$records = $this->model_extension_hbseo_redirect_disabled->getDisabledProducts($data);

		$data['records'] = [];

		foreach ($records as $record) {
			switch ($record['pagetype']) {
				case 'product':
					$redirect_value = $this->model_extension_hbseo_redirect_disabled->getProductName($record['redirect']);
					break;

				case 'category':
					$redirect_value = $this->model_extension_hbseo_redirect_disabled->getCategoryName($record['redirect']);
					break;
				
				case 'information':
					$redirect_value = $this->model_extension_hbseo_redirect_disabled->getInformationName($record['redirect']);
					break;

				case 'custom':
					$redirect_value = $record['redirect'];
					break;
				
				default:
					$redirect_value = '';
					break;
			}

			$data['records'][] = array(
				'id'			=> $record['rdp_id'],
				'item_id' 			=> $record['item_id'],
				'name' 				=> $record['name'],
				'model' 			=> $record['model'],
				'pagetype' 			=> $record['pagetype'],
				'redirect' 			=> $redirect_value,
				'redirect_type'		=> $record['redirect_type'],
				'redirect_hits'		=> $record['redirect_hits'],
				'date_added' 		=> $record['date_added'],
				'date_modified'	 	=> $record['date_modified']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/redirect_disabled/products', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['type'] = 'product';

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/redirect_disabled_items'.$this->hb_template_extension, $data));
	}

	public function categories() {  		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['search'])) {
			$url .= '&search=' . $this->request->get['search'];
		}
		
		$data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'search'	=> strtolower($search),
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total = $this->model_extension_hbseo_redirect_disabled->getTotalDisabledCategory($data); 		
		$records = $this->model_extension_hbseo_redirect_disabled->getDisabledCategories($data);

		$data['records'] = [];

		foreach ($records as $record) {
			switch ($record['pagetype']) {
				case 'product':
					$redirect_value = $this->model_extension_hbseo_redirect_disabled->getProductName($record['redirect']);
					break;

				case 'category':
					$redirect_value = $this->model_extension_hbseo_redirect_disabled->getCategoryName($record['redirect']);
					break;
				
				case 'information':
					$redirect_value = $this->model_extension_hbseo_redirect_disabled->getInformationName($record['redirect']);
					break;

				case 'custom':
					$redirect_value = $record['redirect'];
					break;
				
				default:
					$redirect_value = '';
					break;
			}

			$data['records'][] = array(
				'id'			=> $record['rdc_id'],
				'item_id' 		=> $record['item_id'],
				'name' 				=> $record['name'],
				'pagetype' 			=> $record['pagetype'],
				'redirect' 			=> $redirect_value,
				'redirect_type'		=> $record['redirect_type'],
				'redirect_hits'		=> $record['redirect_hits'],
				'date_added' 		=> $record['date_added'],
				'date_modified'	 	=> $record['date_modified']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/redirect_disabled/categories', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$data['type'] = 'category';
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/redirect_disabled_items'.$this->hb_template_extension, $data));
	}

	public function informations() {  		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['search'])) {
			$url .= '&search=' . $this->request->get['search'];
		}
		
		$data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'search'	=> strtolower($search),
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total = $this->model_extension_hbseo_redirect_disabled->getTotalDisabledInformation($data); 		
		$records = $this->model_extension_hbseo_redirect_disabled->getDisabledInformations($data);

		$data['records'] = [];

		foreach ($records as $record) {
			switch ($record['pagetype']) {
				case 'product':
					$redirect_value = $this->model_extension_hbseo_redirect_disabled->getProductName($record['redirect']);
					break;

				case 'category':
					$redirect_value = $this->model_extension_hbseo_redirect_disabled->getCategoryName($record['redirect']);
					break;
				
				case 'information':
					$redirect_value = $this->model_extension_hbseo_redirect_disabled->getInformationName($record['redirect']);
					break;

				case 'custom':
					$redirect_value = $record['redirect'];
					break;
				
				default:
					$redirect_value = '';
					break;
			}

			$data['records'][] = array(
				'id'				=> $record['rdi_id'],
				'item_id' 			=> $record['item_id'],
				'name' 				=> $record['name'],
				'pagetype' 			=> $record['pagetype'],
				'redirect' 			=> $redirect_value,
				'redirect_type'		=> $record['redirect_type'],
				'redirect_hits'		=> $record['redirect_hits'],
				'date_added' 		=> $record['date_added'],
				'date_modified'	 	=> $record['date_modified']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/redirect_disabled/informations', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$data['type'] = 'information';
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/redirect_disabled_items'.$this->hb_template_extension, $data));
	}

	public function link_selector_form(){
		$data['pagetype'] = isset($this->request->get['pagetype']) ? $this->request->get['pagetype'] : 'product';

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/redirect_disabled_product_selector_form'.$this->hb_template_extension, $data));
	}

	public function save_redirect(){
		$json = [];

		$data['type'] = (isset($this->request->post['type']))? $this->request->post['type'] : 0; //block type

		$data['item_id'] = (isset($this->request->post['item_id']))? $this->request->post['item_id'] : 0;
		$data['pagetype'] = (isset($this->request->post['pagetype']))? $this->request->post['pagetype'] : '';

		if ($data['pagetype'] == 'product'){
			$data['redirect'] = (isset($this->request->post['redirect_product_id']) && !empty($this->request->post['redirect_product_id']))? $this->request->post['redirect_product_id'] : '0';
		}

		if ($data['pagetype'] == 'category'){
			$data['redirect'] = (isset($this->request->post['redirect_category_id']) && !empty($this->request->post['redirect_category_id']))? $this->request->post['redirect_category_id'] : '0';
		}
		
		if ($data['pagetype'] == 'information'){
			$data['redirect'] = (isset($this->request->post['redirect_information_id']) && !empty($this->request->post['redirect_information_id']))? $this->request->post['redirect_information_id'] : '0';
		}

		if ($data['pagetype'] == 'custom'){
			$data['redirect'] = (isset($this->request->post['redirect_custom']) && !empty($this->request->post['redirect_custom']))? $this->request->post['redirect_custom'] : '0';
		}
		
		$data['redirect_type'] = (isset($this->request->post['redirect_type']))? $this->request->post['redirect_type'] : '302';

		if ($data['item_id'] == 0 || $data['pagetype'] == '') {
			$json['error'] = $this->language->get('error_check_form');
		}else{
			if ($data['redirect'] != 0) {
				$this->model_extension_hbseo_redirect_disabled->setRedirect($data);
				$json['success'] = $this->language->get('success_row_updated');
			}else{
				$json['error'] = $this->language->get('error_check_form');
			}				
		}		
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function logs(){
		$type = isset($this->request->get['type']) ? $this->request->get['type'] : '';
		$item_id = isset($this->request->get['item_id']) ? $this->request->get['item_id'] : '';

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
			'type'	=> $type,
			'item_id'=> $item_id
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total = $this->model_extension_hbseo_redirect_disabled->getTotalLogs($data); 		
		$records = $this->model_extension_hbseo_redirect_disabled->getLogs($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'rdl_id'		=> $record['rdl_id'],
				'referrer'		=> urldecode($record['referrer']),
				'user_agent'	=> $record['user_agent'],
				'ip'			=> $record['ip'],
				'date_added'	=> $record['date_added']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/redirect_disabled/logs', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] .'&type='.$type.'&item_id'.$item_id.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/redirect_disabled_logs'.$this->hb_template_extension, $data));
	
	}

	public function product_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/product');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'filter_status' => 1,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}

				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function category_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'filter_status' => 1,
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_hbseo_redirect_disabled->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function information_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_hbseo_redirect_disabled->getInformations($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'information_id' => $result['information_id'],
					'name'            => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function install() { 
		$this->model_extension_hbseo_redirect_disabled->install();
	}
	
	public function uninstall() { 
		$this->model_extension_hbseo_redirect_disabled->uninstall();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/redirect_disabled')) {
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