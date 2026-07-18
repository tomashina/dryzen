<?php
class ControllerExtensionPaymentCorvusPay extends Controller {
    public function index() {
        $data['button_confirm'] = $this->language->get('button_confirm');

        $this->load->model('checkout/order');

        $this->load->language('extension/payment/corvuspay');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->load->model('localisation/currency');
        //$this->data['allcurrencies'] = array();
        $results = $this->model_localisation_currency->getCurrencies();

        //live url
        if (!$this->config->get('payment_corvuspay_test')){
            $data['action'] = 'https://wallet.corvuspay.com/checkout/';
        }else{
            //test url
            $data['action'] = 'https://test-wallet.corvuspay.com/checkout/';
        }





        $locale = substr($this->session->data['language'], 0, -3);


        $data['merchant'] = $this->config->get('payment_corvuspay_merchant');
        $data['password'] = $this->config->get('payment_corvuspay_password');
        //$data['number_of_installments'] = $this->config->get('corvuspay_fx_id');
        $data['number_of_installments'] = 'Y0299';
        $data['order_id'] = $order_info['order_id'];
        //$data['currency'] = $currency;
        $data['currency'] = $order_info['currency_code'];
        $data['description'] = $this->config->get('config_name') . ' - #' . $order_info['order_id'];
        $data['total'] = number_format($order_info['total'],2, '.', '');
        $data['address'] = $order_info['payment_address_1'];
        $data['city'] = $order_info['payment_city'];
        $data['firstname'] = $order_info['payment_firstname'];
        $data['lastname'] = $order_info['payment_lastname'];
        $data['postcode'] = $order_info['payment_postcode'];
        $data['country'] = $order_info['payment_iso_code_2'];
        $data['telephone'] = $order_info['telephone'];
        $data['email'] = $order_info['email'];

        $data['language'] = $locale;



        //readability
        $ukupno =  $data['total'];

        $keym = $data['password'] ;
        $wpass = $data['password'];

        $ordernum=$order_info['order_id'];



        //  $data['md5']  = SHA1($keym.':'.$ordernum.':'.$ukupno.':'.$data['currency']);

        // $hash = SHA1($keym.':'.$ordernum.':'.$ukupno.':'.$data['currency']);


        $string = 'amount'.$ukupno.'cardholder_address'.$data['address'].'cardholder_city'.$data['city'].'cardholder_country'.$data['country'].'cardholder_email'.$data['email'].'cardholder_name'.$data['firstname'].'cardholder_phone'.$data['telephone'].'cardholder_surname'.$data['lastname'].'cardholder_zip_code'.$data['postcode'].'cartWeb shop kupnja '.$ordernum.'currency'.$data['currency'].'language'.$data['language'].'order_number'.$ordernum.'payment_all'.$data['number_of_installments'].'require_completefalsestore_id'.$data['merchant'].'version1.3';





        $data['md5'] = hash_hmac('sha256', $string, $keym);




        return $this->load->view('extension/payment/corvuspay', $data);


        $this->render();
    }

    public function callback() {
      
      if (isset($this->request->post['order_number'])) {
               $order_id = $this->request->post['order_number'];
           } else {
               $order_id = 0;
           }

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($order_id);

        // If there is no order info then fail.
        if (!$order_info) {
            $this->session->data['error'] = $this->language->get('error_no_order');
            $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));


        }
        

        if (isset($_POST['approval_code'])) {

            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_corvuspay_order_status_id'), '', true);
            $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));




        }





    }
}
?>
