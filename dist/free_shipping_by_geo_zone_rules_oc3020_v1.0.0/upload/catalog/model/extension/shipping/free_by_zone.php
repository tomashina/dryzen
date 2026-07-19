<?php
class ModelExtensionShippingFreeByZone extends Model {
	public function getQuote($address) {
		$this->load->language('extension/shipping/free_by_zone');

		$rules = $this->config->get('shipping_free_by_zone_rules');
		$available = false;

		if (!is_array($rules)) {
			$rules = array();
		}

		foreach ($rules as $rule) {
			if (!is_array($rule) || empty($rule['status'])) {
				continue;
			}

			$geo_zone_id = isset($rule['geo_zone_id']) ? (int)$rule['geo_zone_id'] : 0;
			$matches = false;

			if (!$geo_zone_id) {
				$matches = true;
			} else {
				$country_id = isset($address['country_id']) ? (int)$address['country_id'] : 0;
				$zone_id = isset($address['zone_id']) ? (int)$address['zone_id'] : 0;

				$query = $this->db->query("SELECT zone_to_geo_zone_id FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . $geo_zone_id . "' AND country_id = '" . $country_id . "' AND (zone_id = '" . $zone_id . "' OR zone_id = '0') LIMIT 1");

				$matches = (bool)$query->num_rows;
			}

			if ($matches) {
				$total = isset($rule['total']) ? (float)$rule['total'] : 0;
				$available = ($this->cart->getSubTotal() >= $total);
				break;
			}
		}

		$method_data = array();

		if ($available) {
			$quote_data = array();

			$quote_data['free'] = array(
				'code'         => 'free_by_zone.free',
				'title'        => $this->language->get('text_description'),
				'cost'         => 0.00,
				'tax_class_id' => 0,
				'text'         => $this->currency->format(0.00, $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'free_by_zone',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_free_by_zone_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}
}
