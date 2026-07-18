<?php
namespace MpImporterExporter;

set_time_limit(0);
ini_set('memory_limit', '999M');
ini_set('set_time_limit', '0');

// https://www.dropzonejs.com/#configuration
// Autoloader
require_once(DIR_SYSTEM . 'library/mpimporterexporter/composer/vendor/autoload.php');

use \Controller as OpenCartController;

class Controller extends OpenCartController {

	protected $token = 'token';
	protected $extension_page = 'extension/extension';
	protected $controllers = [
		'customer/customer' => [
			'path' => 'customer/customer'
		],
		'customer/customer_group' => [
			'path' => 'customer/customer_group'
		]
	];
	protected $models = [
		'extension/extension' => [
			'path' => 'extension/extension',
			'obj' => 'model_extension_extension'
		],
		'customer/custom_field' => [
			'path' => 'customer/custom_field',
			'obj' => 'model_customer_custom_field',
		],
		'customer/customer' => [
			'path' => 'customer/customer',
			'obj' => 'model_customer_customer',
		],
		'customer/customer_group' => [
			'path' => 'customer/customer_group',
			'obj' => 'model_customer_customer_group',
		]
	];
	protected $isdir_extension = 'extension/';
	protected $model_extension = 'extension_';
	protected $prefix_extension = ['module' => '', 'shipping' => '', 'payment' => '', 'captcha' => ''];
	public function __construct($registry) {
		parent :: __construct($registry);
		if (VERSION < '2.2.0.0') {
			$this->models['customer/customer'] = [
				'path' => 'sale/customer',
				'obj' => 'model_sale_customer',
			];
			$this->models['customer/customer_group'] = [
				'path' => 'sale/customer_group',
				'obj' => 'model_sale_customer_group',
			];
			$this->controllers['customer/customer'] = [
				'path' => 'sale/customer'
			];
			$this->controllers['customer/customer_group'] = [
				'path' => 'sale/customer_group'
			];
		}

		if (VERSION < '2.0.3.1') {
			$this->models['customer/custom_field'] = [
				'path' => 'sale/custom_field',
				'obj' => 'model_sale_custom_field',
			];
		}

		if(VERSION >= '3.0.0.0') {
			$this->token = 'user_token';
			$this->extension_page = 'marketplace/extension';
			$this->models['extension/extension'] = [
				'path' => 'setting/extension',
				'obj' => 'model_setting_extension'
			];

			$this->prefix_extension = ['module' => 'module_', 'shipping' => 'shipping_', 'payment' => 'payment_', 'captcha' => 'captcha_'];
		}

		if (VERSION <= '2.2.0.0') {
			$this->isdir_extension = '';
			$this->model_extension = '';
		}

		$registry->set('mpalphanumexcel', new \MpImporterExporter\MpAlphaNumExcel());
	}

	protected function breadcrumbs(&$data) {
		$this->load->language($this->isdir_extension . 'module/mpimportexport');

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token.'=' . $this->session->data[$this->token], true)
		);

		if (VERSION > '2.2.0.0') {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_extension'),
				'href' => $this->url->link($this->extension_page, $this->token.'=' . $this->session->data[$this->token] . '&type=module', true)
			);
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->isdir_extension . 'module/mpimportexport', $this->token.'=' . $this->session->data[$this->token], true)
		);

	}

	protected function backLink(&$data) {
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['cancel'] = $this->url->link($this->isdir_extension . 'module/mpimportexport', $this->token.'=' . $this->session->data[$this->token] . '&type=module', true);
	}

	protected function loadView($route, &$data=[], $tpl = false) {

		$data['get_token'] = $this->token;
		$data['token'] = $this->session->data[$this->token];
		$data['isdir_extension'] = $this->isdir_extension;

		if (VERSION < '3.0.0.0') {
			$this->getAllLanguageMpimporterexporter($data);
		}

		// remove .tpl from route
		if (utf8_substr($route, -4) === '.tpl') {
			$route = str_replace(utf8_substr($route, -4), "", $route);
		}

		// remove .twig from route

		if (utf8_substr($route, -5) === '.twig') {
			$route = str_replace(utf8_substr($route, -5), "", $route);
		}

		if(VERSION >= '3.0.0.0') {
			if($tpl) {
				// we load tpl view
	    	$old_template = $this->config->get('template_engine');
				$this->config->set('template_engine', 'template');
			}

			$file = $this->load->view($route, $data);
			if($tpl) {
				$this->config->set('template_engine', $old_template);
			}

		} else {

			$file = $this->load->view($route . '.tpl', $data);
		}

		return $file;
	}

	public function getAllLanguageMpimporterexporter(&$data) {
		// method comes through ocmod.
		if (method_exists($this->language, 'getAllLanguageMpimporterexporter')) {
			foreach ($this->language->getAllLanguageMpimporterexporter() as $key => $value) {
				if (!isset($data[$key])) {
					$data[$key] = $value;
				}
			}
		}

		// from oc2.3x we have language all method.
		if (method_exists($this->language, 'all')) {
			foreach ($this->language->all() as $key => $value) {
				if (!isset($data[$key])) {
					$data[$key] = $value;
				}
			}
		}
	}

	protected function clean($data) {
		if (is_array($data)) {
		   foreach ($data as $key => $value) {
		    unset($data[$key]);

		    $data[$this->clean($key)] = $this->clean($value);
		   }
	  	} else {
			$data = htmlspecialchars($this->ifnull($data), ENT_COMPAT, 'UTF-8');
		}
		return $data;

	}

	protected function validate_json($str) {
	    if (is_string($str) && $str) {
	        @json_decode($str);
	        return (json_last_error() === JSON_ERROR_NONE);
	    }
	    return false;
	}

	/**
	 * @07 jun, 2024
	 *
	 * https://stackoverflow.com/questions/9560723/mysql-query-replace-null-with-empty-string-in-select
	 *
	 * If expr1 is not NULL, IFNULL() returns expr1; otherwise it returns expr2.
	 *
	 */
	protected function ifnull($expr1, $expr2 = '') {
		if (!is_null($expr1)) {
			return $expr1;
		}
		return $expr2;
	}


	public function fileDownload() {
		if (!empty($this->request->get['file_name'])) {
			$file_to_save = DIR_UPLOAD . $this->request->get['file_name'];
			if (file_exists($file_to_save)) {
				// header('Content-Type: '. mime_content_type($file_to_save));
				// header('Content-Type: application/vnd.ms-excel');
				// header('Content-Type: application/json');
				// header('Content-Type: application/xml');

				header('Pragma: public');
				header('Expires: 0');
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');

				header('Content-Disposition: attachment;filename="'. $this->request->get['file_name'] .'"');
				header('Content-Transfer-Encoding: binary');
				header('Content-Length: '. filesize($file_to_save));
				header('Cache-Control: max-age=0');
				header('Accept-Ranges: bytes');
				readfile($file_to_save);

				unlink($file_to_save);
			}
		}
	}


	// public function fileDownload1() {
	// 	if (!empty($this->request->get['file_name'])) {
	// 		$file_to_save = DIR_UPLOAD . $this->request->get['file_name'];
	// 		if (file_exists($file_to_save)) {
	// 			// XLS: application/vnd.ms-excel
	// 			// XLSX: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
	// 			// CSV: text/plain
	// 			// XML: text/xml
	// 			// JSON: text/plain

	// 			// _GET.find_format=xls

	// 			if (function_exists('mime_content_type')) {
	// 				header('Content-Type: '. mime_content_type($file_to_save));
	// 			} else {
	// 				switch ($this->request->get['find_format']) {
	// 					case 'XLS':
	// 					header('Content-Type: application/vnd.ms-excel');
	// 					break;
	// 					case 'XLSX':
	// 					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	// 					break;
	// 					case 'CSV':
	// 					header('Content-Type: text/plain');
	// 					break;
	// 					case 'XML':
	// 					header('Content-Type: text/xml');
	// 					break;
	// 					case 'JSON':
	// 					header('Content-Type: text/plain');
	// 					break;

	// 					default:
	// 					header('Content-Type: text/plain');
	// 					break;
	// 				}
	// 			}

	// 			header('Content-Type: '. mime_content_type($file_to_save));
	// 			// header('Content-Type: application/vnd.ms-excel');
	// 			// header('Content-Type: application/json');
	// 			// header('Content-Type: application/xml');

	// 			header('Content-Disposition: attachment;filename="'. $this->request->get['file_name'] .'"');
	// 			header('Content-Transfer-Encoding: binary');
	// 			header('Content-Length: '. filesize($file_to_save));
	// 			header('Cache-Control: max-age=0');
	// 			header('Accept-Ranges: bytes');
	// 			readfile($file_to_save);

	// 			unlink($file_to_save);
	// 		}
	// 	}
	// }
}
