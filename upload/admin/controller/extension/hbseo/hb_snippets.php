<?php
class ControllerExtensionHbseoHbSnippets extends Controller {	
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
		
		$this->hb_extension_version	= '3.10.1';	
		$this->doc_link = 'https://www.huntbee.com/resources/docs/seo-structured-data/';

		$this->load->model('extension/hbseo/hb_snippets');		
		$this->load->language($this->hb_extension_route.'/hb_snippets');
	}
	
	public function index() {   
		$data['extension_version'] = $this->hb_extension_version;

		$data['store_id'] = (isset($this->request->get['store_id']))? (int)$this->request->get['store_id']:0;
		
		$this->load->language($this->hb_extension_route.'/hb_snippets');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_snippets', $this->request->post, $this->request->get['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_snippets', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$text_strings = array(
				'heading_title',
				'tab_sd','tab_contact','tab_og','tab_tc',
				'button_save',
				'button_cancel','button_remove',
				'btn_generate'
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}
	
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['logo'])) {
			$data['error_logo'] = $this->error['logo'];
		} else {
			$data['error_logo'] = '';
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
			'href'      => $this->url->link($this->hb_extension_route.'/hb_snippets', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_snippets', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;
		
		$data['doc_link'] = $this->doc_link;

		$this->load->model('localisation/stock_status');
		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
		
		$data['availability'] = ['InStock','InStoreOnly','LimitedAvailability','OnlineOnly','OutOfStock','PreOrder','PreSale','SoldOut','BackOrder','Discontinued'];
		
		$store_info = $this->model_setting_setting->getSetting('hb_snippets', $this->request->get['store_id']);
				
		if (isset($this->request->post['hb_snippets_logo'])) {
			$data['hb_snippets_logo'] = $this->request->post['hb_snippets_logo'];
		} elseif (isset($store_info['hb_snippets_logo'])) {
			$data['hb_snippets_logo'] = $store_info['hb_snippets_logo'];
		} else {
			$data['hb_snippets_logo'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['hb_snippets_logo']) && is_file(DIR_IMAGE . $this->request->post['hb_snippets_logo'])) {
			$data['logo_thumb'] = $this->model_tool_image->resize($this->request->post['hb_snippets_logo'], 250, 100);
		} elseif (isset($store_info['hb_snippets_logo']) && is_file(DIR_IMAGE . $store_info['hb_snippets_logo'])) {
			$data['logo_thumb'] = $this->model_tool_image->resize($store_info['hb_snippets_logo'], 250, 100);
		} else {
			$data['logo_thumb'] = $this->model_tool_image->resize('no_image.png', 250, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 250, 100);

		$data['hb_snippets_prod_enable'] 	= isset($store_info['hb_snippets_prod_enable'])? $store_info['hb_snippets_prod_enable']:'';
		$data['hb_snippets_description'] 	= isset($store_info['hb_snippets_description'])? $store_info['hb_snippets_description']:'meta_description';
		$data['hb_snippets_incl_tax'] 		= isset($store_info['hb_snippets_incl_tax'])? $store_info['hb_snippets_incl_tax']:'';
		$data['hb_snippets_pricevalid'] 	= isset($store_info['hb_snippets_pricevalid'])? $store_info['hb_snippets_pricevalid']:'';
		$data['hb_snippets_pricevaliddate'] = isset($store_info['hb_snippets_pricevaliddate'])? $store_info['hb_snippets_pricevaliddate']:date('Y-m-d', strtotime('+3 years'));
		$data['hb_snippets_brand'] 			= isset($store_info['hb_snippets_brand'])? $store_info['hb_snippets_brand']:'';
		$data['hb_snippets_stock'] 			= isset($store_info['hb_snippets_stock'])? $store_info['hb_snippets_stock'] : [];
		$data['hb_snippets_bc_enable'] 		= isset($store_info['hb_snippets_bc_enable'])? $store_info['hb_snippets_bc_enable']:'';
		$data['hb_snippets_bc_type'] 		= isset($extn_info['hb_snippets_bc_type'])? $extn_info['hb_snippets_bc_type']:'smart';
		$data['hb_snippets_list_enable'] 	= isset($store_info['hb_snippets_list_enable'])? $store_info['hb_snippets_list_enable']:'';

		//shipping
		$data['hb_snippets_shipping'] 		= isset($store_info['hb_snippets_shipping'])? $store_info['hb_snippets_shipping']:'';
		$data['hb_snippets_shipping_rules'] = isset($store_info['hb_snippets_shipping_rules'])?$store_info['hb_snippets_shipping_rules']:[];	

		//return
		$data['hb_snippets_return'] 		= isset($store_info['hb_snippets_return'])? $store_info['hb_snippets_return']:'';
		$data['hb_snippets_return_rules'] 	= isset($store_info['hb_snippets_return_rules'])?$store_info['hb_snippets_return_rules']:[];

		//image metadata
		$data['hb_snippets_img_enable'] 	= isset($store_info['hb_snippets_img_enable'])? $store_info['hb_snippets_img_enable']:'';
		$data['hb_snippets_img_license'] 	= isset($store_info['hb_snippets_img_license'])? $store_info['hb_snippets_img_license']:'';
		$data['hb_snippets_img_acquire'] 	= isset($store_info['hb_snippets_img_acquire'])? $store_info['hb_snippets_img_acquire']:'';
		$data['hb_snippets_img_credit'] 	= isset($store_info['hb_snippets_img_credit'])? $store_info['hb_snippets_img_credit']: $this->config->get('config_owner');
		$data['hb_snippets_img_creator'] 	= isset($store_info['hb_snippets_img_creator'])? $store_info['hb_snippets_img_creator']:$this->config->get('config_name');
		$data['hb_snippets_img_copyright'] 	= isset($store_info['hb_snippets_img_copyright'])? $store_info['hb_snippets_img_copyright']:'';

		$data['hb_snippets_contact'] 		= isset($store_info['hb_snippets_contact'])?$store_info['hb_snippets_contact']:[];
		$data['hb_snippets_emails'] 		= isset($store_info['hb_snippets_emails'])?$store_info['hb_snippets_emails']:[];
		$data['hb_snippets_socials'] 		= isset($store_info['hb_snippets_socials'])?$store_info['hb_snippets_socials']:[];	
		$data['hb_snippets_search_enable'] 	= isset($store_info['hb_snippets_search_enable'])?$store_info['hb_snippets_search_enable']:'';
		
		$data['hb_snippets_kg_enable'] 	= isset($store_info['hb_snippets_kg_enable'])?$store_info['hb_snippets_kg_enable']:'';	
		$data['hb_snippets_vat'] 		= isset($store_info['hb_snippets_vat'])?$store_info['hb_snippets_vat']:'';	
		$data['hb_snippets_payment'] 	= isset($store_info['hb_snippets_payment'])?$store_info['hb_snippets_payment']:['PayPal','Visa', 'MasterCard', 'Stripe'];
		
		$data['hb_snippets_og_enable'] 	= isset($store_info['hb_snippets_og_enable'])?$store_info['hb_snippets_og_enable']:'';
		$data['hb_snippets_og_id']		= isset($store_info['hb_snippets_og_id'])?$store_info['hb_snippets_og_id']:'';
		$data['hb_snippets_ogp'] 		= isset($store_info['hb_snippets_ogp'])?$store_info['hb_snippets_ogp']:'';
		$data['hb_snippets_ogc'] 		= isset($store_info['hb_snippets_ogc'])?$store_info['hb_snippets_ogc']:'';
		$data['hb_snippets_og_diw'] 	= isset($store_info['hb_snippets_og_ciw'])?$store_info['hb_snippets_og_diw']:'820';
		$data['hb_snippets_og_dih'] 	= isset($store_info['hb_snippets_og_cih'])?$store_info['hb_snippets_og_dih']:'312';
		$data['hb_snippets_og_piw'] 	= isset($store_info['hb_snippets_og_piw'])?$store_info['hb_snippets_og_piw']:'500';
		$data['hb_snippets_og_pih'] 	= isset($store_info['hb_snippets_og_pih'])?$store_info['hb_snippets_og_pih']:'500';
		$data['hb_snippets_og_ciw'] 	= isset($store_info['hb_snippets_og_ciw'])?$store_info['hb_snippets_og_ciw']:'500';
		$data['hb_snippets_og_cih'] 	= isset($store_info['hb_snippets_og_cih'])?$store_info['hb_snippets_og_cih']:'500';
				
		$data['hb_snippets_og_img'] = isset($store_info['hb_snippets_og_img'])?$store_info['hb_snippets_og_img']:'';
		
		if (isset($data['hb_snippets_og_img']) && is_file(DIR_IMAGE . $data['hb_snippets_og_img'])) {
			$data['ogimg'] = $this->model_tool_image->resize($data['hb_snippets_og_img'], 340, 126);
		} else {
			$data['ogimg'] = $this->model_tool_image->resize('no_image.png', 340, 126);
		}	
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 340, 126);
		
		$data['hb_snippets_tc_enable'] 		= isset($store_info['hb_snippets_tc_enable'])?$store_info['hb_snippets_tc_enable']:'';
		$data['hb_snippets_tc_username'] 	= isset($store_info['hb_snippets_tc_username'])?$store_info['hb_snippets_tc_username']:'';
		$data['hb_snippets_tcp'] 			= isset($store_info['hb_snippets_tcp'])?$store_info['hb_snippets_tcp']:'';
		$data['hb_snippets_tcc'] 			= isset($store_info['hb_snippets_tcc'])?$store_info['hb_snippets_tcc']:'';
		
		$data['hb_snippets_local_name'] 	= isset($store_info['hb_snippets_local_name'])?$store_info['hb_snippets_local_name']: $this->config->get('config_name');
		$data['hb_snippets_local_st'] 		= isset($store_info['hb_snippets_local_st'])?$store_info['hb_snippets_local_st']:	$this->config->get('config_address');
		$data['hb_snippets_local_location'] = isset($store_info['hb_snippets_local_location'])?$store_info['hb_snippets_local_location']: $this->config->get('config_city');
		$data['hb_snippets_local_state'] 	= isset($store_info['hb_snippets_local_state'])?$store_info['hb_snippets_local_state']:'NY';	
		$data['hb_snippets_local_postal'] 	= isset($store_info['hb_snippets_local_postal'])?$store_info['hb_snippets_local_postal']:	$this->config->get('config_postcode');
		$data['hb_snippets_local_country'] 	= isset($store_info['hb_snippets_local_country'])?$store_info['hb_snippets_local_country']:'US';
		$data['hb_snippets_store_image'] 	= isset($store_info['hb_snippets_store_image'])?$store_info['hb_snippets_store_image']:'';
		$data['hb_snippets_price_range'] 	= isset($store_info['hb_snippets_price_range'])?$store_info['hb_snippets_price_range']:'';
		$data['hb_snippets_local_snippet'] 	= isset($store_info['hb_snippets_local_snippet'])?$store_info['hb_snippets_local_snippet']:'';
		$data['hb_snippets_local_enable'] 	= isset($store_info['hb_snippets_local_enable'])?$store_info['hb_snippets_local_enable']:'';	
					
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_snippets'.$this->hb_template_extension, $data));
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_snippets')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
				
		if ($this->request->post['hb_snippets_logo']) {
			$image = DIR_IMAGE.$this->request->post['hb_snippets_logo'];
			list($width, $height) = getimagesize($image);
			if ($width < 112 or $height < 112) {
				$this->error['logo'] = $this->language->get('error_logo');
				$this->error['warning'] = $this->language->get('error_logo');
			}
		}

		if (isset($this->request->post['hb_snippets_img_enable'])) {
			if (empty(trim($this->request->post['hb_snippets_img_license'])) || empty(trim($this->request->post['hb_snippets_img_acquire'])) || empty(trim($this->request->post['hb_snippets_img_credit'])) || empty(trim($this->request->post['hb_snippets_img_creator'])) || empty(trim($this->request->post['hb_snippets_img_copyright']))){
				$this->error['warning'] = $this->language->get('error_image_metadata');
			}			
		}

		if (isset($this->request->post['hb_snippets_shipping_rules'])) {
			$rules = $this->request->post['hb_snippets_shipping_rules'];
			$pattern = '/^\d+-\d+:[A-Z]{2}(?:-[a-zA-Z\s]+)?:\d+:[A-Z]{3}:\d+-\d+:\d+-\d+$/';
	
			foreach ($rules as $rule) {
				$rule = trim($rule);
				if (!preg_match($pattern, $rule)) {
					$this->error['warning'] = $this->language->get('error_shipping_rule');
					break;
				}
			}			
		}		
		
		if (isset($this->request->post['hb_snippets_return_rules'])) {
			$rules = $this->request->post['hb_snippets_return_rules'];
			
			// Define the regex pattern to validate the return rule format
			// This pattern supports both MRFRW (with additional fields) and MRNP (without additional fields)
			$pattern = '/^[A-Z]{2}:(MRFRW:(\d{1,3}:(RBM|RTK|RIS):(RFCR|FR|RSF)(?::\d+:\w{3})?)?|MRNP)$/';
		
			foreach ($rules as $rule) {
				$rule = trim($rule);
				
				// If the rule doesn't match the pattern, set an error
				if (!preg_match($pattern, $rule)) {
					$this->error['warning'] = $this->language->get('error_return_rule');
					break; // Stop on first error
				}
			}            
		}		
		
		if (empty(trim($this->request->post['hb_snippets_local_name'])) && empty(trim($this->request->post['hb_snippets_local_st'])) && empty(trim($this->request->post['hb_snippets_local_location'])) && empty(trim($this->request->post['hb_snippets_local_postal'])) && empty(trim($this->request->post['hb_snippets_local_country'])) && empty(trim($this->request->post['hb_snippets_store_image'])) && empty(trim($this->request->post['hb_snippets_price_range']))){
			$this->error['warning'] = $this->language->get('error_local');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
	
	public function generatelocalsnippet(){
		$json = [];

		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_snippets')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json){
			$data['store_id'] = (int)$this->request->get['store_id'];
		
			$name = $this->request->post['name'];
			$street = $this->request->post['street'];
			$location = $this->request->post['location'];
			$postal = $this->request->post['postal'];
			
			$phone= $this->config->get('config_telephone');
			$country = $this->request->post['country'];
			$store_image = $this->request->post['store_image'];
			$price_range = $this->request->post['price_range'];
			$state = $this->request->post['state'];
			
			if ($data['store_id'] == 0){
				$store_url = HTTPS_CATALOG;
			}else{
				$store = $this->db->query("SELECT `url` FROM ".DB_PREFIX."store WHERE store_id = '".$data['store_id']."'");
				$store_url = $store->row['url'];
			}
					
			$code = '<script type="application/ld+json">
{
	"@context": "https://schema.org",
	"@type": "Store",
	"@id": "'.$store_url.'",
	"image": "'.$store_image.'",
	"name": "'.$name.'",
	"address": {
		"@type": "PostalAddress",
		"streetAddress": "'.$street.'",
		"addressLocality": "'.$location.'",
		"addressRegion": "'.$state.'",
		"postalCode": "'.$postal.'",
		"addressCountry": "'.$country.'"
	},
	"telephone": "'.$phone.'",
	"priceRange": "'.$price_range.'"
}
</script>';
			$json['success'] = $code;
		}
		

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function install() {
		$this->model_extension_hbseo_hb_snippets->install();
	}
	
	public function uninstall() {
		$this->model_extension_hbseo_hb_snippets->uninstall();
	}

	public function update(){
		$this->model_extension_hbseo_hb_snippets->update();
		return true;
	}
}
?>