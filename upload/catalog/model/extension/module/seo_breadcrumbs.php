<?php

/*
This file is subject to the terms and conditions defined in the "EULA.txt"
file, which is part of this source code package and is also available on the
page: https://raw.githubusercontent.com/ocmod-space/license/main/EULA.txt.
*/

class ModelExtensionModuleSeoBreadcrumbs extends Model {
	public function getCategoryName($category_id) {
		$store_id = (int)$this->config->get('config_store_id');
		$language_id = (int)$this->config->get('config_language_id');
		$cache_id = 'category.seo_breadcrumbs.name.' . (int)$category_id . 's' . $store_id . 'l' . $language_id;
		$name = $this->cache->get($cache_id);

		if (!$name) {
			$q = $this->db->query('SELECT DISTINCT name FROM ' . DB_PREFIX . 'category c LEFT JOIN ' . DB_PREFIX . 'category_description cd ON (c.category_id = cd.category_id) LEFT JOIN ' . DB_PREFIX . 'category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = "' . (int)$category_id . '" AND cd.language_id = "' . $language_id . '" AND c2s.store_id = "' . $store_id . '" AND c.status = "1"');

			if ($q->rows && $q->row['name']) {
				$name = $q->row['name'];

				$this->cache->set($cache_id, $name);
			}
		}

		return $name;
	}

	public function getManufacturerName($manufacturer_id) {
		$store_id = (int)$this->config->get('config_store_id');
		$cache_id = 'manufacturer.seo_breadcrumbs.name.' . (int)$manufacturer_id . 's' . $store_id;
		$name = $this->cache->get($cache_id);

		if (!$name) {
			$q = $this->db->query('SELECT name FROM ' . DB_PREFIX . 'manufacturer m LEFT JOIN ' . DB_PREFIX . 'manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.manufacturer_id = "' . (int)$manufacturer_id . '" AND m2s.store_id = "' . $store_id . '"');

			if ($q->rows && $q->row['name']) {
				$name = $q->row['name'];

				$this->cache->set($cache_id, $name);
			}
		}

		return $name;
	}

	// Returns category path IDs: ["1", "2"]
	public function getCategoryPathIds($category_id) {
		$cache_id = 'category.seo_breadcrumbs.path_ids.' . (int)$category_id;
		$path_ids = $this->cache->get($cache_id);

		if (!$path_ids) {
			$path_ids = array();
			$q = $this->db->query('SELECT path_id FROM ' . DB_PREFIX . "category_path WHERE category_id = '" . (int)$category_id . "' ORDER BY level ASC");

			if ($q->rows) {
				foreach ($q->rows as $category) {
					$path_ids[] = $category['path_id'];
				}

				$this->cache->set($cache_id, $path_ids);
			}
		}

		return $path_ids;
	}

	// Returns array of path IDs of all categories belong to product: [["2", "3"], ["4", "5", "7"]]
	public function getProductPathIds($product_id) {
		$cache_id = 'product.seo_breadcrumbs.path_ids.' . (int)$product_id;
		$path_ids = $this->cache->get($cache_id);

		if (!$path_ids) {
			$path_ids = array();

			// Get an array with category IDs the product belongs to
			$q = $this->db->query('SELECT category_id FROM ' . DB_PREFIX . 'product_to_category WHERE product_id = "' . (int)$product_id . '"');

			foreach ($q->rows as $category) {
				$path_ids[] = $this->getCategoryPathIds($category['category_id']);
			}

			if ($path_ids) {
				asort($path_ids);

				$this->cache->set($cache_id, $path_ids);
			}
		}

		return $path_ids;
	}
}
