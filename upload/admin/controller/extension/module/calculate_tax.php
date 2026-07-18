<?php 
class ControllerExtensionModuleCalculateTax extends Controller {
	public function index() {
		if (isset($this->session->data['shipping_address'])) {
			$this->setShippingAddress($this->session->data['shipping_address']['country_id'], $this->session->data['shipping_address']['zone_id']);
		} elseif ($this->config->get('config_tax_default') == 'shipping') {
			$this->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		}

		if (isset($this->session->data['payment_address'])) {
			$this->setPaymentAddress($this->session->data['payment_address']['country_id'], $this->session->data['payment_address']['zone_id']);
		} elseif ($this->config->get('config_tax_default') == 'payment') {
			$this->setPaymentAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		}

		$this->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
	
		$json = array();
		
		$config_tax = 1;
		
		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = 0;
		}
		
		if (isset($this->request->post['price'])) {
			$price = (float)$this->request->post['price'];
		} else {
			$price = 0;
		}
		
		if (isset($this->request->post['tax_class_id'])) {
			$tax_class_id = (int)$this->request->post['tax_class_id'];
		} else {
			$tax_class_id = 0;
		}
		
		if (isset($this->request->post['opr'])) {
			$opr = $this->request->post['opr'];
		} else {
			$opr = 'plus';
		}		
		
		$this->language->load('product/product');
		$this->load->model('catalog/product');
		
		if($tax_class_id != 0)  {
			if ($price) {
				//$json['new_price']['price_with_tax'] = $this->currency->format($this->calculate($price, $tax_class_id, $config_tax));
				$json['price_with_tax'] = $this->calculate($price, $tax_class_id, $config_tax, $opr);
			} else {
				$json['price_with_tax'] = false;
			}
			$json['success'] = true;
		} else {
			$json['price_with_tax'] = 'Tax not apply';
			$json['success'] = false;
		}		
				
		echo json_encode($json);
		exit;
  	}
	
	private $tax_rates = array();
	
	
	
	public function setShippingAddress($country_id, $zone_id) {
		$tax_query = $this->db->query("SELECT tr1.tax_class_id, tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority FROM " . DB_PREFIX . "tax_rule tr1 LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id) INNER JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id) LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone z2gz ON (tr2.geo_zone_id = z2gz.geo_zone_id) LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id) WHERE tr1.based = 'shipping' AND tr2cg.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND z2gz.country_id = '" . (int)$country_id . "' AND (z2gz.zone_id = '0' OR z2gz.zone_id = '" . (int)$zone_id . "') ORDER BY tr1.priority ASC");

		foreach ($tax_query->rows as $result) {
			$this->tax_rates[$result['tax_class_id']][$result['tax_rate_id']] = array(
				'tax_rate_id' => $result['tax_rate_id'],
				'name'        => $result['name'],
				'rate'        => $result['rate'],
				'type'        => $result['type'],
				'priority'    => $result['priority']
			);
		}
	}

	public function setPaymentAddress($country_id, $zone_id) {
		$tax_query = $this->db->query("SELECT tr1.tax_class_id, tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority FROM " . DB_PREFIX . "tax_rule tr1 LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id) INNER JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id) LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone z2gz ON (tr2.geo_zone_id = z2gz.geo_zone_id) LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id) WHERE tr1.based = 'payment' AND tr2cg.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND z2gz.country_id = '" . (int)$country_id . "' AND (z2gz.zone_id = '0' OR z2gz.zone_id = '" . (int)$zone_id . "') ORDER BY tr1.priority ASC");

		foreach ($tax_query->rows as $result) {
			$this->tax_rates[$result['tax_class_id']][$result['tax_rate_id']] = array(
				'tax_rate_id' => $result['tax_rate_id'],
				'name'        => $result['name'],
				'rate'        => $result['rate'],
				'type'        => $result['type'],
				'priority'    => $result['priority']
			);
		}
	}

	public function setStoreAddress($country_id, $zone_id) {
		$tax_query = $this->db->query("SELECT tr1.tax_class_id, tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority FROM " . DB_PREFIX . "tax_rule tr1 LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id) INNER JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id) LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone z2gz ON (tr2.geo_zone_id = z2gz.geo_zone_id) LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id) WHERE tr1.based = 'store' AND tr2cg.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND z2gz.country_id = '" . (int)$country_id . "' AND (z2gz.zone_id = '0' OR z2gz.zone_id = '" . (int)$zone_id . "') ORDER BY tr1.priority ASC");

		foreach ($tax_query->rows as $result) {
			$this->tax_rates[$result['tax_class_id']][$result['tax_rate_id']] = array(
				'tax_rate_id' => $result['tax_rate_id'],
				'name'        => $result['name'],
				'rate'        => $result['rate'],
				'type'        => $result['type'],
				'priority'    => $result['priority']
			);
		}
	}
	
	public function calculate($value, $tax_class_id, $calculate = true, $opr) {
		if ($tax_class_id && $calculate) {
			$amount = 0;
			
			$tax_rates = $this->getRates($value, $tax_class_id);
			$taxPercent = 0; 
			$taxFixed = 0;
			$taxprice = 0;
			//print_r($tax_rates);
			foreach ($tax_rates as $tax_rate) {
				/*if ($calculate != 'P' && $calculate != 'F') {
					$amount += $tax_rate['amount'];
				} elseif ($tax_rate['type'] == $calculate) {
					$amount += $tax_rate['amount'];
				}*/
				
				if($opr == 'plus'){	
					if($tax_rate['type'] == 'F'){
						$taxprice += $tax_rate['rate'];
					}elseif($tax_rate['type'] == 'P'){
						$taxprice += ($value / 100 * $tax_rate['rate']);
					}
				}elseif($opr == 'minus'){
					if($tax_rate['type'] == 'F'){
						$taxFixed += $tax_rate['rate'];
					}elseif($tax_rate['type'] == 'P'){
						$taxPercent +=  $tax_rate['rate']/100;
					}					
				}	
			}
			
			if($opr == 'plus'){		
				return round(($value + $taxprice), 4);
			}elseif($opr == 'minus'){
				return round(($value - $taxFixed)/(1+$taxPercent), 4);
			}	
		} else {
			return round($value, 4);
		}
	}
	
	public function getRates($value, $tax_class_id) {
		$tax_rate_data = array();

		if (isset($this->tax_rates[$tax_class_id])) {
			foreach ($this->tax_rates[$tax_class_id] as $tax_rate) {
				if (isset($tax_rate_data[$tax_rate['tax_rate_id']])) {
					$amount = $tax_rate_data[$tax_rate['tax_rate_id']]['amount'];
				} else {
					$amount = 0;
				}

				if ($tax_rate['type'] == 'F') {
					$amount += $tax_rate['rate'];
				} elseif ($tax_rate['type'] == 'P') {
					$amount += ($value / 100 * $tax_rate['rate']);
				}

				$tax_rate_data[$tax_rate['tax_rate_id']] = array(
					'tax_rate_id' => $tax_rate['tax_rate_id'],
					'name'        => $tax_rate['name'],
					'rate'        => $tax_rate['rate'],
					'type'        => $tax_rate['type'],
					'amount'      => $amount
				);
			}
		}

		return $tax_rate_data;
	}
}