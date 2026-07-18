<?php

/**
 * Class ModelExtensionPaymentLeanpayStores.
 *
 * @property Loader $load
 * @property ModelSettingStore $model_setting_store
 * @property ModelSettingSetting $model_setting_setting
 */
class ModelExtensionPaymentLeanpayStores extends Model
{

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('setting/store');
        $this->load->model('setting/setting');
    }

    public function getStoresList()
    {
        $defaultStoreName = $this->model_setting_setting->getSettingValue('config_name');
        $defaultStoreUrl = HTTP_CATALOG;
        $defaultStoreSecure = $this->model_setting_setting->getSettingValue('config_secure');
        if ($defaultStoreSecure) {
            $defaultStoreUrl = HTTPS_CATALOG;
        }
        $list = ['0' => sprintf('%s (%s)', $defaultStoreName, $defaultStoreUrl)];

        $extraStores = $this->model_setting_store->getStores();
        if (!$extraStores) {
            return $list;
        }

        foreach ($extraStores as $store) {
            $url = $store['url'];
            if ($this->model_setting_setting->getSettingValue('config_secure', $store['store_id'])) {
                $url = $store['ssl'];
            }
            $list[$store['store_id']] = sprintf('%s (%s)', $store['name'], $url);
        }

        return $list;
    }

    public function storeExists($storeId)
    {
        if ($storeId === 0) {
            return true;
        }

        return (bool)$this->model_setting_store->getStore($storeId);
    }
}
