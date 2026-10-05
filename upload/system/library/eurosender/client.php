<?php
namespace Eurosender;

class ApiException extends \RuntimeException {
	private $http_status;
	private $response_body;
	private $transport_error;
	private $ambiguous_response;

	public function __construct($message, $http_status = 0, $response_body = '', $transport_error = false, $ambiguous_response = false) {
		parent::__construct((string)$message, (int)$http_status);
		$this->http_status = (int)$http_status;
		$this->response_body = (string)$response_body;
		$this->transport_error = (bool)$transport_error;
		$this->ambiguous_response = (bool)$ambiguous_response;
	}

	public function getHttpStatus() {
		return $this->http_status;
	}

	public function getResponseBody() {
		return $this->response_body;
	}

	public function isTransportError() {
		return $this->transport_error;
	}

	public function isAmbiguousResponse() {
		return $this->ambiguous_response;
	}
}

class Client {
	const API_SPEC_VERSION = '2025-03.1';
	const SANDBOX_API_URL = 'https://sandbox-api.eurosender.com';
	const PRODUCTION_API_URL = 'https://api.eurosender.com';

	private $registry;
	private $config;
	private $api_key = '';
	private $environment = 'sandbox';
	private $base_url = self::SANDBOX_API_URL;
	private $connect_timeout = 10;
	private $timeout = 30;

	public function __construct($registry) {
		$this->registry = $registry;
		$this->config = $registry->has('config') ? $registry->get('config') : null;

		$this->loadEnvironment();
		$this->api_key = $this->resolveApiKey();
		$this->environment = $this->resolveEnvironment();
		$this->base_url = $this->resolveBaseUrl();
		$this->connect_timeout = $this->positiveIntegerConfig('shipping_eurosender_connect_timeout', 10);
		$this->timeout = $this->positiveIntegerConfig('shipping_eurosender_timeout', 30);

		// OpenCart registers nested libraries under their basename ("client"),
		// which is too generic. Keep a provider-specific registry key as well.
		$registry->set('eurosender_client', $this);
	}

	public function isConfigured() {
		return $this->api_key !== '' && $this->isHttpsUrl($this->base_url);
	}

	public function getConnectionInfo() {
		return array(
			'configured'        => $this->isConfigured(),
			'api_key_configured'=> $this->api_key !== '',
			'environment'       => $this->environment,
			'base_url'          => $this->base_url,
			'api_spec_version'  => self::API_SPEC_VERSION
		);
	}

	public function quote(array $payload) {
		return $this->request('POST', '/v1/quotes', $payload);
	}

	public function validateOrder(array $payload) {
		return $this->request('POST', '/v1/orders/validate_creation', $payload);
	}

	public function createOrder(array $payload) {
		return $this->request('POST', '/v1/orders', $payload);
	}

	public function getOrder($order_code) {
		return $this->request('GET', '/v1/orders/' . rawurlencode($this->requireOrderCode($order_code)));
	}

	public function getLabels($order_code) {
		return $this->request(
			'GET',
			'/v1/orders/' . rawurlencode($this->requireOrderCode($order_code)) . '/labels',
			null,
			'application/pdf',
			false
		);
	}

	public function getTracking($order_code) {
		return $this->request('GET', '/v1/orders/' . rawurlencode($this->requireOrderCode($order_code)) . '/tracking');
	}

	protected function request($method, $path, $payload = null, $accept = 'application/json', $expect_json = true) {
		if (!$this->isConfigured()) {
			throw new ApiException('Eurosender API ključ nije konfiguriran u upload/env.php.', 0, '', false, false);
		}

		if (!function_exists('curl_init')) {
			throw new ApiException('Eurosender API zahtijeva PHP cURL ekstenziju.', 0, '', true, false);
		}

		$method = strtoupper((string)$method);
		$url = rtrim($this->base_url, '/') . '/' . ltrim((string)$path, '/');
		$headers = array(
			'Accept: ' . $accept,
			'x-api-key: ' . $this->api_key,
			'User-Agent: DryZen-OpenCart-Eurosender/1.0'
		);
		$body = null;

		if ($payload !== null) {
			$body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

			if ($body === false) {
				throw new ApiException('Eurosender API: nije moguće pripremiti JSON zahtjev.');
			}

			$headers[] = 'Content-Type: application/json; charset=utf-8';
		}

		$handle = curl_init($url);
		curl_setopt_array($handle, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST  => $method,
			CURLOPT_HTTPHEADER     => $headers,
			// The production host's libcurl negotiates HTTP/2 with the
			// Eurosender edge, which can terminate otherwise valid requests
			// with PROTOCOL_ERROR. HTTP/1.1 is fully supported by the API and
			// avoids that transport-level incompatibility.
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CONNECTTIMEOUT => $this->connect_timeout,
			CURLOPT_TIMEOUT        => $this->timeout,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2
		));

		if ($body !== null) {
			curl_setopt($handle, CURLOPT_POSTFIELDS, $body);
		}

		$response_body = curl_exec($handle);
		$error_number = curl_errno($handle);
		$error_message = curl_error($handle);
		$http_status = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
		$content_type = (string)curl_getinfo($handle, CURLINFO_CONTENT_TYPE);
		curl_close($handle);

		if ($error_number) {
			throw new ApiException(
				'Eurosender API mrežna pogreška: ' . $error_message,
				0,
				'',
				true,
				true
			);
		}

		$response_body = (string)$response_body;

		if ($http_status < 200 || $http_status >= 300) {
			throw new ApiException(
				$this->buildHttpErrorMessage($http_status, $response_body),
				$http_status,
				$response_body,
				false,
				$http_status >= 500
			);
		}

		if (!$expect_json) {
			if (strncmp($response_body, '%PDF-', 5) === 0 || stripos($content_type, 'application/pdf') !== false) {
				return $response_body;
			}

			$decoded = json_decode($response_body, true);

			if (is_array($decoded)) {
				return $decoded;
			}

			// The public schema does not document label response bytes. Preserve
			// the exact response for the shipment manager's conservative detector.
			return $response_body;
		}

		if ($response_body === '') {
			return array();
		}

		$decoded = json_decode($response_body, true);

		if (!is_array($decoded)) {
			throw new ApiException(
				'Eurosender API vratio je neispravan JSON odgovor.',
				$http_status,
				$response_body,
				false,
				true
			);
		}

		return $decoded;
	}

	private function loadEnvironment() {
		if (defined('OC_ENV')) {
			return;
		}

		$candidates = array();

		if (defined('DIR_APPLICATION')) {
			$candidates[] = dirname(rtrim(DIR_APPLICATION, '/\\')) . '/env.php';
		}

		if (defined('DIR_SYSTEM')) {
			$candidates[] = dirname(rtrim(DIR_SYSTEM, '/\\')) . '/env.php';
		}

		foreach (array_unique($candidates) as $file) {
			if (is_file($file)) {
				require_once($file);
				break;
			}
		}
	}

	private function resolveApiKey() {
		if (!defined('OC_ENV') || !is_array(OC_ENV)) {
			return '';
		}

		if (!isset(OC_ENV['eurosender']) || !is_array(OC_ENV['eurosender'])) {
			return '';
		}

		return trim(isset(OC_ENV['eurosender']['api_key']) ? (string)OC_ENV['eurosender']['api_key'] : '');
	}

	private function resolveEnvironment() {
		$environment = strtolower(trim((string)$this->configValue('shipping_eurosender_environment', 'sandbox')));

		return $environment === 'production' ? 'production' : 'sandbox';
	}

	private function resolveBaseUrl() {
		return $this->environment === 'production' ? self::PRODUCTION_API_URL : self::SANDBOX_API_URL;
	}

	private function configValue($key, $default = '') {
		if (!$this->config || !method_exists($this->config, 'get')) {
			return $default;
		}

		$value = $this->config->get($key);

		return ($value !== null && $value !== '') ? $value : $default;
	}

	private function positiveIntegerConfig($key, $default) {
		$value = (int)$this->configValue($key, $default);

		return $value > 0 ? $value : (int)$default;
	}

	private function requireOrderCode($order_code) {
		$order_code = trim((string)$order_code);

		if ($order_code === '') {
			throw new \InvalidArgumentException('Eurosender order code nedostaje.');
		}

		return $order_code;
	}

	private function buildHttpErrorMessage($http_status, $response_body) {
		$message = '';
		$decoded = json_decode((string)$response_body, true);

		if (is_array($decoded)) {
			foreach (array('message', 'description', 'error', 'title', 'detail') as $key) {
				if (isset($decoded[$key]) && is_scalar($decoded[$key])) {
					$message = trim((string)$decoded[$key]);
					break;
				}
			}

			if ($message === '' && !empty($decoded['errors'])) {
				$encoded_errors = json_encode($decoded['errors'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				$message = $encoded_errors !== false ? $encoded_errors : '';
			}
		}

		if ($message === '') {
			$message = trim(strip_tags((string)$response_body));
		}

		if (strlen($message) > 1000) {
			$message = substr($message, 0, 1000) . '...';
		}

		return 'Eurosender API HTTP ' . (int)$http_status . ($message !== '' ? ': ' . $message : '');
	}

	private function isHttpsUrl($url) {
		$url = trim((string)$url);

		return filter_var($url, FILTER_VALIDATE_URL) !== false
			&& strtolower((string)parse_url($url, PHP_URL_SCHEME)) === 'https';
	}
}
