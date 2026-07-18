<?php
/**
 * @author Benjamin Cizej, Moja koda d.o.o.
 */

/**
 * Class ModelExtensionPaymentLeanpaySettings
 *
 * @property Loader $load
 * @property ModelSettingSetting $model_setting_setting
 */
class ModelExtensionPaymentLeanpaySettings extends Model
{
    protected $defaultSettings = [
        'payment_leanpay_global_settings' => 1,
        'payment_leanpay_status' => 0,
        'payment_leanpay_country' => 'SI',
        'payment_leanpay_title' => 'Leanpay',
        'payment_leanpay_description' => 'You will be redirected to Leanpay payment page.',
        'payment_leanpay_api_key' => '',
        'payment_leanpay_secret_word' => '',
        'payment_leanpay_test_mode' => 0,
        'payment_leanpay_total_min' => 50,
        'payment_leanpay_total_max' => 5000,
        'payment_leanpay_order_status_id' => 1,
        'payment_leanpay_payment_status_id' => 2,
        'payment_leanpay_geo_zone_id' => 0,
        'payment_leanpay_debug' => 0,
        'payment_leanpay_sort_order' => 2
    ];

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('setting/setting');
    }

    public function getDefaultSettings($storeId = 0)
    {
        $storeSecure = $this->model_setting_setting->getSettingValue('config_secure', $storeId);
        if ($storeId === 0) {
            $url = $storeSecure ? HTTPS_CATALOG : HTTP_CATALOG;
        } else {
            if (!$storeSecure) {
                $url = $this->model_setting_setting->getSettingValue('config_url', $storeId);
            } else {
                $url = $this->model_setting_setting->getSettingValue('config_ssl', $storeId);
            }
        }

        return array_merge(
            $this->defaultSettings,
            ['payment_leanpay_status_url' => $url . 'index.php?route=extension/payment/leanpay/status']
        );
    }
}
