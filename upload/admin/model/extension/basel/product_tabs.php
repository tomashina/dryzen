<?php
class ModelExtensionBaselProductTabs extends Model {
	public function addTab($data) {
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_tabs SET 
		sort_order = '" . (int)$data['sort_order'] . "', 
		global = '" . (int)$data['global'] . "', 
		status = '" . (int)$data['status'] . "'");
		
		$tab_id = $this->db->getLastId();
			
		foreach ($data['tab_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_tabs_description SET tab_id = '" . (int)$tab_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		
		// To product
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_tabs_to_product SET tab_id = '" . (int)$tab_id . "', product_id = '" . (int)$related_id . "'");
			}
		}
		
		// To categories
		if (isset($data['category_related'])) {
			foreach ($data['category_related'] as $related_category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_tabs_to_category SET tab_id = '" . (int)$tab_id . "', category_id = '" . (int)$related_category_id . "'");
			}
		}
		
		return $tab_id;
	}

	public function editTab($tab_id, $data) {
		
		$this->db->query("UPDATE " . DB_PREFIX . "product_tabs SET sort_order = '" . (int)$data['sort_order'] . "', global = '" . (int)$data['global'] . "', status = '" . (int)$data['status'] . "' WHERE tab_id = '" . (int)$tab_id . "'");
			
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tabs_description WHERE tab_id = '" . (int)$tab_id . "'");

		foreach ($data['tab_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_tabs_description SET tab_id = '" . (int)$tab_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		
		// To products
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tabs_to_product WHERE tab_id = '" . (int)$tab_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_tabs_to_product SET tab_id = '" . (int)$tab_id . "', product_id = '" . (int)$related_id . "'");
			}
		}
		
		// To categories
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tabs_to_category WHERE tab_id = '" . (int)$tab_id . "'");
		if (isset($data['category_related'])) {
			foreach ($data['category_related'] as $related_category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_tabs_to_category SET tab_id = '" . (int)$tab_id . "', category_id = '" . (int)$related_category_id . "'");
			}
		}
		
		
	}
	
	public function deleteTab($tab_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tabs WHERE tab_id = '" . (int)$tab_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tabs_description WHERE tab_id = '" . (int)$tab_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tabs_to_product WHERE tab_id = '" . (int)$tab_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tabs_to_category WHERE tab_id = '" . (int)$tab_id . "'");
	}

	public function getProductTab($tab_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tabs pt LEFT JOIN " . DB_PREFIX . "product_tabs_description ptd ON (pt.tab_id = ptd.tab_id) LEFT JOIN " . DB_PREFIX . "product_tabs_to_product pt2p ON (pt.tab_id = pt2p.tab_id) WHERE pt.tab_id = '" . (int)$tab_id . "' AND ptd.language_id = '" . $this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProductTabs($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product_tabs pt LEFT JOIN " . DB_PREFIX . "product_tabs_description ptd ON (pt.tab_id = ptd.tab_id) WHERE  ptd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ptd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY pt.tab_id";

		$sort_data = array(
			'tab_id',
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
		
	}

	public function getProductTabsDescriptions($tab_id) {
		$product_tabs_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tabs_description WHERE tab_id = '" . (int)$tab_id . "'");

		foreach ($query->rows as $result) {
			$product_tabs_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description']
			);
		}

		return $product_tabs_description_data;
	}

	public function getProductTabsProducts($tab_id) {
        $product_id = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tabs_to_product WHERE tab_id = '" . (int)$tab_id . "'");

        foreach ($query->rows as $result) {
            $product_id[] = $result['product_id'];
        }

        return $product_id;
    }
	
	public function getProductTabsCategories($tab_id) {
        $category_id = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tabs_to_category WHERE tab_id = '" . (int)$tab_id . "'");

        foreach ($query->rows as $result) {
            $category_id[] = $result['category_id'];
        }

        return $category_id;
    }
	
	public function getTotalProductTabs($data = array()) {
		$sql = "SELECT COUNT(DISTINCT pt.tab_id) AS total FROM " . DB_PREFIX . "product_tabs pt LEFT JOIN " . DB_PREFIX . "product_tabs_description ptd ON (pt.tab_id = ptd.tab_id)";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE ptd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
	
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	
}