<?php
// Version 1.0.1
if (!class_exists('Equotix')) {
	class Equotix extends Controller {
		public function generateOutput($file, $data = array()) {
			$data['license'] =  array(
				'license_key'		=> $this->config->get($this->code . '_license_license_key'),
				'order_id'			=> $this->config->get($this->code . '_license_order_id'),
				'name' 				=> $this->config->get($this->code . '_license_name'),
				'date_purchased' 	=> $this->config->get($this->code . '_license_date_purchased'),
				'date_expired' 		=> $this->config->get($this->code . '_license_date_expired'),
				'domains' 			=> $this->config->get($this->code . '_license_domains') ? $this->config->get($this->code . '_license_domains') : array()
			);

			$data['services'] = $this->config->get($this->code . '_license_services');
			
			if (version_compare(VERSION, '3.0.0.0', '>=')) {
				$folder = 'extension/module';
				
				$data['equotix_token'] = '&user_token=' . $this->session->data['user_token'];
			} elseif (version_compare(VERSION, '2.3.0.0', '>=')) {
				$folder = 'extension/module';
				
				$data['equotix_token'] = '&token=' . $this->session->data['token'];
			} else {
				$folder = 'module';
				
				$data['equotix_token'] = '&token=' . $this->session->data['token'];
			}
			
			$data['folder'] = isset($this->folder) ? $this->folder : $folder;
			$data['code'] = $this->code;
			$data['purchase_url'] = $this->purchase_url;
			$data['extension'] = $this->extension;
			$data['version'] = $this->version;
						
			$data['about'] = $this->getTabButton();
			$data['tab'] = $this->getTabContent($data);
			
			if (!empty($this->request->server['HTTPS'])) {
				$base = str_replace('http://', 'https://', (defined('HTTPS_CATALOG') ? HTTPS_CATALOG : HTTP_CATALOG) . 'system/library/equotix/' . $this->code . '/');
			} else {
				$base = str_replace('https://', 'http://', (defined('HTTPS_CATALOG') ? HTTPS_CATALOG : HTTP_CATALOG) . 'system/library/equotix/' . $this->code . '/');
			}
			
			$search = array(
				'view/javascript/jquery/jquery-1.7.1.min.js',
				'view/javascript/jquery/jquery-1.6.1.min.js',
				'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
				'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
			);
			
			$replace = array(
				$base . 'js/jquery-1.11.3.min.js',
				$base . 'js/jquery-1.11.3.min.js',
				$base . 'js/jquery-1.11.3.min.js',
				'<!DOCTYPE html>'
			);
			
			if (version_compare(VERSION, '2.0.0.0', '>=')) {
				$this->response->setOutput(str_replace($search, $replace, $this->load->view($file, $data)));
			} else {
				$this->document->addStyle($base . 'bootstrap/css/bootstrap.min.css');
				$this->document->addStyle($base . 'fontawesome/css/font-awesome.min.css');
				$this->document->addStyle($base . 'css/equotix.css');
				$this->document->addScript($base . 'bootstrap/js/bootstrap.min.js');
				$this->document->addScript($base . 'js/jquery-migrate-1.2.1.min.js');
				$this->document->addScript($base . 'js/equotix.js');
				
				$this->data = array_merge($this->data, $data);
				
				$this->template = $file;
				$this->children = array(
					'common/header',
					'common/footer'
				);

				$this->response->setOutput(str_replace($search, $replace, $this->render()));
			}
		}
		
		private function saveSetting($group, $data) {
			if (version_compare(VERSION, '2.0.0.0', '>')) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '0' AND `code` = '" . $this->db->escape($group) . "'");

				foreach ($data as $key => $value) {
					if (!is_array($value)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
					} else {
						if (version_compare(VERSION, '2.1.0.0', '>=')) {
							$value = json_encode($value);
						} else {
							$value = serialize($value);
						}
					
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "', serialized = '1'");
					}
				}
			} else {
				$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '0' AND `group` = '" . $this->db->escape($group) . "'");

				foreach ($data as $key => $value) {
					if (!is_array($value)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
					}
				}
			}
		}
		
		protected function getTabButton() {
			$html = '<li><a href="#tab-about" data-toggle="tab"><i class="fa fa-question-circle"></i> About</a></li>';
		
			return $html;
		}
		
		protected function getTabContent($data) {
			foreach ($data as $key => $value) {
				${$key} = $value;
			}
			
			ob_start();
			
			require_once('equotix.tpl');
			
			return ob_get_clean();
		}
	}
}