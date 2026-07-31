<?php
class ControllerEventBoxnow extends Controller {
	// catalog/model/checkout/order/addOrderHistory/after
	public function afterOrderHistory(&$route, &$args, &$output) {
		$order_id = isset($args[0]) ? (int)$args[0] : 0;
		$order_status_id = isset($args[1]) ? (int)$args[1] : 0;

		if (!$this->config->get('shipping_boxnow_status') || $order_id < 1 || !$this->isEligibleStatus($order_status_id)) {
			return;
		}

		try {
			$this->load->library('boxnow/shipment_manager');
			$shipment = $this->shipment_manager->createShipment($order_id);
		} catch (\Throwable $exception) {
			$this->log->write('Automatsko kreiranje BOX NOW pošiljke za narudžbu #' . $order_id . ' nije uspjelo: ' . $exception->getMessage());
			return;
		}

		$tracking_result = $this->shipment_manager->sendTrackingEmail($order_id);

		if (!empty($tracking_result['email_error'])) {
			$this->log->write('Automatski BOX NOW tracking email za narudžbu #' . $order_id . ' nije poslan: ' . $tracking_result['email_error']);
		}

		try {
			$this->shipment_manager->sendLabelEmail($order_id);
		} catch (\Throwable $exception) {
			$this->log->write('Automatsko slanje BOX NOW adresnice za narudžbu #' . $order_id . ' nije uspjelo: ' . $exception->getMessage());
		}

		if (empty($shipment['existing'])) {
			$this->log->write('Automatski kreirana BOX NOW pošiljka za narudžbu #' . $order_id . ' (parcel ' . $shipment['parcel_id'] . ').');
		}
	}

	private function isEligibleStatus($order_status_id) {
		if ($order_status_id < 1) {
			return false;
		}

		$statuses = array_merge(
			(array)$this->config->get('config_processing_status'),
			(array)$this->config->get('config_complete_status')
		);

		// Revolut can use a dedicated "completed" status (for example
		// "Provedena") which is not necessarily listed in OpenCart's global
		// processing/complete status settings.
		$revolut_completed_status_id = (int)$this->config->get('payment_revolut_completed_status_id');
		if ($revolut_completed_status_id > 0) {
			$statuses[] = $revolut_completed_status_id;
		}

		$statuses = array_map('intval', $statuses);

		return in_array($order_status_id, $statuses, true);
	}
}
