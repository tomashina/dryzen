<?php
class ControllerExtensionHbseoHbSeoreport extends Controller {
	protected $registry;
	private $error = array(); 
	
	public function __construct($registry) {
		$this->registry = $registry;
		$this->hb_extension_version	= '3.2.2';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/seo-reports/';

		$this->load->model('extension/hbseo/hb_seoreport');		
		$this->load->language('extension/hbseo/hb_seoreport');
		$this->load->model('setting/setting');
	}
	
	public function index() {   
		$data['extension_version'] =  $this->hb_extension_version;
		
		$data['store_id'] = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : 0;
		
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/hbseo/hb_seoreport');

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
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=hbseo', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/hbseo/hb_seoreport', 'user_token=' . $this->session->data['user_token'].'&store_id='.$data['store_id'], true)
   		);
		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=hbseo', true);
		
		$data['user_token'] = $this->session->data['user_token'];

		$data['doc_link']	= $this->doc_link;
				
		$data['header'] 		= $this->load->controller('common/header');
		$data['column_left'] 	= $this->load->controller('common/column_left');
		$data['footer'] 		= $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_seoreport', $data));
	}

	public function dashboard(){
		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : 0;

		// Check if the session data for individual page SEO scores is set
		$data['basic_page_seo'] = isset($this->session->data['basic_page_seo'.$store_id]) ? $this->session->data['basic_page_seo'.$store_id] : 0;
		$data['product_page_seo'] = isset($this->session->data['product_page_seo'.$store_id]) ? $this->session->data['product_page_seo'.$store_id] : 0;
		$data['category_page_seo'] = isset($this->session->data['category_page_seo'.$store_id]) ? $this->session->data['category_page_seo'.$store_id] : 0;
		$data['manufacturer_page_seo'] = isset($this->session->data['manufacturer_page_seo'.$store_id]) ? $this->session->data['manufacturer_page_seo'.$store_id] : 0;
		$data['information_page_seo'] = isset($this->session->data['information_page_seo'.$store_id]) ? $this->session->data['information_page_seo'.$store_id] : 0;

	
		// Calculate the total SEO score if all values are present
		if ($data['basic_page_seo'] && $data['product_page_seo'] && $data['category_page_seo'] && $data['manufacturer_page_seo'] && $data['information_page_seo']) {
			$data['total_seo_score'] = round((
				$data['basic_page_seo'] +
				$data['product_page_seo'] +
				$data['category_page_seo'] +
				$data['manufacturer_page_seo'] +
				$data['information_page_seo']
			) / 5, 2);
		} else {
			$data['total_seo_score'] = 0;
		}
	
		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_seoreport_dashboard', $data));
	}	
	
	public function basic() {
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$total_items = count($data['languages']);
		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : 0;

		$hb_metatags = $this->model_extension_hbseo_hb_seoreport->getCountBySettingCode($store_id, 'hb_metatags');
		$keys = $hb_metatags > 0 ? [
			'meta_title_key' => 'hb_metatags_hmtitle',
			'meta_description_key' => 'hb_metatags_hmdesc',
			'meta_keyword_key' => 'hb_metatags_hmkeyword',
		] : [
			'meta_title_key' => 'config_meta_title',
			'meta_description_key' => 'config_meta_description',
			'meta_keyword_key' => 'config_meta_keyword',
		];
	
		$checks = [
			'meta_title' => [$keys['meta_title_key'], 50, 60],
			'meta_description' => [$keys['meta_description_key'], 150, 160],
			'meta_keyword' => [$keys['meta_keyword_key'], 50, 255],
			'canonical' => ($this->getConfigValue('hb_canonical_status', $store_id)) ? 0 : $total_items / 2,
			'hreflang' => (count($data['languages']) <= 1 || $this->getConfigValue('hb_seourl_hreflang', $store_id)) ? 0 : $total_items,
			'home_schema' => $this->getConfigValue('hb_snippets_kg_enable', $store_id) ? 0 : $total_items,
			'og' => $this->getConfigValue('hb_snippets_og_enable', $store_id) ? 0 : $total_items,
			'twitter' => $this->getConfigValue('hb_snippets_tc_enable', $store_id) ? 0 : $total_items,
			'robots' => file_exists('../robots.txt') && strpos(file_get_contents('../robots.txt'), 'Disallow: /') !== false ? 0 : $total_items,
			'sitemap' => $this->getConfigValue('hb_sitemap_enable', $store_id) ? 0 : $total_items,
			'https'=> (strpos(HTTP_CATALOG, 'https://') === false) ? $total_items : 0,
			'seo_url'=> ($this->getConfigValue('config_seo_url', 0) && $this->getConfigValue('config_seo_url', 0) == '1') ? 0 : $total_items,
			'broken_link'=> ($this->getConfigValue('hb_brokenlinks_defaulturl', $store_id) && !empty($this->getConfigValue('hb_brokenlinks_defaulturl', $store_id))) ? 0 : $total_items,
			'index_follow' => $this->getConfigValue('hb_crawl_status', $store_id) ? 0 : $total_items,
		];
	
		$total_seo_scores = 0;
		$check_count = 0;
	
		foreach ($checks as $key => $params) {
			if (is_array($params)) {
				// Handle checks with character limits
				$issue_count = $this->model_extension_hbseo_hb_seoreport->getSettingCountByCharacterLimit($store_id, ...$params);
				if ($hb_metatags == 0) {
					$issue_count += $total_items - 1;
				}
			} else {
				// Handle static checks
				$issue_count = $params;
			}
	
			$seo_score = $this->model_extension_hbseo_hb_seoreport->calculateSeoScore($issue_count, $total_items);
			$data['checklist'][$key] = [
				'title' => $this->language->get("text_{$key}"),
				'status' => $issue_count == 0,
				'analysis' => $issue_count == 0 ? $this->language->get("text_check_{$key}_true") : sprintf($this->language->get("text_check_{$key}_false"), $issue_count),
				'seo_score' => $seo_score,
				'report_button' => false
			];
	
			$total_seo_scores += $seo_score;
			$check_count++;
		}
	
		// Calculate total SEO score
		$data['total_seo_score'] = $check_count > 0 ? round($total_seo_scores / $check_count, 2) : 0;
		$this->session->data['basic_page_seo'.$store_id] = $data['total_seo_score'];
	
		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_seoreport_page_report', $data));
	}

	public function product() {
		$data['page_type'] = 'product';
		$this->load->model('localisation/language');
		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : 0;

		$total_items = $this->model_extension_hbseo_hb_seoreport->getTotalItems('product');
		$data['total_items'] = $total_items;
	
		// Checklist definitions for dynamic checks
		$checks = [
			'description'      => ['product_description', 'description', 300, false],
			'meta_title'       => ['product_description', 'meta_title', 50, 60],
			'meta_description' => ['product_description', 'meta_description', 150, 160],
			'meta_keyword'     => ['product_description', 'meta_keyword', 50, false],
			'h1'               => ['product_description', 'h1', 20, 70],
			'h2'               => ['product_description', 'h2', 20, 60],
			'image_alt'        => ['product_description', 'image_alt', 5, 125],
			'image_title'      => ['product_description', 'image_title', 10, 70],
			'tag'              => ['product_description', 'tag', 10, false],
			'seo_image'        => $this->model_extension_hbseo_hb_seoreport->getTotalInvalidImageNames('product'),
		];
	
		$total_seo_scores = 0;
		$check_count = 0;
	
		foreach ($checks as $key => $params) {
			$issue_count = is_array($params)
				? $this->model_extension_hbseo_hb_seoreport->getItemCountByCharacterLimit($total_items, ...$params)
				: $params; // Handle custom counts (e.g., `seo_image`)
	
			$seo_score = $this->model_extension_hbseo_hb_seoreport->calculateSeoScore($issue_count, $total_items);
			$data['checklist'][$key] = [
				'title'    => $this->language->get("text_{$key}"),
				'status'   => ($issue_count == 0) ? true : false,
				'analysis' => ($issue_count == 0) ? $this->language->get("text_check_{$key}_true") : sprintf($this->language->get("text_check_{$key}_false"), $issue_count),
				'seo_score' => $seo_score,
				'report_button' => true
			];
	
			$total_seo_scores += $seo_score;
			$check_count++;
		}
	
		// Additional static checks
		$static_checks = [
			'canonical'  => $this->getConfigValue('hb_canonical_status', $store_id) ? ($this->getConfigValue('hb_canonical_type', $store_id) === '0'? 0 : $total_items / 2) : 0,
			'schema'     => $this->getConfigValue('hb_snippets_prod_enable', $store_id) ? 0 : $total_items,
			'breadcrumb' => $this->getConfigValue('hb_snippets_bc_enable', $store_id) ? 0 : $total_items,
			'og'         => $this->getConfigValue('hb_snippets_og_enable', $store_id) ? 0 : $total_items,
			'twitter'    => $this->getConfigValue('hb_snippets_tc_enable', $store_id) ? 0 : $total_items,
			'hreflang'   => (count($this->model_localisation_language->getLanguages()) > 1) ? (($this->getConfigValue('hb_seourl_hreflang', $store_id)) ? 0 : $total_items) : 0,
			'redirect_disabled' => $this->model_extension_hbseo_hb_seoreport->tableExists('redirect_disabled_product')
				? $this->model_extension_hbseo_hb_seoreport->getTotalEmptyRedirectItems('product')
				: $this->model_extension_hbseo_hb_seoreport->getTotalDisabledItems('product'),
			'reviews'    => $this->getConfigValue('seo_reviews_status', $store_id) ? 0 : $total_items,
			'index_follow' => ($this->getConfigValue('hb_crawl_status', $store_id) && $this->getConfigValue('hb_crawl_product', $store_id) == 'index, follow') ? 0 : $total_items,
		];
	
		foreach ($static_checks as $key => $issue_count) {
			$seo_score = $this->model_extension_hbseo_hb_seoreport->calculateSeoScore($issue_count, $total_items);
			$data['checklist'][$key] = [
				'title'    => $this->language->get("text_{$key}"),
				'status'   => ($issue_count == 0) ? true : false,
				'analysis' => ($issue_count == 0) ? $this->language->get("text_check_{$key}_true") : sprintf($this->language->get("text_check_{$key}_false"), $issue_count),
				'seo_score' => $seo_score,
				'report_button' => false
			];
	
			$total_seo_scores += $seo_score;
			$check_count++;
		}
	
		// Calculate total SEO score
		$data['total_seo_score'] = $check_count > 0 ? round($total_seo_scores / $check_count, 2) : 0;

		$this->session->data['product_page_seo'.$store_id] = $data['total_seo_score'];
	
		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_seoreport_page_report', $data));
	}		

	public function category() {
		$data['page_type'] = 'category';
		$this->load->model('localisation/language');
		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : 0;

		$total_items = $this->model_extension_hbseo_hb_seoreport->getTotalItems('category');
		$data['total_items'] = $total_items;
		
		$checks = [
			'description'      => ['category_description', 'description', 250, false],
			'meta_title'       => ['category_description', 'meta_title', 50, 60],
			'meta_description' => ['category_description', 'meta_description', 150, 160],
			'meta_keyword'     => ['category_description', 'meta_keyword', 50, false],
			'h1'               => ['category_description', 'h1', 20, 70],
			'h2'               => ['category_description', 'h2', 20, 60],
			'image_alt'        => ['category_description', 'image_alt', 5, 125],
			'image_title'      => ['category_description', 'image_title', 10, 70],
			'seo_image'        => $this->model_extension_hbseo_hb_seoreport->getTotalInvalidImageNames('category'),
		];

		$total_seo_scores = 0;
		$check_count = 0;
	
		foreach ($checks as $key => $params) {
			$issue_count = is_array($params)
				? $this->model_extension_hbseo_hb_seoreport->getItemCountByCharacterLimit($total_items, ...$params)
				: $params; // Handle custom counts (e.g., `seo_image`)
			
			$seo_score = $this->model_extension_hbseo_hb_seoreport->calculateSeoScore($issue_count, $total_items);
			$data['checklist'][$key] = [
				'title'    => $this->language->get("text_{$key}"),
				'status'   => ($issue_count == 0) ? true : false,
				'analysis' => ($issue_count == 0) ? $this->language->get("text_check_{$key}_true") : sprintf($this->language->get("text_check_{$key}_false"), $issue_count),
				'seo_score' => $seo_score,
				'report_button' => true
			];

			$total_seo_scores += $seo_score;
			$check_count++;
		}
	
		// Additional static checks
		$static_checks = [
			'canonical'  => $this->getConfigValue('hb_canonical_status', $store_id) ? 0 : $total_items / 2,
			'schema'     => $this->getConfigValue('hb_snippets_list_enable', $store_id) ? 0 : $total_items,
			'breadcrumb' => $this->getConfigValue('hb_snippets_bc_enable', $store_id) ? 0 : $total_items,
			'og'         => $this->getConfigValue('hb_snippets_og_enable', $store_id) ? 0 : $total_items,
			'twitter'    => $this->getConfigValue('hb_snippets_tc_enable', $store_id) ? 0 : $total_items,
			'hreflang'   => (count($this->model_localisation_language->getLanguages()) > 1) ? (($this->getConfigValue('hb_seourl_hreflang', $store_id)) ? 0 : $total_items) : 0,
			'redirect_disabled' => $this->model_extension_hbseo_hb_seoreport->tableExists('redirect_disabled_category')
				? $this->model_extension_hbseo_hb_seoreport->getTotalEmptyRedirectItems('category')
				: $this->model_extension_hbseo_hb_seoreport->getTotalDisabledItems('category'),
			'index_follow' => ($this->getConfigValue('hb_crawl_status', $store_id) && $this->getConfigValue('hb_crawl_category', $store_id) == 'index, follow') ? 0 : $total_items,
		];
	
		foreach ($static_checks as $key => $issue_count) {
			$seo_score = $this->model_extension_hbseo_hb_seoreport->calculateSeoScore($issue_count, $total_items);
			$data['checklist'][$key] = [
				'title'    => $this->language->get("text_{$key}"),
				'status'   => ($issue_count == 0) ? true : false,
				'analysis' => ($issue_count == 0) ? $this->language->get("text_check_{$key}_true") : sprintf($this->language->get("text_check_{$key}_false"), $issue_count),
				'seo_score' => $seo_score,
				'report_button' => false
			];

			$total_seo_scores += $seo_score;
			$check_count++;
		}

		// Calculate total SEO score
		$data['total_seo_score'] = $check_count > 0 ? round($total_seo_scores / $check_count, 2) : 0;

		$this->session->data['category_page_seo'.$store_id] = $data['total_seo_score'];

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_seoreport_page_report', $data));
	}	

	public function manufacturer() {
		$data['page_type'] = 'manufacturer';
		$this->load->model('localisation/language');
		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : 0;

		$total_items = $this->model_extension_hbseo_hb_seoreport->getTotalItems('manufacturer');
		$data['total_items'] = $total_items;

		if ($this->model_extension_hbseo_hb_seoreport->tableExists('manufacturer_description')) {
			$table = 'manufacturer_description';
		}else{
			$table = 'manufacturer';
		}
		
		$checks = [
			'description'      => [$table, 'description', 250, false],
			'meta_title'       => [$table, 'meta_title', 50, 60],
			'meta_description' => [$table, 'meta_description', 150, 160],
			'meta_keyword'     => [$table, 'meta_keyword', 50, false],
			'h1'               => [$table, 'h1', 20, 70],
			'h2'               => [$table, 'h2', 20, 60],
			'image_alt'        => [$table, 'image_alt', 5, 125],
			'image_title'      => [$table, 'image_title', 10, 70],
			'seo_image'        => $this->model_extension_hbseo_hb_seoreport->getTotalInvalidImageNames('manufacturer'),
		];

		$total_seo_scores = 0;
		$check_count = 0;
	
		foreach ($checks as $key => $params) {
			$issue_count = is_array($params)
				? $this->model_extension_hbseo_hb_seoreport->getItemCountByCharacterLimit($total_items, ...$params)
				: $params; // Handle custom counts (e.g., `seo_image`)
			
			$seo_score = $this->model_extension_hbseo_hb_seoreport->calculateSeoScore($issue_count, $total_items);
			$data['checklist'][$key] = [
				'title'    => $this->language->get("text_{$key}"),
				'status'   => ($issue_count == 0) ? true : false,
				'analysis' => ($issue_count == 0) ? $this->language->get("text_check_{$key}_true") : sprintf($this->language->get("text_check_{$key}_false"), $issue_count),
				'seo_score' => $seo_score,
				'report_button' => true
			];

			$total_seo_scores += $seo_score;
			$check_count++;
		}
	
		// Additional static checks
		$static_checks = [
			'canonical'  => $this->getConfigValue('hb_canonical_status', $store_id) ? 0 : $total_items / 2,
			'hreflang'   => (count($this->model_localisation_language->getLanguages()) > 1) ? (($this->getConfigValue('hb_seourl_hreflang', $store_id)) ? 0 : $total_items) : 0,
			'index_follow' => ($this->getConfigValue('hb_crawl_status', $store_id) && $this->getConfigValue('hb_crawl_product', $store_id) == 'index, follow') ? 0 : $total_items,
		];
	
		foreach ($static_checks as $key => $issue_count) {
			$seo_score = $this->model_extension_hbseo_hb_seoreport->calculateSeoScore($issue_count, $total_items);
			$data['checklist'][$key] = [
				'title'    => $this->language->get("text_{$key}"),
				'status'   => ($issue_count == 0) ? true : false,
				'analysis' => ($issue_count == 0) ? $this->language->get("text_check_{$key}_true") : sprintf($this->language->get("text_check_{$key}_false"), $issue_count),
				'seo_score' => $seo_score,
				'report_button' => false
			];

			$total_seo_scores += $seo_score;
			$check_count++;
		}

		// Calculate total SEO score
		$data['total_seo_score'] = $check_count > 0 ? round($total_seo_scores / $check_count, 2) : 0;

		$this->session->data['manufacturer_page_seo'.$store_id] = $data['total_seo_score'];

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_seoreport_page_report', $data));
	}

	public function information() {
		$data['page_type'] = 'information';
		$this->load->model('localisation/language');
		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : 0;

		$total_items = $this->model_extension_hbseo_hb_seoreport->getTotalItems('information');
		$data['total_items'] = $total_items;
		
		$checks = [
			'description'      => ['information_description', 'description', 300, false],
			'meta_title'       => ['information_description', 'meta_title', 50, 60],
			'meta_description' => ['information_description', 'meta_description', 150, 160],
			'meta_keyword'     => ['information_description', 'meta_keyword', 50, false],
		];

		$total_seo_scores = 0;
		$check_count = 0;
	
		foreach ($checks as $key => $params) {
			$issue_count = is_array($params)
				? $this->model_extension_hbseo_hb_seoreport->getItemCountByCharacterLimit($total_items, ...$params)
				: $params; // Handle custom counts (e.g., `seo_image`)
			
			$seo_score = $this->model_extension_hbseo_hb_seoreport->calculateSeoScore($issue_count, $total_items);
			$data['checklist'][$key] = [
				'title'    => $this->language->get("text_{$key}"),
				'status'   => ($issue_count == 0) ? true : false,
				'analysis' => ($issue_count == 0) ? $this->language->get("text_check_{$key}_true") : sprintf($this->language->get("text_check_{$key}_false"), $issue_count),
				'seo_score' => $seo_score,
				'report_button' => true
			];

			$total_seo_scores += $seo_score;
			$check_count++;
		}
	
		// Additional static checks
		$static_checks = [
			'canonical'  => $this->getConfigValue('hb_canonical_status', $store_id) ? 0 : $total_items / 2,
			'hreflang'   => (count($this->model_localisation_language->getLanguages()) > 1) ? (($this->getConfigValue('hb_seourl_hreflang', $store_id)) ? 0 : $total_items) : 0,
			'redirect_disabled' => $this->model_extension_hbseo_hb_seoreport->tableExists('redirect_disabled_information')
				? $this->model_extension_hbseo_hb_seoreport->getTotalEmptyRedirectItems('information')
				: $this->model_extension_hbseo_hb_seoreport->getTotalDisabledItems('information'),
			'index_follow' => ($this->getConfigValue('hb_crawl_status', $store_id) && $this->getConfigValue('hb_crawl_information', $store_id) == 'index, follow') ? 0 : $total_items,
		];
	
		foreach ($static_checks as $key => $issue_count) {
			$seo_score = $this->model_extension_hbseo_hb_seoreport->calculateSeoScore($issue_count, $total_items);
			$data['checklist'][$key] = [
				'title'    => $this->language->get("text_{$key}"),
				'status'   => ($issue_count == 0) ? true : false,
				'analysis' => ($issue_count == 0) ? $this->language->get("text_check_{$key}_true") : sprintf($this->language->get("text_check_{$key}_false"), $issue_count),
				'seo_score' => $seo_score,
				'report_button' => false
			];

			$total_seo_scores += $seo_score;
			$check_count++;
		}

		// Calculate total SEO score
		$data['total_seo_score'] = $check_count > 0 ? round($total_seo_scores / $check_count, 2) : 0;

		$this->session->data['information_page_seo'.$store_id] = $data['total_seo_score'];

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_seoreport_page_report', $data));
	}

	public function view_report() {
		$store_id = isset($this->request->get['store_id']) ? (int)$this->request->get['store_id'] : 0;

		$total_items = $this->model_extension_hbseo_hb_seoreport->getTotalItems($this->request->get['page_type']);

		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = 20;
	
		$data = [
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
			'page_type' => $this->request->get['page_type'] ?? 'product',
			'column' => $this->request->get['key'] ?? 'meta_title'
		];
	
		$pageTypeMapping = [
			'product' => ['route' => 'catalog/product/edit', 'id' => 'product_id'],
			'category' => ['route' => 'catalog/category/edit', 'id' => 'category_id'],
			'manufacturer' => ['route' => 'catalog/manufacturer/edit', 'id' => 'manufacturer_id'],
			'information' => ['route' => 'catalog/information/edit', 'id' => 'information_id'],
		];
	
		$columnSettings = [
			'description' => ['minLimit' => ($data['page_type'] == 'product') ? 300 : 250, 'maxLimit' => false],
			'meta_title' => ['minLimit' => 50, 'maxLimit' => 60],
			'meta_description' => ['minLimit' => 150, 'maxLimit' => 160],
			'meta_keyword' => ['minLimit' => 50, 'maxLimit' => false],
			'h1' => ['minLimit' => 20, 'maxLimit' => 70],
			'h2' => ['minLimit' => 20, 'maxLimit' => 60],
			'image_alt' => ['minLimit' => 5, 'maxLimit' => 125],
			'image_title' => ['minLimit' => 10, 'maxLimit' => 70],
			'tag' => ['minLimit' => 10, 'maxLimit' => false],
			'seo_image' => ['table' => 'product'],
		];
	
		// Set page type specific values
		$pageType = $pageTypeMapping[$data['page_type']] ?? $pageTypeMapping['product'];
		$edit_route = $pageType['route'];
		$edit_id = $pageType['id'];
	
		// Set column-specific settings
		$columnSetting = $columnSettings[$data['column']] ?? $columnSettings['meta_title'];
		$data['minLimit'] = $columnSetting['minLimit'] ?? null;
		$data['maxLimit'] = $columnSetting['maxLimit'] ?? null;

		if ($this->model_extension_hbseo_hb_seoreport->tableExists('manufacturer_description')) {
			$data['table'] = $columnSetting['table'] ?? $data['page_type'] . '_description';
		} else {
			$data['table'] = $columnSetting['table'] ?? ($data['page_type'] == 'manufacturer' ? 'manufacturer' : $data['page_type'] . '_description');
		}
	
		// Fetch report data
		if ($data['column'] == 'seo_image') {
			$reports_total = $this->model_extension_hbseo_hb_seoreport->getTotalInvalidImageNames($data['page_type']);
			$records = $this->model_extension_hbseo_hb_seoreport->getInvalidImageNames($data);
		} else {
			$reports_total = $this->model_extension_hbseo_hb_seoreport->getItemCountByCharacterLimit($total_items, $data['table'], $data['column'], $data['minLimit'], $data['maxLimit']);
			$records = $this->model_extension_hbseo_hb_seoreport->getItemsByCharacterLimit($data);
		}
	
		$data['records'] = array_map(function ($record) use ($edit_route, $edit_id) {
			return [
				'id' => $record['id'],
				'name' => $record['name'],
				'value' => html_entity_decode($record['value'], ENT_QUOTES, 'UTF-8'),
				'characters' => $record['characters'],
				'edit' => $this->url->link($edit_route, 'user_token=' . $this->session->data['user_token'] . '&' . $edit_id . '=' . $record['id'], true),
			];
		}, $records);
	
		// Setup pagination
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/hbseo/hb_seoreport/view_report', 'user_token=' . $this->session->data['user_token'] . '&page_type='.$data['page_type'].'&key='.$data['column'].'&page={page}', true);
	
		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));
	
		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_seoreport_items', $data));
	}
	
	private function getConfigValue($key, $store_id = 0) {    
        $query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key` = '" . $this->db->escape($key) . "' AND `store_id` = '" . (int)$store_id . "'");
    
        if ($query->num_rows > 0) {
            return $query->row['value'];
        }
    
        return false;
    }

	public function install(){
		$this->model_extension_hbseo_hb_seoreport->install();
	}
	
	public function uninstall(){
		$this->model_extension_hbseo_hb_seoreport->uninstall();
	}
		
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/hbseo/hb_seoreport')) {
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