<?php
class ModelLocalisationDeliveryCountry extends Model {
	public function getCountries() {
		$this->load->model('localisation/country');

		$countries = $this->model_localisation_country->getCountries();
		$country_ids = $this->getDeliveryCountryIds();

		// A null scope means that at least one enabled delivery method is
		// available globally (or has no geo-zone setting we can narrow down).
		if ($country_ids === null) {
			return $countries;
		}

		if (!$country_ids) {
			return array();
		}

		$country_lookup = array_fill_keys($country_ids, true);

		return array_values(array_filter($countries, function($country) use ($country_lookup) {
			return isset($country_lookup[(int)$country['country_id']]);
		}));
	}

	public function getAvailableCountryId($countries, $country_id) {
		foreach ($countries as $country) {
			if ((int)$country['country_id'] === (int)$country_id) {
				return (int)$country_id;
			}
		}

		return $countries ? (int)$countries[0]['country_id'] : 0;
	}

	private function getDeliveryCountryIds() {
		$extensions = $this->db->query(
			"SELECT code FROM " . DB_PREFIX . "extension WHERE type = 'shipping'"
		)->rows;

		$has_delivery_method = false;
		$country_ids = array();

		foreach ($extensions as $extension) {
			$code = $extension['code'];

			if (!$this->config->get('shipping_' . $code . '_status')) {
				continue;
			}

			if ($code === 'xshippingpro') {
				$scope = $this->getXShippingProScope();

				if (!$scope['has_method']) {
					continue;
				}

				$has_delivery_method = true;

				if ($scope['country_ids'] === null) {
					return null;
				}

				$country_ids = array_merge($country_ids, $scope['country_ids']);
				continue;
			}

			if ($code === 'weight') {
				$geo_zone_ids = $this->getEnabledWeightGeoZoneIds();

				if (!$geo_zone_ids) {
					continue;
				}
			} else {
				$geo_zone_key = 'shipping_' . $code . '_geo_zone_id';

				// Custom delivery extensions without a geo-zone option cannot be
				// safely narrowed, so keep the complete country list for them.
				if (!$this->config->has($geo_zone_key) || !(int)$this->config->get($geo_zone_key)) {
					return null;
				}

				$geo_zone_ids = array((int)$this->config->get($geo_zone_key));
			}

			$has_delivery_method = true;
			$method_country_ids = $this->getCountryIdsForGeoZones($geo_zone_ids);

			if ($method_country_ids === null) {
				return null;
			}

			$country_ids = array_merge($country_ids, $method_country_ids);
		}

		if (!$has_delivery_method) {
			return array();
		}

		return array_values(array_unique(array_map('intval', $country_ids)));
	}

	private function getEnabledWeightGeoZoneIds() {
		$geo_zone_ids = array();
		$geo_zones = $this->db->query(
			"SELECT geo_zone_id FROM " . DB_PREFIX . "geo_zone"
		)->rows;

		foreach ($geo_zones as $geo_zone) {
			$geo_zone_id = (int)$geo_zone['geo_zone_id'];

			if ($this->config->get('shipping_weight_' . $geo_zone_id . '_status')) {
				$geo_zone_ids[] = $geo_zone_id;
			}
		}

		return $geo_zone_ids;
	}

	private function getXShippingProScope() {
		$scope = array(
			'has_method'  => false,
			'country_ids' => array()
		);

		$table_query = $this->db->query(
			"SHOW TABLES LIKE '" . $this->db->escape(DB_PREFIX . "xshippingpro") . "'"
		);

		if (!$table_query->num_rows) {
			return $scope;
		}

		$methods = $this->db->query(
			"SELECT method_data FROM `" . DB_PREFIX . "xshippingpro` ORDER BY sort_order ASC"
		)->rows;

		foreach ($methods as $method) {
			$data = json_decode($method['method_data'], true);

			if (!is_array($data) || empty($data['status'])) {
				continue;
			}

			if (isset($data['visibility']) && !$data['visibility']) {
				continue;
			}

			if (!$this->isXShippingProMethodAvailableForStore($data)) {
				continue;
			}

			$scope['has_method'] = true;
			$method_country_ids = null;

			if (!empty($data['geo_zone']) && empty($data['geo_zone_all'])) {
				$method_country_ids = $this->getCountryIdsForGeoZones(
					$this->normaliseIds($data['geo_zone'])
				);
			}

			if (!empty($data['country']) && empty($data['country_all'])) {
				$method_country_ids = $this->intersectCountryScopes(
					$method_country_ids,
					$this->normaliseIds($data['country'])
				);
			}

			if (!empty($data['zone']) && empty($data['zone_all'])) {
				$method_country_ids = $this->intersectCountryScopes(
					$method_country_ids,
					$this->getCountryIdsForZones($this->normaliseIds($data['zone']))
				);
			}

			if ($method_country_ids === null) {
				$scope['country_ids'] = null;
				return $scope;
			}

			$scope['country_ids'] = array_merge($scope['country_ids'], $method_country_ids);
		}

		$scope['country_ids'] = array_values(array_unique(array_map('intval', $scope['country_ids'])));

		return $scope;
	}

	private function isXShippingProMethodAvailableForStore($data) {
		if (empty($data['store']) || !empty($data['store_all'])) {
			return true;
		}

		return in_array(
			(int)$this->config->get('config_store_id'),
			$this->normaliseIds($data['store']),
			true
		);
	}

	private function getCountryIdsForGeoZones($geo_zone_ids) {
		$geo_zone_ids = $this->normaliseIds($geo_zone_ids);

		if (!$geo_zone_ids) {
			return array();
		}

		$query = $this->db->query(
			"SELECT DISTINCT country_id FROM " . DB_PREFIX . "zone_to_geo_zone" .
			" WHERE geo_zone_id IN (" . implode(',', $geo_zone_ids) . ")"
		);

		$country_ids = array();

		foreach ($query->rows as $row) {
			$country_id = (int)$row['country_id'];

			if (!$country_id) {
				return null;
			}

			$country_ids[] = $country_id;
		}

		return array_values(array_unique($country_ids));
	}

	private function getCountryIdsForZones($zone_ids) {
		$zone_ids = $this->normaliseIds($zone_ids);

		if (!$zone_ids) {
			return array();
		}

		$query = $this->db->query(
			"SELECT DISTINCT country_id FROM " . DB_PREFIX . "zone" .
			" WHERE zone_id IN (" . implode(',', $zone_ids) . ")"
		);

		return array_values(array_unique(array_map('intval', array_column($query->rows, 'country_id'))));
	}

	private function intersectCountryScopes($left, $right) {
		if ($left === null) {
			return $right;
		}

		if ($right === null) {
			return $left;
		}

		return array_values(array_intersect($left, $right));
	}

	private function normaliseIds($ids) {
		if (!is_array($ids)) {
			$ids = preg_split('/\s*,\s*/', (string)$ids, -1, PREG_SPLIT_NO_EMPTY);
		}

		$ids = array_filter(array_map('intval', $ids));

		return array_values(array_unique($ids));
	}
}
