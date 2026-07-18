<?php
/**
 * @author Benjamin Cizej, Moja koda d.o.o.
 */

/**
 * Class ModelExtensionPaymentLeanpayLanguage
 *
 * @property Loader $load
 * @property Language $language
 */
class ModelExtensionPaymentLeanpayLanguage extends Model
{
    protected $settingsFormStrings = [
        'heading_title',
        'text_enabled',
        'text_disabled',
        'text_payment',
        'text_edit_success',
        'text_edit',
        'text_yes',
        'text_no',
        'text_all_zones',
        'button_save',
        'button_cancel',
        'entry_payment_api_key',
        'entry_payment_secret_word',
        'entry_payment_secret_word_tip',
        'entry_payment_test_mode',
        'entry_order_status',
        'entry_order_status_tip',
        'entry_order_status_paid',
        'entry_order_status_paid_tip',
        'entry_payment_status_url',
        'entry_payment_status_url_tip',
        'entry_total_min',
        'entry_total_min_tip',
        'entry_total_max',
        'entry_total_max_tip',
        'entry_geo_zone',
        'entry_multistore',
        'entry_multistore_tip',
        'entry_global_settings',
        'entry_store',
        'entry_store_tip',
        'entry_status',
        'entry_country',
        'entry_country_tip',
        'entry_sort_order',
        'entry_debug',
        'entry_debug_tip'
    ];

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->language('extension/payment/leanpay');
    }

    public function getFormStrings()
    {
        $strings = [];
        foreach ($this->settingsFormStrings as $formString) {
            $strings[$formString] = $this->language->get($formString);
        }

        return $strings;
    }
}
