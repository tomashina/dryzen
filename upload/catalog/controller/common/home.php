<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));
		$this->document->addStyle('catalog/view/theme/basel/stylesheet/dryzen-homepage.css?v=20260916c');
		$this->document->addScript('catalog/view/theme/basel/js/dryzen-homepage.js?v=20260916a');

		$this->document->addLink($this->getCanonicalHomeUrl(), 'canonical');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/home', $data));
	}

	private function getCanonicalHomeUrl() {
		$base_url = rtrim((string)($this->config->get('config_ssl') ?: $this->config->get('config_url')), '/') . '/';
		$current_language_id = (int)$this->config->get('config_language_id');
		$default_language = (string)$this->config->get('config_language');

		$query = $this->db->query(
			"SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '"
			. $this->db->escape($default_language)
			. "' LIMIT 1"
		);
		$default_language_id = $query->num_rows ? (int)$query->row['language_id'] : $current_language_id;

		if ($current_language_id === $default_language_id) {
			return $base_url;
		}

		$query = $this->db->query(
			"SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = 'language_id="
			. $current_language_id
			. "' AND language_id = '" . $current_language_id
			. "' AND store_id = '" . (int)$this->config->get('config_store_id')
			. "' LIMIT 1"
		);

		return ($query->num_rows && $query->row['keyword'])
			? $base_url . ltrim($query->row['keyword'], '/')
			: $base_url;
	}
}
