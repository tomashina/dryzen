<?php

require_once(__DIR__ . '/revolut.php');

class ControllerExtensionPaymentRevolutCard extends ControllerExtensionPaymentRevolut
{
    public function index()
    {   
        $this->load->language('extension/payment/revolut');
        $data['testmode'] = $this->config->get('payment_revolut_test');
        $data['widget_type'] = $this->config->get('payment_revolut_card_widget_type');
        $data['js_domain_source'] = 'merchant';
        $data['text_testmode'] = $this->language->get('text_testmode');
        $data['text_card_details'] = $this->language->get('text_card_details');
        $data['text_testmode'] = $this->language->get('text_testmode');
        $data['button_confirm'] = $this->language->get('button_confirm');
        if ($this->config->get('payment_revolut_test')) {
            $data['js_domain_source'] = 'sandbox-merchant';
        }

        return $this->load->view('extension/payment/revolut_card', $data);
    }
    
    public function params() {
        $this->sendJsonResponse($this->revolut_params());
    }
}
