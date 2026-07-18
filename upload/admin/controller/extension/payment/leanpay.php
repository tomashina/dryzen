<?php
/**
 * @author Benjamin Cizej, Moja koda d.o.o.
 */

require_once DIR_SYSTEM . 'library/leanpay/vendor/autoload.php';

/**
 * Class ControllerExtensionPaymentLeanpay
 *
 * @property Loader $load
 * @property Document $document
 * @property Language $language
 * @property Request $request
 * @property ModelSettingSetting $model_setting_setting
 * @property Session $session
 * @property Response $response
 * @property Url $url
 * @property ModelExtensionPaymentLeanpayDatabase $model_extension_payment_leanpay_database
 * @property ModelExtensionPaymentLeanpayLanguage $model_extension_payment_leanpay_language
 * @property ModelExtensionPaymentLeanpaySettings $model_extension_payment_leanpay_settings
 * @property ModelExtensionPaymentLeanpayStores $model_extension_payment_leanpay_stores
 * @property ModelLocalisationGeoZone $model_localisation_geo_zone
 * @property ModelLocalisationOrderStatus $model_localisation_order_status
 * @property \Cart\User $user
 */
class ControllerExtensionPaymentLeanpay extends Controller
{
    protected $data = [];
    protected $errors = [];
    protected $storeId = 0;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('extension/payment/leanpay/language');
        $this->load->model('extension/payment/leanpay/settings');
        $this->load->model('extension/payment/leanpay/database');
        $this->load->model('extension/payment/leanpay/stores');
        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');
        $this->load->language('extension/payment/leanpay');

        if (isset($this->request->get['store_id'])) {
            $this->storeId = (int)$this->request->get['store_id'];
        }
        if (!$this->model_extension_payment_leanpay_stores->storeExists($this->storeId)) {
            $this->response->redirect($this->url->link(
                'extension/payment/leanpay',
                'user_token=' . $this->session->data['user_token'],
                true
            ));
        }
        $this->document->addScript('view/javascript/leanpay/settings.js');
    }

    public function index()
    {
        $this->document->setTitle($this->language->get('heading_title'));
        $this->setFormActions();
        $this->setFormData();
        $this->setBreadcrumbs();
        $this->setCommonTemplates();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if ($this->request->post['payment_leanpay_global_settings']) {
                $this->storeId = 0;
            }
            $this->model_setting_setting->editSetting('payment_leanpay', $this->request->post, $this->storeId);
            $this->model_setting_setting->editSettingValue(
                'payment_leanpay',
                'payment_leanpay_global_settings',
                $this->request->post['payment_leanpay_global_settings']
            );
            $this->session->data['success'] = $this->language->get('text_edit_success');

            $urlParams = ['user_token' => $this->session->data['user_token']];
            if (isset($this->request->get['store_id'])) {
                $urlParams['store_id'] = $this->storeId;
            }
            $this->response->redirect($this->url->link('extension/payment/leanpay', $urlParams, true));
        }

        $this->data['errors'] = $this->errors;

        $this->response->setOutput($this->load->view('extension/payment/leanpay/form', $this->data));
    }

    public function install()
    {
        $this->model_extension_payment_leanpay_database->installTables();
    }

    public function uninstall()
    {
        $this->model_extension_payment_leanpay_database->uninstallTables();
    }

    protected function setFormActions()
    {
        $this->data['cancel'] = $this->url->link('marketplace/extension', [
            'user_token' => $this->session->data['user_token'],
            'type' => 'payment'
        ], true);
        $this->data['form_action'] = $this->url->link('extension/payment/leanpay', [
            'user_token' => $this->session->data['user_token'],
            'store_id' => $this->storeId
        ], true);
    }

    protected function setBreadcrumbs()
    {
        $this->data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
            ],
            [
                'text' => $this->language->get('text_payment'),
                'href' => $this->url->link('marketplace/extension', [
                    'user_token' => $this->session->data['user_token'],
                    'type' => 'payment'
                ], true)
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/payment/leanpay', [
                    'user_token' => $this->session->data['user_token'],
                    'store_id' => $this->storeId
                ], true)
            ]
        ];
    }

    protected function setFormData()
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && !isset($this->request->post['payment_leanpay_global_settings'])) {
            $this->request->post['payment_leanpay_global_settings'] = '0';
        }
        $this->data = array_merge(
            $this->data,
            $this->model_extension_payment_leanpay_language->getFormStrings(),
            $this->model_extension_payment_leanpay_settings->getDefaultSettings($this->storeId),
            $this->model_setting_setting->getSetting('payment_leanpay', $this->storeId),
            ['payment_leanpay_global_settings' => $this->model_setting_setting->getSettingValue('payment_leanpay_global_settings')],
            $this->request->post,
            [
                'success' => !empty($this->session->data['success']) ? $this->session->data['success'] : '',
                'order_statuses' => $this->model_localisation_order_status->getOrderStatuses(),
                'geo_zones' => $this->model_localisation_geo_zone->getGeoZones(),
                'stores' => $this->model_extension_payment_leanpay_stores->getStoresList(),
                'store_id_config' => $this->storeId,
                'form_base_url' => $this->url->link('extension/payment/leanpay', 'user_token=' . $this->session->data['user_token'], true)
            ]
        );
        unset($this->session->data['success']);
    }

    protected function setCommonTemplates()
    {
        $this->data['header'] = $this->load->controller('common/header');
        $this->data['column_left'] = $this->load->controller('common/column_left');
        $this->data['footer'] = $this->load->controller('common/footer');
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/leanpay')) {
            $this->errors['permission'] = $this->language->get('error_permission');
            return false;
        }
        if (empty($this->request->post['payment_leanpay_api_key'])) {
            $this->errors['api_key_empty'] = $this->language->get('error_api_key_required');
        }
        if (empty($this->request->post['payment_leanpay_secret_word'])) {
            $this->errors['secret_word_empty'] = $this->language->get('error_secret_word_required');
        }

        $minAmount = 50;
        $maxAmount = 5000;
        if (
            isset($this->request->post['payment_leanpay_country']) &&
            strtolower($this->request->post['payment_leanpay_country']) === 'hr'
        ) {
            $minAmount = 375;
            $maxAmount = 22500;
        }
        if ((float)$this->request->post['payment_leanpay_total_min'] < $minAmount) {
            $this->errors['min_amount_invalid'] = sprintf($this->language->get('error_min_amount'), $minAmount);
        }
        if ((float)$this->request->post['payment_leanpay_total_max'] > $maxAmount) {
            $this->errors['max_amount_invalid'] = sprintf($this->language->get('error_max_amount'), $maxAmount);
        }
        if ((float)$this->request->post['payment_leanpay_total_min'] > (float)$this->request->post['payment_leanpay_total_max']) {
            $this->errors['min_greater_max'] = $this->language->get('error_min_greater_than_max');
        }
        if ($this->errors) {
            $this->errors['title'] = $this->language->get('error_title');
        }

        return !$this->errors;
    }
}
