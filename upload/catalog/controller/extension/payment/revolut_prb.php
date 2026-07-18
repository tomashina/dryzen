<?php

require_once(__DIR__ . '/revolut.php');

class ControllerExtensionPaymentRevolutPrb extends ControllerExtensionPaymentRevolut
{
    public function index()
    {
        $this->load->language('extension/payment/revolut_prb');
        $data['testmode'] = $this->config->get('payment_revolut_test');
        $data['js_domain_source'] = 'merchant';

        if ($this->config->get('payment_revolut_test')) {
            $data['js_domain_source'] = 'sandbox-merchant';
        }

        return $this->load->view('extension/payment/revolut_prb', $data);
    }

    public function params() {
        $this->sendJsonResponse($this->revolut_params());
    }
}
