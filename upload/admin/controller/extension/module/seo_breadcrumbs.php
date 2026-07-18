<?php

/*
This file is subject to the terms and conditions defined in the "EULA.txt"
file, which is part of this source code package and is also available on the
page: https://raw.githubusercontent.com/ocmod-space/license/main/EULA.txt.
*/

class ControllerExtensionModuleSeoBreadcrumbs extends Controller {
	private $error = array();
	private $paths = array('default', 'direct', 'short', 'full', 'last', 'manufacturer');

	public function index() {
		$this->load->language('extension/module/seo_breadcrumbs');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$token = $this->session->data['user_token'];

		if (('POST' == $this->request->server['REQUEST_METHOD']) && $this->validate()) {
			$this->model_setting_setting->editSetting('module_seo_breadcrumbs', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			// $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $token . '&type=module', true));
		}

		if (isset($this->error['permission'])) {
			$data['error_permission'] = $this->error['permission'];
		} else {
			$data['error_permission'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $token, true),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $token . '&type=module', true),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/seo_breadcrumbs','user_token=' . $token, true),
		);

		$data['action'] = $this->url->link('extension/module/seo_breadcrumbs', 'user_token=' . $token, true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $token . '&type=module', true);

		if (isset($this->request->post['module_seo_breadcrumbs_status'])) {
			$data['status'] = $this->request->post['module_seo_breadcrumbs_status'];
		} else {
			$data['status'] = $this->config->get('module_seo_breadcrumbs_status');
		}

		if (isset($this->request->post['module_seo_breadcrumbs'])) {
			$data['seo_breadcrumbs'] = $this->request->post['module_seo_breadcrumbs'];
		} else {
			$data['seo_breadcrumbs'] = $this->config->get('module_seo_breadcrumbs');
		}

		if (!method_exists($this->document, 'addTag') || !method_exists($this->document, 'getTags')) {
			$data['seo_breadcrumbs']['breadcrumbs']['json'] = false;
			$data['breadcrumbs_json_disabled'] = true;
		} else {
			if (isset($this->request->post['module_seo_breadcrumbs']['breadcrumbs']['json'])) {
				$data['seo_breadcrumbs']['breadcrumbs']['json'] = $this->request->post['module_seo_breadcrumbs']['breadcrumbs']['json'];
			} elseif (isset($this->config->get('module_seo_breadcrumbs')['breadcrumbs']['json'])) {
				$data['seo_breadcrumbs']['breadcrumbs']['json'] = $this->config->get('module_seo_breadcrumbs')['breadcrumbs']['json'];
			} else {
				$data['seo_breadcrumbs']['breadcrumbs']['json'] = false;
			}
		}

		$data['paths'] = $this->paths;
		$data['heading_title'] = $this->language->get('heading_title');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/seo_breadcrumbs', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/seo_breadcrumbs')) {
			$this->error['permission'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function install() {
		$dir_stylesheet = DIR_CATALOG . 'view/theme/' . $this->config->get('theme_directory') . $this->config->get('config_theme') . '/stylesheet/';

		if ($dir_stylesheet) {
			$css_bold = $dir_stylesheet . 'breadcrumbs_bold.css';
			$css_nolink = $dir_stylesheet . 'breadcrumbs_nolink.css';

			if (!file_exists($css_bold)) {
				$css_text = "ul.breadcrumb li:last-child a {\n\tfont-weight: bold;\n}";

				file_put_contents($css_bold, $css_text, FILE_USE_INCLUDE_PATH);
			}

			if (!file_exists($css_nolink)) {
				$css_text = "ul.breadcrumb li:last-child a {\n\tcursor: default!important;\n\tpointer-events: none;\n\tcolor: inherit;\n}";

				file_put_contents($css_nolink, $css_text, FILE_USE_INCLUDE_PATH);
			}
		}

		$this->load->model('setting/event');

		$this->model_setting_event->deleteEventByCode('seo_breadcrumbs_subst_breadcrumbs');
		$this->model_setting_event->deleteEventByCode('seo_breadcrumbs_style_breadcrumbs');
		$this->model_setting_event->deleteEventByCode('seo_breadcrumbs_add_jsonld');
		$this->model_setting_event->deleteEventByCode('seo_breadcrumbs_hide');

		$this->model_setting_event->addEvent('seo_breadcrumbs_subst_breadcrumbs', 'catalog/view/*/before', 'extension/module/seo_breadcrumbs/substBreadcrumbs');
		$this->model_setting_event->addEvent('seo_breadcrumbs_style_breadcrumbs', 'catalog/controller/common/header/before', 'extension/module/seo_breadcrumbs/styleBreadcrumbs');
		$this->model_setting_event->addEvent('seo_breadcrumbs_add_jsonld', 'catalog/view/*/after', 'extension/module/seo_breadcrumbs/addJsonLdScript');
		$this->model_setting_event->addEvent('seo_tools_hide', 'admin/view/design/layout_form/before', 'extension/module/seo_breadcrumbs/hideFromDesignLayoutForm');
	}

	public function uninstall() {
		$dir_stylesheet = DIR_CATALOG . 'view/theme/' .
			$this->config->get('theme_directory') . $this->config->get('config_theme') . '/stylesheet/';

		if ($dir_stylesheet) {
			$css_bold = $dir_stylesheet . 'breadcrumb_bold.css';
			$css_nolink = $dir_stylesheet . 'breadcrumb_nolink.css';

			if (file_exists($css_bold)) {
				unlink($css_bold);
			}

			if (file_exists($css_nolink)) {
				unlink($css_nolink);
			}
		}

		$this->load->model('setting/event');

		$this->model_setting_event->deleteEventByCode('seo_breadcrumbs_subst_breadcrumbs');
		$this->model_setting_event->deleteEventByCode('seo_breadcrumbs_style_breadcrumbs');
		$this->model_setting_event->deleteEventByCode('seo_breadcrumbs_add_jsonld');
		$this->model_setting_event->deleteEventByCode('seo_breadcrumbs_hide');
	}

	//  Hide module from the list of the modules in Design > Layouts
	//  https://forum.opencart.com/viewtopic.php?p=799279#p799279
	public function hideFromDesignLayoutForm(&$route, &$data) {
		foreach ($data['extensions'] as $key => $extension) {
			if ('seo_breadcrumbs' == $extension['code']) {
				unset($data['extensions'][$key]);
			}
		}

		return null;
	}
}
