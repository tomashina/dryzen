<?php
class ControllerMpGdprMpGdpr extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('mpgdpr/mpgdpr');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('view/stylesheet/mpgdpr/mpgdpr.css');

		$this->document->addStyle('view/javascript/mpgdpr/colorpicker/css/bootstrap-colorpicker.css');
		$this->document->addScript('view/javascript/mpgdpr/colorpicker/js/bootstrap-colorpicker.js');

		// run table installer
		$this->mpgdpr->install();

		$this->load->model('setting/setting');
		if(isset($this->request->get['store_id'])) {
			$store_id = $data['store_id'] = $this->request->get['store_id'];
		} else {
			$store_id = $data['store_id'] = 0;
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('mpgdpr', $this->request->post, $store_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('mpgdpr/mpgdpr', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . '&store_id=' . $store_id, $this->mpgdpr->ssl));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_default_page'] = $this->language->get('text_default_page');
		$data['text_store'] = $this->language->get('text_store');
		$data['text_access_personaldata'] = $this->language->get('text_access_personaldata');
		$data['text_acceptpolicy_gdpr'] = $this->language->get('text_acceptpolicy_gdpr');


		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_maxrequests'] = $this->language->get('entry_maxrequests');
		$data['entry_acceptpolicy_customer'] = $this->language->get('entry_acceptpolicy_customer');
		$data['entry_policy_customer'] = $this->language->get('entry_policy_customer');
		$data['entry_acceptpolicy_contactus'] = $this->language->get('entry_acceptpolicy_contactus');
		$data['entry_policy_contactus'] = $this->language->get('entry_policy_contactus');
		$data['entry_acceptpolicy_checkout'] = $this->language->get('entry_acceptpolicy_checkout');
		$data['entry_policy_checkout'] = $this->language->get('entry_policy_checkout');
		$data['entry_hasright_todelete'] = $this->language->get('entry_hasright_todelete');
		$data['entry_login_gdprforms'] = $this->language->get('entry_login_gdprforms');
		$data['entry_captcha_gdprforms'] = $this->language->get('entry_captcha_gdprforms');
		$data['entry_captcha'] = $this->language->get('entry_captcha');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_locationservices'] = $this->language->get('entry_locationservices');
		$data['entry_otherservices'] = $this->language->get('entry_otherservices');
		$data['entry_requestget_personaldata'] = $this->language->get('entry_requestget_personaldata');
		$data['entry_requestdelete_personaldata'] = $this->language->get('entry_requestdelete_personaldata');
		$data['entry_file_ext_allowed'] = $this->language->get('entry_file_ext_allowed');
		$data['entry_file_mime_allowed'] = $this->language->get('entry_file_mime_allowed');

		$data['entry_cbstatus'] = $this->language->get('entry_cbstatus');
		$data['entry_cbpolicy'] = $this->language->get('entry_cbpolicy');
		$data['entry_cbpolicy_page'] = $this->language->get('entry_cbpolicy_page');

		$data['entry_cbinitial'] = $this->language->get('entry_cbinitial');
		$data['entry_cbaction_close'] = $this->language->get('entry_cbaction_close');
		$data['entry_cbshowagain'] = $this->language->get('entry_cbshowagain');
		$data['entry_cbpptrack'] = $this->language->get('entry_cbpptrack');
		$data['entry_cookie_stricklyrequired'] = $this->language->get('entry_cookie_stricklyrequired');
		$data['entry_cookie_analytics'] = $this->language->get('entry_cookie_analytics');
		$data['entry_cookie_marketing'] = $this->language->get('entry_cookie_marketing');
		$data['entry_cookie_domain'] = $this->language->get('entry_cookie_domain');
		$data['entry_cookielanguage'] = $this->language->get('entry_cookielanguage');
		$data['entry_cookietext_msg'] = $this->language->get('entry_cookietext_msg');
		$data['entry_cookietext_policy'] = $this->language->get('entry_cookietext_policy');
		$data['entry_cookiebtn_accept'] = $this->language->get('entry_cookiebtn_accept');
		$data['entry_cookiebtn_deny'] = $this->language->get('entry_cookiebtn_deny');
		$data['entry_cookiebtn_prefrence'] = $this->language->get('entry_cookiebtn_prefrence');
		$data['entry_cookiebtn_showagain'] = $this->language->get('entry_cookiebtn_showagain');
		$data['entry_cbposition'] = $this->language->get('entry_cbposition');
		$data['entry_cbcolors'] = $this->language->get('entry_cbcolors');
		$data['entry_cbboxbg'] = $this->language->get('entry_cbboxbg');
		$data['entry_cbboxtext'] = $this->language->get('entry_cbboxtext');
		$data['entry_cbbtnbg'] = $this->language->get('entry_cbbtnbg');
		$data['entry_cbbtntext'] = $this->language->get('entry_cbbtntext');
		$data['entry_cbcss'] = $this->language->get('entry_cbcss');

		$data['help_status'] = $this->language->get('help_status');
		$data['help_maxrequests'] = $this->language->get('help_maxrequests');
		$data['help_acceptpolicy_customer'] = $this->language->get('help_acceptpolicy_customer');
		$data['help_policy_customer'] = $this->language->get('help_policy_customer');
		$data['help_acceptpolicy_contactus'] = $this->language->get('help_acceptpolicy_contactus');
		$data['help_policy_contactus'] = $this->language->get('help_policy_contactus');
		$data['help_acceptpolicy_checkout'] = $this->language->get('help_acceptpolicy_checkout');
		$data['help_policy_checkout'] = $this->language->get('help_policy_checkout');
		$data['help_hasright_todelete'] = $this->language->get('help_hasright_todelete');
		$data['help_login_gdprforms'] = $this->language->get('help_login_gdprforms');
		$data['help_captcha_gdprforms'] = $this->language->get('help_captcha_gdprforms');
		$data['help_captcha'] = $this->language->get('help_captcha');
		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['help_locationservices'] = $this->language->get('help_locationservices');
		$data['help_otherservices'] = $this->language->get('help_otherservices');
		$data['help_access_personaldata'] = $this->language->get('help_access_personaldata');
		$data['help_requestget_personaldata'] = $this->language->get('help_requestget_personaldata');
		$data['help_requestdelete_personaldata'] = $this->language->get('help_requestdelete_personaldata');
		$data['help_file_ext_allowed'] = $this->language->get('help_file_ext_allowed');
		$data['help_file_mime_allowed'] = $this->language->get('help_file_mime_allowed');

		$data['help_cbstatus'] = $this->language->get('help_cbstatus');
		$data['help_cbpolicy'] = $this->language->get('help_cbpolicy');
		$data['help_cbpolicy_page'] = $this->language->get('help_cbpolicy_page');
		$data['help_cbinitial'] = $this->language->get('help_cbinitial');
		$data['help_cbaction_close'] = $this->language->get('help_cbaction_close');
		$data['help_cbshowagain'] = $this->language->get('help_cbshowagain');
		$data['help_cbpptrack'] = $this->language->get('help_cbpptrack');
		$data['help_cookie_stricklyrequired'] = $this->language->get('help_cookie_stricklyrequired');
		$data['help_cookie_analytics'] = $this->language->get('help_cookie_analytics');
		$data['help_cookie_marketing'] = $this->language->get('help_cookie_marketing');
		$data['help_cookie_domain'] = $this->language->get('help_cookie_domain');
		$data['help_cookielanguage'] = $this->language->get('help_cookielanguage');
		$data['help_cookietext_msg'] = $this->language->get('help_cookietext_msg');
		$data['help_cookietext_policy'] = $this->language->get('help_cookietext_policy');
		$data['help_cookiebtn_accept'] = $this->language->get('help_cookiebtn_accept');
		$data['help_cookiebtn_deny'] = $this->language->get('help_cookiebtn_deny');
		$data['help_cookiebtn_prefrence'] = $this->language->get('help_cookiebtn_prefrence');
		$data['help_cookiebtn_showagain'] = $this->language->get('help_cookiebtn_showagain');
		$data['help_cbposition'] = $this->language->get('help_cbposition');
		$data['help_cbcolors'] = $this->language->get('help_cbcolors');
		$data['help_cbboxbg'] = $this->language->get('help_cbboxbg');
		$data['help_cbboxtext'] = $this->language->get('help_cbboxtext');
		$data['help_cbbtnbg'] = $this->language->get('help_cbbtnbg');
		$data['help_cbbtntext'] = $this->language->get('help_cbbtntext');
		$data['help_cbcss'] = $this->language->get('help_cbcss');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_settings'] = $this->language->get('tab_settings');
		$data['tab_cookieconsent'] = $this->language->get('tab_cookieconsent');
		$data['tab_modulepoints'] = $this->language->get('tab_modulepoints');
		$data['tab_other'] = $this->language->get('tab_other');

		$data['legend_general'] = $this->language->get('legend_general');
		$data['legend_captcha'] = $this->language->get('legend_captcha');
		$data['legend_upload'] = $this->language->get('legend_upload');
		$data['legend_requesttimeout'] = $this->language->get('legend_requesttimeout');
		$data['legend_cookiemanager'] = $this->language->get('legend_cookiemanager');
		$data['legend_language'] = $this->language->get('legend_language');

		$data['button_save'] = $this->language->get('button_save');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token], $this->mpgdpr->ssl)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('mpgdpr/mpgdpr', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token], $this->mpgdpr->ssl)
		);

		$data['action'] = $this->url->link('mpgdpr/mpgdpr', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . '&store_id=' . $store_id, $this->mpgdpr->ssl);
		$data['token'] = $this->session->data[$this->mpgdpr->token];
		$data['var_token'] = $this->mpgdpr->token;

		$data['stores'] = array();
		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();
		$data['stores'][] = array(
			'name' => $this->language->get('text_default'),
			'store_id' => 0,
		);
		$data['store_name'] = $this->language->get('text_default');
		foreach ($stores as $store) {
			$data['stores'][] = array(
				'name' => $store['name'],
				'store_id' => $store['store_id'],
			);
			if($store['store_id'] == $store_id) {
				$data['store_name'] = $store['name'];
			}
		}



		$this->load->model('catalog/information');

		$information_pages = $this->model_catalog_information->getInformations();

		$data['information_pages'] = array();

		foreach ($information_pages as $information_page) {
			$data['information_pages'][] = array(
				'information_id' => $information_page['information_id'],
				'title' => $information_page['title'],
			);
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->mpgdpr->getLanguages($this->model_localisation_language->getLanguages());

		if(VERSION >= '3.0.0.0') {
			$prefix_captcha_module = 'captcha_';
			$this->load->model('setting/extension');
			$model_extension_string = 'model_setting_extension';
		} else {
			$prefix_captcha_module = '';
			$this->load->model('extension/extension');
			$model_extension_string = 'model_extension_extension';
		}

		$data['captchas'] = array();


		if(VERSION >= '2.1.0.1') {
			// Get a list of installed captchas
			$extensions = $this->{$model_extension_string}->getInstalled('captcha');

			foreach ($extensions as $code) {

				$capthca_path_lang = 'captcha/' . $code;

				if(VERSION > '2.2.0.0') {
					$capthca_path_lang = 'extension/' . $capthca_path_lang;
				}

				$this->load->language($capthca_path_lang);

				if ($this->config->get($prefix_captcha_module.$code . '_status')) {
					$data['captchas'][] = array(
						'text'  => $this->language->get('heading_title'),
						'value' => $code
					);
				}
			}
		} else {
			if(VERSION == '2.0.2.0') {
				if($this->config->get('config_google_captcha_status')) {
					$data['captchas'][] = array(
						'text'  => $this->language->get('text_oc_captcha'),
						'value' => 'oc_captcha'
					);
				}
			}
			if(VERSION < '2.0.2.0') {
				$data['captchas'][] = array(
					'text'  => $this->language->get('text_oc_captcha'),
					'value' => 'oc_captcha'
				);
			}
		}

		$module = $this->model_setting_setting->getSetting('mpgdpr', $store_id);


		if (isset($this->request->post['mpgdpr_status'])) {
			$data['mpgdpr_status'] = $this->request->post['mpgdpr_status'];
		} elseif(isset($module['mpgdpr_status'])) {
			$data['mpgdpr_status'] = $module['mpgdpr_status'];
		} else {
			$data['mpgdpr_status'] = 0;
		}

		if (isset($this->request->post['mpgdpr_acceptpolicy_customer'])) {
			$data['mpgdpr_acceptpolicy_customer'] = $this->request->post['mpgdpr_acceptpolicy_customer'];
		} elseif(isset($module['mpgdpr_acceptpolicy_customer'])) {
			$data['mpgdpr_acceptpolicy_customer'] = $module['mpgdpr_acceptpolicy_customer'];
		} else {
			$data['mpgdpr_acceptpolicy_customer'] = 0;
		}

		if (isset($this->request->post['mpgdpr_policy_customer'])) {
			$data['mpgdpr_policy_customer'] = $this->request->post['mpgdpr_policy_customer'];
		} elseif(isset($module['mpgdpr_policy_customer'])) {
			$data['mpgdpr_policy_customer'] = $module['mpgdpr_policy_customer'];
		} else {
			$data['mpgdpr_policy_customer'] = 0;
		}

		if (isset($this->request->post['mpgdpr_acceptpolicy_contactus'])) {
			$data['mpgdpr_acceptpolicy_contactus'] = $this->request->post['mpgdpr_acceptpolicy_contactus'];
		} elseif(isset($module['mpgdpr_acceptpolicy_contactus'])) {
			$data['mpgdpr_acceptpolicy_contactus'] = $module['mpgdpr_acceptpolicy_contactus'];
		} else {
			$data['mpgdpr_acceptpolicy_contactus'] = 0;
		}

		if (isset($this->request->post['mpgdpr_policy_contactus'])) {
			$data['mpgdpr_policy_contactus'] = $this->request->post['mpgdpr_policy_contactus'];
		} elseif(isset($module['mpgdpr_policy_contactus'])) {
			$data['mpgdpr_policy_contactus'] = $module['mpgdpr_policy_contactus'];
		} else {
			$data['mpgdpr_policy_contactus'] = 0;
		}

		if (isset($this->request->post['mpgdpr_acceptpolicy_checkout'])) {
			$data['mpgdpr_acceptpolicy_checkout'] = $this->request->post['mpgdpr_acceptpolicy_checkout'];
		} elseif(isset($module['mpgdpr_acceptpolicy_checkout'])) {
			$data['mpgdpr_acceptpolicy_checkout'] = $module['mpgdpr_acceptpolicy_checkout'];
		} else {
			$data['mpgdpr_acceptpolicy_checkout'] = 0;
		}

		if (isset($this->request->post['mpgdpr_policy_checkout'])) {
			$data['mpgdpr_policy_checkout'] = $this->request->post['mpgdpr_policy_checkout'];
		} elseif(isset($module['mpgdpr_policy_checkout'])) {
			$data['mpgdpr_policy_checkout'] = $module['mpgdpr_policy_checkout'];
		} else {
			$data['mpgdpr_policy_checkout'] = 0;
		}

		if (isset($this->request->post['mpgdpr_hasright_todelete'])) {
			$data['mpgdpr_hasright_todelete'] = $this->request->post['mpgdpr_hasright_todelete'];
		} elseif(isset($module['mpgdpr_hasright_todelete'])) {
			$data['mpgdpr_hasright_todelete'] = $module['mpgdpr_hasright_todelete'];
		} else {
			$data['mpgdpr_hasright_todelete'] = 0;
		}

		if (isset($this->request->post['mpgdpr_maxrequests'])) {
			$data['mpgdpr_maxrequests'] = $this->request->post['mpgdpr_maxrequests'];
		} elseif(isset($module['mpgdpr_maxrequests'])) {
			$data['mpgdpr_maxrequests'] = $module['mpgdpr_maxrequests'];
		} else {
			$data['mpgdpr_maxrequests'] = 3;
		}
		/*// for 3x versions
		if (isset($this->request->post['mpgdpr_keyword'])) {
			$data['mpgdpr_keyword'] = $this->request->post['mpgdpr_keyword'];
		} elseif(isset($module['mpgdpr_keyword'])) {
			$data['mpgdpr_keyword'] = $module['mpgdpr_keyword'];
		} else {
			$data['mpgdpr_keyword'] = '';
		}
		// for 2x or less version
		if (isset($this->request->post['mpgdpr_keyword'])) {
			$data['mpgdpr_keyword'] = $this->request->post['mpgdpr_keyword'];
		} else {
			$data['mpgdpr_keyword'] = $this->config->get('mpgdpr_keyword');
		}*/

		if (isset($this->request->post['mpgdpr_login_gdprforms'])) {
			$data['mpgdpr_login_gdprforms'] = $this->request->post['mpgdpr_login_gdprforms'];
		} elseif(isset($module['mpgdpr_login_gdprforms'])) {
			$data['mpgdpr_login_gdprforms'] = $module['mpgdpr_login_gdprforms'];
		} else {
			$data['mpgdpr_login_gdprforms'] = 0;
		}

		if (isset($this->request->post['mpgdpr_captcha_gdprforms'])) {
			$data['mpgdpr_captcha_gdprforms'] = $this->request->post['mpgdpr_captcha_gdprforms'];
		} elseif(isset($module['mpgdpr_captcha_gdprforms'])) {
			$data['mpgdpr_captcha_gdprforms'] = $module['mpgdpr_captcha_gdprforms'];
		} else {
			$data['mpgdpr_captcha_gdprforms'] = 0;
		}

		if (isset($this->request->post['mpgdpr_captcha'])) {
			$data['mpgdpr_captcha'] = $this->request->post['mpgdpr_captcha'];
		} elseif(isset($module['mpgdpr_captcha'])) {
			$data['mpgdpr_captcha'] = $module['mpgdpr_captcha'];
		} else {
			$data['mpgdpr_captcha'] = 0;
		}

		if (isset($this->request->post['mpgdpr_services'])) {
			$data['mpgdpr_services'] = $this->request->post['mpgdpr_services'];
		} elseif(isset($module['mpgdpr_services'])) {
			$data['mpgdpr_services'] = (array)$module['mpgdpr_services'];
		} else {
			$data['mpgdpr_services'] = array();
		}

		if (isset($this->request->post['mpgdpr_timeout'])) {
			$data['mpgdpr_timeout'] = $this->request->post['mpgdpr_timeout'];
		} elseif(isset($module['mpgdpr_timeout'])) {
			$data['mpgdpr_timeout'] = (array)$module['mpgdpr_timeout'];
		} else {
			$data['mpgdpr_timeout'] = array();
		}


		if (isset($this->request->post['mpgdpr_file_ext_allowed'])) {
			$data['mpgdpr_file_ext_allowed'] = $this->request->post['mpgdpr_file_ext_allowed'];
		} elseif(isset($module['mpgdpr_file_ext_allowed'])) {
			$data['mpgdpr_file_ext_allowed'] = $module['mpgdpr_file_ext_allowed'];
		} else {
			$data['mpgdpr_file_ext_allowed'] = $this->config->get('config_file_ext_allowed');
		}

		if (isset($this->request->post['mpgdpr_file_mime_allowed'])) {
			$data['mpgdpr_file_mime_allowed'] = $this->request->post['mpgdpr_file_mime_allowed'];
		} elseif(isset($module['mpgdpr_file_mime_allowed'])) {
			$data['mpgdpr_file_mime_allowed'] = $module['mpgdpr_file_mime_allowed'];
		} else {
			$data['mpgdpr_file_mime_allowed'] = $this->config->get('config_file_mime_allowed');
		}

		// cb = cookie bar aka cookie consent bar
		if (isset($this->request->post['mpgdpr_cbstatus'])) {
			$data['mpgdpr_cbstatus'] = $this->request->post['mpgdpr_cbstatus'];
		} elseif(isset($module['mpgdpr_cbstatus'])) {
			$data['mpgdpr_cbstatus'] = $module['mpgdpr_cbstatus'];
		} else {
			$data['mpgdpr_cbstatus'] = 0;
		}

		if (isset($this->request->post['mpgdpr_cbpolicy'])) {
			$data['mpgdpr_cbpolicy'] = $this->request->post['mpgdpr_cbpolicy'];
		} elseif(isset($module['mpgdpr_cbpolicy'])) {
			$data['mpgdpr_cbpolicy'] = $module['mpgdpr_cbpolicy'];
		} else {
			$data['mpgdpr_cbpolicy'] = 0;
		}

		if (isset($this->request->post['mpgdpr_cbpolicy_page'])) {
			$data['mpgdpr_cbpolicy_page'] = $this->request->post['mpgdpr_cbpolicy_page'];
		} elseif(isset($module['mpgdpr_cbpolicy_page'])) {
			$data['mpgdpr_cbpolicy_page'] = $module['mpgdpr_cbpolicy_page'];
		} else {
			$data['mpgdpr_cbpolicy_page'] = 0;
		}


		if (isset($this->request->post['mpgdpr_cbinitial'])) {
			$data['mpgdpr_cbinitial'] = $this->request->post['mpgdpr_cbinitial'];
		} elseif(isset($module['mpgdpr_cbinitial'])) {
			$data['mpgdpr_cbinitial'] = $module['mpgdpr_cbinitial'];
		} else {
			$data['mpgdpr_cbinitial'] = 0;
		}

		if (isset($this->request->post['mpgdpr_cbaction_close'])) {
			$data['mpgdpr_cbaction_close'] = $this->request->post['mpgdpr_cbaction_close'];
		} elseif(isset($module['mpgdpr_cbaction_close'])) {
			$data['mpgdpr_cbaction_close'] = $module['mpgdpr_cbaction_close'];
		} else {
			$data['mpgdpr_cbaction_close'] = 0;
		}

		if (isset($this->request->post['mpgdpr_cbshowagain'])) {
			$data['mpgdpr_cbshowagain'] = $this->request->post['mpgdpr_cbshowagain'];
		} elseif(isset($module['mpgdpr_cbshowagain'])) {
			$data['mpgdpr_cbshowagain'] = $module['mpgdpr_cbshowagain'];
		} else {
			$data['mpgdpr_cbshowagain'] = 0;
		}

		if (isset($this->request->post['mpgdpr_cbpptrack'])) {
			$data['mpgdpr_cbpptrack'] = $this->request->post['mpgdpr_cbpptrack'];
		} elseif(isset($module['mpgdpr_cbpptrack'])) {
			$data['mpgdpr_cbpptrack'] = $module['mpgdpr_cbpptrack'];
		} else {
			$data['mpgdpr_cbpptrack'] = 0;
		}

		if (isset($this->request->post['mpgdpr_cookie_stricklyrequired'])) {
			$data['mpgdpr_cookie_stricklyrequired'] = $this->request->post['mpgdpr_cookie_stricklyrequired'];
		} elseif(isset($module['mpgdpr_cookie_stricklyrequired'])) {
			$data['mpgdpr_cookie_stricklyrequired'] = $module['mpgdpr_cookie_stricklyrequired'];
		} else {
			$data['mpgdpr_cookie_stricklyrequired'] = "PHPSESSID\ndefault \nlanguage \ncurrency \ncookieconsent_status \nmpcookie_preferencesdisable";
		}

		if (isset($this->request->post['mpgdpr_cookie_analytics'])) {
			$data['mpgdpr_cookie_analytics'] = $this->request->post['mpgdpr_cookie_analytics'];
		} elseif(isset($module['mpgdpr_cookie_analytics'])) {
			$data['mpgdpr_cookie_analytics'] = $module['mpgdpr_cookie_analytics'];
		} else {
			$data['mpgdpr_cookie_analytics'] = "_ga\n _gid \n_gat \n__atuvc \n__atuvs \n__utma \n__cfduid";
		}

		if (isset($this->request->post['mpgdpr_cookie_marketing'])) {
			$data['mpgdpr_cookie_marketing'] = $this->request->post['mpgdpr_cookie_marketing'];
		} elseif(isset($module['mpgdpr_cookie_marketing'])) {
			$data['mpgdpr_cookie_marketing'] = $module['mpgdpr_cookie_marketing'];
		} else {
			$data['mpgdpr_cookie_marketing'] = "_gads \nIDE";
		}

		if (isset($this->request->post['mpgdpr_cookie_domain'])) {
			$data['mpgdpr_cookie_domain'] = $this->request->post['mpgdpr_cookie_domain'];
		} elseif(isset($module['mpgdpr_cookie_domain'])) {
			$data['mpgdpr_cookie_domain'] = $module['mpgdpr_cookie_domain'];
		} else {
			$data['mpgdpr_cookie_domain'] = '';
		}

		if (isset($this->request->post['mpgdpr_cookielang'])) {
			$data['mpgdpr_cookielang'] = $this->request->post['mpgdpr_cookielang'];
		} elseif(isset($module['mpgdpr_cookielang'])) {
			$data['mpgdpr_cookielang'] = (array) $module['mpgdpr_cookielang'];
		} else {
			$data['mpgdpr_cookielang'] = array();
		}

		if (isset($this->request->post['mpgdpr_cbposition'])) {
			$data['mpgdpr_cbposition'] = $this->request->post['mpgdpr_cbposition'];
		} elseif(isset($module['mpgdpr_cbposition'])) {
			$data['mpgdpr_cbposition'] = $module['mpgdpr_cbposition'];
		} else {
			$data['mpgdpr_cbposition'] = '';
		}

		if (isset($this->request->post['mpgdpr_cbcolor'])) {
			$data['mpgdpr_cbcolor'] = $this->request->post['mpgdpr_cbcolor'];
		} elseif(isset($module['mpgdpr_cbcolor'])) {
			$data['mpgdpr_cbcolor'] = (array)$module['mpgdpr_cbcolor'];
		} else {
			$data['mpgdpr_cbcolor'] = array();
		}

		if (isset($this->request->post['mpgdpr_cbcss'])) {
			$data['mpgdpr_cbcss'] = $this->request->post['mpgdpr_cbcss'];
		} elseif(isset($module['mpgdpr_cbcss'])) {
			$data['mpgdpr_cbcss'] = $module['mpgdpr_cbcss'];
		} else {
			$data['mpgdpr_cbcss'] = '';
		}

		$data['cbpositions'] = array();
		$data['cbpositions'][] = array(
			'value' => 'bottom-left',
			'text' =>$this->language->get('text_cbposition_left')
		);
		$data['cbpositions'][] = array(
			'value' => 'bottom-right',
			'text' =>$this->language->get('text_cbposition_right')
		);
		$data['cbpositions'][] = array(
			'value' => 'static',
			'text' =>$this->language->get('text_cbposition_static')
		);
		$data['cbpositions'][] = array(
			'value' => 'top',
			'text' =>$this->language->get('text_cbposition_top')
		);
		$data['cbpositions'][] = array(
			'value' => 'bottom',
			'text' =>$this->language->get('text_cbposition_bottom')
		);

		$data['cbinitials'] = array();
		$data['cbinitials'][] = array(
			'value' => 'cookieanalytic_block',
			'text' =>$this->language->get('text_cookie_analytic_block')
		);
		$data['cbinitials'][] = array(
			'value' => 'cookiemarketing_block',
			'text' =>$this->language->get('text_cookie_marketing_block')
		);
		$data['cbinitials'][] = array(
			'value' => 'cookieanalyticmarketing_block',
			'text' =>$this->language->get('text_cookie_analyticmarketing_block')
		);
		$data['cbinitials'][] = array(
			'value' => 'idel',
			'text' =>$this->language->get('text_cookie_idel')
		);

		$data['cbactions_close'] = array();
		$data['cbactions_close'][] = array(
			'value' => 'cookieanalytic_block',
			'text' =>$this->language->get('text_cookie_analytic_block')
		);
		$data['cbactions_close'][] = array(
			'value' => 'cookiemarketing_block',
			'text' =>$this->language->get('text_cookie_marketing_block')
		);
		$data['cbactions_close'][] = array(
			'value' => 'cookieanalyticmarketing_block',
			'text' =>$this->language->get('text_cookie_analyticmarketing_block')
		);
		$data['cbactions_close'][] = array(
			'value' => 'idel',
			'text' =>$this->language->get('text_cookie_idel')
		);


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->mpgdpr->view('mpgdpr/mpgdpr', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'mpgdpr/mpgdpr')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}