<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');

		if (isset($this->session->data['order_id'])) {

			  $order_id = $this->session->data['order_id'];

			$this->cart->clear();

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
		//	unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->load->model('checkout/order');

		if(isset($order_id)){



	        $oc_order = $this->model_checkout_order->getOrder($order_id);

	        $data['paymethod'] = $oc_order['payment_code'];
	        $data['order_id'] = $order_id;


	         $this->load->model('account/order');
	        // Totals
	        $data['totals'] = array();

	        $totals = $this->model_account_order->getOrderTotals($order_id);

	        foreach ($totals as $total) {


	            if ($total['title']=='Ukupno'){

	                $ukupno = $this->currency->format($total['value'], $oc_order['currency_code'], $oc_order['currency_value']);
	                $ukupnohub = number_format((float)$total['value'], 2, '.', '');
	                $ukupnohub = $ukupnohub * 100;
	            }

	            if($oc_order['currency_code']=='HRK'){
	                $text =  $this->currency->format($total['value'], $oc_order['currency_code'], $oc_order['currency_value']).' <small>('.$this->currency->format($total['value'], 'EUR'). ')</small> ';
	            }
	            else{
	                $text = $this->currency->format($total['value'], $oc_order['currency_code'], $oc_order['currency_value']);
	            }


	            $data['totals'][] = array(
	                'title' => $total['title'],
	                'text'  => $text,
	            );
	        }







	   /// orderinoend
	        if (isset($data['paymethod'])) {

	            if ($data['paymethod'] == 'bank_transfer') {

	                $nhs_no = $order_id.''.date("y");

	                $pozivnabroj = $nhs_no;

	                $data['text_message'] = sprintf($this->language->get('text_bank'), $order_id, $ukupno, $pozivnabroj);

	                $hubstring = array (
	                    'renderer' => 'image',
	                    'options' =>
	                        array (
	                            "format" => "jpg",
	                            "scale" =>  3,
	                            "ratio" =>  3,
	                            "color" =>  "#2c3e50",
	                            "bgColor" => "#fff",
	                            "padding" => 20
	                        ),
	                    'data' =>
	                        array (
	                           'amount' => (int)$ukupnohub,
								'currency' => 'EUR',
	                            'sender' =>
	                                array (
	                                    'name' => $oc_order['payment_firstname'].' '.$oc_order['payment_lastname'],
	                                    'street' => $oc_order['shipping_address_1'],
	                                    'place' => $oc_order['shipping_postcode'].' '.$oc_order['shipping_city'],
	                                ),
	                            'receiver' =>
	                                array (
	                                    'name' => 'GORDOM USLUGE d.o.o.',
	                                    'street' => 'Wickerhauserova ulica 52',
	                                    'place' => '10000 Zagreb',
	                                    'iban' => 'HR6824020061100462543',
	                                    'model' => '00',
	                                    'reference' => $pozivnabroj,
	                                ),
	                            'purpose' => 'SUPP',
	                            'description' => 'Web narudžba Dryzen',
	                        ),
	                );




	                $postString = json_encode($hubstring);

	                $url = 'https://hub3.bigfish.software/api/v2/barcode';
	                $ch = curl_init($url);

	                # Setting our options
	                curl_setopt($ch, CURLOPT_POST, 1);
	                curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
	                curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	                # Get the response

	                $response = curl_exec($ch);
	                curl_close($ch);


	                $json = json_decode($response);

	                if(isset($json->message)){
	                    $this->db->query("UPDATE " . DB_PREFIX . "order SET scanimage = '" . $json->errors[0] . "' WHERE order_id = '" . (int)$order_id . "'");
	                    $data['uplatnica'] = 'error';
	                }
	                else{

	                    $response = base64_encode($response);
	                    $data['uplatnica'] = $response;
	                    $this->db->query("UPDATE " . DB_PREFIX . "order SET scanimage = '" . $response . "' WHERE order_id = '" . (int)$order_id . "'");


	                    $scimg = 'data:image/png;base64,'.$response;

	                    list($type, $scimg) = explode(';', $scimg);
	                    list(, $scimg)      = explode(',', $scimg);
	                    $scimg = base64_decode($scimg);

	                    file_put_contents(DIR_IMAGE.'tmp/'.$order_id.'.png', $scimg);

	                    $data['scan'] = HTTPS_SERVER.'image/tmp/'.$order_id.'.png';

	                }


	            }


	        }



	}


        unset($this->session->data['order_id']);





		$this->response->setOutput($this->load->view('common/success', $data));
	}
}