<?php
class ModelExtensionShippingEurosender extends Model {
	public function installSchema() {
		$this->getShipmentManager()->installSchema();
	}

	public function getShipmentByOrderId($order_id) {
		return $this->getShipmentManager()->getShipmentByOrderId($order_id);
	}

	public function getShipment($order_id) {
		return $this->getShipmentByOrderId($order_id);
	}

	public function previewShipment($order_id) {
		return $this->getShipmentManager()->previewShipment($order_id);
	}

	public function createShipment($order_id, $confirmation_token) {
		return $this->getShipmentManager()->createShipment($order_id, $confirmation_token);
	}

	public function refreshTracking($order_id) {
		return $this->getShipmentManager()->refreshTracking($order_id);
	}

	public function getLabel($order_id) {
		return $this->getShipmentManager()->getLabel($order_id);
	}

	public function getStatusLabel($status) {
		$language = $this->load->language('extension/shipping/eurosender');
		$normalized = strtolower(trim((string)$status));
		$map = array(
			'validating'                     => 'status_validating',
			'creating'                       => 'status_creating',
			'created'                        => 'status_created',
			'order received'                 => 'status_order_received',
			'deferred payment'               => 'status_deferred_payment',
			'awaiting payment'               => 'status_awaiting_payment',
			'awaiting customs documentation' => 'status_awaiting_customs',
			'awaiting pickup'                => 'status_awaiting_pickup',
			'pickup confirmed'               => 'status_pickup_confirmed',
			'collected'                      => 'status_collected',
			'confirmed'                      => 'status_confirmed',
			'pending'                        => 'status_pending',
			'in_transit'                     => 'status_in_transit',
			'in-transit'                     => 'status_in_transit',
			'in transit'                     => 'status_in_transit',
			'inforeceived'                   => 'status_info_received',
			'intransit'                      => 'status_in_transit',
			'out_for_delivery'               => 'status_out_for_delivery',
			'outfordelivery'                 => 'status_out_for_delivery',
			'attemptfail'                    => 'status_attempt_failed',
			'availableforpickup'             => 'status_available_for_pickup',
			'delivered'                      => 'status_delivered',
			'cancelled'                      => 'status_cancelled',
			'canceled'                       => 'status_cancelled',
			'returned'                       => 'status_returned',
			'exception'                      => 'status_exception',
			'expired'                        => 'status_expired',
			'failed'                         => 'status_failed',
			'error'                          => 'status_error',
			'unknown'                        => 'status_unknown'
		);

		if (isset($map[$normalized]) && isset($language[$map[$normalized]])) {
			return $language[$map[$normalized]];
		}

		return $normalized !== '' ? trim((string)$status) : (isset($language['status_unavailable']) ? $language['status_unavailable'] : 'N/A');
	}

	public function getTrackingUrl($shipment_or_tracking_url) {
		return $this->getShipmentManager()->getTrackingUrl($shipment_or_tracking_url);
	}

	public function isConfigured() {
		return $this->getShipmentManager()->isConfigured();
	}

	public function getConnectionInfo() {
		return $this->getShipmentManager()->getConnectionInfo();
	}

	private function getShipmentManager() {
		if (!$this->registry->has('eurosender_shipment_manager')) {
			require_once(DIR_SYSTEM . 'library/eurosender/shipment_manager.php');
			$this->registry->set('eurosender_shipment_manager', new \Eurosender\ShipmentManager($this->registry));
		}

		return $this->registry->get('eurosender_shipment_manager');
	}
}
