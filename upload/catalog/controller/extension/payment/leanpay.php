<?php
/**
 * @author Benjamin Cizej, Moja koda d.o.o.
 */

use Mojakoda\Leanpay\Exception\InvalidMd5SignatureException;
use Mojakoda\Leanpay\Exception\ResponseStatusValidationException;
use Mojakoda\Leanpay\Exception\TokenRequestException;
use Mojakoda\Leanpay\Exception\TokenRequestValidationException;
use Mojakoda\Leanpay\OpenCart\Logger;
use Mojakoda\Leanpay\Request\Endpoint;
use Mojakoda\Leanpay\Request\TokenRequest;
use Mojakoda\Leanpay\Request\Validation\Token\TokenRequestValidation;
use Mojakoda\Leanpay\Response\Status;
use Mojakoda\Leanpay\Response\Validation\ResponseStatusValidation;
use Mojakoda\Leanpay\Token;

require_once DIR_SYSTEM . 'library/leanpay/vendor/autoload.php';

/**
 * Class ControllerExtensionPaymentLeanpay
 *
 * @property Loader $load
 * @property Language $language
 * @property Url $url
 * @property Config $config
 * @property ModelAccountOrder $model_account_order
 * @property ModelCheckoutOrder $model_checkout_order
 * @property Session $session
 * @property ModelExtensionPaymentLeanpay $model_extension_payment_leanpay
 * @property ModelExtensionPaymentLeanpayDatabase $model_extension_payment_leanpay_database
 * @property ModelExtensionPaymentLeanpayPayload $model_extension_payment_leanpay_payload
 * @property ModelExtensionPaymentLeanpaySettings $model_extension_payment_leanpay_settings
 * @property Response $response
 * @property \Cart\Currency $currency
 */
class ControllerExtensionPaymentLeanpay extends Controller
{

    /**
     * @var Status
     */
    protected $responseStatus;

    /**
     * @var array
     */
    protected $leanPayTransaction;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('account/order');
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/leanpay');
        $this->load->model('extension/payment/leanpay/database');
        $this->load->model('extension/payment/leanpay/payload');
        $this->load->model('extension/payment/leanpay/settings');
        $this->load->language('extension/payment/leanpay');

        $this->logger = new Logger(
            new Log('leanpay-log-'. date('Y-m-d') . '.log'),
            $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_debug')
        );
    }

    public function index()
    {
        $data = [
            'button_confirm' => $this->language->get('button_confirm'),
            'action' => $this->url->link('extension/payment/leanpay/init', '', true),
        ];

        return $this->load->view('extension/payment/leanpay/form', $data);
    }

    public function init()
    {
        $orderId = $this->session->data['order_id'];
        $this->logger->write(
            '--- Payment init starting - Order ID: ' . $orderId . ' ---',
            Logger::DEBUG
        );
        $products = $this->model_account_order->getOrderProducts($orderId);
        if (!$products) {
            $this->logger->write('No products in cart.');
            $this->session->data['error'] = $this->language->get('no_products');
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }
        $orderInfo = $this->model_checkout_order->getOrder($orderId);
        $baseCurrency = $this->model_extension_payment_leanpay_settings->getBaseCurrency();
        if (!array_key_exists($baseCurrency, $this->model_extension_payment_leanpay->getActiveCurrencies())) {
            $this->logger->write($baseCurrency . ' currency is not enabled in shop.');
            $this->session->data['error'] = sprintf($this->language->get('invalid_currency'), $baseCurrency);
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }
        $orderTotal = $this->currency->format($orderInfo['total'], $baseCurrency, '', false);
        if (
            $orderTotal > $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_total_max') ||
            $orderTotal < $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_total_min')
        ) {
            $this->logger->write('Order total out of allowed range: ' . $orderTotal . ' ' . $baseCurrency);
            $this->session->data['error'] = sprintf(
                $this->language->get('total_out_of_range'),
                $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_total_min'),
                $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_total_max')
            );
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }

        $vendorTransactionId = $this->model_extension_payment_leanpay_database->createPayment(
            $orderId,
            $orderInfo['total'],
            $orderInfo['currency_code'],
            $orderInfo['currency_value']
        );
        $payload = $this->model_extension_payment_leanpay_payload->create($orderInfo, $vendorTransactionId);
        $leanpayToken = new Token(
            new TokenRequestValidation(),
            new TokenRequest(),
            $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_test_mode'),
            $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_country')
        );
        try {
            $token = $leanpayToken->get($payload);
            $this->logger->write(
                $this->model_extension_payment_leanpay_payload->stripSensitiveData($payload),
                Logger::DEBUG
            );
            $this->model_checkout_order->addOrderHistory(
                $this->leanPayTransaction['order_id'],
                $this->model_extension_payment_leanpay_settings->getSetting('leanpay_order_status_id')
            );
            $checkoutUrl = Endpoint::getCheckoutUrl(
                $token,
                $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_test_mode'),
                $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_country')
            );
            $this->response->redirect($checkoutUrl);
        } catch (TokenRequestValidationException $ex) {
            $this->logger->write('Token request validation exception: ' . $ex->getMessage());
            $this->paymentFailureRedirect();
        } catch (TokenRequestException $ex) {
            $this->logger->write('Token request exception with HTTP status code: ' . $ex->getCode());
            $this->paymentFailureRedirect();
        }
    }

    public function status()
    {
        if (!$this->verifyResponseStatus()) {
            die();
        }
        $this->processResponseStatus();
        die();
    }

    protected function loadTemplate($template, array $data = [])
    {
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/leanpay/' . $template)) {
            return $this->load->view(
                $this->config->get('config_template') . '/template/extension/payment/leanpay/' . $template,
                $data
            );
        } else {
            return $this->load->view('default/template/extension/payment/leanpay/' . $template, $data);
        }
    }

    protected function paymentFailureRedirect()
    {
        $this->session->data['error'] = $this->language->get('gateway_redirect_error');
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
    }

    protected function verifyResponseStatus()
    {
        $this->logger->write('----- Response verification starting -----', Logger::DEBUG);
        $body = json_decode(file_get_contents('php://input'), true);
        $this->logger->write($body, Logger::DEBUG);
        if (!is_array($body) || json_last_error()) {
            $this->logger->write('Error parsing JSON: ' . json_last_error_msg());
            return false;
        }
        try {
            $validator = new ResponseStatusValidation();
            $validator->validate($body);
            $this->responseStatus = new Status(
                $body['leanPayTransactionId'],
                $body['vendorTransactionId'],
                $body['amount'],
                $body['status'],
                $body['md5Signature']
            );
            $this->responseStatus->verifySignature($this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_secret_word'));
            $this->leanPayTransaction = $this->model_extension_payment_leanpay_database->loadFromVendorTransactionId(
                $this->responseStatus->getVendorTransactionId()
            );

            if (!$this->leanPayTransaction) {
                $this->logger->write('Transaction not found in database.');
                return false;
            }
            $this->logger->write('----- Response verification end -----', Logger::DEBUG);

            return true;
        } catch (ResponseStatusValidationException $ex) {
            $this->logger->write('Response validation exception: ' . $ex->getMessage());
            return false;
        } catch (InvalidMd5SignatureException $ex) {
            $this->logger->write('Invalid Md5Signature: ' . $ex->getReceivedSignature() . ' Calculated: ' . $ex->getCalculatedSignature());
            return false;
        }
    }

    protected function processResponseStatus()
    {
        $this->logger->write('----- Response process starting -----', Logger::DEBUG);
        $leanpayTransactionId = $this->responseStatus->getLeanPayTransactionId();
        $vendorTransactionId = $this->responseStatus->getVendorTransactionId();
        if ($this->responseStatus->getStatus() == Status::SUCCESS) {
            $orderInfo = $this->model_checkout_order->getOrder($this->leanPayTransaction['order_id']);
            $orderTotal = round($this->currency->format(
                $orderInfo['total'],
                $this->model_extension_payment_leanpay_settings->getBaseCurrency(),
                $this->leanPayTransaction['currency_value'],
                false
            ), 2);
            if ($orderTotal != $this->responseStatus->getAmount()) {
                $historyComment = sprintf(
                    $this->language->get('payment_mismatch_note'),
                    $leanpayTransactionId,
                    $vendorTransactionId,
                    $this->responseStatus->getAmount()
                );
            } else {
                $historyComment = sprintf(
                    $this->language->get('payment_success_note'),
                    $leanpayTransactionId,
                    $vendorTransactionId
                );
            }
            $this->model_checkout_order->addOrderHistory(
                $this->leanPayTransaction['order_id'],
                $this->model_extension_payment_leanpay_settings->getSetting('payment_leanpay_payment_status_id'),
                $historyComment
            );
        } else {
            $this->model_checkout_order->addOrderHistory(
                $this->leanPayTransaction['order_id'],
                10
            );
        }

        $this->model_extension_payment_leanpay_database->updateStatus(
            $vendorTransactionId,
            $this->responseStatus->getStatus()
        );
        $this->logger->write('----- Response process end -----', Logger::DEBUG);
    }
}
