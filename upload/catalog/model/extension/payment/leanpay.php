<?php
/**
 * @author Benjamin Cizej, Moja koda d.o.o.
 */

/**
 * Class ModelExtensionPaymentLeanpay
 *
 * @property Loader $load
 * @property DB $db
 * @property Language $language
 * @property \Cart\Currency $currency
 * @property ModelExtensionPaymentLeanpaySettings $model_extension_payment_leanpay_settings
 */
class ModelExtensionPaymentLeanpay extends Model
{
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->language('extension/payment/leanpay');
        $this->load->model('extension/payment/leanpay/settings');
    }

    public function getMethod($address, $total)
    {
        $baseCurrency = $this->model_extension_payment_leanpay_settings->getBaseCurrency();
        if (!array_key_exists($baseCurrency, $this->getActiveCurrencies())) {
            return [];
        }
        $totalBaseCurrency = $this->currency->format($total, $baseCurrency, '', false);
        if (
            $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_total_min') > 0 &&
            $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_total_min') > $totalBaseCurrency
        ) {
            return [];
        }
        if ($this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_total_max') < $totalBaseCurrency) {
            return [];
        }
        if ($this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_geo_zone_id')) {
            $query = $this->db->query(sprintf(
                'SELECT * FROM %s 
                 WHERE geo_zone_id = %d AND country_id = %d AND (zone_id = %d OR zone_id = 0)',
                $this->db->escape(DB_PREFIX . 'zone_to_geo_zone'),
                (int)$this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_geo_zone_id'),
                (int)$address['country_id'],
                (int)$address['zone_id']
            ));
            if (!$query->num_rows) {
                return [];
            }
        }

        return [
            'code' => 'leanpay',
            'terms' => '',
            'title' => $this->language->get('text_title'),
            'sort_order' => $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_sort_order')
        ];
    }

    public function getActiveCurrencies() {
        $query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'currency WHERE status = 1');

        $currencies = [];
        foreach ($query->rows as $result) {
            $currencies[$result['code']] = [
                'currency_id'   => $result['currency_id'],
                'title'         => $result['title'],
                'symbol_left'   => $result['symbol_left'],
                'symbol_right'  => $result['symbol_right'],
                'decimal_place' => $result['decimal_place'],
                'value'         => $result['value']
            ];
        }

        return $currencies;
    }
}
