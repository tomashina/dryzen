<?php

class ControllerExtensionMpImporterExporterExportProduct extends \MpImporterExporter\Controller {
	private $error = [];

	public function getMenu() {
		$this->load->language('mpimporterexporter/export_product_menu');
		$menu = [];
		if ($this->user->hasPermission('access', $this->isdir_extension . 'mpimporterexporter/export_product')) {
			$menu = [
				'name'	   => $this->language->get('text_product_exporter'),
				'href'     => $this->url->link($this->isdir_extension . 'mpimporterexporter/export_product', $this->token . '=' . $this->session->data[$this->token], true),
				'children' => []
			];
		}
		return $menu;
	}

	public function index() {
		$this->load->language('mpimporterexporter/export_product');
		$this->load->model($this->isdir_extension . 'mpimporterexporter/export_product');

		$this->document->addStyle('view/stylesheet/mpimporterexporter/export_product.css');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$this->breadcrumbs($data);
		// reload page specific language file to avoid language variable conflicts
		$this->load->language('mpimporterexporter/export_product');

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->isdir_extension . 'mpimporterexporter/export_product', $this->token . '=' . $this->session->data[$this->token], true)
		];

		$this->backLink($data);

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

		$this->load->model('setting/store');
		$this->load->model('localisation/language');
		$this->load->model('localisation/stock_status');

		$data['stores'] = [];
		$stores = $this->model_setting_store->getStores();
		$data['stores'][] = [
			'store_id' => 0,
			'name' => $this->language->get('text_default')
		];
		foreach ($stores as $store) {
			$data['stores'][] = [
				'store_id' => $store['store_id'],
				'name' => $store['name']
			];
		}

		$data['languages'] = $this->model_localisation_language->getLanguages();
		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['productexport_setting_fields'])) {
			$data['productexport_setting_fields'] = $this->request->post['productexport_setting_fields'];
		} else {
			$data['productexport_setting_fields'] = (array)$this->config->get('productexport_setting_fields');
		}

		// foreach ($data['productexport_setting_fields'] as $key => $value) {
		// 	$data['productexport_setting_fields'][$key]['alphabet'] = '';
		// 	if ((int)$value['sort_order'] > 0) {
		// 		$data['productexport_setting_fields'][$key]['alphabet'] = $this->mpalphanumexcel->numberToAlphabet((int)$value['sort_order']);
		// 	}
		// }

		$data['find_fields'] = $this->getFindFields();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->loadView($this->isdir_extension . 'mpimporterexporter/export_product', $data));

	}

	protected function getFindFields() {

		$find_fields = [];
		$find_fields[] = [
			'code' => 'product_id',
			'help' => '',
			'title' => $this->language->get('cell_product_id'),
		];
		$find_fields[] = [
			'code' => 'product_name',
			'help' => '',
			'title' => $this->language->get('cell_product_name'),
		];
		$find_fields[] = [
			'code' => 'product_model',
			'help' => '',
			'title' => $this->language->get('cell_product_model'),
		];
		$find_fields[] = [
			'code' => 'language_code',
			'help' => '',
			'title' => $this->language->get('cell_language_code'),
		];
		$find_fields[] = [
			'code' => 'store',
			'help' => '',
			'title' => $this->language->get('cell_store'),
		];
		$find_fields[] = [
			'code' => 'description',
			'help' => '',
			'title' => $this->language->get('cell_description'),
		];
		$find_fields[] = [
			'code' => 'meta_title',
			'help' => '',
			'title' => $this->language->get('cell_meta_title'),
		];
		$find_fields[] = [
			'code' => 'meta_description',
			'help' => '',
			'title' => $this->language->get('cell_meta_description'),
		];
		$find_fields[] = [
			'code' => 'meta_keyword',
			'help' => '',
			'title' => $this->language->get('cell_meta_keyword'),
		];
		$find_fields[] = [
			'code' => 'tag',
			'help' => '',
			'title' => $this->language->get('cell_tag'),
		];
		$find_fields[] = [
			'code' => 'product_image',
			'help' => '',
			'title' => $this->language->get('cell_image'),
		];
		$find_fields[] = [
			'code' => 'sku',
			'help' => '',
			'title' => $this->language->get('cell_sku'),
		];
		$find_fields[] = [
			'code' => 'upc',
			'help' => '',
			'title' => $this->language->get('cell_upc'),
		];
		$find_fields[] = [
			'code' => 'ean',
			'help' => '',
			'title' => $this->language->get('cell_ean'),
		];
		$find_fields[] = [
			'code' => 'jan',
			'help' => '',
			'title' => $this->language->get('cell_jan'),
		];
		$find_fields[] = [
			'code' => 'isbn',
			'help' => '',
			'title' => $this->language->get('cell_isbn'),
		];
		$find_fields[] = [
			'code' => 'mpn',
			'help' => '',
			'title' => $this->language->get('cell_mpn'),
		];
		$find_fields[] = [
			'code' => 'location',
			'help' => '',
			'title' => $this->language->get('cell_location'),
		];
		$find_fields[] = [
			'code' => 'price',
			'help' => '',
			'title' => $this->language->get('cell_price'),
		];
		$find_fields[] = [
			'code' => 'minimum_quantity',
			'help' => '',
			'title' => $this->language->get('cell_minimum_quantity'),
		];
		$find_fields[] = [
			'code' => 'quantity',
			'help' => '',
			'title' => $this->language->get('cell_quantity'),
		];
		$find_fields[] = [
			'code' => 'status',
			'help' => '',
			'title' => $this->language->get('cell_status'),
		];
		$find_fields[] = [
			'code' => 'sort_order',
			'help' => '',
			'title' => $this->language->get('cell_sort_order'),
		];
		$find_fields[] = [
			'code' => 'tax_class_id',
			'help' => '',
			'title' => $this->language->get('cell_tax_class_id'),
		];
		$find_fields[] = [
			'code' => 'tax_class_name',
			'help' => '',
			'title' => $this->language->get('cell_tax_class_name'),
		];
		$find_fields[] = [
			'code' => 'subtract',
			'help' => '',
			'title' => $this->language->get('cell_subtract'),
		];
		$find_fields[] = [
			'code' => 'stock_status_id',
			'help' => '',
			'title' => $this->language->get('cell_stock_status_id'),
		];
		$find_fields[] = [
			'code' => 'stock_status_name',
			'help' => '',
			'title' => $this->language->get('cell_stock_status_name'),
		];
		$find_fields[] = [
			'code' => 'shipping_required',
			'help' => '',
			'title' => $this->language->get('cell_shipping_required'),
		];
		$find_fields[] = [
			'code' => 'seo_keyword',
			'help' => '',
			'title' => $this->language->get('cell_seo_keyword'),
		];
		$find_fields[] = [
			'code' => 'date_available',
			'help' => '',
			'title' => $this->language->get('cell_date_available'),
		];
		$find_fields[] = [
			'code' => 'length',
			'help' => '',
			'title' => $this->language->get('cell_length'),
		];
		$find_fields[] = [
			'code' => 'length_class_id',
			'help' => '',
			'title' => $this->language->get('cell_length_class_id'),
		];
		$find_fields[] = [
			'code' => 'length_class_name',
			'help' => '',
			'title' => $this->language->get('cell_length_class_name'),
		];
		$find_fields[] = [
			'code' => 'width',
			'help' => '',
			'title' => $this->language->get('cell_width'),
		];
		$find_fields[] = [
			'code' => 'height',
			'help' => '',
			'title' => $this->language->get('cell_height'),
		];
		$find_fields[] = [
			'code' => 'weight',
			'help' => '',
			'title' => $this->language->get('cell_weight'),
		];
		$find_fields[] = [
			'code' => 'weight_class_id',
			'help' => '',
			'title' => $this->language->get('cell_weight_class_id'),
		];
		$find_fields[] = [
			'code' => 'weight_class_name',
			'help' => '',
			'title' => $this->language->get('cell_weight_class_name'),
		];
		$find_fields[] = [
			'code' => 'manufacturer_id',
			'help' => '',
			'title' => $this->language->get('cell_manufacturer_id'),
		];
		$find_fields[] = [
			'code' => 'manufacturer_name',
			'help' => '',
			'title' => $this->language->get('cell_manufacturer_name'),
		];
		$find_fields[] = [
			'code' => 'category_ids',
			'help' => '',
			'title' => $this->language->get('cell_category_ids'),
		];
		$find_fields[] = [
			'code' => 'category_name',
			'help' => '',
			'title' => $this->language->get('cell_category_name'),
		];
		$find_fields[] = [
			'code' => 'filter',
			'help' => '',
			'title' => $this->language->get('cell_filter'),
		];
		$find_fields[] = [
			'code' => 'download',
			'help' => '',
			'title' => $this->language->get('cell_download'),
		];
		$find_fields[] = [
			'code' => 'related_products',
			'help' => '',
			'title' => $this->language->get('cell_related_products'),
		];
		$find_fields[] = [
			'code' => 'attribute',
			'help' => '',
			'title' => $this->language->get('cell_attribute'),
		];
		$find_fields[] = [
			'code' => 'options',
			'help' => '',
			'title' => $this->language->get('cell_options'),
		];
		$find_fields[] = [
			'code' => 'discount',
			'help' => '',
			'title' => $this->language->get('cell_discount'),
		];
		$find_fields[] = [
			'code' => 'special',
			'help' => '',
			'title' => $this->language->get('cell_special'),
		];
		$find_fields[] = [
			'code' => 'points',
			'help' => '',
			'title' => $this->language->get('cell_points'),
		];
		$find_fields[] = [
			'code' => 'reward',
			'help' => '',
			'title' => $this->language->get('cell_reward'),
		];
		$find_fields[] = [
			'code' => 'viewed',
			'help' => '',
			'title' => $this->language->get('cell_viewed'),
		];
		$find_fields[] = [
			'code' => 'date_added',
			'help' => '',
			'title' => $this->language->get('cell_date_added'),
		];
		$find_fields[] = [
			'code' => 'date_modified',
			'help' => '',
			'title' => $this->language->get('cell_date_modified'),
		];

		$find_fields[] = [
			'code' => 'find_image',
			'help' => '',
			'title' => $this->language->get('entry_image'),
		];
		$find_fields[] = [
			'code' => 'find_review',
			'help' => '',
			'title' => $this->language->get('entry_review'),
		];

		$extrafields = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getExtraFields();

		foreach ($extrafields as $key => $value) {
			foreach ($value['fields'] as $key1 => $field) {
				$find_fields[] = [
					'code' => $value['tablename'] . '__' . $field,
					'help' => $this->language->get('help_extra_field'),
					'title' => $this->language->get('entry_extra_field') . ' :: ' . $value['title'] . ' . ' . $field,
				];
			}
		}

		return $find_fields;
	}

	// Product Export Function
	public function export() {

		$this->load->language('mpimporterexporter/export_product');

		$this->load->model($this->isdir_extension . 'mpimporterexporter/export_product');
		$this->load->model('setting/store');

		if (isset($this->request->post['find_store_id']) && $this->request->post['find_store_id'] != '') {
			$find_store_id = $this->request->post['find_store_id'];
		} else {
			$find_store_id = null;
		}

		if (isset($this->request->post['find_language_id']) && $this->request->post['find_language_id'] != '') {
			$find_language_id = $this->request->post['find_language_id'];
		} else {
			$find_language_id = null;
		}

		if (isset($this->request->post['find_quantity_start']) && $this->request->post['find_quantity_start'] != '') {
			$find_quantity_start = $this->request->post['find_quantity_start'];
		} else {
			$find_quantity_start = '';
		}

		if (isset($this->request->post['find_quantity_limit']) && $this->request->post['find_quantity_limit'] != '') {
			$find_quantity_limit = $this->request->post['find_quantity_limit'];
		} else {
			$find_quantity_limit = '';
		}

		if (isset($this->request->post['find_price_start']) && $this->request->post['find_price_start'] != '') {
			$find_price_start = $this->request->post['find_price_start'];
		} else {
			$find_price_start = '';
		}

		if (isset($this->request->post['find_price_limit']) && $this->request->post['find_price_limit'] != '') {
			$find_price_limit = $this->request->post['find_price_limit'];
		} else {
			$find_price_limit = '';
		}

		if (isset($this->request->post['find_product_start']) && $this->request->post['find_product_start'] != '') {
			$find_product_start = $this->request->post['find_product_start'];
		} else {
			$find_product_start = '';
		}

		if (isset($this->request->post['find_product_limit']) && $this->request->post['find_product_limit'] != '') {
			$find_product_limit = $this->request->post['find_product_limit'];
		} else {
			$find_product_limit = '';
		}

		if (isset($this->request->post['find_status']) && $this->request->post['find_status'] != '') {
			$find_status = $this->request->post['find_status'];
		} else {
			$find_status = null;
		}

		if (isset($this->request->post['find_stock_status_id']) && $this->request->post['find_stock_status_id'] != '') {
			$find_stock_status_id = $this->request->post['find_stock_status_id'];
		} else {
			$find_stock_status_id = null;
		}

		if (isset($this->request->post['find_format']) && $this->request->post['find_format'] != '') {
			$find_format = in_array($this->request->post['find_format'], ['csv','xls','xlsx','json','xml']) ? $this->request->post['find_format'] : 'xlsx';
		} else {
			$find_format = 'xlsx';
		}

		if (isset($this->request->post['find_model']) && $this->request->post['find_model'] != '') {
			$find_model = $this->request->post['find_model'];
		} else {
			$find_model = null;
		}

		if (isset($this->request->post['find_product']) && $this->request->post['find_product'] != '') {
			$find_product = $this->request->post['find_product'];
		} else {
			$find_product = null;
		}

		if (isset($this->request->post['find_manufacturer']) && $this->request->post['find_manufacturer'] != '') {
			$find_manufacturer = $this->request->post['find_manufacturer'];
		} else {
			$find_manufacturer = null;
		}

		if (isset($this->request->post['find_category']) && $this->request->post['find_category'] != '') {
			$find_category = $this->request->post['find_category'];
		} else {
			$find_category = null;
		}

		if (!empty($this->request->post['productexport_setting_fields'])) {
			$find_fields = (array)$this->request->post['productexport_setting_fields'];
		}else{
			$find_fields = [];
		}

		// foreach ($find_fields as $code => $value) {
		// 	$find_fields[$code]['alphabet'] = $this->mpalphanumexcel->numberToAlphabet($value['sort_order']);
		// }

		$find_extrafields = [];
		// {{ extrafield.tablename }}::{{ extrafield_name }}
		$extrafields = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getExtraFields();
		foreach ($extrafields as $key => $value) {
			foreach ($value['fields'] as $key1 => $field) {
				if (isset($find_fields[$value['tablename'] . '__' . $field])) {
					$find_extrafields[$value['tablename'] . '__' . $field] = $find_fields[$value['tablename'] . '__' . $field];
					$find_extrafields[$value['tablename'] . '__' . $field]['title'] = $value['title'] ." :: ". $value['tablename'] .".". $field;
					$find_extrafields[$value['tablename'] . '__' . $field]['field'] = $field;
					$find_extrafields[$value['tablename'] . '__' . $field]['tablename'] = $value['tablename'];
					// $this->language->get('entry_extra_field') . ' :: ' . $value['title'] . ' . ' . $field
				}
			}
		}

		$find_image = (isset($find_fields['find_image']) && isset($find_fields['find_image']['code']));

		$find_review = (isset($find_fields['find_review']) && isset($find_fields['find_review']['code']));

		$filter_data = [
			'find_store_id' => $find_store_id,
			'find_language_id' => $find_language_id,
			'find_model' => $find_model,
			'find_status' => $find_status,
			'find_quantity_start' => $find_quantity_start,
			'find_quantity_limit' => $find_quantity_limit,
			'find_price_start' => $find_price_start,
			'find_price_limit' => $find_price_limit,
			'find_product_start' => $find_product_start,
			'find_product_limit' => $find_product_limit,
			'find_stock_status_id' => $find_stock_status_id,
			'find_product' => $find_product,
			'find_manufacturer' => $find_manufacturer,
			'find_category' => $find_category,
		];

		// Fetch Products
		$results = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProducts($filter_data);
		if (in_array($find_format, ['xls','xlsx','csv'])) {


			$objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

			$objPHPExcel->setActiveSheetIndex(0);
			$i = 1;
			$char = 'A';

			// $objPHPExcel->getActiveSheet()->getStyle('1')->getFill()->applyFromArray(array(
			// 	'type' => PHPExcel_Style_Fill::FILL_SOLID,
			// 	'startcolor' 	=> array(
			// 	'rgb'  => '017FBE',
			// 	),
			// ));

			// $objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray(array(
			// 	'font'  => array(
			// 	'color' => array('rgb' => 'FFFFFF'),
			// 	'bold'  => true,
			// 	)
			// ));

			// $objPHPExcel->getActiveSheet()->freezePane('D2');

			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_product_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_product_name'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_model'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_language'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_store'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_description'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_meta_title'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_meta_description'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_meta_keyword'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_tag'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_image'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_sku'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_upc'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_ean'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_jan'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_isbn'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char++ .$i, $this->language->get('export_mpn'));
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_location'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_price'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_min_quantity'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_quantity'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_status'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_sort_order'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_taxclass_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_tax_class'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_subtract'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_stock_status_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_stock_status'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_shipping'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_seo'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_date_avaiable'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_length'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_length_class_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_length_class'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_width'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_height'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_weight'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_weight_class_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_weight_class'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_manufacturer_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_manufacturer'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_categories'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_categories_name'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_filter'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_download'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_related_products'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_attribute'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_options'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_discount'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_special'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_images'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_points'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_reward'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_viewed'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_date_added'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_date_modified'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char++ .$i, $this->language->get('export_review'));

			if (!empty($find_extrafields)) {
				foreach ($find_extrafields as $find_key_extrafield => $find_extrafield) {
					if (isset($find_fields[$find_key_extrafield]['code'])) {
						$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $find_extrafield['title'])->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
					}
				}
			}

			// Background Color
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.$objPHPExcel->getActiveSheet()->getHighestColumn().'1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('1A017FBE');
			// Font Color
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.$objPHPExcel->getActiveSheet()->getHighestColumn().'1')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FFFFFFFF');

			if ($results) {
				// Fetch Total Products
				$objPHPExcel->getActiveSheet()->setTitle(sprintf($this->language->get('export_title'), count($results)));

				foreach ($results as $result) {
					$char_value = 'A'; $i++;

					// Language
					$language_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getLanguage($result['language_id']);
					$result['language'] = ($language_info) ? $language_info['code']: '';

					// Store
					if (isset($find_store_id) && $find_store_id != '') {
						$stores = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductStores($result['product_id'], $find_store_id);
					} else {
						$stores = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductStores($result['product_id']);
					}

					$export_stores = [];
					foreach ($stores as $store_id) {
						if ($store_id == '0') {
							$export_stores[] = $this->language->get('text_default');
						} else {
							$store_info = $this->model_setting_store->getStore($store_id);
							$export_stores[] = ($store_info) ? $store_info['name'] : '';
						}
					}
					$result['store'] = implode('::', $export_stores);

					// Tax Class
					$tax_class_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getTaxClass($result['tax_class_id']);
					$result['tax_class'] = ($tax_class_info) ? $tax_class_info['title'] : '';

					// Stock Status
					$stock_status_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getStockStatus($result['stock_status_id'], $result['language_id']);
					$result['stock_status'] = ($stock_status_info) ? $stock_status_info['name'] : '';

					// Keyword
					$keyword_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getKeyword($result['product_id'], $result['language_id'], $find_store_id);
					$result['seo_url'] = ($keyword_info) ? $keyword_info['keyword'] : '';

					// Length Class
					$length_class_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getLengthClass($result['length_class_id'], $result['language_id']);
					$result['length_class'] = ($length_class_info) ? $length_class_info['title'] : '';

					// Weight Class
					$weight_class_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getWeightClass($result['weight_class_id'], $result['language_id']);

					$result['weight_class'] = ($weight_class_info) ? $weight_class_info['title'] : '';

					// Manufacturer
					$manufacturer_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getManufacturer($result['manufacturer_id'], $result['language_id'], $find_store_id);
					$result['manufacturer'] = ($manufacturer_info) ? $manufacturer_info['name'] : '';

					// Categories
					$categories_ids = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductCategories($result['product_id']);
					$result['categories_ids'] = ($categories_ids) ? implode(',', $categories_ids) : '';

					// Category Names
					$category_names = [];
					foreach ($categories_ids as $category_id) {
						$category_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getCategory($category_id, $result['language_id']);
						if ($category_info) {
							$category_names[] = $category_info['name'];
						}
					}
					$result['category_names'] = ($category_names) ? implode(' :: ', $category_names) : '';

					// Filters
					$filter_ids = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductFilters($result['product_id']);
					$filter_names = [];
					foreach ($filter_ids as $filter_id) {
						$filter_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getFilter($filter_id, $result['language_id']);
						if ($filter_info) {
							$filter_names[] = $filter_info['group'] .' - '. $filter_info['name'];
						}
					}

					$result['filter_names'] = ($filter_names) ? implode(' :: ', $filter_names) : '';

					// Downloads
					$downloads = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductDownloads($result['product_id']);
					$download_names = [];
					foreach ($downloads as $download_id) {
						$download_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getDownload($download_id, $result['language_id']);
						if ($download_info) {
							$download_names[] = $download_info['name'];
						}
					}
					$result['download_names'] = ($download_names) ? implode(' :: ', $download_names) : '';

					// Related Products
					$product_ids = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductRelated($result['product_id']);
					$result['related_products'] = ($product_ids) ? implode(',', $product_ids) : '';

					// Attribute
					$attributes = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductAttributes($result['product_id']);
					$attribute_names = [];
					foreach ($attributes as $attribute) {
						$attribute_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getAttribute($attribute['attribute_id'], $result['language_id']);
						if ($attribute_info) {
							$attribute_group_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getAttributeGroup($attribute_info['attribute_group_id'], $result['language_id']);
							if ($attribute_group_info) {
								$attribute_names[] = $attribute_info['name'].'::'.$attribute_group_info['name'].'::'.$attribute['product_attribute_description'][$result['language_id']]['text'];
							}
						}
					}

					$result['attribute_names'] = ($attribute_names) ? implode('; ', $attribute_names) : '';

					// Images
					$result['additional_images'] = '';
					if ($find_image) {
						$images = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductImages($result['product_id']);
						$additional_images = [];
						foreach ($images as $image) {
							$additional_images[] = $image['image'];
						}
						$result['additional_images'] = ($additional_images) ? implode(' :: ', $additional_images) : '';
					}

					// Specials
					$specials = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductSpecials($result['product_id']);
					$specials_offers = [];
					foreach ($specials as $special) {
						$specials_offers[] = $special['customer_group_id']. '::' .$special['priority'] .'::'. $special['price'] .'::'. $special['date_start'] .'::'. $special['date_end'];;
					}
					$result['specials_offers'] = ($specials_offers) ? implode('; ', $specials_offers) : '';

					// Discount Offer
					$discounts = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductDiscounts($result['product_id']);
					$discounts_offers = [];
					foreach ($discounts as $discount) {
						$discounts_offers[] = $discount['customer_group_id']. '::' .$discount['quantity'] .'::' .$discount['priority'] .'::'. $discount['price'] .'::'. $discount['date_start'] .'::'. $discount['date_end'];
					}
					$result['discounts_offers'] = ($discounts_offers) ? implode('; ', $discounts_offers) : '';

					// Rewards
					$rewards = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductRewards($result['product_id']);
					$rewards_data = [];
					foreach ($rewards as $customer_group_id => $reward) {
						$rewards_data[] = $customer_group_id .'::'. $reward['points'];
					}
					$result['rewards_data'] = ($rewards_data) ? implode('; ', $rewards_data) : '';

					// Options
					$options = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductOptions($result['product_id'], $result['language_id']);
					$options_data = [];
					foreach ($options as $option) {
						$options_string = html_entity_decode($this->ifnull($option['name']), ENT_QUOTES, 'UTF-8') .' :: '.$option['type'].' :: '.$option['required'] .' :: ';
						if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
							$option_value_row = 1;
							foreach ($option['product_option_value'] as $option_value_key => $product_option_value) {
								$options_string .= $product_option_value['name'] .' ^^ '.$product_option_value['quantity'] .' ^^ '. $product_option_value['subtract'].' ^^ '. $product_option_value['price'] .' ^^ '. $product_option_value['price_prefix'] .' ^^ '. $product_option_value['points'] .' ^^ '. $product_option_value['points_prefix'] .' ^^ '. $product_option_value['weight'] .' ^^ '. $product_option_value['weight_prefix'];

								if (count($option['product_option_value']) != $option_value_row) {
									$options_string .= ' || ';
								}

								$option_value_row++;
							}
						} elseif ($option['type'] == 'file') {
							// No Value for type file;
						} else {
							$options_string .= $option['value'];
						}

						$options_data[] = $options_string;
					}

					$result['options_data'] = ($options_data) ? implode(';; ', $options_data) : '';

					// Reviews
					$result['reviews_data'] = '';
					if ($find_review) {
						$reviews = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getReviews($result['product_id'], $result['language_id']);
						$reviews_data = [];
						foreach ($reviews as $review) {
							$reviews_data[] = $review['customer_id'] .' :: '. $review['author'] .' :: '. $review['text'] .' :: '. $review['rating'] .' :: '. $review['status'] .' :: '. $review['date_added'] .' :: '. $review['date_modified'];
						}
						$result['reviews_data'] = ($reviews_data) ? implode(';; ', $reviews_data) : '';
					}

					if (isset($find_fields['product_id']['code'])) {
						$result_product_id = $result['product_id'];
					} else {
						$result_product_id = '';
					}

					if (isset($find_fields['product_name']['code'])) {
						$result_product_name = $result['name'];
					} else {
						$result_product_name = '';
					}

					if (isset($find_fields['product_model']['code'])) {
						$result_product_model = $result['model'];
					} else {
						$result_product_model = '';
					}

					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_product_id);

					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, html_entity_decode($this->ifnull($result_product_name), ENT_QUOTES, 'UTF-8'));
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_product_model);

					if (isset($find_fields['language_code']['code'])) {
						$result_language = $result['language'];
					} else {
						$result_language = '';
					}

					if (isset($find_fields['store']['code'])) {
						$result_store = $result['store'];
					} else {
						$result_store = '';
					}

					if (isset($find_fields['description']['code'])) {
						$result_description = $result['description'];
					} else {
						$result_description = '';
					}

					if (isset($find_fields['meta_title']['code'])) {
						$result_meta_title = $result['meta_title'];
					} else {
						$result_meta_title = '';
					}

					if (isset($find_fields['meta_description']['code'])) {
						$result_meta_description = $result['meta_description'];
					} else {
						$result_meta_description = '';
					}

					if (isset($find_fields['meta_keyword']['code'])) {
						$result_meta_keyword = $result['meta_keyword'];
					} else {
						$result_meta_keyword = '';
					}

					if (isset($find_fields['tag']['code'])) {
						$result_tag = $result['tag'];
					} else {
						$result_tag = '';
					}

					if (isset($find_fields['product_image']['code'])) {
						$result_image = $result['image'];
					} else {
						$result_image = '';
					}

					if (isset($find_fields['sku']['code'])) {
						$result_sku = $result['sku'];
					} else {
						$result_sku = '';
					}

					if (isset($find_fields['upc']['code'])) {
						$result_upc = $result['upc'];
					} else {
						$result_upc = '';
					}

					if (isset($find_fields['ean']['code'])) {
						$result_ean = $result['ean'];
					} else {
						$result_ean = '';
					}

					if (isset($find_fields['jan']['code'])) {
						$result_jan = $result['jan'];
					} else {
						$result_jan = '';
					}

					if (isset($find_fields['isbn']['code'])) {
						$result_isbn = $result['isbn'];
					} else {
						$result_isbn = '';
					}

					if (isset($find_fields['mpn']['code'])) {
						$result_mpn = $result['mpn'];
					} else {
						$result_mpn = '';
					}

					if (isset($find_fields['location']['code'])) {
						$result_location = $result['location'];
					} else {
						$result_location = '';
					}

					if (isset($find_fields['price']['code'])) {
						$result_price = $result['price'];
					} else {
						$result_price = '';
					}

					if (isset($find_fields['minimum_quantity']['code'])) {
						$result_minimum = $result['minimum'];
					} else {
						$result_minimum = '';
					}

					if (isset($find_fields['quantity']['code'])) {
						$result_quantity = $result['quantity'];
					} else {
						$result_quantity = '';
					}

					if (isset($find_fields['status']['code'])) {
						$result_status = $result['status'];
					} else {
						$result_status = '';
					}

					if (isset($find_fields['sort_order']['code'])) {
						$result_sort_order = $result['sort_order'];
					} else {
						$result_sort_order = '';
					}

					if (isset($find_fields['tax_class_id']['code'])) {
						$result_tax_class_id = $result['tax_class_id'];
					} else {
						$result_tax_class_id = '';
					}


					if (isset($find_fields['tax_class_name']['code'])) {
						$result_tax_class = $result['tax_class'];
					} else {
						$result_tax_class = '';
					}

					if (isset($find_fields['subtract']['code'])) {
						$result_subtract = $result['subtract'];
					} else {
						$result_subtract = '';
					}

					if (isset($find_fields['stock_status_id']['code'])) {
						$result_stock_status_id = $result['stock_status_id'];
					} else {
						$result_stock_status_id = '';
					}

					if (isset($find_fields['stock_status_name']['code'])) {
						$result_stock_status = $result['stock_status'];
					} else {
						$result_stock_status = '';
					}

					if (isset($find_fields['shipping_required']['code'])) {
						$result_shipping = $result['shipping'];
					} else {
						$result_shipping = '';
					}

					if (isset($find_fields['seo_keyword']['code'])) {
						$result_seo_url = $result['seo_url'];
					} else {
						$result_seo_url = '';
					}

					if (isset($find_fields['date_available']['code'])) {
						$result_date_available = $result['date_available'];
					} else {
						$result_date_available = '';
					}

					if (isset($find_fields['length']['code'])) {
						$result_length = $result['length'];
					} else {
						$result_length = '';
					}

					if (isset($find_fields['length_class_id']['code'])) {
						$result_length_class_id = $result['length_class_id'];
					} else {
						$result_length_class_id = '';
					}

					if (isset($find_fields['length_class_name']['code'])) {
						$result_length_class = $result['length_class'];
					} else {
						$result_length_class = '';
					}

					if (isset($find_fields['width']['code'])) {
						$result_width = $result['width'];
					} else {
						$result_width = '';
					}

					if (isset($find_fields['height']['code'])) {
						$result_height = $result['height'];
					} else {
						$result_height = '';
					}

					if (isset($find_fields['weight']['code'])) {
						$result_weight = $result['weight'];
					} else {
						$result_weight = '';
					}

					if (isset($find_fields['weight_class_id']['code'])) {
						$result_weight_class_id = $result['weight_class_id'];
					} else {
						$result_weight_class_id = '';
					}

					if (isset($find_fields['weight_class_name']['code'])) {
						$result_weight_class = $result['weight_class'];
					} else {
						$result_weight_class = '';
					}

					if (isset($find_fields['manufacturer_id']['code'])) {
						$result_manufacturer_id = $result['manufacturer_id'];
					} else {
						$result_manufacturer_id = '';
					}

					if (isset($find_fields['manufacturer_name']['code'])) {
						$result_manufacturer = $result['manufacturer'];
					} else {
						$result_manufacturer = '';
					}

					if (isset($find_fields['category_ids']['code'])) {
						$result_categories_ids = $result['categories_ids'];
					} else {
						$result_categories_ids = '';
					}

					if (isset($find_fields['category_name']['code'])) {
						$result_category_names = $result['category_names'];
					} else {
						$result_category_names = '';
					}

					if (isset($find_fields['filter']['code'])) {
						$result_filter_names = $result['filter_names'];
					} else {
						$result_filter_names = '';
					}

					if (isset($find_fields['download']['code'])) {
						$result_download_names = $result['download_names'];
					} else {
						$result_download_names = '';
					}

					if (isset($find_fields['related_products']['code'])) {
						$result_related_products = $result['related_products'];
					} else {
						$result_related_products = '';
					}

					if (isset($find_fields['attribute']['code'])) {
						$result_attribute_names = $result['attribute_names'];
					} else {
						$result_attribute_names = '';
					}

					if (isset($find_fields['options']['code'])) {
						$result_options_data = $result['options_data'];
					} else {
						$result_options_data = '';
					}

					if (isset($find_fields['discount']['code'])) {
						$result_discounts_offers = $result['discounts_offers'];
					} else {
						$result_discounts_offers = '';
					}

					if (isset($find_fields['special']['code'])) {
						$result_specials_offers = $result['specials_offers'];
					} else {
						$result_specials_offers = '';
					}

					if (isset($find_fields['points']['code'])) {
						$result_points = $result['points'];
					} else {
						$result_points = '';
					}

					if (isset($find_fields['reward']['code'])) {
						$result_rewards_data = $result['rewards_data'];
					} else {
						$result_rewards_data = '';
					}

					if (isset($find_fields['viewed']['code'])) {
						$result_viewed = $result['viewed'];
					} else {
						$result_viewed = '';
					}

					if (isset($find_fields['date_added']['code'])) {
						$result_date_added = $result['date_added'];
					} else {
						$result_date_added = '';
					}

					if (isset($find_fields['date_modified']['code'])) {
						$result_date_modified = $result['date_modified'];
					} else {
						$result_date_modified = '';
					}

					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_language);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_store);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, html_entity_decode($this->ifnull($result_description), ENT_QUOTES, 'UTF-8'));
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_meta_title);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_meta_description);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_meta_keyword);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_tag);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_image);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_sku);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_upc);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_ean);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_jan);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_isbn);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_mpn);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_location);
					if ($result_price) {
						$objPHPExcel->getActiveSheet()->setCellValue($char_value .$i, number_format((float)$result_price, 2))->getStyle($char_value++ .$i)->getNumberFormat()->setFormatCode('#,##0.00');
					} else {
						$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_price);
					}
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_minimum);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_quantity);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_status);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_sort_order);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_tax_class_id);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_tax_class);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_subtract);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_stock_status_id);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_stock_status);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_shipping);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_seo_url);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_date_available);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_length);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_length_class_id);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_length_class);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_width);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_height);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_weight);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_weight_class_id);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_weight_class);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_manufacturer_id);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_manufacturer);

					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_categories_ids);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, html_entity_decode($this->ifnull($result_category_names), ENT_QUOTES, 'UTF-8'));
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_filter_names);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_download_names);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_related_products);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_attribute_names);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_options_data);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_discounts_offers);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_specials_offers);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['additional_images']);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_points);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_rewards_data);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_viewed);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_date_added);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result_date_modified);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['reviews_data']);

					if (!empty($find_extrafields)) {
						foreach ($find_extrafields as $find_key_extrafield => $find_extrafield) {
							if (isset($find_fields[$find_key_extrafield]['code']) && isset($result[$find_extrafield['field']])) {
								$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, html_entity_decode($this->ifnull($result[$find_extrafield['field']]), ENT_QUOTES, 'UTF-8'));
							}
						}
					}
				}

				// Find Format
				if ($find_format == 'xls') {
					$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xls($objPHPExcel);
					$file_name = 'ProductList.xls';
				} elseif ($find_format == 'xlsx') {
					$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
					$file_name = 'ProductList.xlsx';
				} elseif ($find_format == 'csv') {
					$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Csv($objPHPExcel);
					$file_name = 'ProductList.csv';
				} else {
					$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
					$file_name = 'ProductList.xlsx';
				}

				$file_to_save = DIR_UPLOAD . $file_name;
				$objWriter->save(DIR_UPLOAD . $file_name);

			}
		}

		if ('json' == $find_format) {
			$export_data = [];
			// add meta data in json file, if possible with php
			if ($results) {
				// Fetch Total Products
				// $objPHPExcel->getActiveSheet()->setTitle(sprintf($this->language->get('export_title'), count($results)));
				$i = 0;
				foreach ($results as $result) {
					// Language
					$language_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getLanguage($result['language_id']);
					$result['language'] = ($language_info) ? $language_info['code']: '';

					// Store
					if (isset($find_store_id) && $find_store_id != '') {
						$stores = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductStores($result['product_id'], $find_store_id);
					} else {
						$stores = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductStores($result['product_id']);
					}

					$export_stores = [];
					foreach ($stores as $store_id) {
						if ($store_id == '0') {
							$export_stores[] = $this->language->get('text_default');
						} else {
							$store_info = $this->model_setting_store->getStore($store_id);
							$export_stores[] = ($store_info) ? $store_info['name'] : '';
						}
					}
					$result['store'] = implode('::', $export_stores);

					// Tax Class
					$tax_class_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getTaxClass($result['tax_class_id']);
					$result['tax_class'] = ($tax_class_info) ? $tax_class_info['title'] : '';

					// Stock Status
					$stock_status_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getStockStatus($result['stock_status_id'], $result['language_id']);
					$result['stock_status'] = ($stock_status_info) ? $stock_status_info['name'] : '';

					// Keyword
					$keyword_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getKeyword($result['product_id'], $result['language_id'], $find_store_id);
					$result['seo_url'] = ($keyword_info) ? $keyword_info['keyword'] : '';

					// Length Class
					$length_class_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getLengthClass($result['length_class_id'], $result['language_id']);
					$result['length_class'] = ($length_class_info) ? $length_class_info['title'] : '';

					// Weight Class
					$weight_class_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getWeightClass($result['weight_class_id'], $result['language_id']);

					$result['weight_class'] = ($weight_class_info) ? $weight_class_info['title'] : '';

					// Manufacturer
					$manufacturer_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getManufacturer($result['manufacturer_id'], $result['language_id'], $find_store_id);
					$result['manufacturer'] = ($manufacturer_info) ? $manufacturer_info['name'] : '';

					// Categories
					$categories_ids = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductCategories($result['product_id']);
					$result['categories_ids'] = ($categories_ids) ? implode(',', $categories_ids) : '';

					// Category Names
					$category_names = [];
					foreach ($categories_ids as $category_id) {
						$category_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getCategory($category_id, $result['language_id']);
						if ($category_info) {
							$category_names[] = $category_info['name'];
						}
					}
					$result['category_names'] = ($category_names) ? implode(' :: ', $category_names) : '';

					// Filters
					$filter_ids = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductFilters($result['product_id']);
					$filter_names = [];
					foreach ($filter_ids as $filter_id) {
						$filter_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getFilter($filter_id, $result['language_id']);
						if ($filter_info) {
							$filter_names[] = $filter_info['group'] .' - '. $filter_info['name'];
						}
					}

					$result['filter_names'] = ($filter_names) ? implode(' :: ', $filter_names) : '';

					// Downloads
					$downloads = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductDownloads($result['product_id']);
					$download_names = [];
					foreach ($downloads as $download_id) {
						$download_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getDownload($download_id, $result['language_id']);
						if ($download_info) {
							$download_names[] = $download_info['name'];
						}
					}
					$result['download_names'] = ($download_names) ? implode(' :: ', $download_names) : '';

					// Related Products
					$product_ids = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductRelated($result['product_id']);
					$result['related_products'] = ($product_ids) ? implode(',', $product_ids) : '';

					// Attribute
					$attributes = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductAttributes($result['product_id']);
					$attribute_names = [];
					foreach ($attributes as $attribute) {
						$attribute_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getAttribute($attribute['attribute_id'], $result['language_id']);
						if ($attribute_info) {
							$attribute_group_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getAttributeGroup($attribute_info['attribute_group_id'], $result['language_id']);
							if ($attribute_group_info) {
								$attribute_names[] = $attribute_info['name'].'::'.$attribute_group_info['name'].'::'.$attribute['product_attribute_description'][$result['language_id']]['text'];
							}
						}
					}

					$result['attribute_names'] = ($attribute_names) ? implode('; ', $attribute_names) : '';

					// Images
					$result['additional_images'] = '';
					if ($find_image) {
						$images = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductImages($result['product_id']);
						$additional_images = [];
						foreach ($images as $image) {
							$additional_images[] = $image['image'];
						}
						$result['additional_images'] = ($additional_images) ? implode(' :: ', $additional_images) : '';
					}

					// Specials
					$specials = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductSpecials($result['product_id']);
					$specials_offers = [];
					foreach ($specials as $special) {
						$specials_offers[] = $special['customer_group_id']. '::' .$special['priority'] .'::'. $special['price'] .'::'. $special['date_start'] .'::'. $special['date_end'];;
					}
					$result['specials_offers'] = ($specials_offers) ? implode('; ', $specials_offers) : '';

					// Discount Offer
					$discounts = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductDiscounts($result['product_id']);
					$discounts_offers = [];
					foreach ($discounts as $discount) {
						$discounts_offers[] = $discount['customer_group_id']. '::' .$discount['quantity'] .'::' .$discount['priority'] .'::'. $discount['price'] .'::'. $discount['date_start'] .'::'. $discount['date_end'];
					}
					$result['discounts_offers'] = ($discounts_offers) ? implode('; ', $discounts_offers) : '';

					// Rewards
					$rewards = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductRewards($result['product_id']);
					$rewards_data = [];
					foreach ($rewards as $customer_group_id => $reward) {
						$rewards_data[] = $customer_group_id .'::'. $reward['points'];
					}
					$result['rewards_data'] = ($rewards_data) ? implode('; ', $rewards_data) : '';

					// Options
					$options = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductOptions($result['product_id'], $result['language_id']);
					$options_data = [];
					foreach ($options as $option) {
						$options_string = html_entity_decode($this->ifnull($option['name']), ENT_QUOTES, 'UTF-8') .' :: '.$option['type'].' :: '.$option['required'] .' :: ';
						if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
							$option_value_row = 1;
							foreach ($option['product_option_value'] as $option_value_key => $product_option_value) {
								$options_string .= $product_option_value['name'] .' ^^ '.$product_option_value['quantity'] .' ^^ '. $product_option_value['subtract'].' ^^ '. $product_option_value['price'] .' ^^ '. $product_option_value['price_prefix'] .' ^^ '. $product_option_value['points'] .' ^^ '. $product_option_value['points_prefix'] .' ^^ '. $product_option_value['weight'] .' ^^ '. $product_option_value['weight_prefix'];

								if (count($option['product_option_value']) != $option_value_row) {
									$options_string .= ' || ';
								}

								$option_value_row++;
							}
						} elseif ($option['type'] == 'file') {
							// No Value for type file;
						} else {
							$options_string .= $option['value'];
						}

						$options_data[] = $options_string;
					}

					$result['options_data'] = ($options_data) ? implode(';; ', $options_data) : '';

					// Reviews
					$result['reviews_data'] = '';
					if ($find_review) {
						$reviews = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getReviews($result['product_id'], $result['language_id']);
						$reviews_data = [];
						foreach ($reviews as $review) {
							$reviews_data[] = $review['customer_id'] .' :: '. $review['author'] .' :: '. $review['text'] .' :: '. $review['rating'] .' :: '. $review['status'] .' :: '. $review['date_added'] .' :: '. $review['date_modified'];
						}
						$result['reviews_data'] = ($reviews_data) ? implode(';; ', $reviews_data) : '';
					}

					if (isset($find_fields['product_id']['code'])) {
						$export_data[$i]['product_id'] = [
							'text' => $this->language->get('export_product_id'),
							'value' => $result['product_id']
						];
					}

					if (isset($find_fields['product_name']['code'])) {
						$export_data[$i]['name'] = [
							'text' => $this->language->get('export_product_name'),
							'value' => $result['name']
						];
					}

					if (isset($find_fields['product_model']['code'])) {
						$export_data[$i]['model'] = [
							'text' => $this->language->get('export_model'),
							'value' => $result['model']
						];
					}

					if (isset($find_fields['language_code']['code'])) {
						$export_data[$i]['language'] = [
							'text' => $this->language->get('export_language'),
							'value' => $result['language']
						];
					}

					if (isset($find_fields['store']['code'])) {
						$export_data[$i]['store'] = [
							'text' => $this->language->get('export_store'),
							'value' => $result['store']
						];
					}

					if (isset($find_fields['description']['code'])) {
						$export_data[$i]['description'] = [
							'text' => $this->language->get('export_description'),
							'value' => html_entity_decode($this->ifnull($result['description']), ENT_QUOTES, 'UTF-8')
						];
					}

					if (isset($find_fields['meta_title']['code'])) {
						$export_data[$i]['meta_title'] = [
							'text' => $this->language->get('export_meta_title'),
							'value' => $result['meta_title']
						];
					}

					if (isset($find_fields['meta_description']['code'])) {
						$export_data[$i]['meta_description'] = [
							'text' => $this->language->get('export_meta_description'),
							'value' => $result['meta_description']
						];
					}

					if (isset($find_fields['meta_keyword']['code'])) {
						$export_data[$i]['meta_keyword'] = [
							'text' => $this->language->get('export_meta_keyword'),
							'value' => $result['meta_keyword']
						];
					}

					if (isset($find_fields['tag']['code'])) {
						$export_data[$i]['tag'] = [
							'text' => $this->language->get('export_tag'),
							'value' => $result['tag']
						];
					}

					if (isset($find_fields['product_image']['code'])) {
						$export_data[$i]['image'] = [
							'text' => $this->language->get('export_image'),
							'value' => $result['image']
						];
					}

					if (isset($find_fields['sku']['code'])) {
						$export_data[$i]['sku'] = [
							'text' => $this->language->get('export_sku'),
							'value' => $result['sku']
						];
					}

					if (isset($find_fields['upc']['code'])) {
						$export_data[$i]['upc'] = [
							'text' => $this->language->get('export_upc'),
							'value' => $result['upc']
						];
					}

					if (isset($find_fields['ean']['code'])) {
						$export_data[$i]['ean'] = [
							'text' => $this->language->get('export_ean'),
							'value' => $result['ean']
						];
					}

					if (isset($find_fields['jan']['code'])) {
						$export_data[$i]['jan'] = [
							'text' => $this->language->get('export_jan'),
							'value' => $result['jan']
						];
					}

					if (isset($find_fields['isbn']['code'])) {
						$export_data[$i]['isbn'] = [
							'text' => $this->language->get('export_isbn'),
							'value' => $result['isbn']
						];
					}

					if (isset($find_fields['mpn']['code'])) {
						$export_data[$i]['mpn'] = [
							'text' => $this->language->get('export_mpn'),
							'value' => $result['mpn']
						];
					}

					if (isset($find_fields['location']['code'])) {
						$export_data[$i]['location'] = [
							'text' => $this->language->get('export_location'),
							'value' => $result['location']
						];
					}

					if (isset($find_fields['price']['code'])) {
						$export_data[$i]['price'] = [
							'text' => $this->language->get('export_price'),
							'value' => number_format((float)$result['price'], 2)
						];
					}

					if (isset($find_fields['minimum_quantity']['code'])) {
						$export_data[$i]['minimum'] = [
							'text' => $this->language->get('export_min_quantity'),
							'value' => $result['minimum']
						];
					}

					if (isset($find_fields['quantity']['code'])) {
						$export_data[$i]['quantity'] = [
							'text' => $this->language->get('export_quantity'),
							'value' => $result['quantity']
						];
					}

					if (isset($find_fields['status']['code'])) {
						$export_data[$i]['status'] = [
							'text' => $this->language->get('export_status'),
							'value' => $result['status']
						];
					}

					if (isset($find_fields['sort_order']['code'])) {
						$export_data[$i]['sort_order'] = [
							'text' => $this->language->get('export_sort_order'),
							'value' => $result['sort_order']
						];
					}

					if (isset($find_fields['tax_class_id']['code'])) {
						$export_data[$i]['tax_class_id'] = [
							'text' => $this->language->get('export_taxclass_id'),
							'value' => $result['tax_class_id']
						];
					}


					if (isset($find_fields['tax_class_name']['code'])) {
						$export_data[$i]['tax_class'] = [
							'text' => $this->language->get('export_tax_class'),
							'value' => $result['tax_class']
						];
					}

					if (isset($find_fields['subtract']['code'])) {
						$export_data[$i]['subtract'] = [
							'text' => $this->language->get('export_subtract'),
							'value' => $result['subtract']
						];
					}

					if (isset($find_fields['stock_status_id']['code'])) {
						$export_data[$i]['stock_status_id'] = [
							'text' => $this->language->get('export_stock_status_id'),
							'value' => $result['stock_status_id']
						];
					}

					if (isset($find_fields['stock_status_name']['code'])) {
						$export_data[$i]['stock_status'] = [
							'text' => $this->language->get('export_stock_status'),
							'value' => $result['stock_status']
						];
					}

					if (isset($find_fields['shipping_required']['code'])) {
						$export_data[$i]['shipping'] = [
							'text' => $this->language->get('export_shipping'),
							'value' => $result['shipping']
						];
					}

					if (isset($find_fields['seo_keyword']['code'])) {
						$export_data[$i]['seo_url'] = [
							'text' => $this->language->get('export_seo'),
							'value' => $result['seo_url']
						];
					}

					if (isset($find_fields['date_available']['code'])) {
						$export_data[$i]['date_available'] = [
							'text' => $this->language->get('export_date_avaiable'),
							'value' => $result['date_available']
						];
					}

					if (isset($find_fields['length']['code'])) {
						$export_data[$i]['length'] = [
							'text' => $this->language->get('export_length'),
							'value' => $result['length']
						];
					}

					if (isset($find_fields['length_class_id']['code'])) {
						$export_data[$i]['length_class_id'] = [
							'text' => $this->language->get('export_length_class_id'),
							'value' => $result['length_class_id']
						];
					}

					if (isset($find_fields['length_class_name']['code'])) {
						$export_data[$i]['length_class'] = [
							'text' => $this->language->get('export_length_class'),
							'value' => $result['length_class']
						];
					}

					if (isset($find_fields['width']['code'])) {
						$export_data[$i]['width'] = [
							'text' => $this->language->get('export_width'),
							'value' => $result['width']
						];
					}

					if (isset($find_fields['height']['code'])) {
						$export_data[$i]['height'] = [
							'text' => $this->language->get('export_height'),
							'value' => $result['height']
						];
					}

					if (isset($find_fields['weight']['code'])) {
						$export_data[$i]['weight'] = [
							'text' => $this->language->get('export_weight'),
							'value' => $result['weight']
						];
					}

					if (isset($find_fields['weight_class_id']['code'])) {
						$export_data[$i]['weight_class_id'] = [
							'text' => $this->language->get('export_weight_class_id'),
							'value' => $result['weight_class_id']
						];
					}

					if (isset($find_fields['weight_class_name']['code'])) {
						$export_data[$i]['weight_class'] = [
							'text' => $this->language->get('export_weight_class'),
							'value' => $result['weight_class']
						];
					}

					if (isset($find_fields['manufacturer_id']['code'])) {
						$export_data[$i]['manufacturer_id'] = [
							'text' => $this->language->get('export_manufacturer_id'),
							'value' => $result['manufacturer_id']
						];
					}

					if (isset($find_fields['manufacturer_name']['code'])) {
						$export_data[$i]['manufacturer'] = [
							'text' => $this->language->get('export_manufacturer'),
							'value' => $result['manufacturer']
						];
					}

					if (isset($find_fields['category_ids']['code'])) {
						$export_data[$i]['categories_ids'] = [
							'text' => $this->language->get('export_categories'),
							'value' => $result['categories_ids']
						];
					}

					if (isset($find_fields['category_name']['code'])) {
						$export_data[$i]['category_names'] = [
							'text' => $this->language->get('export_categories_name'),
							'value' => html_entity_decode($this->ifnull($result['category_names']), ENT_QUOTES, 'UTF-8')
						];
					}

					if (isset($find_fields['filter']['code'])) {
						$export_data[$i]['filter_names'] = [
							'text' => $this->language->get('export_filter'),
							'value' => $result['filter_names']
						];
					}

					if (isset($find_fields['download']['code'])) {
						$export_data[$i]['download_names'] = [
							'text' => $this->language->get('export_download'),
							'value' => $result['download_names']
						];
					}

					if (isset($find_fields['related_products']['code'])) {
						$export_data[$i]['related_products'] = [
							'text' => $this->language->get('export_related_products'),
							'value' => $result['related_products']
						];
					}

					if (isset($find_fields['attribute']['code'])) {
						$export_data[$i]['attribute_names'] = [
							'text' => $this->language->get('export_attribute'),
							'value' => $result['attribute_names']
						];
					}

					if (isset($find_fields['options']['code'])) {
						$export_data[$i]['options_data'] = [
							'text' => $this->language->get('export_options'),
							'value' => $result['options_data']
						];
					}

					if (isset($find_fields['discount']['code'])) {
						$export_data[$i]['discounts_offers'] = [
							'text' => $this->language->get('export_discount'),
							'value' => $result['discounts_offers']
						];
					}

					if (isset($find_fields['special']['code'])) {
						$export_data[$i]['specials_offers'] = [
							'text' => $this->language->get('export_special'),
							'value' => $result['specials_offers']
						];
					}

					if (isset($find_fields['points']['code'])) {
						$export_data[$i]['points'] = [
							'text' => $this->language->get('export_points'),
							'value' => $result['points']
						];
					}

					if (isset($find_fields['reward']['code'])) {
						$export_data[$i]['rewards_data'] = [
							'text' => $this->language->get('export_reward'),
							'value' => $result['rewards_data']
						];
					}

					if (isset($find_fields['viewed']['code'])) {
						$export_data[$i]['viewed'] = [
							'text' => $this->language->get('export_viewed'),
							'value' => $result['viewed']
						];
					}

					if (isset($find_fields['date_added']['code'])) {
						$export_data[$i]['date_added'] = [
							'text' => $this->language->get('export_date_added'),
							'value' => $result['date_added']
						];
					}

					if (isset($find_fields['date_modified']['code'])) {
						$export_data[$i]['date_modified'] = [
							'text' => $this->language->get('export_date_modified'),
							'value' => $result['date_modified']
						];
					}

					if ($find_image) {
						$export_data[$i]['additional_images'] = [
							'text' => $this->language->get('export_images'),
							'value' => $result['additional_images']
						];
					}

					if ($find_review) {
						$export_data[$i]['reviews_data'] = [
							'text' => $this->language->get('export_review'),
							'value' => $result['reviews_data']
						];
					}

					if (!empty($find_extrafields)) {
						foreach ($find_extrafields as $find_key_extrafield => $find_extrafield) {
							if (isset($find_fields[$find_key_extrafield]['code']) && isset($result[$find_extrafield['field']])) {
								$export_data[$i][$find_key_extrafield] = [
									'text' => $find_extrafield['title'],
									'value' => html_entity_decode($this->ifnull($result[$find_extrafield['field']]), ENT_QUOTES, 'UTF-8')
								];
							}
						}
					}
					$i++;
				}

				// create a file with name.json
				$file_name = 'ProductList.json';
				$file_to_save = DIR_UPLOAD . $file_name;

				$handle = fopen($file_to_save, "w");

				fwrite($handle, json_encode($export_data, JSON_PRETTY_PRINT));
				fclose($handle);
			}
		}

		if ('xml' == $find_format) {
			$export_data = [];
			// add meta data in xml file, if possible with php
			if ($results) {
				$xml = new \DOMDocument('1.0', 'UTF-8');

		    $xml->preserveWhiteSpace = false;
				$xml->formatOutput=true;

				$xml_products = $xml->createElement("products");
				$xml->appendChild($xml_products);

				// Fetch Total Products
				// $objPHPExcel->getActiveSheet()->setTitle(sprintf($this->language->get('export_title'), count($results)));
				foreach ($results as $result) {
					$export_data = [];
					$i = 0;
					// Language
					$language_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getLanguage($result['language_id']);
					$result['language'] = ($language_info) ? $language_info['code']: '';

					// Store
					if (isset($find_store_id) && $find_store_id != '') {
						$stores = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductStores($result['product_id'], $find_store_id);
					} else {
						$stores = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductStores($result['product_id']);
					}

					$export_stores = [];
					foreach ($stores as $store_id) {
						if ($store_id == '0') {
							$export_stores[] = $this->language->get('text_default');
						} else {
							$store_info = $this->model_setting_store->getStore($store_id);
							$export_stores[] = ($store_info) ? $store_info['name'] : '';
						}
					}
					$result['store'] = implode('::', $export_stores);

					// Tax Class
					$tax_class_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getTaxClass($result['tax_class_id']);
					$result['tax_class'] = ($tax_class_info) ? $tax_class_info['title'] : '';

					// Stock Status
					$stock_status_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getStockStatus($result['stock_status_id'], $result['language_id']);
					$result['stock_status'] = ($stock_status_info) ? $stock_status_info['name'] : '';

					// Keyword
					$keyword_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getKeyword($result['product_id'], $result['language_id'], $find_store_id);
					$result['seo_url'] = ($keyword_info) ? $keyword_info['keyword'] : '';

					// Length Class
					$length_class_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getLengthClass($result['length_class_id'], $result['language_id']);
					$result['length_class'] = ($length_class_info) ? $length_class_info['title'] : '';

					// Weight Class
					$weight_class_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getWeightClass($result['weight_class_id'], $result['language_id']);

					$result['weight_class'] = ($weight_class_info) ? $weight_class_info['title'] : '';

					// Manufacturer
					$manufacturer_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getManufacturer($result['manufacturer_id'], $result['language_id'], $find_store_id);
					$result['manufacturer'] = ($manufacturer_info) ? $manufacturer_info['name'] : '';

					// Categories
					$categories_ids = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductCategories($result['product_id']);
					$result['categories_ids'] = ($categories_ids) ? implode(',', $categories_ids) : '';

					// Category Names
					$category_names = [];
					foreach ($categories_ids as $category_id) {
						$category_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getCategory($category_id, $result['language_id']);
						if ($category_info) {
							$category_names[] = $category_info['name'];
						}
					}
					$result['category_names'] = ($category_names) ? implode(' :: ', $category_names) : '';

					// Filters
					$filter_ids = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductFilters($result['product_id']);
					$filter_names = [];
					foreach ($filter_ids as $filter_id) {
						$filter_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getFilter($filter_id, $result['language_id']);
						if ($filter_info) {
							$filter_names[] = $filter_info['group'] .' - '. $filter_info['name'];
						}
					}

					$result['filter_names'] = ($filter_names) ? implode(' :: ', $filter_names) : '';

					// Downloads
					$downloads = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductDownloads($result['product_id']);
					$download_names = [];
					foreach ($downloads as $download_id) {
						$download_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getDownload($download_id, $result['language_id']);
						if ($download_info) {
							$download_names[] = $download_info['name'];
						}
					}
					$result['download_names'] = ($download_names) ? implode(' :: ', $download_names) : '';

					// Related Products
					$product_ids = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductRelated($result['product_id']);
					$result['related_products'] = ($product_ids) ? implode(',', $product_ids) : '';

					// Attribute
					$attributes = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductAttributes($result['product_id']);
					$attribute_names = [];
					foreach ($attributes as $attribute) {
						$attribute_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getAttribute($attribute['attribute_id'], $result['language_id']);
						if ($attribute_info) {
							$attribute_group_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getAttributeGroup($attribute_info['attribute_group_id'], $result['language_id']);
							if ($attribute_group_info) {
								$attribute_names[] = $attribute_info['name'].'::'.$attribute_group_info['name'].'::'.$attribute['product_attribute_description'][$result['language_id']]['text'];
							}
						}
					}

					$result['attribute_names'] = ($attribute_names) ? implode('; ', $attribute_names) : '';

					// Images
					$result['additional_images'] = '';
					if ($find_image) {
						$images = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductImages($result['product_id']);
						$additional_images = [];
						foreach ($images as $image) {
							$additional_images[] = $image['image'];
						}
						$result['additional_images'] = ($additional_images) ? implode(' :: ', $additional_images) : '';
					}

					// Specials
					$specials = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductSpecials($result['product_id']);
					$specials_offers = [];
					foreach ($specials as $special) {
						$specials_offers[] = $special['customer_group_id']. '::' .$special['priority'] .'::'. $special['price'] .'::'. $special['date_start'] .'::'. $special['date_end'];;
					}
					$result['specials_offers'] = ($specials_offers) ? implode('; ', $specials_offers) : '';

					// Discount Offer
					$discounts = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductDiscounts($result['product_id']);
					$discounts_offers = [];
					foreach ($discounts as $discount) {
						$discounts_offers[] = $discount['customer_group_id']. '::' .$discount['quantity'] .'::' .$discount['priority'] .'::'. $discount['price'] .'::'. $discount['date_start'] .'::'. $discount['date_end'];
					}
					$result['discounts_offers'] = ($discounts_offers) ? implode('; ', $discounts_offers) : '';

					// Rewards
					$rewards = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductRewards($result['product_id']);
					$rewards_data = [];
					foreach ($rewards as $customer_group_id => $reward) {
						$rewards_data[] = $customer_group_id .'::'. $reward['points'];
					}
					$result['rewards_data'] = ($rewards_data) ? implode('; ', $rewards_data) : '';

					// Options
					$options = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getProductOptions($result['product_id'], $result['language_id']);
					$options_data = [];
					foreach ($options as $option) {
						$options_string = html_entity_decode($this->ifnull($option['name']), ENT_QUOTES, 'UTF-8') .' :: '.$option['type'].' :: '.$option['required'] .' :: ';
						if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
							$option_value_row = 1;
							foreach ($option['product_option_value'] as $option_value_key => $product_option_value) {
								$options_string .= $product_option_value['name'] .' ^^ '.$product_option_value['quantity'] .' ^^ '. $product_option_value['subtract'].' ^^ '. $product_option_value['price'] .' ^^ '. $product_option_value['price_prefix'] .' ^^ '. $product_option_value['points'] .' ^^ '. $product_option_value['points_prefix'] .' ^^ '. $product_option_value['weight'] .' ^^ '. $product_option_value['weight_prefix'];

								if (count($option['product_option_value']) != $option_value_row) {
									$options_string .= ' || ';
								}

								$option_value_row++;
							}
						} elseif ($option['type'] == 'file') {
							// No Value for type file;
						} else {
							$options_string .= $option['value'];
						}

						$options_data[] = $options_string;
					}

					$result['options_data'] = ($options_data) ? implode(';; ', $options_data) : '';

					// Reviews
					$result['reviews_data'] = '';
					if ($find_review) {
						$reviews = $this->{'model_' . $this->model_extension . 'mpimporterexporter_export_product'}->getReviews($result['product_id'], $result['language_id']);
						$reviews_data = [];
						foreach ($reviews as $review) {
							$reviews_data[] = $review['customer_id'] .' :: '. $review['author'] .' :: '. $review['text'] .' :: '. $review['rating'] .' :: '. $review['status'] .' :: '. $review['date_added'] .' :: '. $review['date_modified'];
						}
						$result['reviews_data'] = ($reviews_data) ? implode(';; ', $reviews_data) : '';
					}

					if (isset($find_fields['product_id']['code'])) {
						$export_data[$i]['product_id'] = [
							'text' => $this->language->get('export_product_id'),
							'value' => $result['product_id']
						];
					}

					if (isset($find_fields['product_name']['code'])) {
						$export_data[$i]['name'] = [
							'text' => $this->language->get('export_product_name'),
							'value' => $result['name']
						];
					}

					if (isset($find_fields['product_model']['code'])) {
						$export_data[$i]['model'] = [
							'text' => $this->language->get('export_model'),
							'value' => $result['model']
						];
					}

					if (isset($find_fields['language_code']['code'])) {
						$export_data[$i]['language'] = [
							'text' => $this->language->get('export_language'),
							'value' => $result['language']
						];
					}

					if (isset($find_fields['store']['code'])) {
						$export_data[$i]['store'] = [
							'text' => $this->language->get('export_store'),
							'value' => $result['store']
						];
					}

					if (isset($find_fields['description']['code'])) {
						$export_data[$i]['description'] = [
							'text' => $this->language->get('export_description'),
							'value' => html_entity_decode($this->ifnull($result['description']), ENT_QUOTES, 'UTF-8')
						];
					}

					if (isset($find_fields['meta_title']['code'])) {
						$export_data[$i]['meta_title'] = [
							'text' => $this->language->get('export_meta_title'),
							'value' => $result['meta_title']
						];
					}

					if (isset($find_fields['meta_description']['code'])) {
						$export_data[$i]['meta_description'] = [
							'text' => $this->language->get('export_meta_description'),
							'value' => $result['meta_description']
						];
					}

					if (isset($find_fields['meta_keyword']['code'])) {
						$export_data[$i]['meta_keyword'] = [
							'text' => $this->language->get('export_meta_keyword'),
							'value' => $result['meta_keyword']
						];
					}

					if (isset($find_fields['tag']['code'])) {
						$export_data[$i]['tag'] = [
							'text' => $this->language->get('export_tag'),
							'value' => $result['tag']
						];
					}

					if (isset($find_fields['product_image']['code'])) {
						$export_data[$i]['image'] = [
							'text' => $this->language->get('export_image'),
							'value' => $result['image']
						];
					}

					if (isset($find_fields['sku']['code'])) {
						$export_data[$i]['sku'] = [
							'text' => $this->language->get('export_sku'),
							'value' => $result['sku']
						];
					}

					if (isset($find_fields['upc']['code'])) {
						$export_data[$i]['upc'] = [
							'text' => $this->language->get('export_upc'),
							'value' => $result['upc']
						];
					}

					if (isset($find_fields['ean']['code'])) {
						$export_data[$i]['ean'] = [
							'text' => $this->language->get('export_ean'),
							'value' => $result['ean']
						];
					}

					if (isset($find_fields['jan']['code'])) {
						$export_data[$i]['jan'] = [
							'text' => $this->language->get('export_jan'),
							'value' => $result['jan']
						];
					}

					if (isset($find_fields['isbn']['code'])) {
						$export_data[$i]['isbn'] = [
							'text' => $this->language->get('export_isbn'),
							'value' => $result['isbn']
						];
					}

					if (isset($find_fields['mpn']['code'])) {
						$export_data[$i]['mpn'] = [
							'text' => $this->language->get('export_mpn'),
							'value' => $result['mpn']
						];
					}

					if (isset($find_fields['location']['code'])) {
						$export_data[$i]['location'] = [
							'text' => $this->language->get('export_location'),
							'value' => $result['location']
						];
					}

					if (isset($find_fields['price']['code'])) {
						$export_data[$i]['price'] = [
							'text' => $this->language->get('export_price'),
							'value' => number_format((float)$result['price'], 2)
						];
					}

					if (isset($find_fields['minimum_quantity']['code'])) {
						$export_data[$i]['minimum'] = [
							'text' => $this->language->get('export_min_quantity'),
							'value' => $result['minimum']
						];
					}

					if (isset($find_fields['quantity']['code'])) {
						$export_data[$i]['quantity'] = [
							'text' => $this->language->get('export_quantity'),
							'value' => $result['quantity']
						];
					}

					if (isset($find_fields['status']['code'])) {
						$export_data[$i]['status'] = [
							'text' => $this->language->get('export_status'),
							'value' => $result['status']
						];
					}

					if (isset($find_fields['sort_order']['code'])) {
						$export_data[$i]['sort_order'] = [
							'text' => $this->language->get('export_sort_order'),
							'value' => $result['sort_order']
						];
					}

					if (isset($find_fields['tax_class_id']['code'])) {
						$export_data[$i]['tax_class_id'] = [
							'text' => $this->language->get('export_taxclass_id'),
							'value' => $result['tax_class_id']
						];
					}


					if (isset($find_fields['tax_class_name']['code'])) {
						$export_data[$i]['tax_class'] = [
							'text' => $this->language->get('export_tax_class'),
							'value' => $result['tax_class']
						];
					}

					if (isset($find_fields['subtract']['code'])) {
						$export_data[$i]['subtract'] = [
							'text' => $this->language->get('export_subtract'),
							'value' => $result['subtract']
						];
					}

					if (isset($find_fields['stock_status_id']['code'])) {
						$export_data[$i]['stock_status_id'] = [
							'text' => $this->language->get('export_stock_status_id'),
							'value' => $result['stock_status_id']
						];
					}

					if (isset($find_fields['stock_status_name']['code'])) {
						$export_data[$i]['stock_status'] = [
							'text' => $this->language->get('export_stock_status'),
							'value' => $result['stock_status']
						];
					}

					if (isset($find_fields['shipping_required']['code'])) {
						$export_data[$i]['shipping'] = [
							'text' => $this->language->get('export_shipping'),
							'value' => $result['shipping']
						];
					}

					if (isset($find_fields['seo_keyword']['code'])) {
						$export_data[$i]['seo_url'] = [
							'text' => $this->language->get('export_seo'),
							'value' => $result['seo_url']
						];
					}

					if (isset($find_fields['date_available']['code'])) {
						$export_data[$i]['date_available'] = [
							'text' => $this->language->get('export_date_avaiable'),
							'value' => $result['date_available']
						];
					}

					if (isset($find_fields['length']['code'])) {
						$export_data[$i]['length'] = [
							'text' => $this->language->get('export_length'),
							'value' => $result['length']
						];
					}

					if (isset($find_fields['length_class_id']['code'])) {
						$export_data[$i]['length_class_id'] = [
							'text' => $this->language->get('export_length_class_id'),
							'value' => $result['length_class_id']
						];
					}

					if (isset($find_fields['length_class_name']['code'])) {
						$export_data[$i]['length_class'] = [
							'text' => $this->language->get('export_length_class'),
							'value' => $result['length_class']
						];
					}

					if (isset($find_fields['width']['code'])) {
						$export_data[$i]['width'] = [
							'text' => $this->language->get('export_width'),
							'value' => $result['width']
						];
					}

					if (isset($find_fields['height']['code'])) {
						$export_data[$i]['height'] = [
							'text' => $this->language->get('export_height'),
							'value' => $result['height']
						];
					}

					if (isset($find_fields['weight']['code'])) {
						$export_data[$i]['weight'] = [
							'text' => $this->language->get('export_weight'),
							'value' => $result['weight']
						];
					}

					if (isset($find_fields['weight_class_id']['code'])) {
						$export_data[$i]['weight_class_id'] = [
							'text' => $this->language->get('export_weight_class_id'),
							'value' => $result['weight_class_id']
						];
					}

					if (isset($find_fields['weight_class_name']['code'])) {
						$export_data[$i]['weight_class'] = [
							'text' => $this->language->get('export_weight_class'),
							'value' => $result['weight_class']
						];
					}

					if (isset($find_fields['manufacturer_id']['code'])) {
						$export_data[$i]['manufacturer_id'] = [
							'text' => $this->language->get('export_manufacturer_id'),
							'value' => $result['manufacturer_id']
						];
					}

					if (isset($find_fields['manufacturer_name']['code'])) {
						$export_data[$i]['manufacturer'] = [
							'text' => $this->language->get('export_manufacturer'),
							'value' => $result['manufacturer']
						];
					}

					if (isset($find_fields['category_ids']['code'])) {
						$export_data[$i]['categories_ids'] = [
							'text' => $this->language->get('export_categories'),
							'value' => $result['categories_ids']
						];
					}

					if (isset($find_fields['category_name']['code'])) {
						$export_data[$i]['category_names'] = [
							'text' => $this->language->get('export_categories_name'),
							'value' => html_entity_decode($this->ifnull($result['category_names']), ENT_QUOTES, 'UTF-8')
						];
					}

					if (isset($find_fields['filter']['code'])) {
						$export_data[$i]['filter_names'] = [
							'text' => $this->language->get('export_filter'),
							'value' => $result['filter_names']
						];
					}

					if (isset($find_fields['download']['code'])) {
						$export_data[$i]['download_names'] = [
							'text' => $this->language->get('export_download'),
							'value' => $result['download_names']
						];
					}

					if (isset($find_fields['related_products']['code'])) {
						$export_data[$i]['related_products'] = [
							'text' => $this->language->get('export_related_products'),
							'value' => $result['related_products']
						];
					}

					if (isset($find_fields['attribute']['code'])) {
						$export_data[$i]['attribute_names'] = [
							'text' => $this->language->get('export_attribute'),
							'value' => $result['attribute_names']
						];
					}

					if (isset($find_fields['options']['code'])) {
						$export_data[$i]['options_data'] = [
							'text' => $this->language->get('export_options'),
							'value' => $result['options_data']
						];
					}

					if (isset($find_fields['discount']['code'])) {
						$export_data[$i]['discounts_offers'] = [
							'text' => $this->language->get('export_discount'),
							'value' => $result['discounts_offers']
						];
					}

					if (isset($find_fields['special']['code'])) {
						$export_data[$i]['specials_offers'] = [
							'text' => $this->language->get('export_special'),
							'value' => $result['specials_offers']
						];
					}

					if (isset($find_fields['points']['code'])) {
						$export_data[$i]['points'] = [
							'text' => $this->language->get('export_points'),
							'value' => $result['points']
						];
					}

					if (isset($find_fields['reward']['code'])) {
						$export_data[$i]['rewards_data'] = [
							'text' => $this->language->get('export_reward'),
							'value' => $result['rewards_data']
						];
					}

					if (isset($find_fields['viewed']['code'])) {
						$export_data[$i]['viewed'] = [
							'text' => $this->language->get('export_viewed'),
							'value' => $result['viewed']
						];
					}

					if (isset($find_fields['date_added']['code'])) {
						$export_data[$i]['date_added'] = [
							'text' => $this->language->get('export_date_added'),
							'value' => $result['date_added']
						];
					}

					if (isset($find_fields['date_modified']['code'])) {
						$export_data[$i]['date_modified'] = [
							'text' => $this->language->get('export_date_modified'),
							'value' => $result['date_modified']
						];
					}

					if ($find_image) {
						$export_data[$i]['additional_images'] = [
							'text' => $this->language->get('export_images'),
							'value' => $result['additional_images']
						];
					}

					if ($find_review) {
						$export_data[$i]['reviews_data'] = [
							'text' => $this->language->get('export_review'),
							'value' => $result['reviews_data']
						];
					}

					if (!empty($find_extrafields)) {
						foreach ($find_extrafields as $find_key_extrafield => $find_extrafield) {
							if (isset($find_fields[$find_key_extrafield]['code']) && isset($result[$find_extrafield['field']])) {
								$export_data[$i][$find_key_extrafield] = [
									'text' => $find_extrafield['title'],
									'value' => html_entity_decode($this->ifnull($result[$find_extrafield['field']]), ENT_QUOTES, 'UTF-8')
								];
							}
						}
					}

					$xml_product = $xml->createElement("product");
					$xml_products->appendChild($xml_product);
					foreach ($export_data[$i] as $key => $edata) {
						if ($edata['value'] == '') {
							$edata['value'] = " ";
						}
						// if ($key == 'custom_field') {
						// 	continue;
						// }



						// $xml_edata = $xml->createElement($key, htmlspecialchars($edata['value'], ENT_QUOTES, 'UTF-8'));

						// create cdata section
						$xml_edata = $xml->createElement($key);

						// $xml_cdata = $xml->createCDATASection(htmlspecialchars($edata['value'], ENT_QUOTES, 'UTF-8'));
						$xml_cdata = $xml->createCDATASection($edata['value']);
						$xml_edata->appendChild($xml_cdata);

						// $xml_edata->setAttribute("text", $edata['text']);
						$xml_product->appendChild($xml_edata);

						// $xml_attr = $xml->createAttribute('text');
						// $xml_attr->value = $edata['text'];
						// $xml_edata->appendChild($xml_attr);
					}
				}

				$file_name = 'ProductList.xml';
				$file_to_save = DIR_UPLOAD . $file_name;

				// echo $xml->saveXML();
				$xml->save($file_to_save);
			}
		}

		if ($results) {
			$json['href'] = str_replace('&amp;', '&', $this->url->link($this->isdir_extension . 'mpimporterexporter/export_product/fileDownload', $this->token . '='. $this->session->data[$this->token] .'&file_name='. $file_name .'&find_format='. $find_format, true));

			$json['success'] = $this->language->get('text_success');
		} else {
			$json['error'] = $this->language->get('text_no_results');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function saveFieldSettings() {
		$json = [];
		$this->load->language('mpimporterexporter/export_product');
		$this->load->model('setting/setting');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			if (empty($this->request->post['productexport_setting_fields'])) {
				$json['error']['warning'] = $this->language->get('error_setting_fields');
			}
			// $sorts_alphabets = [];
			// foreach ($this->request->post['productexport_setting_fields'] as $code => $value) {
			// 	if (empty($value['sort_order'])) {
			// 		$json['sort_order'][$code] = true;
			// 	}
			// 	// valide sort order
			// 	if (in_array($value['sort_order'], $sorts_alphabets)) {
			// 		$json['sort_order'][$code] = true;
			// 	}
			// 	$sorts_alphabets[] = $value['sort_order'];
			// }
			// if (!empty($json['sort_order'])) {
			// 	$json['error']['warning'] = $this->language->get('error_incorrect_sortorder');
			// }
			if (isset($json['error']) && !isset($json['error']['warning'])) {
				$json['error']['warning'] = $this->language->get('error_warning');
			}

			if (!$json) {
				$this->model_setting_setting->editSetting('productexport_setting', $this->request->post);
				$json['success'] = $this->language->get('text_field_settings_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->isdir_extension . 'mpimporterexporter/export_product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function accessValidate() {
		if (!$this->user->hasPermission('access', $this->isdir_extension . 'mpimporterexporter/export_product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}

if (VERSION <= '2.2.0.0') {
	class ControllerMpImporterExporterExportProduct extends ControllerExtensionMpImporterExporterExportProduct { }
}