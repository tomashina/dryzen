<?php

/**
 * @property Loader $load
 * @property Config $config
 * @property ModelSettingSetting $model_setting_setting
 */
class ModelExtensionPaymentLeanpaySettings extends Model
{

    protected $settings = [];

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('setting/setting');
        $this->loadSettings();
    }

    public function getSetting($setting)
    {
        if (!array_key_exists($setting, $this->settings)) {
            return null;
        }

        return $this->settings[$setting];
    }

    public function getBaseCurrency()
    {
        $country = $this->getSetting('payment_leanpay_country');

        switch (strtolower($country)) {
            case 'hr':
                return 'HRK';
            default:
                return 'EUR';
        }
    }

    protected function loadSettings()
    {
        $globalSettings = $this->model_setting_setting->getSettingValue('payment_leanpay_global_settings');
        if ($globalSettings) {
            $storeId = 0;
        } else {
            $storeId = $this->config->get('config_store_id');
        }

        $this->settings = $this->model_setting_setting->getSetting('payment_leanpay', $storeId);
    }
}
