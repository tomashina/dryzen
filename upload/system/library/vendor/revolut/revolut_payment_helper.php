<?php

class RevolutHelper
{
    public function setAvailablePaymentMethods($setCardLogos = false, $revolutConfig, $session)
    {
        require_once(DIR_SYSTEM . 'library/vendor/revolut/api_request.php');
        $this->api_client = new ApiRequest($revolutConfig->get('payment_revolut_api_key'), $revolutConfig->get('payment_revolut_test'));
        $this->api_client->setPublicKey($revolutConfig->get('payment_revolut_api_public_key'));
    
        if($setCardLogos)
        {
            $result = $this->api_client->get("api/public/available-payment-methods?amount=0&currency=" . strtoupper($session->data['currency']));
            $amex_availability = isset($result['response']['available_card_brands']) && is_array($result['response']['available_card_brands']) && in_array('amex', $result['response']['available_card_brands']);
            $card_logos = '<img src="' . HTTPS_SERVER . 'catalog/view/theme/default/image/revolut/visa-logo.svg' . '" title="Visa" alt="Visa" class="img-responsive" style="width:30px;display:inline;margin-left:5px" />' . '<img src="' . HTTPS_SERVER . 'catalog/view/theme/default/image/revolut/master-card-logo.svg' . '" title="Mastercard" alt="Mastercard" class="img-responsive" style="width:30px;display:inline;margin-left:5px" />';
            
            if($amex_availability){
                $card_logos .= '<img src="' . HTTPS_SERVER . 'catalog/view/theme/default/image/revolut/amex-logo.svg' . '" title="Amex" alt="Amex" class="img-responsive" style="width:30px;display:inline;margin-left:5px" />';
            }
            $revolut_pay_logos = ' <img src="' . HTTPS_SERVER . 'catalog/view/theme/default/image/revolut/revolut.svg' . '" title="Revolut Pay" alt="Revolut Pay" class="img-responsive" style="display:inline;margin-left:5px" />' . $card_logos;
        
            $session->data['card_logos'] = $card_logos;
            $session->data['revolut_pay_logos'] = $revolut_pay_logos;
        }
    
        $payment_methods_result = $this->api_client->get("api/public/available-payment-methods?amount=0&currency=" . strtoupper($session->data['currency']));
        
        if(isset($payment_methods_result['response']['available_payment_methods']) && !empty($payment_methods_result['response']['available_payment_methods']))
        {
            $session->data['available_payment_methods'] = $payment_methods_result['response']['available_payment_methods'];
        }
    }
}

?>