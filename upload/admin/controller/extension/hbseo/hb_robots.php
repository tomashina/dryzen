<?php
class ControllerExtensionHbseoHbRobots extends Controller {
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
		
		$this->hb_extension_version	= '1.0.0';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/robots-txt-editor/';

		$this->load->model('extension/hbseo/hb_robots');		
		$this->load->language($this->hb_extension_route.'/hb_robots');

		$this->web_root = substr(DIR_CATALOG, 0, -8);
	}
	
	public function index() {   
		$data['extension_version'] =  $this->hb_extension_version;
		
		$data['store_id'] = 0;		
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		
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
			'href'      => $this->url->link($this->hb_extension_route.'/hb_robots', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
   		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_robots', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true);
		
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;

		$data['doc_link']	= $this->doc_link;
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		$store_info = $this->model_setting_setting->getSetting('hb_robots', 0);
		
		//settings
		//$data['hb_robots_status'] 			= isset($store_info['hb_robots_status'])?$store_info['hb_robots_status']:'';

		// Load current robots.txt
		$robots_file = $this->web_root . '/robots.txt';
		if (file_exists($robots_file)) {
			$data['hb_robots_editor'] = file_get_contents($robots_file);
		} else {
			$data['hb_robots_editor'] = '';
		}
		
		$data['hb_robots_editor'] = html_entity_decode($data['hb_robots_editor'], ENT_QUOTES, 'UTF-8');

		$sitemap_path = 'Sitemap: ' . rtrim(HTTPS_CATALOG, '/') . '/sitemap-index.xml';

		// Parse current rules to set checkbox states
		$data['rules'] = [
			['code' => 'd_admin_system', 'label' => 'Disallow Admin and System Folders', 'checked' => strpos($data['hb_robots_editor'], 'Disallow: /admin/') !== false],
			['code' => 'd_pagination', 'label' => 'Disallow Pagination Parameters', 'checked' => strpos($data['hb_robots_editor'], 'Disallow: /*?page=$') !== false],
			['code' => 'd_sort', 'label' => 'Disallow Sorting Parameters', 'checked' => strpos($data['hb_robots_editor'], 'Disallow: /*?sort=') !== false],
			['code' => 'd_order', 'label' => 'Disallow Ordering Parameters', 'checked' => strpos($data['hb_robots_editor'], 'Disallow: /*?order=') !== false],
			['code' => 'd_limit', 'label' => 'Disallow Limit Parameters', 'checked' => strpos($data['hb_robots_editor'], 'Disallow: /*?limit=') !== false],
			['code' => 'd_filter', 'label' => 'Disallow Filter Parameters', 'checked' => strpos($data['hb_robots_editor'], 'Disallow: /*?filter_name=') !== false],
			['code' => 'd_cart_checkout_account', 'label' => 'Disallow Cart, Checkout, and Account Pages', 'checked' => strpos($data['hb_robots_editor'], 'Disallow: /index.php?route=checkout/') !== false],
			['code' => 'a_social_crawlers', 'label' => 'Allow Meta Social Preview Crawlers', 'checked' => strpos($data['hb_robots_editor'], 'User-agent: facebookexternalhit') !== false],
			['code' => 'a_assets', 'label' => 'Allow JavaScript and Theme Assets', 'checked' => strpos($data['hb_robots_editor'], 'Allow: /catalog/view/javascript/') !== false],
			['code' => 'a_images', 'label' => 'Allow Images', 'checked' => strpos($data['hb_robots_editor'], 'Allow: /image/') !== false],
			['code' => 'sitemap', 'label' => 'Include Sitemap', 'checked' => strpos($data['hb_robots_editor'], $sitemap_path) !== false],
		];
		

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_robots'.$this->hb_template_extension, $data));
	}
	
	public function modifyRule() {
		$json = []; // Initialize response array
	
		if ($this->request->server['REQUEST_METHOD'] === 'POST' && isset($this->request->post['rule_code']) && isset($this->request->post['is_checked']) && isset($this->request->post['content'])) {
			$rule_code = $this->request->post['rule_code'];
			$is_checked = filter_var($this->request->post['is_checked'], FILTER_VALIDATE_BOOLEAN);
			$content = html_entity_decode($this->request->post['content'], ENT_QUOTES, 'UTF-8'); // Decode HTML entities
	
			$sitemap_path = 'Sitemap: ' . rtrim(HTTPS_CATALOG, '/') . '/sitemap-index.xml';

			// Define the rules
			$rules = [
				'd_admin_system' => ['Disallow: /admin/', 'Disallow: /system/'],
				'd_pagination' => ['Disallow: /*?page=$', 'Disallow: /*&page=$'],
				'd_sort' => ['Disallow: /*?sort=', 'Disallow: /*&sort='],
				'd_order' => ['Disallow: /*?order=', 'Disallow: /*&order='],
				'd_limit' => ['Disallow: /*?limit=', 'Disallow: /*&limit='],
				'd_filter' => [
					'Disallow: /*?filter_name=',
					'Disallow: /*&filter_name=',
					'Disallow: /*?filter_sub_category=',
					'Disallow: /*&filter_sub_category=',
					'Disallow: /*?filter_description=',
					'Disallow: /*&filter_description=',
				],
				'd_cart_checkout_account' => [
					'Disallow: /index.php?route=checkout/',
					'Disallow: /index.php?route=account/',
				],
				'a_social_crawlers' => [
					'User-agent: facebookexternalhit',
					'User-agent: Facebot',
					'User-agent: meta-externalagent',
					'User-agent: meta-externalfetcher',
					'Allow: /',
				],
				'a_assets' => ['Allow: /catalog/view/javascript/', 'Allow: /catalog/view/theme/'],
				'a_images' => ['Allow: /image/'],
				'sitemap' => [$sitemap_path],
			];
	
			if (isset($rules[$rule_code])) {
				$rule_texts = $rules[$rule_code];
				$lines = explode("\n", $content); // Split content into lines
				$lines = array_map('trim', $lines); // Trim whitespace from all lines
	
				// Ensure 'User-agent: *' exists at the top
				if (!in_array('User-agent: *', $lines)) {
					array_unshift($lines, 'User-agent: *', ''); // Add 'User-agent: *' followed by a blank line
				}
	
				if ($is_checked) {
					// Add rules if not already present
					foreach ($rule_texts as $rule_text) {
						if (!in_array($rule_text, $lines)) {
							$block = array_merge([''], $rule_texts); // Add blank line before the group
							$lines = array_merge($lines, $block);
							break;
						}
					}
				} else {
					// Remove the rules
					$lines = array_filter($lines, function ($line) use ($rule_texts) {
						return !in_array(trim($line), $rule_texts);
					});
	
					// Remove redundant blank lines
					$lines = array_values(array_filter($lines, function ($line, $index) use ($lines) {
						return !(trim($line) === '' && (isset($lines[$index - 1]) && trim($lines[$index - 1]) === ''));
					}, ARRAY_FILTER_USE_BOTH));
				}
	
				// Trim trailing blank lines
				while (end($lines) === '') {
					array_pop($lines);
				}
	
				// Prepare updated content
				$updated_content = implode("\n", $lines);
				$json['success'] = true;
				$json['updated_content'] = htmlspecialchars($updated_content, ENT_QUOTES, 'UTF-8');
			} else {
				$json['success'] = false;
				$json['error'] = $this->language->get('error_invalid_rule');
			}
		} else {
			$json['success'] = false;
			$json['error'] = $this->language->get('error_invalid_request');
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
			

	public function save() {
		$json = []; // Initialize response array
	
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['content']) && $this->validate()) {
			$content = html_entity_decode($this->request->post['content'], ENT_QUOTES, 'UTF-8'); // Decode content
			$file_path = $this->web_root . '/robots.txt'; 
	
			try {
				if (file_put_contents($file_path, $content)) {
					$json['success'] = $this->language->get('text_success');
				} else {
					$json['error'] = $this->language->get('error_save_failed');
				}
			} catch (Exception $e) {
				$json['error'] = $e->getMessage();
			}
		} else {
			$json['error'] = $this->language->get('error_invalid_request');
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	

	public function restore(){
		if ($this->validate()){
			if ($this->model_extension_hbseo_hb_robots->restore_backup()){
				$json['success'] = $this->language->get('text_restore_success');
			}else{
				$json['error'] = $this->language->get('text_restore_failed');
			}
		}else{
			$json['error'] = $this->error['warning'];
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function present_file_content(){
		$robots_file = $this->web_root . '/robots.txt';

		if (file_exists($robots_file)) {
			$content = file_get_contents($robots_file);
		} else {
			$content = '';
		}

		echo $content;
	}
	
	public function install() { 
		$this->model_extension_hbseo_hb_robots->install();
	}
	
	public function uninstall() { 
		$this->model_extension_hbseo_hb_robots->uninstall();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_robots')) {
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
