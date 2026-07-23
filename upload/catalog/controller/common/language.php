<?php
class ControllerCommonLanguage extends Controller {
	public function index() {
		$this->load->language('common/language');

		$data['action'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);

		$data['code'] = $this->session->data['language'];

		$this->load->model('localisation/language');

		$data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$data['languages'][] = array(
					'name' => $result['name'],
					'code' => $result['code']
				);
			}
		}

		$url_data = $this->request->get;
		$route = isset($url_data['route']) ? $url_data['route'] : 'common/home';

		unset($url_data['route'], $url_data['_route_']);

		$query = $url_data ? http_build_query($url_data, '', '&') : '';

		$data['redirect_route'] = $route;
		$data['redirect_query'] = $query;
		$data['redirect'] = $this->url->link($route, $query, $this->request->server['HTTPS']);

		return $this->load->view('common/language', $data);
	}

	public function language() {
		if (isset($this->request->post['code'])) {
			$this->load->model('localisation/language');

			$languages = $this->model_localisation_language->getLanguages();
			$code = $this->request->post['code'];

			if (isset($languages[$code]) && $languages[$code]['status']) {
				$this->session->data['language'] = $code;
				$this->session->data['language_id'] = (int)$languages[$code]['language_id'];
				$this->config->set('config_language_id', (int)$languages[$code]['language_id']);
			}
		}

		$route = isset($this->request->post['redirect_route'])
			? $this->request->post['redirect_route']
			: 'common/home';

		if (!preg_match('#^[a-z0-9_/-]+$#i', $route)) {
			$route = 'common/home';
		}

		$query_data = array();

		if (isset($this->request->post['redirect_query'])) {
			parse_str(
				html_entity_decode(
					$this->request->post['redirect_query'],
					ENT_QUOTES,
					'UTF-8'
				),
				$query_data
			);
		}

		unset($query_data['route'], $query_data['_route_']);

		$route_keys = array(
			'product/product' => array('product_id'),
			'product/category' => array('path'),
			'product/manufacturer/info' => array('manufacturer_id'),
			'information/information' => array('information_id'),
			'extension/blog/blog' => array('blog_id', 'blogpath'),
		);
		$route_data = array();

		if (isset($route_keys[$route])) {
			foreach ($route_keys[$route] as $key) {
				if (isset($query_data[$key])) {
					$route_data[$key] = $query_data[$key];
					unset($query_data[$key]);
				}
			}
		}

		unset(
			$this->session->data['redirect_route'],
			$this->session->data['additional_url_data'],
			$this->session->data['product_id'],
			$this->session->data['path'],
			$this->session->data['manufacturer_id'],
			$this->session->data['information_id']
		);

		$target = html_entity_decode(
			$this->url->link(
				$route,
				http_build_query($route_data, '', '&'),
				$this->request->server['HTTPS']
			),
			ENT_QUOTES,
			'UTF-8'
		);

		if ($query_data) {
			$target .= (strpos($target, '?') === false ? '?' : '&')
				. http_build_query($query_data, '', '&');
		}

		$this->response->redirect($target);
	}
}
