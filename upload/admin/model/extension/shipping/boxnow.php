<?php
class ModelExtensionShippingBoxnow extends Model {
	public function installSchema() {
		$this->getShipmentManager()->installSchema();
	}

	public function getShipmentByOrderId($order_id) {
		return $this->getShipmentManager()->getShipmentByOrderId($order_id);
	}

	public function createShipment($order_id) {
		$shipment = $this->getShipmentManager()->createShipment($order_id);
		$email_result = $this->sendTrackingEmail($order_id);
		$label_email_result = array();

		try {
			$label_email_result = $this->sendLabelEmail($order_id);
		} catch (\Throwable $exception) {
			$label_email_result = array(
				'label_email_sent'  => false,
				'label_email_error' => $exception->getMessage()
			);
		}

		return array_merge($shipment, $email_result, $label_email_result);
	}

	public function sendTrackingEmail($order_id) {
		return $this->getShipmentManager()->sendTrackingEmail($order_id);
	}

	public function sendLabelEmail($order_id, $force = false) {
		return $this->getShipmentManager()->sendLabelEmail($order_id, $force);
	}

	public function getTrackingUrl($parcel_id) {
		return $this->getShipmentManager()->getTrackingUrl($parcel_id);
	}

	public function getStatusLabel($status, $language_code = '') {
		$status = strtolower(trim((string)$status));
		$language = new Language($language_code !== '' ? $language_code : $this->config->get('config_language'));
		$language->load('extension/shipping/boxnow');
		$keys = array(
			'created'             => 'status_created',
			'new'                 => 'status_new',
			'in-depot'            => 'status_in_transit',
			'in-transit'          => 'status_in_transit',
			'final-destination'   => 'status_final_destination',
			'delivered'           => 'status_delivered',
			'returned'            => 'status_returned',
			'expired'             => 'status_expired',
			'expired-return'      => 'status_expired',
			'canceled'            => 'status_canceled',
			'cancelled'           => 'status_canceled',
			'lost'                => 'status_missing',
			'missing'             => 'status_missing',
			'accepted-to-locker'  => 'status_in_progress',
			'accepted-for-return' => 'status_in_progress',
			'wait-for-load'       => 'status_wait_for_load'
		);

		if (isset($keys[$status])) {
			return $language->get($keys[$status]);
		}

		return $status !== '' ? sprintf($language->get('status_unknown'), $status) : $language->get('status_unavailable');
	}

	public function getLabel($order_id) {
		return $this->getShipmentManager()->getLabel($order_id);
	}

	private function getShipmentManager() {
		if (!$this->registry->has('shipment_manager')) {
			$this->load->library('boxnow/shipment_manager');
		}

		return $this->registry->get('shipment_manager');
	}
}
