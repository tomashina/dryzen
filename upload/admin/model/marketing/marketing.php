<?php
class ModelMarketingMarketing extends Model {
	public function addMarketing($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "marketing SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', code = '" . $this->db->escape($data['code']) . "', date_added = NOW()");

		return $this->db->getLastId();
	}

	public function editMarketing($marketing_id, $data) {
		// Issued links and attribution must keep the original code.
		$this->db->query("UPDATE " . DB_PREFIX . "marketing SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "' WHERE marketing_id = '" . (int)$marketing_id . "'");
	}

	public function deleteMarketing($marketing_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "marketing WHERE marketing_id = '" . (int)$marketing_id . "'");
	}

	public function getMarketing($marketing_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "marketing WHERE marketing_id = '" . (int)$marketing_id . "'");

		return $query->row;
	}

	public function getMarketingByCode($code) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "marketing WHERE code = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	public function isTrackingCodeInUse($code, $exclude_marketing_id = 0) {
		$query = $this->db->query("SELECT marketing_id FROM " . DB_PREFIX . "marketing WHERE code = '" . $this->db->escape($code) . "' AND marketing_id <> '" . (int)$exclude_marketing_id . "' LIMIT 1");

		if ($query->num_rows) {
			return true;
		}

		$query = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "customer_affiliate WHERE tracking = '" . $this->db->escape($code) . "' LIMIT 1");

		return (bool)$query->num_rows;
	}

	public function getMarketings($data = array()) {
		$order_statuses = array_filter(array_unique(array_map('intval', (array)$this->config->get('config_complete_status'))), function($status) { return $status > 0; });
		$status_sql = $order_statuses ? 'o.order_status_id IN (' . implode(',', $order_statuses) . ')' : '1 = 0';

		// OpenCart stores totals in the base currency. currency_value is only for display.
		$sql = "SELECT m.*, (SELECT COUNT(*) FROM `" . DB_PREFIX . "order` o WHERE " . $status_sql . " AND o.marketing_id = m.marketing_id) AS orders, (SELECT COALESCE(SUM(o.total), 0) FROM `" . DB_PREFIX . "order` o WHERE " . $status_sql . " AND o.marketing_id = m.marketing_id) AS revenue FROM " . DB_PREFIX . "marketing m";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "m.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_code'])) {
			$implode[] = "m.code = '" . $this->db->escape($data['filter_code']) . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(m.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'm.name',
			'm.code',
			'm.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY m.name";
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

	public function getTotalMarketings($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "marketing";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_code'])) {
			$implode[] = "code = '" . $this->db->escape($data['filter_code']) . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getMarketingOrders($marketing_id, $start = 0, $limit = 20) {
		$query = $this->db->query("SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, COALESCE(os.name, fallback.name, o.order_status_id) AS order_status, o.total, o.currency_code, o.currency_value, o.date_added FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') LEFT JOIN " . DB_PREFIX . "order_status fallback ON (fallback.order_status_id = o.order_status_id AND fallback.language_id = '1') WHERE o.marketing_id = '" . (int)$marketing_id . "' AND o.order_status_id > 0 ORDER BY o.order_id DESC LIMIT " . max(0, (int)$start) . "," . max(1, (int)$limit));

		return $query->rows;
	}

	public function getTotalMarketingOrders($marketing_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE marketing_id = '" . (int)$marketing_id . "' AND order_status_id > 0");

		return (int)$query->row['total'];
	}

	public function hasMarketingHistory($marketing_id) {
		$marketing = $this->getMarketing($marketing_id);

		if (!empty($marketing['clicks'])) {
			return true;
		}

		$query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE marketing_id = '" . (int)$marketing_id . "' LIMIT 1");

		return (bool)$query->num_rows;
	}
}
