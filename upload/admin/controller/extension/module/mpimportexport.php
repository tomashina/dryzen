<?php
use MpImporterExporter\Controller;
class ControllerExtensionModuleMpImportExport extends Controller {

	public function install() {
		// detect importer/export files and add in access/modify permissions array

		$lists = [];
		$files = glob(DIR_APPLICATION . 'controller/extension/mpimporterexporter/*.php');
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				$lists[] = 'extension/mpimporterexporter/' .$extension;
			}
		}
		$this->addFilesInPermissions((array)$lists);
	}

	public function getMenu() {
		$this->load->language('extension/module/mpimportexport', 'mpimportexport');
		$menu = [];
		if ($this->user->hasPermission('access', 'extension/module/mpimportexport')) {
			$menu = [
				'id'	   => 'menu-mpimportexport',
				'icon'	   => 'fa-mixcloud',
				'name'	   => $this->language->get('mpimportexport')->get('text_menu'),
				'href'     => $this->url->link('extension/module/mpimportexport', $this->token.'=' . $this->session->data[$this->token], true),
				'children' => []
			];
		}
		// if module not installed then not show menu.
		$this->load->model($this->models['extension/extension']['path']);
		$extensions = $this->{$this->models['extension/extension']['obj']}->getInstalled('module');
		if (!in_array('mpimportexport', $extensions)) {
			$menu = [];
		}
		return $menu;
	}

	private function addFilesInPermissions(array $files) {
		if ($this->user->hasPermission('modify', 'extension/module/mpimportexport')) {
			$this->load->model('user/user_group');
			foreach ($files as $file) {
				$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', $file);
				$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', $file);
			}
		}
	}

	private function detectFilesForPermissions() {
		$this->load->model('user/user_group');
		$user_group = $this->model_user_user_group->getUserGroup($this->user->getGroupId());

		$lists = [];

		$files = glob(DIR_APPLICATION . 'controller/extension/mpimporterexporter/*.php');
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				if (!in_array('extension/mpimporterexporter/' .$extension, $user_group['permission']['access'])) {
					// && !in_array('extension/mpimporterexporter/' .$extension, $user_group['permission']['modify'])
					$lists[] = 'extension/mpimporterexporter/' .$extension;
				}
			}
		}
		return $lists;
	}

	public function updatePermissions() {
		$json = [];
		$this->load->language('extension/module/mpimportexport');
		$this->addFilesInPermissions((array)$this->detectFilesForPermissions());
		$this->session->data['success'] = $this->language->get('text_success_files_permission');
		$json['redirect'] = str_replace("&amp;", "&", $this->url->link('extension/module/mpimportexport', $this->token.'=' . $this->session->data[$this->token], true));

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function index() {
		$this->load->language('extension/module/mpimportexport');

		$this->document->setTitle($this->language->get('heading_title'));

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

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = $this->language->get('text_form');

		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_import'] = $this->language->get('button_import');
		$data['button_export'] = $this->language->get('button_export');

		$data['breadcrumbs'] = array();
		$this->breadcrumbs($data);

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];

		$data['action'] = $this->url->link('extension/module/account', $this->token.'=' . $this->session->data[$this->token], true);

		$data['cancel'] = $this->url->link($this->extension_page, $this->token.'=' . $this->session->data[$this->token] . '&type=module', true);

		$this->load->model('user/user_group');
		$user_group = $this->model_user_user_group->getUserGroup($this->user->getGroupId());

		$blocks = [];
		$files = glob(DIR_APPLICATION . 'controller/extension/mpimporterexporter/*.php');
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				// file name with either export_, import_ allowed
				if (substr($extension, 0, strlen('export_')) != 'export_' && substr($extension, 0, strlen('import_')) != 'import_') {
					continue;
				}

				// $this->user->hasPermission('access', 'extension/mpimporterexporter/' .$extension)
				if (!in_array('extension/mpimporterexporter/' .$extension, $user_group['permission']['access'])) {
					//  && !in_array('extension/mpimporterexporter/' .$extension, $user_group['permission']['modify'])
					continue;
				}

				$prefix = utf8_substr($extension, 0, utf8_strpos ($extension, '_'));

				$postfix = utf8_substr($extension, utf8_strpos($extension, '_') + 1);

				$blocks[$postfix][] =  $prefix;

			}
		}

		$data['block_sections'] = [];
		foreach ($blocks as $key => $block) {
			$export_title = '';
			$str = [];
			$link = [];
			$language = [];
			$class = [];
			$icon = [];
			foreach ($block as $index => $action) {

				$extension = $action . '_' . $key;

				$this->load->language('mpimporterexporter/' . $extension.'_menu', 'mpimportexport');

				$export_title = $this->language->get('mpimportexport')->get('export_title');

				$str[] = $this->language->get('mpimportexport')->get('text_'. $key .'_' . $action);
				$link[$action] = $this->url->link('extension/mpimporterexporter/'.$extension, $this->token.'=' . $this->session->data[$this->token], true);
				$language[$action] = $this->language->get('button_' . $action);
				if ($action == 'export') {
					$class[$action] = 'btn-primary';
					$icon[$action] = 'fa fa-download';
				} else {
					$class[$action] = 'btn-danger';
					$icon[$action] = 'fa fa-upload';
				}
			}
			$data['block_sections'][] = [
				'title' => sprintf($export_title, implode("/", $str) ),
				'link' => $link,
				'language' => $language,
				'class' => $class,
				'icon' => $icon,
			];
		}

		$sort_order = array();

		foreach ($data['block_sections'] as $key => $value) {
			$sort_order[$key] = $value['title'];
		}

		array_multisort($sort_order, SORT_ASC, $data['block_sections']);

		// show a alert message for files that are not in premissions list
		if ($this->user->hasPermission('modify', 'extension/module/mpimportexport')) {
			$data['files'] = $this->detectFilesForPermissions();
		} else {
			$data['files'] = [];
		}

		$data['text_files_permission'] = $this->language->get('text_files_permission');
		$data['button_files_permission'] = $this->language->get('button_files_permission');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->loadView('extension/module/mpimportexport', $data, 1));
	}
}