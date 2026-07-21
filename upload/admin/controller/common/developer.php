<?php
class ControllerCommonDeveloper extends Controller {
	public function index() {
		$this->load->language('common/developer');

		$data['user_token'] = $this->session->data['user_token'];

		$data['developer_theme'] = $this->config->get('developer_theme');
		$data['developer_sass'] = $this->config->get('developer_sass');

		$eval = false;

		$eval = '$eval = true;';

		eval($eval);

		if ($eval === true) {
			$data['eval'] = true;
		} else {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('developer', array('developer_theme' => 1), 0);

			$data['eval'] = false;
		}

		$this->response->setOutput($this->load->view('common/developer', $data));
	}

	public function edit() {
		$this->load->language('common/developer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'common/developer')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('developer', $this->request->post, 0);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function theme() {
		$this->load->language('common/developer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'common/developer')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$directories = glob(DIR_CACHE . '/template/*', GLOB_ONLYDIR);

			if ($directories) {
				foreach ($directories as $directory) {
					$files = glob($directory . '/*');

					foreach ($files as $file) { 
						if (is_file($file)) {
							unlink($file);
						}
					}

					if (is_dir($directory)) {
						rmdir($directory);
					}
				}
			}

			$json['success'] = sprintf($this->language->get('text_cache'), $this->language->get('text_theme'));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function sass() {
		$this->load->language('common/developer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'common/developer')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Before we delete we need to make sure there is a sass file to regenerate the css
			$file = DIR_APPLICATION  . 'view/stylesheet/bootstrap.css';

			if (is_file($file) && is_file(DIR_APPLICATION . 'view/stylesheet/sass/_bootstrap.scss')) {
				unlink($file);
			}
			 
			$files = glob(DIR_CATALOG  . 'view/theme/*/stylesheet/sass/_bootstrap.scss');
			 
			foreach ($files as $file) {
				$file = substr($file, 0, -21) . '/bootstrap.css';

				if (is_file($file)) {
					unlink($file);
				}
			}

			$json['success'] = sprintf($this->language->get('text_cache'), $this->language->get('text_sass'));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function cache() {
		$this->load->language('common/developer');

		$redirect = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
		$request_method = isset($this->request->server['REQUEST_METHOD']) ? strtoupper($this->request->server['REQUEST_METHOD']) : '';

		if ($request_method !== 'POST') {
			$this->session->data['error_warning'] = $this->language->get('error_method');
			$this->response->redirect($redirect);

			return;
		}

		if (!$this->user->hasPermission('modify', 'common/developer') || !$this->user->hasPermission('modify', 'marketplace/modification')) {
			$this->session->data['error_warning'] = $this->language->get('error_permission');
			$this->response->redirect($redirect);

			return;
		}

		try {
			// Regenerate OCMOD first so deployed source changes are copied to DIR_MODIFICATION.
			$this->load->controller('marketplace/modification/refresh', array('return' => true));

			$deleted = $this->clearRuntimeCacheFiles();

			// A version change also invalidates page caches when a non-file cache adaptor is used.
			$this->cache->set('dryzen.page.version', sprintf('%.6F', microtime(true)));

			$this->session->data['success'] = sprintf($this->language->get('text_cache_all'), $deleted);
		} catch (Throwable $exception) {
			$this->log->write('Dashboard cache clear failed: ' . $exception->getMessage());
			$this->session->data['error_warning'] = $this->language->get('error_cache');
		}

		$this->response->redirect($redirect);
	}

	private function clearRuntimeCacheFiles() {
		$deleted = 0;
		$cache_files = glob(DIR_CACHE . 'cache.*');

		if ($cache_files) {
			foreach ($cache_files as $file) {
				if (is_file($file)) {
					if (!@unlink($file)) {
						throw new RuntimeException('Unable to delete cache file: ' . $file);
					}

					$deleted++;
				}
			}
		}

		$template_directory = DIR_CACHE . 'template/';

		if (is_dir($template_directory)) {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($template_directory, FilesystemIterator::SKIP_DOTS),
				RecursiveIteratorIterator::CHILD_FIRST
			);

			foreach ($iterator as $item) {
				if ($item->isFile() || $item->isLink()) {
					if (!@unlink($item->getPathname())) {
						throw new RuntimeException('Unable to delete template cache file: ' . $item->getPathname());
					}

					$deleted++;
				} elseif ($item->isDir()) {
					@rmdir($item->getPathname());
				}
			}
		}

		return $deleted;
	}
}
