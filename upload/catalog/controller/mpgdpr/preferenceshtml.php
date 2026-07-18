<?php
class ControllerMpGdprPreferencesHtml extends Controller {

	public function getPreferencesHtml() {

		$this->load->language('mpgdpr/preferenceshtml');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');


		$data['text_heading'] = $this->language->get('text_heading');
		$data['text_cookie_strickly'] = $this->language->get('text_cookie_strickly');
		$data['text_cookie_strickly_detail'] = $this->language->get('text_cookie_strickly_detail');
		$data['text_cookie_analytics'] = $this->language->get('text_cookie_analytics');
		$data['text_cookie_analytics_detail'] = $this->language->get('text_cookie_analytics_detail');
		$data['text_cookie_marketing'] = $this->language->get('text_cookie_marketing');
		$data['text_cookie_marketing_detail'] = $this->language->get('text_cookie_marketing_detail');

		$data['button_close'] = $this->language->get('button_close');
		$data['button_update'] = $this->language->get('button_update');

		$cookies_analytics = $this->config->get('mpgdpr_cookie_analytics');
		$data['cookies_analytics'] = json_encode(str_replace("\r", "", explode("\n", $cookies_analytics)));

		$cookies_marketing = $this->config->get('mpgdpr_cookie_marketing');
		$data['cookies_marketing'] = json_encode(str_replace("\r", "", explode("\n", $cookies_marketing)));

		$cookie_domain = $this->config->get('mpgdpr_cookie_domain');
		$cookie_domains = str_replace("\r", "", explode("\n", $cookie_domain));
		$cookie_domain = array();
		foreach ($cookie_domains as $key => $value) {
			$cookie_domain[$key] = '.'.$value;
		}

		$data['cookie_domain'] = json_encode(array_merge_recursive($cookie_domains, $cookie_domain));

		$data['deniedCookiess'] = json_encode(array());
		$data['denied_cookiess'] = array();
		if(isset($this->request->cookie['mpcookie_preferencesdisable'])) {
			$data['deniedCookiess'] = json_encode(str_replace("\r", "", explode(",", $this->request->cookie['mpcookie_preferencesdisable'])));
			$data['denied_cookiess'] = str_replace("\r", "", explode(",", $this->request->cookie['mpcookie_preferencesdisable']));
		}

		$data['cookieconsentstatuss'] = array('allow','deny');
		$data['cookieconsent_status'] = '';
		if(isset($this->request->cookie['cookieconsent_status'])) {
			$data['cookieconsent_status'] = $this->request->cookie['cookieconsent_status'];
		}


		$data['cbstatus'] = $this->config->get('mpgdpr_cbstatus');
		$data['cbpolicy'] = $this->config->get('mpgdpr_cbpolicy');
		$data['cbshowagain'] = $this->config->get('mpgdpr_cbshowagain');
		$data['cbinitial'] = $this->config->get('mpgdpr_cbinitial');
		$data['cbaction_close'] = $this->config->get('mpgdpr_cbaction_close');
		$data['cbcss'] = $this->config->get('mpgdpr_cbcss');
		$data['cbpptrack'] = $this->config->get('mpgdpr_cbpptrack');
		$data['cbpolicy_page'] = $this->config->get('mpgdpr_cbpolicy_page');

		$cookielang = $this->config->get('mpgdpr_cookielang');
		$cookie_lang = array();
		if(isset($cookielang[(int)$this->config->get('config_language_id')])) {
			$cookie_lang = array_map( "trim", $cookielang[(int)$this->config->get('config_language_id')]) ;
		}

		$data['text_cookielang_msg'] = $this->language->get('text_cookielang_msg');
		if(!empty($cookie_lang['msg'])) {
			// $data['text_cookielang_msg'] = $cookie_lang['msg'];
			$data['text_cookielang_msg'] = trim(preg_replace('/\s\s+/', ' ', $cookie_lang['msg']));
			$data['text_cookielang_msg'] = trim(trim(preg_replace('/\s+/', ' ', $data['text_cookielang_msg'])));
		}

		$data['text_cookielang_btn_accept'] = $this->language->get('text_cookielang_btn_accept');
		if(!empty($cookie_lang['btn_accept'])) {
			$data['text_cookielang_btn_accept'] = $cookie_lang['btn_accept'];
		}

		$data['text_cookielang_btn_deny'] = $this->language->get('text_cookielang_btn_deny');
		if(!empty($cookie_lang['btn_deny'])) {
			$data['text_cookielang_btn_deny'] = $cookie_lang['btn_deny'];
		}


		$data['text_cookielang_btn_prefrence'] = $this->language->get('text_cookielang_btn_prefrence');
		if(!empty($cookie_lang['btn_prefrence'])) {
			$data['text_cookielang_btn_prefrence'] = $cookie_lang['btn_prefrence'];
		}

		$data['text_cookielang_btn_showagain'] = $this->language->get('text_cookielang_btn_showagain');
		if(!empty($cookie_lang['btn_showagain'])) {
			$data['text_cookielang_btn_showagain'] = $cookie_lang['btn_showagain'];
		}

		if(!$data['cbpolicy_page']) {
			$data['cbpolicy_page'] = $this->config->get('config_account_id');
		}

		$data['cbpolicy_page_url'] = '';
		$data['cbpolicy_page_text'] = '';
		if($data['cbpolicy_page']) {
			$this->load->model('catalog/information');
			$information = $this->model_catalog_information->getInformation($data['cbpolicy_page']);
			if($information) {

				$data['cbpolicy_page_url'] = $this->url->link('information/information','information_id=' . $data['cbpolicy_page'], $this->mpgdpr->ssl);

				if(!empty($cookie_lang['text_policy'])) {
					$data['cbpolicy_page_text'] = $cookie_lang['text_policy'];
				}

				if(empty($data['cbpolicy_page_text'])) {
					$data['cbpolicy_page_text'] = $information['title'];
				}
			}
		}

		$data['cbcolor']['box_bg'] = "#000";
		$data['cbcolor']['box_text'] = "#fff";
		$data['cbcolor']['btn_bg'] = "#f1d600";
		$data['cbcolor']['btn_text'] = "#000";
		$data['cbcolor']['btn_padding'] = array();
		$data['cbcolor']['btn_padding']['top'] = '5';
		$data['cbcolor']['btn_padding']['right'] = '25';
		$data['cbcolor']['btn_padding']['bottom'] = '5';
		$data['cbcolor']['btn_padding']['left'] = '25';
		$data['cbcolor']['btn_padding']['unit'] = 'px';
		if($this->config->get('mpgdpr_cbcolor')) {
			$mpgdpr_cbcolor = (array)$this->config->get('mpgdpr_cbcolor');
			$cbcolor = array_map("trim", $mpgdpr_cbcolor);
			if(!empty($cbcolor['box_bg'])) {
				$data['cbcolor']['box_bg'] = $cbcolor['box_bg'];
			}
			if(!empty($cbcolor['box_text'])) {
				$data['cbcolor']['box_text'] = $cbcolor['box_text'];
			}
			if(!empty($cbcolor['btn_bg'])) {
				$data['cbcolor']['btn_bg'] = $cbcolor['btn_bg'];
			}
			if(!empty($cbcolor['btn_text'])) {
				$data['cbcolor']['btn_text'] = $cbcolor['btn_text'];
			}


			if(!empty($cbcolor['btn_padding']['top'])) {
				$data['cbcolor']['btn_text'] = $cbcolor['btn_padding']['top'];
			}
			if(!empty($cbcolor['btn_padding']['right'])) {
				$data['cbcolor']['btn_text'] = $cbcolor['btn_padding']['right'];
			}
			if(!empty($cbcolor['btn_padding']['bottom'])) {
				$data['cbcolor']['btn_text'] = $cbcolor['btn_padding']['bottom'];
			}
			if(!empty($cbcolor['btn_padding']['left'])) {
				$data['cbcolor']['btn_text'] = $cbcolor['btn_padding']['left'];
			}

		}


		$data['position'] = "bottom";
		$data['static'] = false;
		if($this->config->get('mpgdpr_cbposition')) {
			$data['position'] = $this->config->get('mpgdpr_cbposition');
		}
		if($data['position'] == 'static') {
			$data['position'] = "top";
			$data['static'] = true;
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['base'] = $server;

		$data['logging'] = false;

		if($this->config->get('mpgdpr_cbstatus')) {

			$this->response->setOutput($this->mpgdpr->view('mpgdpr/preferenceshtml', $data));

		}
	}
}