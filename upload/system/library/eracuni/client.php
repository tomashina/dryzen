<?php
namespace Eracuni;

/**
 * Minimal e-Racuni WebServices API client.
 *
 * Credentials are intentionally read only from upload/env.php. They are never
 * stored in OpenCart settings or written to a log file.
 */
class Client {
	private $registry;
	private $credentials = array();
	private $timeout = 45;

	public function __construct($registry) {
		$this->registry = $registry;
		$this->loadEnvironment();
		$this->credentials = $this->resolveCredentials();

		if ($this->registry && $this->registry->has('config')) {
			$timeout = (int)$this->registry->get('config')->get('module_eracuni_sync_timeout');

			if ($timeout >= 5 && $timeout <= 120) {
				$this->timeout = $timeout;
			}
		}
	}

	public function isConfigured() {
		return $this->missingConfiguration() === array();
	}

	public function getConnectionInfo() {
		$url = isset($this->credentials['url']) ? $this->credentials['url'] : '';

		return array(
			'configured' => $this->isConfigured(),
			'missing'    => $this->missingConfiguration(),
			'host'       => $url ? (string)parse_url($url, PHP_URL_HOST) : '',
			'endpoint'   => $url ? $this->resolveEndpoint($url) : ''
		);
	}

	/**
	 * @throws \RuntimeException when configuration, transport or API validation fails.
	 */
	public function call($method, array $parameters = array()) {
		$missing = $this->missingConfiguration();

		if ($missing) {
			throw new \RuntimeException('Nedostaje e-Racuni API konfiguracija u upload/env.php: ' . implode(', ', $missing) . '.');
		}

		$payload = array(
			'username'   => $this->credentials['username'],
			'secretKey'  => $this->credentials['secret_key'],
			'token'      => $this->credentials['token'],
			'method'     => (string)$method,
			'parameters' => $parameters
		);

		$json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);

		if ($json === false) {
			throw new \RuntimeException('Nije moguce pripremiti e-Racuni API zahtjev.');
		}

		$response = $this->request($this->resolveEndpoint($this->credentials['url']), $json);
		$decoded = json_decode($response['body'], true);

		if (!is_array($decoded)) {
			throw new \RuntimeException('e-Racuni API nije vratio ispravan JSON odgovor (HTTP ' . $response['status'] . ').');
		}

		$envelope = isset($decoded['response']) && is_array($decoded['response']) ? $decoded['response'] : $decoded;
		$status = isset($envelope['status']) ? strtolower((string)$envelope['status']) : '';

		if ($status === 'error' || $status === 'failed' || $response['status'] >= 400) {
			$description = '';

			foreach (array('description', 'message', 'error') as $key) {
				if (isset($envelope[$key]) && is_scalar($envelope[$key])) {
					$description = trim((string)$envelope[$key]);
					break;
				}
			}

			if ($description === '') {
				$description = 'HTTP ' . $response['status'];
			}

			throw new \RuntimeException($this->friendlyApiError($description));
		}

		if (array_key_exists('result', $envelope)) {
			return $envelope['result'];
		}

		if (array_key_exists('data', $envelope)) {
			return $envelope['data'];
		}

		return $envelope;
	}

	private function request($url, $json) {
		if (function_exists('curl_init')) {
			$handle = curl_init($url);
			curl_setopt_array($handle, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST           => true,
				CURLOPT_POSTFIELDS     => $json,
				CURLOPT_HTTPHEADER     => array(
					'Accept: application/json',
					'Content-Type: application/json; charset=utf-8',
					'User-Agent: DryZen-OpenCart-eRacuni-Sync/1.0'
				),
				CURLOPT_CONNECTTIMEOUT => min(10, $this->timeout),
				CURLOPT_TIMEOUT        => $this->timeout,
				CURLOPT_SSL_VERIFYPEER => true,
				CURLOPT_SSL_VERIFYHOST => 2
			));

			$body = curl_exec($handle);
			$error_number = curl_errno($handle);
			$error = curl_error($handle);
			$status = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
			curl_close($handle);

			if ($error_number) {
				throw new \RuntimeException('Ne mogu se spojiti na e-Racuni API: ' . $error);
			}

			return array('status' => $status, 'body' => (string)$body);
		}

		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json; charset=utf-8',
			'User-Agent: DryZen-OpenCart-eRacuni-Sync/1.0'
		);
		$context = stream_context_create(array('http' => array(
			'method'        => 'POST',
			'header'        => implode("\r\n", $headers),
			'content'       => $json,
			'timeout'       => $this->timeout,
			'ignore_errors' => true
		)));
		$body = @file_get_contents($url, false, $context);
		$status = 0;

		if (isset($http_response_header[0]) && preg_match('/\s([0-9]{3})\s/', $http_response_header[0], $match)) {
			$status = (int)$match[1];
		}

		if ($body === false) {
			throw new \RuntimeException('Ne mogu se spojiti na e-Racuni API.');
		}

		return array('status' => $status, 'body' => (string)$body);
	}

	private function resolveEndpoint($url) {
		$url = rtrim((string)$url, '/');
		$suffix = '/WebServices/API';

		if (substr($url, -strlen($suffix)) !== $suffix) {
			$url .= $suffix;
		}

		return $url;
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

	private function resolveCredentials() {
		$source = array();

		if (defined('OC_ENV') && is_array(OC_ENV)) {
			if (isset(OC_ENV['eracuni']) && is_array(OC_ENV['eracuni'])) {
				$source = OC_ENV['eracuni'];
			} elseif (isset(OC_ENV['import']['api']) && is_array(OC_ENV['import']['api'])) {
				// Backwards compatible with the existing AGmedia order integration.
				$source = OC_ENV['import']['api'];
			}
		}

		return array(
			'username'   => trim(isset($source['username']) ? (string)$source['username'] : ''),
			'secret_key' => trim(isset($source['secret_key']) ? (string)$source['secret_key'] : (isset($source['password']) ? (string)$source['password'] : '')),
			'token'      => trim(isset($source['token']) ? (string)$source['token'] : ''),
			'url'        => trim(isset($source['url']) ? (string)$source['url'] : '')
		);
	}

	private function missingConfiguration() {
		$missing = array();

		foreach (array('username', 'secret_key', 'token', 'url') as $key) {
			if (empty($this->credentials[$key])) {
				$missing[] = $key;
			}
		}

		if (!empty($this->credentials['url']) && !filter_var($this->credentials['url'], FILTER_VALIDATE_URL)) {
			$missing[] = 'ispravan url';
		}

		return $missing;
	}

	private function friendlyApiError($description) {
		if (stripos($description, 'Invalid web services token') !== false) {
			return 'e-Racuni API token nije vazeci. Kopirajte aktualni token iz Postavke > Postavke tvrtke > API Web services i azurirajte upload/env.php.';
		}

		if (stripos($description, 'korisnicko ime ili zaporka') !== false || stripos($description, 'korisničko ime ili zaporka') !== false || stripos($description, 'username or password') !== false) {
			return 'e-Racuni API korisnicko ime ili tajni kljuc nisu valjani. Polje password u upload/env.php mora sadrzavati Secret key API korisnika, a ne lozinku za obicnu prijavu.';
		}

		if (stripos($description, 'password') !== false && stripos($description, 'invalid') !== false) {
			return 'e-Racuni API tajni kljuc nije vazeci. Azurirajte password/secret_key u upload/env.php.';
		}

		return $description;
	}
}
