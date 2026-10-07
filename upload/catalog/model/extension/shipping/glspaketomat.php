<?php
class ModelExtensionShippingGlspaketomat extends Model {
	public function getQuote($address) {
		$this->load->language('extension/shipping/glspaketomat');

		$geo_zone_id = (int)$this->config->get('shipping_glspaketomat_geo_zone_id');
		$status = true;

		if ($geo_zone_id) {
			$country_id = isset($address['country_id']) ? (int)$address['country_id'] : 0;
			$zone_id = isset($address['zone_id']) ? (int)$address['zone_id'] : 0;

			$query = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . $geo_zone_id . "' AND country_id = '" . $country_id . "' AND (zone_id = '" . $zone_id . "' OR zone_id = '0') LIMIT 1");
			$status = (bool)$query->num_rows;
		}

		if (!$status) {
			return array();
		}

		$package_size = strtoupper(trim((string)$this->config->get('shipping_glspaketomat_default_size')));

		if (!in_array($package_size, array('XS', 'S', 'M', 'L', 'XL'), true)) {
			$package_size = 'S';
		}

		$default_costs = array(
			'XS' => 4.50,
			'S'  => 5.50,
			'M'  => 6.50,
			'L'  => 8.50,
			'XL' => 10.50
		);
		$configured_cost = $this->config->get('shipping_glspaketomat_cost_' . strtolower($package_size));
		$cost = ($configured_cost !== null && $configured_cost !== '' && is_numeric($configured_cost)) ? max(0, (float)$configured_cost) : $default_costs[$package_size];
		$configured_free_total = $this->config->get('shipping_glspaketomat_free_total');
		$free_total = ($configured_free_total !== null && $configured_free_total !== '' && is_numeric($configured_free_total)) ? max(0, (float)$configured_free_total) : 50.00;

		if ($free_total > 0 && $this->cart->getSubTotal() >= $free_total) {
			$cost = 0.00;
		}

		$configured_tax_class_id = $this->config->get('shipping_glspaketomat_tax_class_id');
		$tax_class_id = ($configured_tax_class_id !== null && $configured_tax_class_id !== '') ? max(0, (int)$configured_tax_class_id) : 0;
		$currency = !empty($this->session->data['currency']) ? $this->session->data['currency'] : $this->config->get('config_currency');

		$quote_data = array();

		$quote_data['glspaketomat'] = array(
			'code'         => 'glspaketomat.glspaketomat',
			'title'        => sprintf($this->language->get('text_description'), $package_size),
			'cost'         => $cost,
			'tax_class_id' => $tax_class_id,
			'text'         => $cost > 0 ? $this->currency->format($this->tax->calculate($cost, $tax_class_id, $this->config->get('config_tax')), $currency) : $this->language->get('text_free')
		);

		$configured_sort_order = $this->config->get('shipping_glspaketomat_sort_order');

		return array(
			'code'       => 'glspaketomat',
			'title'      => $this->language->get('text_title'),
			'quote'      => $quote_data,
			'sort_order' => ($configured_sort_order !== null && $configured_sort_order !== '') ? max(0, (int)$configured_sort_order) : 2,
			'error'      => false
		);
	}
}
