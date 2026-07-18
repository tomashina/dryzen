<?php

require_once(DIR_SYSTEM . 'library/vendor/revolut/api_request.php');
require_once(DIR_SYSTEM . 'library/vendor/revolut/revolut_payment_helper.php');

class ControllerExtensionPaymentRevolut extends Controller
{

    public function paymentMethodPreprocessEventRevolut()
    {
        $this->api_client = new ApiRequest($this->config->get('payment_revolut_api_key'), $this->config->get('payment_revolut_test'));
        
        //check if AMEX is available and update logos
        $this->api_client->setPublicKey($this->config->get('payment_revolut_api_public_key'));
        $result = $this->api_client->get("api/public/available-payment-methods?amount=0&currency=" . strtoupper($this->session->data['currency']));
        $amex_availability = isset($result['response']['available_card_brands']) && is_array($result['response']['available_card_brands']) && in_array('amex', $result['response']['available_card_brands']);
        $card_logos = '<img src="' . HTTPS_SERVER . 'catalog/view/theme/default/image/revolut/visa-logo.svg' . '" title="Visa" alt="Visa" class="img-responsive" style="width:30px;display:inline;margin-left:5px" />' . '<img src="' . HTTPS_SERVER . 'catalog/view/theme/default/image/revolut/master-card-logo.svg' . '" title="Mastercard" alt="Mastercard" class="img-responsive" style="width:30px;display:inline;margin-left:5px" />';
        
        if($amex_availability){
            $card_logos .= '<img src="' . HTTPS_SERVER . 'catalog/view/theme/default/image/revolut/amex-logo.svg' . '" title="Amex" alt="Amex" class="img-responsive" style="width:30px;display:inline;margin-left:5px" />';
        }
        $revolut_pay_logos = ' <img src="' . HTTPS_SERVER . 'catalog/view/theme/default/image/revolut/revolut.svg' . '" title="Revolut Pay" alt="Revolut Pay" class="img-responsive" style="width:30px;display:inline;margin-left:5px" />' . $card_logos;
    
        $this->session->data['card_logos'] = $card_logos;
        $this->session->data['revolut_pay_logos'] = $revolut_pay_logos;

        $payment_methods_result = $this->api_client->get("api/public/available-payment-methods?amount=0&currency=" . strtoupper($this->session->data['currency']));
        $this->session->data['available_payment_methods'] = $payment_methods_result['response']['available_payment_methods'];
    }

    public function getBillingDetails() {
        $billing = array();
        
        if (isset($this->session->data['payment_address'])) {
            $billing = $this->session->data['payment_address'];
        }
        
        if ($this->customer->isLogged()) {
            $billing['email'] = $this->customer->getEmail();
            $billing['telephone'] = $this->customer->getTelephone();
        } elseif (isset($this->session->data['guest'])) {
            $billing['email'] = $this->session->data['guest']['email'];
            $billing['telephone'] = $this->session->data['guest']['telephone'];
        }
        
        return $billing;
    }
    
    public function create_revolut_order_ajax() {
        try {
            $order = $this->getOrCreateRevolutOrder();
            $data = [
                "public_id" => $order['token'],
                "state" => $order['state']
            ];

            return $this->sendJsonResponse($data);
        } catch (Exception $e) {
            $this->log->write("create_revolut_order_ajax: " . $e->getMessage());
            return $this->sendJsonResponse(['error' => $e->getMessage()]);
        }
    }

    public function createRevolutOrder($body, $oc_order_id) {

        $this->api_request = new ApiRequest(
            $this->config->get('payment_revolut_api_key'),
            $this->config->get('payment_revolut_test')
        );

        $result = $this->api_client->post('orders', $body, true);
        $order = $result['response'];

        $this->load->model('extension/payment/revolut');
        $this->model_extension_payment_revolut->addOrder($order['id'], [
            'order_id' => $oc_order_id,
            'public_id' => $order['token']
        ]);

        $this->session->data['revolut_order_id'] = $order['id'];
        return $order;
    }

    public function getOrCreateRevolutOrder()
    {
        $this->load->model('extension/payment/revolut');

        if(empty($this->session->data['order_id'])) {
            $this->sendJsonResponse(['error' => 'Your session is expired']);
        }
        
        $oc_order_id = $this->session->data['order_id'];
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($oc_order_id);

        if(!$order_info) {
            throw new Exception("Order information not found for order ID: " . $oc_order_id);
        }

        if(empty($order_info['total'])) {
            throw new Exception("Order total is invalid for order ID: " . $oc_order_id);
        }

        if(empty($order_info['currency_code'])) {
            throw new Exception("Order currency is invalid for order ID: " . $oc_order_id);
        }

        $total = $order_info['total']; 
        $currency = $order_info['currency_code']; 
        $billing = $this->getBillingDetails();

        $order_capture_mode = 'manual';
        $cancel_after_time = "PT2M";

        $body = [
            'amount' => $this->getRevolutAmount($total, $currency),
            'currency' => $currency,
            'customer' => [
                'id' => $this->getRevolutCustomerId(),
                'email' => $billing['email']
            ],
            'merchant_order_data' => [
                'reference' => '#' . $oc_order_id
            ],
            'capture_mode' => $order_capture_mode
        ]; 


        if ($this->config->get('payment_revolut_capture_mode') === 'AUTOMATIC') {
            $body['cancel_authorised_after'] = $cancel_after_time;
        }

        $revolut_order_id = $this->session->data['revolut_order_id'] ?? null;

        if (empty($revolut_order_id)) {
            return $this->createRevolutOrder($body, $oc_order_id);
        }

        $exist_order = $this->model_extension_payment_revolut->getOrderByRevolutOrderId($revolut_order_id);

        if (!$exist_order) {
            $this->log->write(sprintf('getOrCreateRevolutOrder: No existing order found for Revolut order ID: %s, creating new order', $revolut_order_id));
            return $this->createRevolutOrder($body, $oc_order_id);
        }

        $result = $this->api_client->get('orders/' . $revolut_order_id, true);
        $order = $result['response'];

        if(empty($order['id'])) {
            $this->log->write(sprintf("getOrCreateRevolutOrder: error fetching revolut order_id : %s -  %s ", $revolut_order_id, $result));
            throw new Exception("getOrCreateRevolutOrder: error fetching revolut order");
        }

        $status = strtolower($order['state']);

        switch ($status) {
            case 'pending':
                $this->model_extension_payment_revolut->setOpenCartOrderId($revolut_order_id, $oc_order_id);
                $order = $this->api_client->patch("orders/{$revolut_order_id}", $body, true)['response'];
                break;

            case 'authorised':
                $this->api_client->post("orders/{$revolut_order_id}/cancel", $body, true);
                $this->model_extension_payment_revolut->deleteOrderByRevolutId($order['id']);
                $order = $this->createRevolutOrder($body, $oc_order_id);
                break;

            case 'cancelled':
            case 'failed':
            case 'processing':
                $this->model_extension_payment_revolut->deleteOrderByRevolutId($order['id']);
                $order = $this->createRevolutOrder($body, $oc_order_id);
                break;

            case 'completed':
                $this->log->write(sprintf('getOrCreateRevolutOrder: Unexpected order state - id: %s state: %s', $order['id'], $order['state']));
                return $order;
                break;
        }
        return $order;
    }

    public function revolut_params() {
        try {        
                $api_request = new ApiRequest(
                    $this->config->get('payment_revolut_api_key'),
                    $this->config->get('payment_revolut_test')
                );

                $base_url = rtrim(HTTP_SERVER, '/');
                $base_url = str_replace(array('http://', 'https://'), '', $base_url);

                $oc_order_id = $this->session->data['order_id'];
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($oc_order_id);

                if(!$order_info) {
                    throw new Exception("Order information not found for order ID: " . $oc_order_id);
                }

                if(empty($order_info['total'])) {
                    throw new Exception("Order total is invalid for order ID: " . $oc_order_id);
                }

                if(empty($order_info['currency_code'])) {
                    throw new Exception("Order currency is invalid for order ID: " . $oc_order_id);
                }

                $total = $order_info['total']; 
                $currency = $order_info['currency_code']; 


                $styles = [
                    'loader'  => HTTPS_SERVER . 'catalog/view/theme/default/image/revolut/loading-gif.gif',
                    'revolut_prb' => [
                        'payment_revolut_prb_theme'   => $this->config->get('payment_revolut_prb_theme'),
                        'payment_revolut_prb_action'  => $this->config->get('payment_revolut_prb_action'),
                        'payment_revolut_prb_radius'  => $this->config->get('payment_revolut_prb_radius'),
                        'payment_revolut_prb_size'    => $this->config->get('payment_revolut_prb_size'),
                    ],
                    'revolut_pay' => [
                        'payment_revolut_pay_theme'   => $this->config->get('payment_revolut_pay_theme'),
                        'payment_revolut_pay_radius'  => $this->config->get('payment_revolut_pay_radius'),
                        'payment_revolut_pay_size'    => $this->config->get('payment_revolut_pay_size'),
                    ],
                    'revolut_card' => [
                        'widget_type' => $this->config->get('payment_revolut_card_widget_type'),
                        'payment_revolut_card_font_colour' => $this->config->get('payment_revolut_card_font_colour'),
                    ]
                ];

                $order = [
                    'amount'               => $this->getRevolutAmount($total, $currency),
                    'currency'             => $currency,
                    'upsell_banner_enabled'=> $this->config->get('payment_revolut_upsell_banner_enabled'),
                    'billing_address'      => $this->getBillingDetails(),
                ];

                return [
                    'public_token'             => $this->config->get('payment_revolut_api_public_key'),
                    'mode'                     => $this->config->get('payment_revolut_test') ? 'sandbox' : 'prod',
                    'mobile_redirection_url'   => $this->url->link('extension/payment/revolut/appRedirection'),
                    'upsell_embed_script_url'  => $api_request->getApiBaseUrl() . 'upsell/embed.js',
                    'base_url'                 => $base_url,
                    'order'                    => $order,
                    'styles'                   => $styles,

                ];
            } catch (Exception $e) {
                $this->log->write("revolut_params: " . $e->getMessage());
                return ['error' => $e->getMessage()];
        }
    }

    public function addOrderHistory($oc_order_id, $order_status_id, $comment) {
        $oc_order_id = (int)$oc_order_id;

        if (!$this->acquireLock($oc_order_id)) {
            return false;
        }

        try {
            
            $this->load->model('extension/payment/revolut');
            $order_history_check = $this->model_extension_payment_revolut->getOrderHistory($oc_order_id, $order_status_id, $comment);

            if (!$order_history_check) {
                $this->load->model('checkout/order');
                $this->model_checkout_order->addOrderHistory($oc_order_id, $order_status_id, $comment);
            }

        } catch (\Exception $e) {
            return false;
        } finally {
            $this->releaseLock($oc_order_id);
        }

        return true;
    }

    public function acquireLock($oc_order_id) {
        try {
            $this->db->query(
                "INSERT INTO `" . DB_PREFIX . "order_history_lock` (order_id)
                VALUES ('" . (int)$oc_order_id . "')"
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function releaseLock($oc_order_id) {
        $this->db->query(
            "DELETE FROM `" . DB_PREFIX . "order_history_lock`
            WHERE order_id = '" . (int)$oc_order_id . "'"
        );
    }
    
    public function webhook()
    {
        $data = file_get_contents("php://input");
        $data = json_decode($data, true);

        if (empty($data['event']) || !$data['order_id'] || $data['event'] != 'ORDER_COMPLETED') {
            header("HTTP/1.0 401 Unauthorized");
            exit;
        }

        $this->api_client = new ApiRequest($this->config->get('payment_revolut_api_key'), $this->config->get('payment_revolut_test'));

        $this->load->model('checkout/order');
        $this->load->model('extension/payment/revolut');

        $order_info = $this->model_extension_payment_revolut->getOrderByRevolutOrderId($data['order_id']);

        if (empty($order_info['order_id'])) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }

        $oc_order_id = $order_info['order_id'];
        $order_info = $this->model_checkout_order->getOrder($oc_order_id);
        $order_status_id = $this->config->get('payment_revolut_completed_status_id');
        $revolut_id = $data['order_id'];
        $event = $data['event'];
        $comment = "Revolut Payment Gateway - Order Payment {$event}. Transaction ID: {$revolut_id}";
        $result = $this->finalizeOrder($oc_order_id, $revolut_id, $order_status_id, $comment);

        if($result['success']) {
            header("HTTP/1.1 200 OK");
            return;
        } 
        
        return $this->sendJsonResponse($result);

    }

    public function check_order_status() {

        $response = [
            'success' => false,
            'error' => null,
            'state' => null
        ];

        try {

            if (empty($this->request->post['public_id'])) {
                $response['error'] = 'missing_public_id';
                $this->sendJsonResponse($response);
                return;
            }

            $public_id = $this->request->post['public_id'];
            $response['public_id'] = $public_id;
            $this->load->model('extension/payment/revolut');
            $revolut_order_info = $this->model_extension_payment_revolut->getOrderByRevolutPublicId($public_id);
            if (empty($revolut_order_info) || empty($revolut_order_info['revolut_id'])) {
                $response['error'] = 'invalid_order';
                $this->sendJsonResponse($response);
                return;
            }

            $revolut_id = $revolut_order_info['revolut_id'];

            $api_key = $this->config->get('payment_revolut_api_key');
            $is_test_mode = $this->config->get('payment_revolut_test');

            $this->api_client = new ApiRequest($api_key, $is_test_mode);

            $api_response = $this->api_client->get('orders/' . $revolut_id, true);

            if (empty($api_response['response']) || !isset($api_response['response']['state'])) {
                $response['error'] = 'api_response_invalid';
                $this->sendJsonResponse($response);
                return;
            }

            $order = $api_response['response'];

            $response['success'] = true;
            $response['state'] = $order['state'];
            $response['error'] = null;

            $this->sendJsonResponse($response);

        } catch (Exception $e) {
            $this->log->write("check_order_status: " . $e->getMessage());
            $response['error'] = 'An error occurred while processing your request : ' . $e->getMessage();
            $this->sendJsonResponse($response);
        }
    }

    public function captureRevolutOrder($revolut_id)
    {
        $result = $this->api_client->post("orders/{$revolut_id}/capture");
        return isset($result['response']['id']);
    }


    public function processCapturedOrder($revolut_order_id, $oc_order_id)
    {
        $order = $this->fetchRevolutOrder($revolut_order_id);
        $state = strtolower($order['state']);

        if ($state === 'completed') {
            $comment = "Revolut Payment Gateway - Order Payment Completed. Transaction ID: {$revolut_order_id}";
            return $this->finalizeOrder($oc_order_id, $revolut_order_id, $this->config->get('payment_revolut_completed_status_id'), $comment);
        }

        if ($state === 'authorised') {
            return ['success' => false, 'error' => 'Payment capture is taking longer then expected' ,'retry' => true];
        }

        $this->log->write("processCapturedOrder: Unexpected state '{$state}' for Revolut order ID: {$revolut_order_id}");
        return ['success' => false, 'error' => 'Unexpected order state'];
    }

    private function finalizeOrder($oc_order_id, $revolut_order_id, $order_status_id, $comment)
    {
        $this->addOrderHistory(
            $oc_order_id,
            $order_status_id,
            $comment
        );

        try {
            $this->updateMethodTitle($oc_order_id);
        } catch (Exception $e) {
            $this->log->write("finalizeOrder: Unable to update order details for Revolut order ID: {$revolut_order_id}. Error: {$e->getMessage()}");
        }

        return [
            'success' => true,
            'redirect' => $this->url->link('checkout/success&orderId='. $oc_order_id, '' )
        ];
    }

    public function fetchRevolutOrder($revolut_order_id)
    {
        $this->api_client = new ApiRequest(
            $this->config->get('payment_revolut_api_key'),
            $this->config->get('payment_revolut_test')
        );

        $result = $this->api_client->get("orders/{$revolut_order_id}", true);
        $order = $result['response'];

        if(!isset($order['id'])) {
            $this->log->write(sprintf("fetchRevolutOrder: error fetching order_id : %s -  %s ", $revolut_order_id, $result));
            return [];
        }

        return $order;
    }
    
    public function process_completed_order() {

        if (empty($this->request->post['public_id'])) {
            return $this->sendJsonResponse(['error' => 'Missing public id']);
        }

        $public_id = $this->request->post['public_id'];
        $this->load->model('extension/payment/revolut');
        $order_record = $this->model_extension_payment_revolut->getOrderByRevolutPublicId($public_id);

        if (!$order_record || empty($order_record['revolut_id']) || empty($order_record['order_id'])) {
            $this->log->write("process_completed_order: oc order not found in db");
            return $this->sendJsonResponse(['error' => 'Payment record not found']);
        }

        $oc_order_id = $order_record['order_id'];
        $revolut_order_id = $order_record['revolut_id'];

        $rev_order = $this->fetchRevolutOrder($revolut_order_id);
        
        if(empty($rev_order)) {
            return $this->sendJsonResponse(["success" => false, "error" => "Unable to load payment"]);
        }

        $state = isset($rev_order['state']) ? strtolower($rev_order['state']) : null;
        
        if($state !== 'completed') {
            $this->log->write("process_completed_order: Expected state 'completed', got {$state} for Revolut order ID: {$revolut_order_id}");
            return $this->sendJsonResponse(['success' => false, 'error' => 'Order is not in completed state.']);
        }

        $this->load->model('checkout/order');
        $oc_order = $this->model_checkout_order->getOrder($oc_order_id);

        if($oc_order['order_status_id'] == $this->config->get('payment_revolut_completed_status_id')) {
            return $this->sendJsonResponse([
                'success' => true,
                'redirect' => $this->url->link('checkout/success&orderId='. $oc_order_id, '' )
            ]);
        }
        $comment = "Revolut Payment Gateway - Order Payment Completed. Transaction ID: {$revolut_order_id}";
        $result = $this->finalizeOrder($oc_order_id, $revolut_order_id, $this->config->get('payment_revolut_completed_status_id'), $comment);
        return $this->sendJsonResponse($result);
    }

    public function processOrder($revolut_order_id, $oc_order_id) {

        $this->load->model('extension/payment/revolut');

        if (!$this->model_extension_payment_revolut->setOpenCartOrderId($revolut_order_id, $oc_order_id)) {
            return ['error' => 'Unable to update oc order id'];
        }

        $order = $this->fetchRevolutOrder($revolut_order_id);
        $state = isset($order['state']) ? strtolower($order['state']) : null;
        if ($state !== 'authorised') {
            $this->log->write("processOrder: Expected state 'authorised', got {$state} for Revolut order ID: {$revolut_order_id}");
            return ['success' => false, 'error' => 'Order is not in authorised state.'];
        }

        // Manual capture mode
        if ($this->config->get('payment_revolut_capture_mode') === 'MANUAL') {
            $comment = "Revolut Payment Gateway - Order Payment Authorised. Transaction ID: {$revolut_order_id}";
            return $this->finalizeOrder($oc_order_id, $revolut_order_id, $this->config->get('payment_revolut_authorised_status_id'), $comment);
        }

        // Automatic capture
        if (!$this->captureRevolutOrder($revolut_order_id)) {
            return ['success' => false, 'error' => "Payment capture failed."];
        }

        $capture_result = $this->processCapturedOrder($revolut_order_id, $oc_order_id);

        return $capture_result;
    }

    public function completeOrder()
    {
        if (empty($this->request->post['public_id'])) {
            $this->sendJsonResponse(['error' => 'invalid_public_id']);
            return;
        }

        $public_id = $this->request->post['public_id'];
        
        $this->load->model('extension/payment/revolut');
        $this->load->model('checkout/order');

        $order_record = $this->model_extension_payment_revolut->getOrderByRevolutPublicId($public_id);
        
        if (!$order_record || empty($order_record['revolut_id']) || empty($order_record['order_id'])) {
            $this->log->write("completeOrder: oc order not found in db");
            $this->sendJsonResponse(['error' => 'payment_record_not_found']);
            return;
        }
        $oc_order_id = $order_record['order_id'];
        $revolut_order_id = $order_record['revolut_id'];

        if (!$this->model_checkout_order->getOrder($oc_order_id)) {
            $this->log->write("completeOrder: Missing order info for OC order ID: {$oc_order_id} / Revolut order ID: {$revolut_order_id}");
            $this->sendJsonResponse(['error' => 'Missing order info']);
            return;
        }

        $retry = isset($this->request->post['retry']) ? $this->request->post['retry'] : null;

        // Capture request already sent but order was still in authorised state
        if ($retry) {
            $capture_result = $this->processCapturedOrder($revolut_order_id, $oc_order_id);
            return $this->sendJsonResponse($capture_result);
        }

        $process_result = $this->processOrder($revolut_order_id, $oc_order_id);
        $this->sendJsonResponse($process_result);
    }

    public function process_captured_order_ajax() {

        if (empty($this->request->post['public_id'])) {
            $this->sendJsonResponse(['error' => 'invalid_public_id']);
            return;
        }

        $public_id = $this->request->post['public_id'];
        
        $this->load->model('extension/payment/revolut');
        $this->load->model('checkout/order');

        $order_record = $this->model_extension_payment_revolut->getOrderByRevolutPublicId($public_id);
        
        if (!$order_record || empty($order_record['revolut_id']) || empty($order_record['order_id'])) {
            $this->log->write("completeOrder: oc order not found in db");
            $this->sendJsonResponse(['error' => 'payment_record_not_found']);
            return;
        }

        $oc_order_id = $order_record['order_id'];
        $revolut_order_id = $order_record['revolut_id'];
        
        $order = $this->fetchRevolutOrder($revolut_order_id);
        $state = strtolower($order['state']);

        if(!in_array($state, ['completed', 'authorised'])) {
            $this->log->write("process_captured_order_ajax unexpected state : {$state}");
            return $this->sendJsonResponse(['success' => false, "error" => "Unexpected order state {$state}"]);
        }

        if($state == 'completed') {
            $comment = "Revolut Payment Gateway - Order Payment Completed. Transaction ID: {$revolut_order_id}";
            $result = $this->finalizeOrder($oc_order_id, $revolut_order_id, $this->config->get('payment_revolut_completed_status_id'), $comment);
            return $this->sendJsonResponse($result);
        }


        $comment = sprintf("If the order is not moved to the Processing state after 24h, please check your Revolut account to verify that this payment was taken.
                    You might need to contact your customer if it wasn't.");

        $result = $this->finalizeOrder($oc_order_id, $revolut_order_id, $this->config->get('payment_revolut_processing_status_id'), $comment);
        
        return $this->sendJsonResponse($result);
    }

    public function updateMerchantOrderId($revolut_order_id, $merchant_order_id)
    {
        $this->api_client = new ApiRequest($this->config->get('payment_revolut_api_key'), $this->config->get('payment_revolut_test'));

        $body = ['merchant_order_data' => ['reference' => '#' . $merchant_order_id]];

        $this->api_client->patch("orders/{$revolut_order_id}", $body, true);
    }

    public function updateMethodTitle($oc_order_id) {
        $gateway = get_class($this);

        $payment_title = 'Revolut Pay';

        if ($gateway == 'ControllerExtensionPaymentRevolutCard') {
            $this->load->language('extension/payment/revolut');
            $payment_title = $this->config->get('payment_revolut_payment_title') ? $this->config->get('payment_revolut_payment_title') : $this->language->get('text_title');
        }

        if ($gateway == 'ControllerExtensionPaymentRevolutPrb') {
            $this->load->language('extension/payment/revolut_prb');
            $payment_title = $this->config->get('payment_revolut_payment_title') ? $this->config->get('payment_revolut_payment_title') : $this->language->get('text_title');
        }

        $this->model_extension_payment_revolut->updatePaymentMethodName($oc_order_id, $payment_title);

    }

    public function eventPostModelAddOrderHistory($route, &$args)
    {
        $this->api_client = new ApiRequest($this->config->get('payment_revolut_api_key'), $this->config->get('payment_revolut_test'));

        $order_id = $args[0];
        $order_status_id = $args[1];

        $this->load->model('checkout/order');
        $this->load->model('extension/payment/revolut');

        $revolut_order_info = $this->model_extension_payment_revolut->getRevolutOrder($order_id);

        if (
            $this->config->get('payment_revolut_capture_mode') != 'MANUAL'
            || $order_status_id != $this->config->get('payment_revolut_capture_status_id')
            || isset($this->session->data['revolut_capture'])
            || !$revolut_order_info['revolut_id']
        ) {
            return;
        }

        $result = $this->api_client->post('orders/' . $revolut_order_info['revolut_id'] . '/capture');
        $response = $result['response'];

        if (isset($response['state']) && $response['state'] == 'COMPLETED') {
            $comment = 'Revolut Payment Gateway - Payment Captured. Transaction State: ' . $response['state'] . '. Transaction ID: ' . $response['id'];
            $this->model_checkout_order->addOrderHistory($order_id, $order_status_id, $comment, false);
        }
    }
    public function injectUpsellScriptConfirmationPage($route, &$args)
    {
        if (!$this->config->get('payment_revolut_upsell_banner_enabled'))
            return;

        $upsell_embed_file = "https://merchant.revolut.com/upsell/embed.js";
        if ($this->config->get('payment_revolut_test')) {
            $upsell_embed_file = "https://sandbox-merchant.revolut.com/upsell/embed.js";
        }

        $this->document->addScript($upsell_embed_file, 'header');
        $this->document->addScript('catalog/view/javascript/revolut_upsell_banner.js', 'header');
    }

    public function upsellBannerHandler()
    {
        if(!isset($this->request->get['orderId'])){
            return $this->sendJsonResponse(['enabled' => false]);
        }

        $oc_order_id = $this->request->get['orderId'];

        $this->load->model('extension/payment/revolut');
        $order_record = $this->model_extension_payment_revolut->getOrderByOcOrderId($oc_order_id);
        $response['public_id'] = $order_record['revolut_public_id'];
        $response['token'] = $this->config->get('payment_revolut_api_public_key');
        $response['enabled'] = $this->config->get('payment_revolut_upsell_banner_enabled');

        return $this->sendJsonResponse($response);
    }

    public function appRedirection()
    {

        if(isset($this->request->get['_rp_dos'])){
            return $this->response->redirect($this->url->link('checkout/checkout'));
        }

        if (isset($this->request->get['_rp_fr'])) {
            $error_message = $this->request->get['_rp_fr'];
            $this->displayError($error_message);
            return;
        }

        $publicId = isset($this->request->get['_rp_oid']) ? $this->request->get['_rp_oid'] : null;

        if (!$publicId) {
            return $this->displayError("public_id was not passed");
        }

        $this->load->model('extension/payment/revolut');
        $order_record = $this->model_extension_payment_revolut->getOrderByRevolutPublicId($publicId);
        
        if (!$order_record || empty($order_record['revolut_id']) || empty($order_record['order_id'])) {
            $this->log->write("appRedirection: oc order not found in db");
            return $this->displayError("Payment record not found");
        }

        $oc_order_id = $order_record['order_id'];
        $revolut_order_id = $order_record['revolut_id'];
        
        $this->load->model('checkout/order');
        if (!$this->model_checkout_order->getOrder($oc_order_id)) {
            $this->log->write("appRedirection: Missing order info for OC order ID: {$oc_order_id} / Revolut order ID: {$revolut_order_id}");
            return $this->displayError("Missing order info");
        }

        $processResult = $this->processOrder($revolut_order_id, $oc_order_id);

        if (isset($processResult['success']) && $processResult['success']) {
            return $this->response->redirect($processResult['redirect']);
        }

        $error = isset($processResult['error'] ) ? $processResult['error']  : "Something went wrong";
        $this->displayError($error);
    }

    public function createRevolutCustomer($billing_phone, $billing_email)
    {
        $this->api_client = new ApiRequest($this->config->get('payment_revolut_api_key'), $this->config->get('payment_revolut_test'));
        $revolut_customer_id = "";
        try {
            if (empty($billing_phone) || empty($billing_email)) {
                return;
            }
            $body = [
                'phone' => $billing_phone,
                'email' => $billing_email,
            ];
            try {
                $create_customer = $this->api_client->post('/customers', $body);
            } catch (Exception $e) {
                $this->log->write('createRevolutCustomer : Customer email already registered or api call failed. error : ' . $e->getMessage());
            }
            if (!empty($create_customer['response']['id'])) {
                $revolut_customer_id = $create_customer['response']['id'];
            }

            return $revolut_customer_id;
        } catch (Exception $e) {
            $this->log->write('createRevolutCustomer : ' . $e->getMessage());
        }
    }

    public function getRevolutCustomerId()
    {
        $this->api_client = new ApiRequest($this->config->get('payment_revolut_api_key'), $this->config->get('payment_revolut_test'));
        $this->load->model('checkout/order');
        $billing = $this->getBillingDetails();
        try {
            $customer_email = $billing['email'];
            $customer_phone = $billing['telephone'];

            $revolut_customer_search = $this->api_client->get('/customers?term=' . $customer_email);

            $revolut_customer_id = !empty($revolut_customer_search['response'][0]['id']) ? $revolut_customer_search['response'][0]['id'] : '';
            if (!empty($revolut_customer_id)) {
                $revolut_customer = $this->api_client->get('customers/' . $revolut_customer_id);
                $phone_number_exist = isset($revolut_customer['response']['phone']);
                $phone_number_mismatch = $phone_number_exist && $revolut_customer['response']['phone'] !== $customer_phone;

                if ($phone_number_mismatch || !$phone_number_exist) {
                    $body = ['phone' => $customer_phone];
                    $this->api_client->patch("customers/$revolut_customer_id", $body);
                }
            }

            if (empty($revolut_customer_id)) {
                $revolut_customer_id = $this->createRevolutCustomer($customer_phone, $customer_email);
            }
            return $revolut_customer_id;
        } catch (Exception $e) {
            $this->log->write('should_createRevolutCustomer : ' . $e->getMessage());
        }
    }
    public function displayError($message)
    {
        $data['heading_title'] = "Something went wrong while taking the payment";
        $data['text_error'] = "Error: " . $message;
        $data['button_continue'] = "Back to checkout";
        $data['continue'] = $this->url->link('checkout/checkout');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/payment/revolut_error', $data));
    }

    public function collectLineItems($oc_order_id) {
        $this->load->model('checkout/order');
        $this->load->model('catalog/product');
        $this->load->model('account/order'); 

        $order_info = $this->model_checkout_order->getOrder($oc_order_id);

        if (empty($order_info)) {
            return;
        }

        $order_products = $this->model_account_order->getOrderProducts($oc_order_id);
        $line_items = [];

        foreach ($order_products as $product) {

            $product_id = $product['product_id'];
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if (empty($product_info) || empty($product_id)) {
                return;
            }

            $product_name =  $product['name'];
            $product_qty = $product['quantity'];
            $product_unit_price = round($product['price'] * 100);

            $product_url = $this->url->link('product/product', 'product_id=' . $product_id);
            $taxes = $this->getOrderTaxes($oc_order_id);
            $total_amount = $product_unit_price;

            if(!empty($taxes)) {
                foreach($taxes as $tax) {
                    $total_amount += $tax['amount'];
                }
            }

            $query = $this->db->query("SELECT shipping FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
            $shipping_required = $query->row['shipping'];

            $product_type = $shipping_required ? 'physical' : 'service';
            $product_description = $product_info['description'];
            if (!empty($product_description)) {
                $product_description = substr($product_info['description'], 0, 1024);
            }

            if(empty($product_name) || empty($product_qty) || empty($product_unit_price) || empty($total_amount) || empty($product_type)) {
                continue;
            }

            $image_urls = $this->getProductImages($product_id, $product_info );
            $line_item = [
                'name' => $product_name,
                'type' => $product_type,
                'quantity' => [
                    'value' => $product_qty,
                ],
                'unit_price_amount' => $product_unit_price,
                'total_amount' => $total_amount,
                'taxes' => empty($taxes) ? null : $taxes,
                'description' => empty($product_description) ? null : $product_description,
                'external_id' => $product_id,
            ];

            $line_items[] = $line_item;
        }

        return $line_items;

    }
    
    public function getProductImages($product_id, $product_info) {

        try {
            $this->load->model('catalog/product');
            $product_images = $this->model_catalog_product->getProductImages($product_id);

            if(!isset($product_info['image']) || (empty($product_info['image']) && empty($product_images))) {
                return [];
            }
            $main_image = $this->config->get('config_url') .'image/'. $product_info['image'];
            $image_urls = [$main_image];

            foreach ($product_images as $additional_image) {
                array_push($image_urls, $this->config->get('config_url') .'image/'. $additional_image['image']);
            }

            return $image_urls;
        } catch (Exception $e) {
            return [];
        }

    }

    public function getOrderTaxes($oc_order_id) {
        try {
            $this->load->model('checkout/order');
            $order_totals = $this->model_checkout_order->getOrderTotals($oc_order_id);
            $taxes = [];
    
            foreach ($order_totals as $total) {
                if ($total['code'] == 'tax') {
                    array_push($taxes, ['name' => $total['title'], 'amount' => round($total['value'] * 100) ]);
                }
            }
    
            return $taxes;
        } catch (Exception $e) {
            return [];
        }
    }

    public function collectShippingInformation($oc_order_id) {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($oc_order_id);

        $street_line_1 = empty($order_info['shipping_address_1']) ? null : $order_info['shipping_address_1'] ;
        $street_line_2 = empty($order_info['shipping_address_2']) ? null : $order_info['shipping_address_2'] ;
        $post_code = empty($order_info['shipping_postcode']) ? null : $order_info['shipping_postcode'] ;
        $city = empty($order_info['shipping_city']) ? null : $order_info['shipping_city'];
        $region = empty($order_info['shipping_zone']) ? null : $order_info['shipping_zone'];
        $country = empty($order_info['shipping_iso_code_2']) ? null : $order_info['shipping_iso_code_2'];

        if (empty($street_line_1) || empty($post_code) || empty($city) || empty($country)) {
            return [];
        }

        $address_info = [
            'street_line_1' => $street_line_1,
            'street_line_2' => $street_line_2,
            'postcode' => $post_code,
            'city' => $city,
            'region' => $region,
            'country_code' => $country,
        ];

        $first_name = empty($order_info['firstname']) ? null : $order_info['firstname'];
        $last_name = empty($order_info['lastname']) ? null : $order_info['lastname'];
        $telephone = empty($order_info['telephone']) ? null : $order_info['telephone'];
        $email = empty($order_info['email']) ? null : $order_info['email'];

        if (empty($telephone) && empty($email)) {
            return ['address' => $address_info];
        }

        $contact_info = [
            'name' => empty($first_name) || empty($last_name) ? null : "{$first_name} {$last_name}",
            'email' => $email,
            'phone' => $telephone,
        ];

        $shipping_details = [
            'address' => $address_info,
            'contact' => $contact_info,
        ];
        
        return $shipping_details; 
    }

    public function updateRevolutOrderLineItemsAndShippingData($oc_order_id, $revolut_id)
    {
        if(empty($oc_order_id) || empty($revolut_id)) {
            return;
        }

        try {

            $params = array_filter([
                'line_items' => $this->collectLineItems($oc_order_id),
                'shipping' => $this->collectShippingInformation($oc_order_id),
            ]);

            if (!empty($params)) {
                $this->api_client->patch("orders/{$revolut_id}", $params, true);
            }
            
        } catch (Exception $e) {
           $this->log->write('unable to update line items and shipping data : ' . $e->getMessage());
        }
    }

    public function getRevolutAmount($amount, $currency_code)
    {
        $formatter = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);
        $formatter->setTextAttribute(NumberFormatter::CURRENCY_CODE, $currency_code);
        $fractionalLength = $formatter->getAttribute(NumberFormatter::FRACTION_DIGITS);
        $minorUnitFactor = pow(10, $fractionalLength);
        return (int) round($amount * $minorUnitFactor);
    }

    public function sendJsonResponse($data) {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
        return;
    }

    public function revolutOrderConfirmationEvent() {

        if( ! isset($this->session->data['revolut_order_id'])) {
            return;
        }

        try {
            $revolut_order_id = $this->session->data['revolut_order_id'];
            $this->api_client = new ApiRequest($this->config->get('payment_revolut_api_key'), $this->config->get('payment_revolut_test'));
            $result = $this->api_client->get('orders/' . $revolut_order_id, true);
            $order = $result['response'];
            $order_state = isset($order['state']) ? $order['state'] : null;

            if($order_state === 'completed') {
                unset($this->session->data['revolut_order_id']);
            }

        } catch(Throwable $e) {
            $this->log->write("revolutOrderConfirmation error : " . $e->getMessage());
        }
    }

}
