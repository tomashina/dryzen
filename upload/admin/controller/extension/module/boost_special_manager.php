<?php
//==============================================
// Special Manager
// Author 	: OpenCartBoost
// Email 	: support@opencartboost.com
// Website 	: http://www.opencartboost.com
//==============================================
class ControllerExtensionModuleBoostSpecialManager extends Controller {
    private $error = [];
    
    public function index() {
		$this->load->language('extension/module/boost_special_manager');
		
		$this->document->setTitle($this->language->get('text_title'));
		
		$this->document->addStyle('https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.css');
		$this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css');
		$this->document->addStyle('view/stylesheet/boost_special_manager.css');
		
		$this->document->addScript('https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.js');
		$this->document->addScript('https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table-locale-all.min.js');
		$this->document->addScript('view/javascript/jquery/jquery-tabledit-master/jquery.tabledit.js');
        
		$data['breadcrumbs'] = [];
        
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/boost_special_manager', 'user_token=' . $this->session->data['user_token'], true)
		];
		
		$text_languages = [
			'text_title',
			'text_extension',
			'text_module',
			'text_yes',
			'text_no',
			'tab_products',
			'tab_categories',
			'tab_setting',
			'button_add',
			'button_delete',
			'button_save',
			'button_cancel',
			'entry_include_special',
			'entry_percentage_discount',
			'entry_fixed_discount',
			'entry_flat_price',
			'help_include_special',
			'column_image',
			'column_product_name',
			'column_price',
			'column_special',
			'column_categories',
			'column_manufacturer',
			'column_status',
			'column_date_start',
			'column_date_end',
			'column_customer_group',
			'column_priority',
			'column_category_name',
			'column_discount',
			'column_discount_type',
			'confirm_delete',
			'error_entry_value',
		];
		
		foreach ($text_languages as $text_translation) {
			$data[$text_translation] = $this->language->get($text_translation);
		}
		
		$locale_lists = [
			'af-ZA','ar-SA','ca-ES','ca-CZ','da-DK','de-DE','el-GR','en-US','es-AR','es-CL', 
			'es-CR','es-ES','es-MX','es-NI','es-SP','et-EE','eu-EU','fa-IR','fi-FI','fr-BE',
			'fr-FR','he-IL','hr-HR','hu-HU','id-ID','it-IT','ja-JP','ka-GE','ko-KR','ms-MY', 
			'nb-NO','nl-NL','pl-PL','pt-PT','pt-BR','ro-RO','ru-RU','sk-SK','sv-SE','th-TH',
			'tr-TR','uk-UA','ur-PK','vi-VN','zh-CN','zh-TW','uz-Latn-UZ',
		];

		$lang_first = substr($this->config->get('config_admin_language'),0,-2);
		$lang_last = substr($this->config->get('config_admin_language'),-2);
		
		$lang_code = $lang_first . strtoupper($lang_last);
		
		if (in_array($lang_code, $locale_lists)) {
			$data['lang_code'] = $lang_code;
		} else {
			$data['lang_code'] = 'en-US';
		}
  
		$data['addurl'] = htmlspecialchars_decode($this->url->link('extension/module/boost_special_manager/add', 'user_token=' . $this->session->data['user_token'], true));
		$data['deleteurl'] = htmlspecialchars_decode($this->url->link('extension/module/boost_special_manager/delete', 'user_token=' . $this->session->data['user_token'], true));
		$data['addcaturl'] = htmlspecialchars_decode($this->url->link('extension/module/boost_special_manager/addcat', 'user_token=' . $this->session->data['user_token'], true));
		$data['deletecaturl'] = htmlspecialchars_decode($this->url->link('extension/module/boost_special_manager/deletecat', 'user_token=' . $this->session->data['user_token'], true));
		
		$data['setting'] = $this->url->link('extension/module/boost_special_manager/setting', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$data['user_token'] = $this->session->data['user_token'];
		
		if (isset($this->session->data['showtab'])) {
			$data['showtab'] = $this->session->data['showtab'];

			unset($this->session->data['showtab']);
		} else {
			$data['showtab'] = '';
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];

			unset($this->session->data['error_warning']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$data['include_special'] = $this->config->get('module_boost_special_manager_include');
		
		$this->load->model('extension/module/boost_special_manager');
		
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		
		$special_products = $this->model_extension_module_boost_special_manager->getSpecialProducts();
		
		$special_categories = $this->model_extension_module_boost_special_manager->getSpecialCategories();
		
		$customer_groups = $this->model_extension_module_boost_special_manager->getCustomerGroups();
		
		$data['categories_list'] = [];
		
		$this->load->model('catalog/category');
		
		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
            $data['categories_list'][] = [
				'category_id' => $category['category_id'],
                'name'        => $category['name'],
			];
		}
		
		$data['categories'] = [];
		$data['products'] = [];
		$data['customer_groups'] = [];
		$data['json_customer_group'] = [];
				
		foreach ($customer_groups  as $customer_group) {
			$data['customer_groups'][] = [
				'value'	=> $customer_group['customer_group_id'],
				'text' 	=> $customer_group['name']
			];
			
			$id = $customer_group['customer_group_id'];
			$data['json_customer_group'][$id] =  $customer_group['name'];
		}
		
		foreach ($special_products as $result) {
			if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
			}

			$categories =  $this->model_catalog_product->getProductCategories($result['product_id']);
			
			$manufacturer = $this->model_extension_module_boost_special_manager->getManufacturer($result['manufacturer_id']);
			
			$man_name = ($manufacturer) ? $manufacturer['name'] : '';
			
			$customer_group = $this->model_extension_module_boost_special_manager->getCustomerGroup($result['customer_group_id']);
			
			$customer_group_name = ($customer_group) ? $customer_group['name'] : '';
			
			$date_start = ($result['date_start'] != '0000-00-00') ? $result['date_start'] : '';
			$date_end = ($result['date_end'] != '0000-00-00') ? $result['date_end'] : '';
			
			$data['products'][] = [
				'product_special_id' 	=> $result['product_special_id'],
				'product_id' 			=> $result['product_id'],
				'name'       			=> $result['name'],
				'categories' 			=> $categories,
				'price'      			=> $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    			=> number_format($result['special_price'], 2),
				'special_formatted'		=> $this->currency->format($result['special_price'], $this->config->get('config_currency')),
				'date_start' 			=> $date_start,
				'date_end' 	 			=> $date_end,
				'image'      			=> $image,
				'quantity'   			=> $result['quantity'],
				'customer_group_id'   	=> $result['customer_group_id'],
				'customer_group_name'	=> $customer_group_name,
				'manufacturer'   		=> $man_name,
				'priority'   			=> $result['priority'],
				'status'     			=> ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'selected'   			=> isset($this->request->post['selected']) && in_array($result['product_id'], $this->request->post['selected'])
			];
    	}

		foreach ($special_categories as $result2) {
			$customer_group = $this->model_extension_module_boost_special_manager->getCustomerGroup($result2['customer_group_id']);
			
			$customer_group_name = ($customer_group) ? $customer_group['name'] : '';
			
			$date_start = ($result2['date_start'] != '0000-00-00') ? $result2['date_start'] : '';
			$date_end = ($result2['date_end'] != '0000-00-00') ? $result2['date_end'] : '';

			$discount_type = '';
			switch ($result2['discount_type']) {
				case 0:
					$discount_type = $this->language->get('entry_percentage_discount');
					break;

				case 1:
					$discount_type = $this->language->get('entry_fixed_discount');
					break;

				case 2:
					$discount_type = $this->language->get('entry_flat_price');
					break;
			
				default:
					$discount_type = '';
					break;
			}

			$discount_formatted = $this->currency->format($result2['discount'], $this->config->get('config_currency'));
			if($result2['discount_type'] == 0){
				$discount_formatted = number_format($result2['discount'], 2, '.', '') . '%';
			}
      		
			$data['categories'][] = [
				'cat_special_id' 		=> $result2['cat_special_id'],
				'category_id' 			=> $result2['category_id'],
				'name'       			=> $result2['name'],
				'discount'      		=> number_format($result2['discount'], 2),
				'discount_formatted'	=> $discount_formatted,
				'discount_type'    		=> $discount_type,
				'date_start' 			=> $date_start,
				'date_end' 	 			=> $date_end,
				'customer_group_id'   	=> $result2['customer_group_id'],
				'customer_group_name'	=> $customer_group_name,
				'priority'   			=> $result2['priority'],
				'selected'   			=> isset($this->request->post['selected']) && in_array($result2['cat_special_id'], $this->request->post['selected'])
			];
		}

		if (!$this->user->hasPermission('modify', 'extension/module/boost_special_manager')) {
			$data['modify_permission'] = false;
		} else {
			$data['modify_permission'] = true;
		}
		
		$data['currency_symbol_left'] = $this->currency->getSymbolLeft($this->config->get('config_currency'));
		$data['currency_symbol_right'] = $this->currency->getSymbolRight($this->config->get('config_currency'));
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/module/boost_special_manager/index', $data));
   }
	
	public function setting() {
		$isError = false;

		$this->load->language('extension/module/boost_special_manager');

		if (!$this->user->hasPermission('modify', 'extension/module/boost_special_manager')) {
			$this->error['warning'] = $this->language->get('error_permission');
			$isError = true;
		}
        
		$this->load->model('setting/setting');
		
		$this->session->data['showtab'] = 'setting';

		if ($isError) {
			$this->session->data['error_warning'] = $this->error['warning'];
			$this->response->redirect($this->url->link('extension/module/boost_special_manager', 'user_token=' . $this->session->data['user_token'], true));
		}
		    	
		if (($this->request->server['REQUEST_METHOD'] == 'POST' ) && !$isError) {
			$this->session->data['success'] = $this->language->get('text_success3');
			
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}
			//$this->config->set('config_include_boost_special_managers', $this->request->post['setting_special']);
			$this->model_setting_setting->editSettingValue('module_boost_special_manager', 'module_boost_special_manager_include', $this->request->post['setting_special']);
		
			$this->response->redirect($this->url->link('extension/module/boost_special_manager', 'user_token=' . $this->session->data['user_token'], true));
		}
	}
	
	public function add() {
		unset($this->session->data['showtab']);
		
		$this->load->model('extension/module/boost_special_manager');
		$this->load->language('extension/module/boost_special_manager');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm() && ($this->user->hasPermission('modify', 'extension/module/boost_special_manager'))) {
			$this->session->data['success'] = $this->language->get('text_success');
			
			if ($this->error && !isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_warning');
			}
			
			$this->model_extension_module_boost_special_manager->addSpecialProducts($this->request->post);
			$this->response->redirect($this->url->link('extension/module/boost_special_manager', 'user_token=' . $this->session->data['user_token'], true));

		} else {
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()){
				if (!$this->user->hasPermission('modify', 'extension/module/boost_special_manager')) {
					$this->error['warning'] = $this->language->get('error_permission');
				}
			}

			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->error['customer_group_id'])) {
				$data['error_customer_group_id'] = $this->error['customer_group_id'];
			} else {
				$data['error_customer_group_id'] = '';
			}
			
			if (isset($this->error['product_list'])) {
				$data['error_product_list'] = $this->error['product_list'];
			} else {
				$data['error_product_list'] = '';
			}

			if (isset($this->error['price'])) {
				$data['error_value'] = $this->error['price'];
			} else {
				$data['error_value'] = '';
			}
			
			$customer_groups = $this->model_extension_module_boost_special_manager->getCustomerGroups();
			$manufacturers = $this->model_extension_module_boost_special_manager->getManufacturers();
			$categoriesx = $this->model_extension_module_boost_special_manager->getCategories();
			
			$data['manufacturers'] = [];
			$data['categoriesx'] = [];
			$data['customer_groups'] = [];
			
			foreach ($customer_groups  as $customer_group) {
				$data['customer_groups'][] = [
					'customer_group_id'	=> $customer_group['customer_group_id'],
					'name' 				=> $customer_group['name']
				];
			}
			
			foreach ($manufacturers  as $manufacturer) {
				$data['manufacturers'][] = [
					'manufacturer_id'	=> $manufacturer['manufacturer_id'],
					'name' 				=> $manufacturer['name']
				];
			}
			
			foreach ($categoriesx  as $category) {
				$data['categoriesx'][] = [
					'category_id'	=> $category['category_id'],
					'name' 			=> $category['name']
				];
			}
			
			$this->document->setTitle($this->language->get('text_title'));
			
			$text_languages = [
				'text_title',
				'text_form_product_special',
				'text_products',
				'text_rule',
				'text_select',
				'entry_categories',
				'entry_manufacturers',
				'entry_products',
				'entry_discount_type',
				'entry_customer_groups',
				'entry_percentage_discount',
				'entry_fixed_discount',
				'entry_flat_price',
				'entry_date_start',
				'entry_date_end',
				'entry_value',
				'entry_priority',
				'error_categories',
				'error_manufacturers',
				'button_save',
				'button_cancel',
				'button_add_selected',
				'button_remove_selected',
				'button_add_all',
				'button_remove_all'
			];
		
			foreach ($text_languages as $text_translation) {
				$data[$text_translation] = $this->language->get($text_translation);
			}
			
			$data['breadcrumbs'] = [];
			
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			];
			
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_title'),
				'href' => $this->url->link('extension/module/boost_special_manager', 'user_token=' . $this->session->data['user_token'], true)
			];
			
			$data['user_token'] = $this->session->data['user_token'];
			
			$data['action'] = $this->url->link('extension/module/boost_special_manager/add', 'user_token=' . $this->session->data['user_token'], true);
			$data['cancel'] = $this->url->link('extension/module/boost_special_manager', 'user_token=' . $this->session->data['user_token'], true);
			
			$data['getproductsbycategory'] = htmlspecialchars_decode($this->url->link('extension/module/boost_special_manager/getproductsbycategory', 'user_token=' . $this->session->data['user_token'], true));
			$data['getproductsbymanufacturer'] = htmlspecialchars_decode($this->url->link('extension/module/boost_special_manager/getproductsbymanufacturer', 'user_token=' . $this->session->data['user_token'], true));			
					
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
		}
		
		$this->response->setOutput($this->load->view('extension/module/boost_special_manager/product_form', $data));
	}
	
	public function getproductsbymanufacturer() {
		if (!$this->user->hasPermission('modify', 'extension/module/boost_special_manager')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$this->load->model('extension/module/boost_special_manager');
		$this->model_extension_module_boost_special_manager->getProductsByManufacturerId($this->request->post);
	}

	public function getproductsbycategory() {
		if (!$this->user->hasPermission('modify', 'extension/module/boost_special_manager')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$this->load->model('extension/module/boost_special_manager');
		$this->model_extension_module_boost_special_manager->getProductsByCategoryId($this->request->post);
	}
	
	public function update() {
		$this->load->language('extension/module/boost_special_manager');

		if (!$this->user->hasPermission('access', 'extension/module/boost_special_manager')) {
			echo json_encode([
				'status' => false,
				'message' => $this->language->get('error_permission')
			]);
			return;
		}
		
		if (!$this->user->hasPermission('modify', 'extension/module/boost_special_manager')) {
			echo json_encode([
				'status' => false,
				'message' => $this->language->get('error_permission')
			]);
			return;
		}
		
		$this->load->model('extension/module/boost_special_manager');
		$this->model_extension_module_boost_special_manager->editProduct($this->request->post);

		if ($this->request->post['action'] === 'delete'){
			echo json_encode([
				'status' => true,
				'message' => $this->language->get('text_delete_success')
			]);
		} else {
			echo json_encode([
				'status' => true,
				'message' => $this->language->get('text_success')
			]);
		}
	}
	
	public function delete() {
		$this->load->language('extension/module/boost_special_manager');
		
		if (!$this->user->hasPermission('modify', 'extension/module/boost_special_manager')) {
			echo json_encode([
				'status' => false,
				'message' => $this->language->get('error_permission')
			]);
			return;
		}
		
		$this->load->model('extension/module/boost_special_manager');
		$this->model_extension_module_boost_special_manager->deleteProduct($this->request->post);

		echo json_encode([
			'status' => true,
			'message' => $this->language->get('text_delete_success')
		]);
		return;
		
	}
	
	public function addcat() {
		$this->document->addStyle('https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css');
		$this->document->addScript('https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js');
		
		$this->load->model('extension/module/boost_special_manager');
		$this->load->language('extension/module/boost_special_manager');
		
		$this->session->data['showtab'] = 'category';
    	
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm2() && ($this->user->hasPermission('modify', 'extension/module/boost_special_manager'))) {
			$this->session->data['success'] = $this->language->get('text_success2');

			if ($this->error && !isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_warning2');
			}
		
			$this->model_extension_module_boost_special_manager->addSpecialCategories($this->request->post);
			$this->response->redirect($this->url->link('extension/module/boost_special_manager', 'user_token=' . $this->session->data['user_token'], true));
		} else {
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm2()) {
				if (!$this->user->hasPermission('modify', 'extension/module/boost_special_manager')) {
					$this->error['warning'] = $this->language->get('error_permission');
				}
			}

			$customer_groups = $this->model_extension_module_boost_special_manager->getCustomerGroups();
			$manufacturers = $this->model_extension_module_boost_special_manager->getManufacturers();
			$categoriesx = $this->model_extension_module_boost_special_manager->getCategories();
			
			$data['manufacturers'] = [];
			$data['categoriesx'] = [];
			$data['customer_groups'] = [];
			
			foreach ($customer_groups  as $customer_group) {
				$data['customer_groups'][] = [
					'customer_group_id'	=> $customer_group['customer_group_id'],
					'name' 				=> $customer_group['name']
				];
			}
			
			foreach ($manufacturers  as $manufacturer) {
				$data['manufacturers'][] = [
					'manufacturer_id'	=> $manufacturer['manufacturer_id'],
					'name' 				=> $manufacturer['name']
				];
			}
			
			foreach ($categoriesx  as $category) {
				$data['categoriesx'][] = [
					'category_id'	=> $category['category_id'],
					'name' 			=> $category['name']
				];
			}
			
			$this->document->setTitle($this->language->get('text_title'));
			
			$text_languages = [
				'text_title',
				'text_form_category_special',
				'text_categories',
				'text_rule',
				'text_select',
				'button_save',
				'button_cancel',
				'entry_categories',
				'entry_customer_groups',
				'entry_discount_type',
				'entry_percentage_discount',
				'entry_fixed_discount',
				'entry_flat_price',
				'entry_date_start',
				'entry_date_end',
				'entry_value',
				'entry_priority'
			];
		
			foreach ($text_languages as $text_translation) {
				$data[$text_translation] = $this->language->get($text_translation);
			}
			
			$data['breadcrumbs'] = [];
			
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			];
			
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_title'),
				'href' => $this->url->link('extension/module/boost_special_manager', 'user_token=' . $this->session->data['user_token'], true)
			];
			
			$data['user_token'] = $this->session->data['user_token'];
			
			$data['cancel'] = $this->url->link('extension/module/boost_special_manager', 'user_token=' . $this->session->data['user_token'], true);
			$data['action'] = $this->url->link('extension/module/boost_special_manager/addcat', 'user_token=' . $this->session->data['user_token'], true);
			
			$data['getproductsbycategory'] = htmlspecialchars_decode($this->url->link('extension/module/boost_special_manager/getproductsbycategory', 'user_token=' . $this->session->data['user_token'], true));
			$data['getproductsbymanufacturer'] = htmlspecialchars_decode($this->url->link('extension/module/boost_special_manager/getproductsbymanufacturer', 'user_token=' . $this->session->data['user_token'], true));			
					
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->error['customer_group_id'])) {
				$data['error_customer_group_id'] = $this->error['customer_group_id'];
			} else {
				$data['error_customer_group_id'] = '';
			}
			
			if (isset($this->error['category_id'])) {
				$data['error_category_id'] = $this->error['category_id'];
			} else {
				$data['error_category_id'] = '';
			}

			if (isset($this->error['price'])) {
				$data['error_value'] = $this->error['price'];
			} else {
				$data['error_value'] = '';
			}
		}
		
		$this->response->setOutput($this->load->view('extension/module/boost_special_manager/category_form', $data));
	}

	public function updatecat() {
		$this->load->language('extension/module/boost_special_manager');

		if (!$this->user->hasPermission('modify', 'extension/module/boost_special_manager')) {
			echo json_encode([
				'status' => false,
				'message' => $this->language->get('error_permission')
			]);
			return;
		}
		
		$this->load->model('extension/module/boost_special_manager');
		$this->model_extension_module_boost_special_manager->editCategory($this->request->post);

		if($this->request->post['action'] === 'delete'){
			echo json_encode([
				'status' => true,
				'message' => $this->language->get('text_deletecat_success')
			]);
		} else {
			echo json_encode([
				'status' => true,
				'message' => $this->language->get('text_success2')
			]);
		}
		
	}
	
	public function deletecat() {
		$this->load->language('extension/module/boost_special_manager');

		if (!$this->user->hasPermission('modify', 'extension/module/boost_special_manager')) {
			echo json_encode([
				'status' => false,
				'message' => $this->language->get('error_permission')
			]);
			return;
		}
		
		$this->load->model('extension/module/boost_special_manager');
		$this->model_extension_module_boost_special_manager->deleteCategory($this->request->post);

		echo json_encode([
			'status' => true,
			'message' => $this->language->get('text_deletecat_success')
		]);
	}
	
	protected function validateForm() {
		if ($this->request->post['customer_group_id'] == 0) {
			$this->error['customer_group_id'] = $this->language->get('error_customer_group_id');
		}
		
		if ($this->request->post['price'] == 0) {
			$this->error['price'] = $this->language->get('error_value');
		}
		
		if ($this->request->post['ProductList'] == '') {
			$this->error['product_list'] = $this->language->get('error_product_list');
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
	
	protected function validateForm2() {
		if ($this->request->post['customer_group_id'] == 0) {
			$this->error['customer_group_id'] = $this->language->get('error_customer_group_id');
		}
		
		if ($this->request->post['price'] == 0) {
			$this->error['price'] = $this->language->get('error_value');
		}
		
		if ($_POST['ProductList'] < 1) {
			$this->error['category_id'] = $this->language->get('error_category_id');
		} 
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
	
	public function install() {
		$this->load->model('extension/module/boost_special_manager'); 
		$this->model_extension_module_boost_special_manager->install();
	}
	
	public function uninstall() {
		$this->load->model('extension/module/boost_special_manager');
		$this->model_extension_module_boost_special_manager->uninstall();
	}
}
