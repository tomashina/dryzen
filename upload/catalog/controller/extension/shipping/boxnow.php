<?php
class ControllerExtensionShippingBoxnow extends Controller {
	public function webhook() {
		$raw = file_get_contents('php://input');
		$payload = json_decode($raw, true);

		if (!is_array($payload)) {
			$this->response->addHeader('HTTP/1.1 400 Bad Request');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode(array('error' => 'Invalid JSON')));
			return;
		}

		$secret = (string)$this->config->get('shipping_boxnow_webhook_secret');

		if ($secret !== '' && (empty($payload['datasignature']) || !$this->isValidSignature($payload, $raw, $secret))) {
			$this->response->addHeader('HTTP/1.1 403 Forbidden');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode(array('error' => 'Invalid signature')));
			return;
		}

		$parcel_id = $this->getPayloadValue($payload, array('data', 'parcelId'));

		if ($parcel_id === '') {
			$parcel_id = $this->getPayloadValue($payload, array('data', 'parcel', 'id'));
		}

		if ($parcel_id === '') {
			$parcel_id = $this->getPayloadValue($payload, array('subject'));
		}

		$status = $this->getPayloadValue($payload, array('data', 'event'));

		if ($status === '') {
			$status = $this->getPayloadValue($payload, array('data', 'parcelState'));
		}

		if ($parcel_id !== '') {
			$table_query = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape(DB_PREFIX . "boxnow_shipment") . "'");

			if ($table_query->num_rows) {
				$this->db->query("UPDATE `" . DB_PREFIX . "boxnow_shipment` SET status = '" . $this->db->escape($status) . "', webhook_payload = '" . $this->db->escape(json_encode($payload)) . "', date_modified = NOW() WHERE parcel_id = '" . $this->db->escape($parcel_id) . "'");
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode(array('ok' => true)));
	}

	private function getPayloadValue($payload, $path) {
		$value = $payload;

		foreach ($path as $key) {
			if (!is_array($value) || !isset($value[$key])) {
				return '';
			}

			$value = $value[$key];
		}

		return is_scalar($value) ? trim((string)$value) : '';
	}

	private function isValidSignature($payload, $raw, $secret) {
		$signature = strtolower(trim((string)$payload['datasignature']));

		$candidates = array(
			$raw
		);

		if (isset($payload['data'])) {
			$candidates[] = json_encode($payload['data']);
			$candidates[] = json_encode($payload['data'], JSON_UNESCAPED_SLASHES);
		}

		foreach ($candidates as $candidate) {
			$digest = hash_hmac('sha256', (string)$candidate, $secret);

			if (hash_equals($digest, $signature)) {
				return true;
			}
		}

		return false;
	}
}
