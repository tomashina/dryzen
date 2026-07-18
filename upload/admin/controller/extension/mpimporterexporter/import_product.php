<?php

class ControllerExtensionMpImporterExporterImportProduct extends \MpImporterExporter\Controller {
	private $error = [];

	public function getMenu() {
		$this->load->language('importer/product_menu');
		$menu = [];

		if ($this->user->hasPermission('access', $this->isdir_extension . 'mpimporterexporter/import_product')) {
			$menu = array(
				'name'	   => $this->language->get('text_product_importer'),
				'href'     => $this->url->link($this->isdir_extension . 'mpimporterexporter/import_product', $this->token . '=' . $this->session->data[$this->token], true),
				'children' => []
			);
		}
		return $menu;
	}

	public function index() {
		$this->load->language('mpimporterexporter/import_product');

		$this->load->model($this->isdir_extension . 'mpimporterexporter/import_product');

		$this->document->addStyle('view/stylesheet/mpimporterexporter/import_product.css');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$this->breadcrumbs($data);
		// reload page specific language file to avoid language variable conflicts
		$this->load->language('mpimporterexporter/import_product');

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->isdir_extension . 'mpimporterexporter/import_product', $this->token . '=' . $this->session->data[$this->token], true)
		);

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

		$this->load->config('mpimporterexporter/import_product');
		$data['sample_download'] = $this->config->get('mpimportexport_sample_file');

		$data['cell_operations'] = [];
		$data['cell_operations']['product_name'] = $this->language->get('cell_product_name');
		$data['cell_operations']['model'] = $this->language->get('cell_model');
		$data['cell_operations']['store'] = $this->language->get('cell_store');
		$data['cell_operations']['description'] = $this->language->get('cell_description');
		$data['cell_operations']['meta_title'] = $this->language->get('cell_meta_title');
		$data['cell_operations']['meta_description'] = $this->language->get('cell_meta_description');
		$data['cell_operations']['meta_keyword'] = $this->language->get('cell_meta_keyword');
		$data['cell_operations']['meta_tag'] = $this->language->get('cell_meta_tag');
		$data['cell_operations']['product_image'] = $this->language->get('cell_image');
		$data['cell_operations']['sku'] = $this->language->get('cell_sku');
		$data['cell_operations']['upc'] = $this->language->get('cell_upc');
		$data['cell_operations']['ean'] = $this->language->get('cell_ean');
		$data['cell_operations']['jan'] = $this->language->get('cell_jan');
		$data['cell_operations']['isbn'] = $this->language->get('cell_isbn');
		$data['cell_operations']['mpn'] = $this->language->get('cell_mpn');
		$data['cell_operations']['location'] = $this->language->get('cell_location');
		$data['cell_operations']['price'] = $this->language->get('cell_price');
		$data['cell_operations']['minimum_quantity'] = $this->language->get('cell_minimum_quantity');
		$data['cell_operations']['quantity'] = $this->language->get('cell_quantity');
		$data['cell_operations']['status'] = $this->language->get('cell_status');
		$data['cell_operations']['sort_order'] = $this->language->get('cell_sort_order');
		$data['cell_operations']['tax_class_id'] = $this->language->get('cell_tax_class_id');
		$data['cell_operations']['subtract'] = $this->language->get('cell_subtract');
		$data['cell_operations']['stock_status_id'] = $this->language->get('cell_stock_status_id');
		$data['cell_operations']['shipping_required'] = $this->language->get('cell_shipping_required');
		$data['cell_operations']['seo_keyword'] = $this->language->get('cell_seo_keyword');
		$data['cell_operations']['date_available'] = $this->language->get('cell_date_available');
		$data['cell_operations']['length'] = $this->language->get('cell_length');
		$data['cell_operations']['length_class_id'] = $this->language->get('cell_length_class_id');
		$data['cell_operations']['width'] = $this->language->get('cell_width');
		$data['cell_operations']['height'] = $this->language->get('cell_height');
		$data['cell_operations']['weight'] = $this->language->get('cell_weight');
		$data['cell_operations']['weight_class_id'] = $this->language->get('cell_weight_class_id');
		$data['cell_operations']['manufacturer_id'] = $this->language->get('cell_manufacturer_id');
		$data['cell_operations']['category_ids'] = $this->language->get('cell_category_ids');
		$data['cell_operations']['filter'] = $this->language->get('cell_filter');
		$data['cell_operations']['download'] = $this->language->get('cell_download');
		$data['cell_operations']['related_products'] = $this->language->get('cell_related_products');
		$data['cell_operations']['attribute'] = $this->language->get('cell_attribute');
		$data['cell_operations']['options'] = $this->language->get('cell_options');
		$data['cell_operations']['discount'] = $this->language->get('cell_discount');
		$data['cell_operations']['special'] = $this->language->get('cell_special');
		$data['cell_operations']['points'] = $this->language->get('cell_points');
		$data['cell_operations']['reward'] = $this->language->get('cell_reward');
		$data['cell_operations']['viewed'] = $this->language->get('cell_viewed');
		$data['cell_operations']['date_added'] = $this->language->get('cell_date_added');
		$data['cell_operations']['date_modified'] = $this->language->get('cell_date_modified');


		$this->load->model('setting/store');
		$this->load->model('localisation/language');

		$data['stores'] = $this->model_setting_store->getStores();
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->loadView($this->isdir_extension . 'mpimporterexporter/import_product', $data));
	}

	public function import() {


		$this->load->language('mpimporterexporter/import_product');

		$this->load->model($this->isdir_extension . 'mpimporterexporter/import_product');

		$json = [];

		if (!$this->user->hasPermission('modify', $this->isdir_extension . 'mpimporterexporter/import_product')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->files['find_file'])) {
			$json['error']['file'] = $this->language->get('error_file');
			$json['error']['warning'] = $this->language->get('error_file');
		}

		if (!isset($this->request->get['find_store'])) {
			$json['error']['store'] = $this->language->get('error_store');
		}

		if (empty($this->request->get['find_language_id'])) {
			$json['error']['language'] = $this->language->get('error_language');
		}

		if (empty($this->request->get['find_importon'])) {
			$json['error']['importon'] = $this->language->get('error_importon');
		}

		/* if (empty($this->request->get['find_format'])) {
			$json['error']['file'] = $this->language->get('error_format');
			$json['error']['warning'] = $this->language->get('error_format');
		} */

		// Check to see if any PHP files are trying to be uploaded
		if (!empty($this->request->files['find_file'])) {
			$content = file_get_contents($this->request->files['find_file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error']['file'] = $this->language->get('error_filetype');
				$json['error']['warning'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['find_file']['error'] != UPLOAD_ERR_OK) {
				$json['error']['file'] = $this->language->get('error_upload_' . $this->request->files['find_file']['error']);
				$json['error']['warning'] = $this->language->get('error_upload_' . $this->request->files['find_file']['error']);
			}
		}

		if (!$json && $this->request->files) {
			$file = basename($this->request->files['find_file']['name']);
			move_uploaded_file($this->request->files['find_file']['tmp_name'], $file);
			$inputFileName = $file;

			$extension = pathinfo($inputFileName);

			$extension['extension'] = strtolower(strtoupper($extension['extension']));

			if (!in_array($extension['extension'], array('xls','xlsx','csv'))) {
				$json['error']['file'] = $this->language->get('error_format_diff');
				$json['error']['warning'] = $this->language->get('error_format_diff');
			}

			/* if ($extension['extension'] != $this->request->get['find_format']) {
				$json['error']['file'] = $this->language->get('error_format_diff');
				$json['error']['warning'] = $this->language->get('error_format_diff');
			} */

			if ($extension['extension']=='xlsx' || $extension['extension']=='xls' || $extension['extension']=='csv') {
				try {
					$inputFileType = $extension['extension'];

					if ($extension['extension'] == 'xlsx') {
						$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
					} elseif ($extension['extension'] == 'xls') {
						$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
					} elseif ($extension['extension'] == 'csv') {
						$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
					}

					$objPHPExcel = $objReader->load($inputFileName);
				} catch (Exception $e){
					$json['error']['warning'] = $this->language->get('error_loading_file') .'"'. pathinfo($inputFileName,PATHINFO_BASENAME) .'": '.$e->getMessage();
				}
			}
		}

		if (!$json) {
			if (isset($this->request->get['find_importon'])) {
				$find_importon = $this->request->get['find_importon'];
			} else {
				$find_importon = 'product_id';
			}

			if (isset($this->request->get['find_store'])) {
				$find_store = $this->request->get['find_store'];
			} else {
				$find_store = [];
			}

			if (isset($this->request->get['find_language_id'])) {
				$find_language_id = $this->request->get['find_language_id'];
			} else {
				$find_language_id = 0;
			}

			if (isset($this->request->get['find_existsupdate'])) {
				$find_existsupdate = $this->request->get['find_existsupdate'];
			} else {
				$find_existsupdate = 0;
			}

			if (isset($this->request->get['find_images'])) {
				$find_images = $this->request->get['find_images'];
			} else {
				$find_images = '';
			}

			if (isset($this->request->get['find_review'])) {
				$find_review = $this->request->get['find_review'];
			} else {
				$find_review = '';
			}

			if (isset($this->request->get['find_custom_fields'])) {
				$find_custom_fields = $this->request->get['find_custom_fields'];
			} else {
				$find_custom_fields = '';
			}

			if (isset($this->request->get['find_cell_operations'])) {
				$find_cell_operations = $this->request->get['find_cell_operations'];
			} else {
				$find_cell_operations = [];
			}

			$find_data = array(
				'store' => $find_store,
				'language_id' => $find_language_id,
				'review' => $find_review,
				'images' => $find_images,
				'custom_fields' => $find_custom_fields,
				'importon' => $find_importon,
				'existsupdate' => $find_existsupdate,
				'cell_operations' => $find_cell_operations,
			);

			$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

			$i=0;
			$insert_product = 0;
			$edit_product = 0;
			$static_columns = 57;

			if (count($allDataInSheet) > 1) {
				foreach ($allDataInSheet as $value) {
					$value = $this->clean($value);
					// Column Names
					if ($i == '0' && $find_custom_fields) {
						// Custom Fields
						$custom_fields_keys = [];
						$custom_fields_columns = [];
						$total_fields = count($value);
						if ($total_fields > $static_columns) {
							$new_columns = [];
							$custom_fields_columnss  = array_slice($value, $static_columns, $total_fields);
							foreach ($custom_fields_columnss as $columnkey => $custom_fields_column_field) {
								$table_fieldnames = array_map("trim", explode('::', $custom_fields_column_field));
								if (isset($table_fieldnames[1])) {
									$table_fieldname = array_map("trim", explode(".", $table_fieldnames[1]));
									if (count($table_fieldname) == 2) {
										$columns_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_import_product'}->getColumns($table_fieldname[0], $table_fieldname[1]);
										if ($columns_info) {
											$custom_fields_columns[$columnkey] = $table_fieldname[0].'::'.$table_fieldname[1];
										}
									}
								}
							}

							$custom_fields_keys = array_keys($custom_fields_columns);
						}
					}

					// Column Values
					if ($i != '0') {
						$product_id = (isset($value['A']) ? (int)$value['A'] : '');
						$product_name = (isset($value['B']) ? $value['B'] : '');

						if (trim($product_name) == '') {
							continue;
						}

						$model_number = (isset($value['C']) ? $value['C'] : '');
						$language_code = (isset($value['D']) ? $value['D'] : '');
						$store_names = (isset($value['E']) ? array_map('trim', explode('::', $value['E'])) : '');
						$description = (isset($value['F']) ? $value['F'] : '');
						$meta_title = (isset($value['G']) ? $value['G'] : '');
						$meta_description = (isset($value['H']) ? $value['H'] : '');
						$meta_keyword = (isset($value['I']) ? $value['I'] : '');
						$meta_tag = (isset($value['J']) ? $value['J'] : '');
						$product_image = (isset($value['K']) ? $value['K'] : '');

						if ($product_image) {
							if ((substr($product_image, 0, 7) == "http://" || substr($product_image, 0, 8) == "https://") && $this->{'model_' . $this->model_extension . 'mpimporterexporter_import_product'}->file_contents_exist($product_image)) {
								  $imageString = file_get_contents($product_image);

								$dir_name = 'catalog/storeimages/';
								$folder_name = DIR_IMAGE. $dir_name;

								$this->{'model_' . $this->model_extension . 'mpimporterexporter_import_product'}->makeDir($folder_name);

								$filename = basename(html_entity_decode($product_image, ENT_QUOTES, 'UTF-8'));

								$filename = str_replace(array(' ', '&nbsp;', '%20'), '_', $filename);

								if (file_exists($folder_name . $filename)) {
									$pathinfo_file = pathinfo($folder_name . $filename);
									if ($pathinfo_file) {
										$final_file = $pathinfo_file['filename'] .'_'. time().rand(0,1000) .'.'.$pathinfo_file['extension'];
									} else {
										$final_file = $filename;
									}
								} else {
									$final_file = $filename;
								}

								$save = file_put_contents($folder_name . $final_file, $imageString);
								$product_image = $dir_name . $final_file;
							}
						}

						$sku = (isset($value['L']) ? $value['L'] : '');
						$upc = (isset($value['M']) ? $value['M'] : '');
						$ean = (isset($value['N']) ? $value['N'] : '');
						$jan = (isset($value['O']) ? $value['O'] : '');
						$isbn = (isset($value['P']) ? $value['P'] : '');
						$mpn = (isset($value['Q']) ? $value['Q'] : '');
						$location = (isset($value['R']) ? $value['R'] : '');
						$price = (isset($value['S']) ? $value['S'] : '');
						$minimum_quantity = (isset($value['T']) ? $value['T'] : '');
						$quantity = (isset($value['U']) ? $value['U'] : '');
						$status = (isset($value['V']) ? $value['V'] : '');
						$sort_order = (isset($value['W']) ? $value['W'] : '');
						$tax_class_id = (isset($value['X']) ? $value['X'] : '');
						$tax_class = (isset($value['Y']) ? $value['Y'] : '');
						$subtract = (isset($value['Z']) ? $value['Z'] : '');
						$stock_status_id = (isset($value['AA']) ? $value['AA'] : '');
						$stock_status = (isset($value['AB']) ? $value['AB'] : '');
						$shipping_required = (isset($value['AC']) ? $value['AC'] : '');
						$seo_keyword = (isset($value['AD']) ? $value['AD'] : '');

						$date_available = (!empty($value['AE']) ?  date('Y-m-d', strtotime($value['AE'])) : '');
						$date_available_count = array_map('trim', explode('-', $date_available));
						$date_available = ((!empty($date_available_count) && count($date_available_count) == '3') ?  date('Y-m-d', strtotime($date_available)) : date('Y-m-d H:i:s'));

						$length = (isset($value['AF']) ? $value['AF'] : '');
						$length_class_id = (isset($value['AG']) ? $value['AG'] : '');
						$length_class = (isset($value['AH']) ? $value['AH'] : '');
						$width = (isset($value['AI']) ? $value['AI'] : '');
						$height = (isset($value['AJ']) ? $value['AJ'] : '');
						$weight = (isset($value['AK']) ? $value['AK'] : '');
						$weight_class_id = (isset($value['AL']) ? $value['AL'] : '');
						$weight_class = (isset($value['AM']) ? $value['AM'] : '');
						$manufacturer_id = (isset($value['AN']) ? $value['AN'] : '');
						$manufacturer = (isset($value['AO']) ? $value['AO'] : '');
						$category_ids = (!empty($value['AP']) ? array_map('trim', explode(',', $value['AP'])) : '');
						$category_name = (isset($value['AQ']) ? array_map('trim', explode('::', $value['AQ'])) : '');
						$filter = (isset($value['AR']) ? array_map('trim', explode('::', $value['AR'])) : '');
						$download = (isset($value['AS']) ? array_map('trim', explode('::', $value['AS'])) : '');
						$related_products = (isset($value['AT']) ? array_map('trim', explode(',', $value['AT'])) : '');
						$attribute = (isset($value['AU']) ? array_map('trim', explode(';', $value['AU'])) : '');
						$options = (isset($value['AV']) ? array_map('trim', explode(';;', $value['AV'])) : '');
						$discount = (isset($value['AW']) ? array_map('trim', explode(';', $value['AW'])) : '');
						$special = (isset($value['AX']) ? array_map('trim', explode(';', $value['AX'])) : '');

						if ($find_images) {
							$additional_images = (isset($value['AY']) ? array_map('trim', explode('::', $value['AY'])) : '');
						} else {
							$additional_images = [];
						}

						$points = (isset($value['AZ']) ? $value['AZ'] : '');
						$reward = (isset($value['BA']) ? array_map('trim', explode('::', $value['BA'])) : '');
						$viewed = (isset($value['BB']) ? $value['BB'] : '');

						$value['BC'] = (isset($value['BC']) ? $value['BC'] : '');
						$value['BD'] = (isset($value['BD']) ? $value['BD'] : '');

						// Date Added
						$date_added = (!empty($value['BC']) ?  date('Y-m-d H:i:s', strtotime($value['BC'])) : date('Y-m-d H:i:s'));
						$date_added_count = array_map('trim', explode('-', $date_added));
						$date_added = ((!empty($date_added) && count($date_added_count) == '3') ?  date('Y-m-d H:i:s', strtotime($date_added)) : date('Y-m-d H:i:s'));

						// Date Mmodified
						$date_modified = (!empty($value['BD']) ?  date('Y-m-d H:i:s', strtotime($value['BD'])) : date('Y-m-d H:i:s'));
						$date_modified_count = array_map('trim', explode('-', $date_modified));
						$date_modified = ((!empty($date_modified) && count($date_modified_count) == '3') ?  date('Y-m-d H:i:s', strtotime($date_modified)) : date('Y-m-d H:i:s'));


						if ($find_review) {
							$reviews = (isset($value['BE']) ? array_map('trim', explode(';;', $value['BE'])) : '');
						} else {
							$reviews = '';
						}

						// Custom Fields
						$custom_fields_data = [];
						if (!empty($custom_fields_keys) && $find_custom_fields) {
							foreach ($custom_fields_keys as $custom_fields_key) {
								$table_fieldname = array_map('trim', explode('::', $custom_fields_columns[$custom_fields_key]));

								if (!empty($table_fieldname[0]) && $table_fieldname[1]) {
									$custom_fields_data[] = array(
										'table_name' => $table_fieldname[0],
										'field_name' => $table_fieldname[1],
										'value'	 => $value[$custom_fields_key],
									);
								}
							}
						}


						// Insert Data
						$insert_data = [];

						$insert_data['product_id'] = $product_id;
						$insert_data['product_name'] = $product_name;
						$insert_data['model'] = $model_number;
						$insert_data['language_code'] = $language_code;
						$insert_data['store_names'] = $store_names;
						$insert_data['description'] = $description;
						$insert_data['meta_title'] = $meta_title;
						$insert_data['meta_description'] = $meta_description;
						$insert_data['meta_keyword'] = $meta_keyword;
						$insert_data['meta_tag'] = $meta_tag;
						$insert_data['product_image'] = $product_image;
						$insert_data['sku'] = $sku;
						$insert_data['upc'] = $upc;
						$insert_data['ean'] = $ean;
						$insert_data['jan'] = $jan;
						$insert_data['isbn'] = $isbn;
						$insert_data['mpn'] = $mpn;
						$insert_data['location'] = $location;
						$insert_data['price'] = $price;
						$insert_data['minimum_quantity'] = $minimum_quantity;
						$insert_data['quantity'] = $quantity;
						$insert_data['status'] = $status;
						$insert_data['sort_order'] = $sort_order;
						$insert_data['tax_class_id'] = $tax_class_id;
						$insert_data['tax_class'] = $tax_class;
						$insert_data['subtract'] = $subtract;
						$insert_data['stock_status_id'] = $stock_status_id;
						$insert_data['stock_status'] = $stock_status;
						$insert_data['shipping_required'] = $shipping_required;
						$insert_data['seo_keyword'] = $seo_keyword;
						$insert_data['date_available'] = $date_available;
						$insert_data['length'] = $length;
						$insert_data['length_class_id'] = $length_class_id;
						$insert_data['length_class'] = $length_class;
						$insert_data['width'] = $width;
						$insert_data['height'] = $height;
						$insert_data['weight'] = $weight;
						$insert_data['weight_class_id'] = $weight_class_id;
						$insert_data['weight_class'] = $weight_class;
						$insert_data['manufacturer_id'] = $manufacturer_id;
						$insert_data['manufacturer'] = $manufacturer;
						$insert_data['category_ids'] = $category_ids;
						$insert_data['category_name'] = $category_name;
						$insert_data['filter'] = $filter;
						$insert_data['download'] = $download;
						$insert_data['related_products'] = $related_products;
						$insert_data['attribute'] = $attribute;
						$insert_data['options'] = $options;
						$insert_data['discount'] = $discount;
						$insert_data['special'] = $special;
						$insert_data['additional_images'] = $additional_images;
						$insert_data['points'] = $points;
						$insert_data['reward'] = $reward;
						$insert_data['viewed'] = $viewed;
						$insert_data['date_added'] = $date_added;
						$insert_data['date_modified'] = $date_modified;
						$insert_data['reviews'] = $reviews;
						$insert_data['custom_fields_data']	= $custom_fields_data;

						if ($find_importon == 'product_id') {
							$product_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_import_product'}->getProductById($product_id);
						} else if ($find_importon == 'model_number') {
							$product_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_import_product'}->getProductByModel($model_number);
						} else {
							$product_info = $this->{'model_' . $this->model_extension . 'mpimporterexporter_import_product'}->getProductByName($product_name, $find_language_id);
						}

						if ($product_info) {
							// Update Exists Product
							if ($find_existsupdate) {
								$this->{'model_' . $this->model_extension . 'mpimporterexporter_import_product'}->editProduct($product_info['product_id'], $insert_data, $find_data);

								$edit_product++;
							}
						} else {
							// ($find_importon == 'model_number' || $find_importon == 'product_name') then check for product id exist or not, if yes then empty coming product id from $insert_data [] \\
							if ($find_importon == 'model_number' || $find_importon == 'product_name') {
								// get Product Info By Excel Product Id
								$product_info_by_excelid = $this->{'model_' . $this->model_extension . 'mpimporterexporter_import_product'}->getProductById($insert_data['product_id']);
								if ($product_info_by_excelid) {
									$insert_data['product_id'] = '';
								}
							}

							// Insert Exists Product
							$this->{'model_' . $this->model_extension . 'mpimporterexporter_import_product'}->addProduct($insert_data, $find_data);

							$insert_product++;
						}
					}

					$i++;
				}

				$text_success  = $this->language->get('text_success');

				if ($edit_product) {
					$text_success .= sprintf($this->language->get('text_success_update'), $edit_product);
				}

				if ($insert_product) {
					$text_success .= sprintf($this->language->get('text_success_insert'), $insert_product);
				}

				if (empty($insert_product) && empty($edit_product)) {
					$text_success = $this->language->get('text_success_zero');
				}

				$json['success'] = $text_success;
			} else {
				$json['error']['warning'] = $this->language->get('text_no_result');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}

if (VERSION <= '2.2.0.0') {
	class ControllerMpImporterExporterImportProduct extends ControllerExtensionMpImporterExporterImportProduct { }
}