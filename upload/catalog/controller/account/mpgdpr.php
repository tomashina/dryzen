<?php
class ControllerAccountMpGdpr extends Controller {
	public function index() {
		if ($this->config->get('mpgdpr_login_gdprforms') && !$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/mpgdpr', '', $this->mpgdpr->ssl);

			$this->response->redirect($this->url->link('account/login', '', $this->mpgdpr->ssl));
		}

		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', $this->mpgdpr->ssl)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_gdpr'),
			'href' => $this->url->link('account/mpgdpr', '', $this->mpgdpr->ssl)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_right_rectification'] = $this->language->get('text_right_rectification');
		$data['text_right_rectification_info'] = $this->language->get('text_right_rectification_info');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_address'] = $this->language->get('text_address');
		$data['text_password'] = $this->language->get('text_password');
		$data['text_newsletter'] = $this->language->get('text_newsletter');

		$data['text_right_portability'] = $this->language->get('text_right_portability');
		$data['text_right_portability_info'] = $this->language->get('text_right_portability_info');
		$data['text_port_personal_data'] = $this->language->get('text_port_personal_data');
		$data['text_port_address'] = $this->language->get('text_port_address');
		$data['text_port_orders'] = $this->language->get('text_port_orders');
		$data['text_my_gdpr_requests'] = $this->language->get('text_my_gdpr_requests');
		$data['text_my_wishlists'] = $this->language->get('text_my_wishlists');
		$data['text_my_transactions'] = $this->language->get('text_my_transactions');
		$data['text_my_history'] = $this->language->get('text_my_history');
		$data['text_my_search'] = $this->language->get('text_my_search');
		$data['text_my_rewardspoints'] = $this->language->get('text_my_rewardspoints');
		$data['text_my_activities'] = $this->language->get('text_my_activities');

		$data['text_right_restriction'] = $this->language->get('text_right_restriction');
		$data['text_right_restriction_info'] = $this->language->get('text_right_restriction_info');
		$data['text_my_restrictions'] = $this->language->get('text_my_restrictions');

		$data['text_right_personsal_data'] = $this->language->get('text_right_personsal_data');
		$data['text_right_personsal_data_info'] = $this->language->get('text_right_personsal_data_info');
		$data['text_personsal_data_request'] = $this->language->get('text_personsal_data_request');


		$data['text_right_forget_me'] = $this->language->get('text_right_forget_me');
		$data['text_right_forget_me_info'] = $this->language->get('text_right_forget_me_info');
		$data['text_forget_me'] = $this->language->get('text_forget_me');

		$data['button_back'] = $this->language->get('button_back');

		$data['account'] = $this->url->link('account/edit', '', $this->mpgdpr->ssl);
		$data['address'] = $this->url->link('account/address', '', $this->mpgdpr->ssl);
		$data['password'] = $this->url->link('account/password', '', $this->mpgdpr->ssl);
		$data['newsletter'] = $this->url->link('account/newsletter', '', $this->mpgdpr->ssl);

		$data['my_restrictions'] = $this->url->link('account/mpgdpr_restriction', '', $this->mpgdpr->ssl);

		$data['data_request'] = $this->url->link('account/mpgdpr_datarequest', '', $this->mpgdpr->ssl);
		$data['deleteme'] = $this->url->link('account/mpgdpr_deleteme', '', $this->mpgdpr->ssl);

		$data['back'] = $this->url->link('account/account', '', $this->mpgdpr->ssl);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		/*here we can override to search histories, in case when in past admin enable log customer search, but now disable that, so on demand we need to show download search histories*/
		$data['customer_search'] = false;// $this->config->get('config_customer_search');

		$this->response->setOutput($this->mpgdpr->view('account/mpgdpr/mpgdpr', $data));
	}

	public function fileDownload() {
		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');
		/*13 sep 2019 gdpr session starts*/
		if($this->customer->isLogged() && !empty($this->request->get['file_name'])) {
		/*13 sep 2019 gdpr session ends*/
			$file_to_save = DIR_UPLOAD . $this->request->get['file_name'];

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'. $this->request->get['file_name'] .'"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '. filesize($file_to_save));
			header('Cache-Control: max-age=0');
			header('Accept-Ranges: bytes');
			readfile($file_to_save);

			unlink($file_to_save);
		} else {
			$this->session->data['warning'] = $this->language->get('error_login_required');
			$this->response->redirect($this->url->link('account/mpgdpr', '', $this->mpgdpr->ssl));
		}
	}
	public function getAccountData() {
		// download account data as csv
		$this->response->addHeader('Content-Type: application/json');
		$json = array();
		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');

		if (!$this->customer->isLogged()) {

			$json['warning'] =  $this->language->get('error_login_required');

		}

		if(!$json) {
			// process all data
			require_once(DIR_SYSTEM.'library/modulepoints/PHPExcel.php');
			$objPHPExcel = new PHPExcel();
			$file_format = 'csv';

			$i = 1;
			$char = 'A';

			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customer_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_firstname'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_lastname'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_telephone'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_fax'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_email'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_newsletter'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_ip'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_date_added'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);

			// process all data
			$customer = $this->model_mpgdpr_mpgdpr->getCustomerData($this->customer->getId());

			if($customer) {
				$char_value = 'A'; $i++;

				if($customer['newsletter']) {
					$newsletter = $this->language->get('text_yes');
				} else {
					$newsletter = $this->language->get('text_no');
				}

				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $customer['customer_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $customer['firstname']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $customer['lastname']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $customer['telephone']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $customer['fax']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $customer['email']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $newsletter);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $customer['ip']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $customer['date_added']);
			}

			if($file_format == 'xls') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$file_name = 'personaldata_request.xls';
			}else if($file_format == 'xlsx') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$file_name = 'personaldata_request.xlsx';
			}else if($file_format == 'csv') {
				$file_name = 'personaldata_request.csv';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			}else{
				$file_name = 'personaldata_request.xls';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			}


			$file_to_save = DIR_UPLOAD . $file_name;
			$objWriter->save(DIR_UPLOAD . $file_name);

			// record download personal data activity and Add to request log

			$request_data = array(
				'customer_id' => $this->customer->getId(),
			);
			$this->model_mpgdpr_mpgdpr->addRequest($this->mpgdpr->codedownloadpersonalinfo, $request_data);

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/mpgdpr/fileDownload', 'file_name='. $file_name .'&file_format='. $file_format, $this->mpgdpr->ssl));
		}
		$this->response->setOutput(json_encode($json));
	}
	public function getAddresses() {
		// download addresses data as csv
		$this->response->addHeader('Content-Type: application/json');
		$json = array();
		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		if (!$this->customer->isLogged()) {

			$json['warning'] =  $this->language->get('error_login_required');

		}

		if(!$json) {
			// process all data
			require_once(DIR_SYSTEM.'library/modulepoints/PHPExcel.php');
			$objPHPExcel = new PHPExcel();
			$file_format = 'csv';

			$i = 1;
			$char = 'A';

			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_address_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customer_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_address_firstname'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_address_lastname'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_address_company'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_address_address1'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_address_address2'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_address_city'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_address_postalcode'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_address_country'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_address_zone'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_address_customfield'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);

			// process all data
			$addresses = $this->model_mpgdpr_mpgdpr->getCustomerAddresses($this->customer->getId());

			foreach($addresses as $address) {
				$char_value = 'A'; $i++;

				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['address_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['customer_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['firstname']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['lastname']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['company']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['address_1']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['address_2']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['city']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['postcode']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['country']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['zone']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $address['custom_field']);
			}

			if($file_format == 'xls') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$file_name = 'customer_addresses.xls';
			}else if($file_format == 'xlsx') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$file_name = 'customer_addresses.xlsx';
			}else if($file_format == 'csv') {
				$file_name = 'customer_addresses.csv';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			}else{
				$file_name = 'customer_addresses.xls';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			}

			$file_to_save = DIR_UPLOAD . $file_name;
			$objWriter->save(DIR_UPLOAD . $file_name);

			// record download addresses activity and Add to request log

			$request_data = array(
				'customer_id' => $this->customer->getId(),
			);
			$this->model_mpgdpr_mpgdpr->addRequest($this->mpgdpr->codedownloadaddress, $request_data);

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/mpgdpr/fileDownload', 'file_name='. $file_name .'&file_format='. $file_format, $this->mpgdpr->ssl));
		}

		$this->response->setOutput(json_encode($json));
	}
	public function getOrders() {
		// download orders data as csv
		$this->response->addHeader('Content-Type: application/json');
		$json = array();
		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$this->load->model('localisation/language');
		if (!$this->customer->isLogged()) {

			$json['warning'] =  $this->language->get('error_login_required');

		}

		if(!$json) {
			// process all data
			require_once(DIR_SYSTEM.'library/modulepoints/PHPExcel.php');
			$objPHPExcel = new PHPExcel();
			$file_format = 'csv';

			$i = 1;
			$char = 'A';

			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customer_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_inv'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_inv_prefix'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_firstname'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_lastname'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_email'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_telephone'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_fax'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_customfield'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_payment_firstname'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_payment_lastname'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_payment_company'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_payment_address1'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_payment_address2'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_payment_city'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_payment_postcode'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_payment_country'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_payment_zone'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_payment_customfield'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_payment_method'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_shipping_firstname'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_shipping_lastname'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_shipping_company'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_shipping_address1'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_shipping_address2'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_shipping_city'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_shipping_postcode'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_shipping_country'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_shipping_zone'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_shipping_customfield'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_shipping_method'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_comment'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_othertotal'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_total'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_ip'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_forwaredip'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_useragent'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_acceptlanguage'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_date_added'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_date_modified'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_history'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_product'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_voucher'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);

			// process all data
			$orders = $this->model_mpgdpr_mpgdpr->getCustomerOrders($this->customer->getId(), (int)$this->config->get('config_language_id'));

			foreach($orders as $order) {
				$char_value = 'A'; $i++;

				$payment_country = $this->model_localisation_country->getCountry($order['payment_country_id']);

				if ($payment_country) {
					$payment_iso_code_2 = $payment_country['iso_code_2'];
					$payment_iso_code_3 = $payment_country['iso_code_3'];
				} else {
					$payment_iso_code_2 = '';
					$payment_iso_code_3 = '';
				}

				$payment_zone = $this->model_localisation_zone->getZone($order['payment_zone_id']);

				if ($payment_zone) {
					$payment_zone_code = $payment_zone['code'];
				} else {
					$payment_zone_code = '';
				}

				$shipping_country = $this->model_localisation_country->getCountry($order['shipping_country_id']);

				if ($shipping_country) {
					$shipping_iso_code_2 = $shipping_country['iso_code_2'];
					$shipping_iso_code_3 = $shipping_country['iso_code_3'];
				} else {
					$shipping_iso_code_2 = '';
					$shipping_iso_code_3 = '';
				}

				$shipping_zone = $this->model_localisation_zone->getZone($order['shipping_zone_id']);

				if ($shipping_zone) {
					$shipping_zone_code = $shipping_zone['code'];
				} else {
					$shipping_zone_code = '';
				}

				// order has missing language then use default language
				if(!$order['language_id']) {
					$order['language_id'] = $this->config->get('config_language_id');
				}

				$language_info = $this->model_localisation_language->getLanguage($order['language_id']);

				if ($language_info) {
					$language_code = $language_info['code'];
				} else {
					$language_code = $this->config->get('config_language');
				}

				$language = new Language($language_code);
				$language->load($language_code);

				// order other totals
				$order_total = array();
				$ordertotals = $this->model_mpgdpr_mpgdpr->getOrderTotals($order['order_id']);
				foreach ($ordertotals as $value) {
					$order_total[][$value['code']] = $this->currency->format($value['value'], $order['currency_code'], $order['currency_value']);
				}
				$order_othertotal = json_encode($order_total);

				// order histories
				$history = array();
				$orderhistories = $this->model_mpgdpr_mpgdpr->getOrderHistories($order['order_id'], $order['language_id']);
				foreach ($orderhistories as $value) {
					$history[] = array(
						'order_status' => $value['status'],
						'notify' => $value['notify'] ? $language->get('text_yes') : $language->get('text_no'),
						'comment' => $value['comment'],
						'date_added' => $value['date_added']
					);
				}
				$order_history = json_encode($history);

				// order products
				$product = array();
				$orderproducts = $this->model_mpgdpr_mpgdpr->getOrderProducts($order['order_id']);

				foreach ($orderproducts as $value) {
					$options = array();

					$orderproduct_options = $this->model_mpgdpr_mpgdpr->getOrderOptions($order['order_id'], $value['order_product_id']);

					foreach($orderproduct_options as $option) {
						$options[] = array(
							'name' => $option['name'],
							'value' => $option['value']
						);
					}

					$product[] = array(
						'product_id' => $value['product_id'],
						'name' => $value['name'],
						'model' => $value['model'],
						'quantity' => $value['quantity'],
						'price' => $this->currency->format($value['price'] + ($this->config->get('config_tax') ? $value['tax'] : 0), $order['currency_code'], $order['currency_value']),
						'total' => $this->currency->format($value['total'] + ($this->config->get('config_tax') ? ($value['tax'] * $value['quantity']) : 0), $order['currency_code'], $order['currency_value']),
						'reward' => $value['reward'],
						'options' => $options
					);
				}

				$order_product = json_encode($product);

				// order vouchers
				$voucher = array();
				$ordervouchers = $this->model_mpgdpr_mpgdpr->getOrderVouchers($order['order_id']);

				foreach ($ordervouchers as $value) {
					$vouchertheme = $this->model_mpgdpr_mpgdpr->getVoucherTheme($value['voucher_id'], $order['language_id']);
					$theme = '';
					if($vouchertheme) {
						$theme = $vouchertheme['name'];
					}
					$voucher[] = array(
						'voucher_id' => $value['voucher_id'],
						'description' => $value['description'],
						'code' => $value['code'],
						'from_name' => $value['from_name'],
						'from_email' => $value['from_email'],
						'to_name' => $value['to_name'],
						'to_email' => $value['to_email'],
						'theme' => $theme,
						'message' => $value['message'],
						'amount' => $this->currency->format($value['amount'], $order['currency_code'], $order['currency_value'])
					);
				}

				$order_voucher = json_encode($voucher);

				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['order_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['customer_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['invoice_no']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['invoice_prefix']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['firstname']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['lastname']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['email']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['telephone']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['fax']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['custom_field']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['payment_firstname']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['payment_lastname']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['payment_company']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['payment_address_1']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['payment_address_2']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['payment_city']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['payment_postcode']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['payment_country']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['payment_zone']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['payment_custom_field']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['payment_method']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['shipping_firstname']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['shipping_lastname']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['shipping_company']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['shipping_address_1']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['shipping_address_2']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['shipping_city']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['shipping_postcode']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['shipping_country']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['shipping_zone']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['shipping_custom_field']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['shipping_method']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['comment']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order_othertotal);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']));
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['ip']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['forwarded_ip']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['user_agent']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['accept_language']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['date_added']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order['date_modified']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order_history);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order_product);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $order_voucher);
			}

			if($file_format == 'xls') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$file_name = 'customer_order.xls';
			}else if($file_format == 'xlsx') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$file_name = 'customer_order.xlsx';
			}else if($file_format == 'csv') {
				$file_name = 'customer_order.csv';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			}else{
				$file_name = 'customer_order.xls';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			}


			$file_to_save = DIR_UPLOAD . $file_name;
			$objWriter->save(DIR_UPLOAD . $file_name);

			// record download orders activity and Add to request log

			$request_data = array(
				'customer_id' => $this->customer->getId(),
			);
			$this->model_mpgdpr_mpgdpr->addRequest($this->mpgdpr->codedownloadorder, $request_data);

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/mpgdpr/fileDownload', 'file_name='. $file_name .'&file_format='. $file_format, $this->mpgdpr->ssl));
		}

		$this->response->setOutput(json_encode($json));
	}
	public function getGDPRRequests() {
		// download all gdpr requests data as csv
		$this->response->addHeader('Content-Type: application/json');
		$json = array();
		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');
		if (!$this->customer->isLogged()) {

			$json['warning'] =  $this->language->get('error_login_required');

		}

		if(!$json) {
			// process all data
			require_once(DIR_SYSTEM.'library/modulepoints/PHPExcel.php');
			$objPHPExcel = new PHPExcel();
			$file_format = 'csv';

			$i = 1;
			$char = 'A';

			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_requestlist_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_requestlist_email'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_requestlist_type'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_requestlist_serverip'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_requestlist_clientip'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_requestlist_useragent'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_requestlist_acceptlanguage'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_requestlist_dateadded'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);


			// process all data

			$requestlists = $this->model_mpgdpr_mpgdpr->getCustomerMpGdprRequestLists($this->customer->getId(), $this->customer->getEmail());

			foreach($requestlists as $requestlist) {
				$char_value = 'A'; $i++;

				$email = $requestlist['email'];
				if(empty($email)) {
					$email = $this->model_mpgdpr_mpgdpr->getCustomerIdEmail($requestlist['customer_id']);
				}

				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $requestlist['mpgdpr_requestlist_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $email);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $this->mpgdpr->getRequestName($requestlist['requessttype']));
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $requestlist['server_ip']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $requestlist['client_ip']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $requestlist['user_agent']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $requestlist['accept_language']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $requestlist['date_added']);
			}

			if($file_format == 'xls') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$file_name = 'customer_mpgdpr_requestlist.xls';
			}else if($file_format == 'xlsx') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$file_name = 'customer_mpgdpr_requestlist.xlsx';
			}else if($file_format == 'csv') {
				$file_name = 'customer_mpgdpr_requestlist.csv';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			}else{
				$file_name = 'customer_mpgdpr_requestlist.xls';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			}


			$file_to_save = DIR_UPLOAD . $file_name;
			$objWriter->save(DIR_UPLOAD . $file_name);

			// record download personal data activity and Add to request log

			$request_data = array(
				'customer_id' => $this->customer->getId(),
			);
			$this->model_mpgdpr_mpgdpr->addRequest($this->mpgdpr->codedownloadgdpr, $request_data);

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/mpgdpr/fileDownload', 'file_name='. $file_name .'&file_format='. $file_format, $this->mpgdpr->ssl));
		}

		$this->response->setOutput(json_encode($json));
	}
	public function getWishlists() {
		// download wishlists data as csv
		$this->response->addHeader('Content-Type: application/json');
		$json = array();
		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');
		if (!$this->customer->isLogged()) {

			$json['warning'] =  $this->language->get('error_login_required');

		}

		if(!$json) {
			// process all data
			require_once(DIR_SYSTEM.'library/modulepoints/PHPExcel.php');
			$objPHPExcel = new PHPExcel();
			$file_format = 'csv';

			$i = 1;
			$char = 'A';

			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customer_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_product_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_product_name'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_product_model'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_product_price'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);

			if(VERSION >= '2.1.0.1') {
				$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_wishlist_date_added'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			}
			// process all data
			$wishlists = $this->model_mpgdpr_mpgdpr->getCustomerWishLists($this->customer->getId());

			foreach($wishlists as $wishlist) {
				$char_value = 'A'; $i++;

				$product_info = $this->model_mpgdpr_mpgdpr->getProduct($wishlist['product_id']);
				if($product_info) {
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $wishlist['customer_id']);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $wishlist['product_id']);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $product_info['name']);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $product_info['model']);
					$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']));

					if(VERSION >= '2.1.0.1') {
						$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $wishlist['date_added']);
					}
				}

			}

			if($file_format == 'xls') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$file_name = 'customer_wishlist.xls';
			}else if($file_format == 'xlsx') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$file_name = 'customer_wishlist.xlsx';
			}else if($file_format == 'csv') {
				$file_name = 'customer_wishlist.csv';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			}else{
				$file_name = 'customer_wishlist.xls';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			}


			$file_to_save = DIR_UPLOAD . $file_name;
			$objWriter->save(DIR_UPLOAD . $file_name);

			// record download personal data activity and Add to request log

			$request_data = array(
				'customer_id' => $this->customer->getId(),
			);
			$this->model_mpgdpr_mpgdpr->addRequest($this->mpgdpr->codedownloadwishlist, $request_data);

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/mpgdpr/fileDownload', 'file_name='. $file_name .'&file_format='. $file_format, $this->mpgdpr->ssl));
		}

		$this->response->setOutput(json_encode($json));
	}
	public function getTransactions() {
		// download wishlists data as csv
		$this->response->addHeader('Content-Type: application/json');
		$json = array();
		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');
		if (!$this->customer->isLogged()) {

			$json['warning'] =  $this->language->get('error_login_required');

		}

		if(!$json) {
			// process all data
			require_once(DIR_SYSTEM.'library/modulepoints/PHPExcel.php');
			$objPHPExcel = new PHPExcel();
			$file_format = 'csv';

			$i = 1;
			$char = 'A';

			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customer_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_order_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_transaction_description'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_transaction_amount'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_transaction_dateadded'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);


			// process all data
			$transactions = $this->model_mpgdpr_mpgdpr->getCustomerTransactions($this->customer->getId());

			foreach($transactions as $transaction) {
				$char_value = 'A'; $i++;

				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $transaction['customer_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $transaction['order_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $transaction['description']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $this->currency->format($transaction['amount'], $this->session->data['currency']));
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $transaction['date_added']);
			}

			$balance = $this->currency->format($this->model_mpgdpr_mpgdpr->getCustomerTransactionTotal($this->customer->getId()), $this->session->data['currency']);

			$char_value = 'A'; $i++;
			$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, '');
			$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, '');
			$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $this->language->get('export_transaction_balance'));
			$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $balance);
			$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, '');


			if($file_format == 'xls') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$file_name = 'customer_transaction.xls';
			}else if($file_format == 'xlsx') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$file_name = 'customer_transaction.xlsx';
			}else if($file_format == 'csv') {
				$file_name = 'customer_transaction.csv';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			}else{
				$file_name = 'customer_transaction.xls';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			}


			$file_to_save = DIR_UPLOAD . $file_name;
			$objWriter->save(DIR_UPLOAD . $file_name);

			// record download personal data activity and Add to request log

			$request_data = array(
				'customer_id' => $this->customer->getId(),
			);
			$this->model_mpgdpr_mpgdpr->addRequest($this->mpgdpr->codedownloadhistorytransaction, $request_data);

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/mpgdpr/fileDownload', 'file_name='. $file_name .'&file_format='. $file_format, $this->mpgdpr->ssl));
		}

		$this->response->setOutput(json_encode($json));
	}
	public function getHistory() {
		// download wishlists data as csv
		$this->response->addHeader('Content-Type: application/json');
		$json = array();
		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');
		if (!$this->customer->isLogged()) {

			$json['warning'] =  $this->language->get('error_login_required');

		}

		if(!$json) {
			// process all data
			require_once(DIR_SYSTEM.'library/modulepoints/PHPExcel.php');
			$objPHPExcel = new PHPExcel();
			$file_format = 'csv';

			$i = 1;
			$char = 'A';

			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customer_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_history_description'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_history_dateadded'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);


			// process all data
			$results = $this->model_mpgdpr_mpgdpr->getCustomerHistories($this->customer->getId());

			foreach($results as $result) {
				$char_value = 'A'; $i++;

				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['customer_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['comment']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['date_added']);
			}



			if($file_format == 'xls') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$file_name = 'customer_history.xls';
			}else if($file_format == 'xlsx') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$file_name = 'customer_history.xlsx';
			}else if($file_format == 'csv') {
				$file_name = 'customer_history.csv';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			}else{
				$file_name = 'customer_history.xls';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			}


			$file_to_save = DIR_UPLOAD . $file_name;
			$objWriter->save(DIR_UPLOAD . $file_name);

			// record download personal data activity and Add to request log

			$request_data = array(
				'customer_id' => $this->customer->getId(),
			);
			$this->model_mpgdpr_mpgdpr->addRequest($this->mpgdpr->codedownloadhistorycustomer, $request_data);

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/mpgdpr/fileDownload', 'file_name='. $file_name .'&file_format='. $file_format, $this->mpgdpr->ssl));
		}

		$this->response->setOutput(json_encode($json));
	}
	public function getSearchHistory() {
		// download wishlists data as csv
		$this->response->addHeader('Content-Type: application/json');
		$json = array();
		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');
		if (!$this->customer->isLogged()) {

			$json['warning'] =  $this->language->get('error_login_required');

		}

		if(!$json) {
			// process all data
			require_once(DIR_SYSTEM.'library/modulepoints/PHPExcel.php');
			$objPHPExcel = new PHPExcel();
			$file_format = 'csv';

			$i = 1;
			$char = 'A';

			// $objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customersearch_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customersearch_keyword'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customersearch_category'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customersearch_insubcategory'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customersearch_indescription'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customersearch_numproducts'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customersearch_ip'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customersearch_dateadded'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);


			// process all data

			$searchhistories = $this->model_mpgdpr_mpgdpr->getCustomerSearchHistory($this->customer->getId());

			foreach($searchhistories as $searchhistory) {
				$char_value = 'A'; $i++;

				$category_name = '';

				$category_info = $this->model_mpgdpr_mpgdpr->getCategory($searchhistory['category_id'], $searchhistory['language_id'], $searchhistory['store_id']);

				if($category_info) {
					$category_name = $category_info['name'];
				}


				// $objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $searchhistory['customer_search_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $searchhistory['keyword']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $category_name);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, ($searchhistory['sub_category'] ? $this->language->get('text_yes') : $this->language->get('text_no')));
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, ($searchhistory['description'] ? $this->language->get('text_yes') : $this->language->get('text_no')));
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $searchhistory['products']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $searchhistory['ip']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $searchhistory['date_added']);
			}

			if($file_format == 'xls') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$file_name = 'customer_search_history.xls';
			}else if($file_format == 'xlsx') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$file_name = 'customer_search_history.xlsx';
			}else if($file_format == 'csv') {
				$file_name = 'customer_search_history.csv';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			}else{
				$file_name = 'customer_search_history.xls';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			}


			$file_to_save = DIR_UPLOAD . $file_name;
			$objWriter->save(DIR_UPLOAD . $file_name);

			// record download personal data activity and Add to request log

			$request_data = array(
				'customer_id' => $this->customer->getId(),
			);
			$this->model_mpgdpr_mpgdpr->addRequest($this->mpgdpr->codedownloadhistorysearch, $request_data);

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/mpgdpr/fileDownload', 'file_name='. $file_name .'&file_format='. $file_format, $this->mpgdpr->ssl));
		}

		$this->response->setOutput(json_encode($json));
	}
	public function getRewardPointsHistory() {
		// download wishlists data as csv
		$this->response->addHeader('Content-Type: application/json');
		$json = array();
		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');
		if (!$this->customer->isLogged()) {

			$json['warning'] =  $this->language->get('error_login_required');

		}

		if(!$json) {
			// process all data.
			require_once(DIR_SYSTEM.'library/modulepoints/PHPExcel.php');
			$objPHPExcel = new PHPExcel();
			$file_format = 'csv';

			$i = 1;
			$char = 'A';

			// $objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customerreward_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customerreward_orderid'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customerreward_description'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customerreward_points'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customerreward_dateadded'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);

			// process all data

			$results = $this->model_mpgdpr_mpgdpr->getCustomerRewardPoints($this->customer->getId());

			foreach($results as $result) {
				$char_value = 'A'; $i++;

				// $objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['customer_reward_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['order_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['description']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['points']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['date_added']);
			}

			$balance = $this->model_mpgdpr_mpgdpr->getCustomerRewardTotal($this->customer->getId());

			$char_value = 'A'; $i++;

			// $objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, '');
			$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, '');
			$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $this->language->get('export_customerreward_balance'));
			$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $balance);
			$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, '');

			if($file_format == 'xls') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$file_name = 'customer_reward_points.xls';
			}else if($file_format == 'xlsx') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$file_name = 'customer_reward_points.xlsx';
			}else if($file_format == 'csv') {
				$file_name = 'customer_reward_points.csv';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			}else{
				$file_name = 'customer_reward_points.xls';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			}


			$file_to_save = DIR_UPLOAD . $file_name;
			$objWriter->save(DIR_UPLOAD . $file_name);

			// record download personal data activity and Add to request log

			$request_data = array(
				'customer_id' => $this->customer->getId(),
			);
			$this->model_mpgdpr_mpgdpr->addRequest($this->mpgdpr->codedownloadhistoryreward, $request_data);

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/mpgdpr/fileDownload', 'file_name='. $file_name .'&file_format='. $file_format, $this->mpgdpr->ssl));
		}

		$this->response->setOutput(json_encode($json));
	}
	public function getActivityHistory() {
		// download wishlists data as csv
		$this->response->addHeader('Content-Type: application/json');
		$json = array();
		$this->load->language('mpgdpr/mpgdpr');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');
		if (!$this->customer->isLogged()) {

			$json['warning'] =  $this->language->get('error_login_required');

		}

		if(!$json) {
			// process all data

			require_once(DIR_SYSTEM.'library/modulepoints/PHPExcel.php');
			$objPHPExcel = new PHPExcel();
			$file_format = 'csv';

			$i = 1;
			$char = 'A';

			// $objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customeractivity_id'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customeractivity_key'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customeractivity_data'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customeractivity_ip'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue($char .$i, $this->language->get('export_customeractivity_dateadded'))->getColumnDimension($char)->setAutoSize(true); $objPHPExcel->getActiveSheet()->getStyle($char++ .$i)->getAlignment()->setWrapText(true);

			// process all data

			$results = $this->model_mpgdpr_mpgdpr->getCustomerActivities($this->customer->getId());

			foreach($results as $result) {
				$char_value = 'A'; $i++;

				// $objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['customer_activity_id']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['key']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['data']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['ip']);
				$objPHPExcel->getActiveSheet()->setCellValue($char_value++ .$i, $result['date_added']);
			}

			if($file_format == 'xls') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$file_name = 'customer_activities.xls';
			}else if($file_format == 'xlsx') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$file_name = 'customer_activities.xlsx';
			}else if($file_format == 'csv') {
				$file_name = 'customer_activities.csv';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			}else{
				$file_name = 'customer_activities.xls';
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			}


			$file_to_save = DIR_UPLOAD . $file_name;
			$objWriter->save(DIR_UPLOAD . $file_name);

			// record download personal data activity and Add to request log

			$request_data = array(
				'customer_id' => $this->customer->getId(),
			);
			$this->model_mpgdpr_mpgdpr->addRequest($this->mpgdpr->codedownloadhistoryactivity, $request_data);

			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('account/mpgdpr/fileDownload', 'file_name='. $file_name .'&file_format='. $file_format, $this->mpgdpr->ssl));
		}

		$this->response->setOutput(json_encode($json));
	}
}