<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['compliance_links'] = array();

		$compliance_information_pages = array(
			13 => $this->language->get('text_compliance_about'),
			5  => $this->language->get('text_compliance_terms'),
			7  => $this->language->get('text_compliance_delivery'),
			12 => $this->language->get('text_compliance_payment'),
			3  => $this->language->get('text_compliance_privacy'),
			17 => $this->language->get('text_compliance_returns')
		);

		foreach ($compliance_information_pages as $information_id => $title) {
			$data['compliance_links'][] = array(
				'title' => $title,
				'href'  => $this->url->link('information/information', 'information_id=' . $information_id)
			);
		}

		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		$data['footer_withdrawal_url'] = $this->url->link('account/return/add', '', true);
		$data['footer_pricelists_url'] = $this->url->link('extension/feed/digital_pricelist/page', '', true);
		$data['footer_guarantee_url'] = $this->url->link('common/header/legalGuaranteeImage', '', true);
		$data['show_footer_pricelists'] = (bool)$this->config->get('feed_digital_pricelist_status');
		$data['text_footer_withdrawal'] = $this->language->get('text_footer_withdrawal');
		$data['text_footer_pricelists'] = $this->language->get('text_footer_pricelists');
		$data['text_footer_guarantee'] = $this->language->get('text_footer_guarantee');
		$data['text_customer_information'] = $this->language->get('text_customer_information');
		$data['text_accepted_payment_methods'] = $this->language->get('text_accepted_payment_methods');
		$data['text_opens_new_window'] = $this->language->get('text_opens_new_window');
		$data['text_back_to_top'] = $this->language->get('text_back_to_top');
		$data['text_dryzen_newsletter_title'] = $this->language->get('text_dryzen_newsletter_title');
		$data['text_dryzen_newsletter_copy'] = $this->language->get('text_dryzen_newsletter_copy');
		$data['text_dryzen_newsletter_note'] = $this->language->get('text_dryzen_newsletter_note');

		$this->load->language('basel/basel_theme');
		$data['module'] = 48;
		$data['widget_module'] = 48;
		$data['newsletter_consent_mode'] = true;
		$data['basel_subscribe_email'] = $this->language->get('basel_subscribe_email');
		$data['basel_subscribe_btn'] = $this->language->get('basel_subscribe_btn');
		$data['basel_subscribe_consent_label'] = $this->language->get('basel_subscribe_consent_label');
		$data['basel_subscribe_consent_error'] = $this->language->get('basel_subscribe_consent_error');
		$data['global_newsletter_form'] = $this->load->view('extension/module/content_widgets/subscribe_field', $data);

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));
		$data['config_email'] = $this->config->get('config_email');

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['scripts'] = $this->document->getScripts('footer');
		$data['styles'] = $this->document->getStyles('footer');
		
		return $this->load->view('common/footer', $data);
	}
}
