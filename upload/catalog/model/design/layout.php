<?php
class ModelDesignLayout extends Model {
	public function getLayout($route) {
		$cache_key = 'layout.route.' . (int)$this->config->get('config_store_id') . '.' . md5($route);
		$layout_id = $this->cache->get($cache_key);

		if ($layout_id !== false) {
			return (int)$layout_id;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE '" . $this->db->escape($route) . "' LIKE route AND store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY route DESC LIMIT 1");

		if ($query->num_rows) {
			$layout_id = (int)$query->row['layout_id'];
		} else {
			$layout_id = 0;
		}

		$this->cache->set($cache_key, $layout_id);

		return $layout_id;
	}
	
	public function getLayoutModules($layout_id, $position) {
		$cache_key = 'layout.modules.' . (int)$layout_id . '.' . md5($position);
		$module_data = $this->cache->get($cache_key);

		if ($module_data !== false) {
			return $module_data;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_module WHERE layout_id = '" . (int)$layout_id . "' AND position = '" . $this->db->escape($position) . "' ORDER BY sort_order");
		$module_data = $query->rows;

		$this->cache->set($cache_key, $module_data);

		return $module_data;
	}
}
