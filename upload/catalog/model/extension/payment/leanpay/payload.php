<?php
/**
 * @author Benjamin Cizej, Moja koda d.o.o.
 */

/**
 * Class ModelExtensionPaymentLeanpayPayload
 *
 * @property Loader $load
 * @property Language $language
 * @property Url $url
 * @property \Cart\Currency $currency
 * @property ModelExtensionPaymentLeanpaySettings $model_extension_payment_leanpay_settings
 */
class ModelExtensionPaymentLeanpayPayload extends Model
{
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('extension/payment/leanpay/settings');
    }

    public function create($orderInfo, $vendorTransactionId)
    {
        $currency = $this->model_extension_payment_leanpay_settings->getBaseCurrency();

        return [
            'vendorApiKey' => $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_api_key'),
            'vendorTransactionId' => $vendorTransactionId,
            'amount' => round($this->currency->format($orderInfo['total'], $currency, '', false), 2),
            'successUrl' => $this->url->link('checkout/success', '', true),
            'errorUrl' => $this->url->link('checkout/failure', '', true),
            'vendorPhoneNumber' => $orderInfo['telephone'],
            'vendorFirstName' => $orderInfo['payment_firstname'],
            'vendorLastName' => $orderInfo['payment_lastname'],
            'vendorAddress' => $orderInfo['payment_address_1'] . (!empty($orderInfo['payment_address_2']) ? ' ' . $orderInfo['payment_address_2'] : ''),
            'vendorZip' => $orderInfo['payment_postcode'],
            'vendorCity' => $orderInfo['payment_city'],
            'language' => substr($this->language->get('code'), 0, 2)
        ];
    }

    public function stripSensitiveData($payload)
    {
        $payload['vendorApiKey'] = '******';
        $payload['vendorPhoneNumber'] = '******';
        $payload['vendorFirstName'] = '******';
        $payload['vendorLastName'] = '******';
        $payload['vendorAddress'] = '******';
        $payload['vendorZip'] = '******';
        $payload['vendorCity'] = '******';

        return $payload;
    }
}
