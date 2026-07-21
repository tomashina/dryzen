<?php
class ModelExtensionShippingBoxnow extends Model {
	private $shipment_table_exists;

	public function getQuote($address) {
		$this->load->language('extension/shipping/boxnow');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_boxnow_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_boxnow_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$cost = (float)$this->config->get('shipping_boxnow_cost');
			$free_total = (float)$this->config->get('shipping_boxnow_free_total');

			if ($free_total > 0 && $this->cart->getSubTotal() >= $free_total) {
				$cost = 0;
			}

			$quote_data = array();

			$quote_data['boxnow'] = array(
				'code'         => 'boxnow.boxnow',
				'title'        => $this->language->get('text_description'),
				'cost'         => $cost,
				'tax_class_id' => $this->config->get('shipping_boxnow_tax_class_id'),
				'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('shipping_boxnow_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'boxnow',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_boxnow_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}

	public function getShipmentByOrderId($order_id) {
		if (!$this->shipmentTableExists()) {
			return array();
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "boxnow_shipment` WHERE order_id = '" . (int)$order_id . "' ORDER BY boxnow_shipment_id DESC LIMIT 1");

		return $query->num_rows ? $query->row : array();
	}

	public function getTrackingUrl($parcel_id) {
		$parcel_id = trim((string)$parcel_id);

		if ($parcel_id === '') {
			return '';
		}

		$base_url = trim((string)$this->config->get('shipping_boxnow_tracking_url'));

		if ($base_url === '') {
			$base_url = 'https://track.boxnow.hr/?track={parcel}';
		}

		if (strpos($base_url, '{parcel}') !== false) {
			return str_replace('{parcel}', rawurlencode($parcel_id), $base_url);
		}

		if (strpos($base_url, 'track.boxnow.hr') !== false) {
			$base_url = preg_replace('#/track/?$#', '', rtrim($base_url, '/'));

			if (preg_match('/([?&]track=)([^&]*)/', $base_url)) {
				return preg_replace('/([?&]track=)([^&]*)/', '$1' . rawurlencode($parcel_id), $base_url);
			}

			return $base_url . (strpos($base_url, '?') !== false ? '&' : '?') . 'track=' . rawurlencode($parcel_id);
		}

		return rtrim($base_url, '/') . '/' . rawurlencode($parcel_id);
	}

	public function getStatusLabel($status) {
		$status = strtolower(trim((string)$status));
		$this->load->language('extension/shipping/boxnow', 'boxnow_tracking');
		$language = $this->language->get('boxnow_tracking');
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

	private function shipmentTableExists() {
		if ($this->shipment_table_exists !== null) {
			return $this->shipment_table_exists;
		}

		$query = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape(DB_PREFIX . "boxnow_shipment") . "'");
		$this->shipment_table_exists = (bool)$query->num_rows;

		return $this->shipment_table_exists;
	}
}
