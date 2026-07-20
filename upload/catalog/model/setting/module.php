<?php
class ModelSettingModule extends Model {
	public function getModule($module_id) {
		$cache_key = 'module.catalog.' . (int)$module_id;
		$module_data = $this->cache->get($cache_key);

		if ($module_data !== false) {
			return $module_data;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module WHERE module_id = '" . (int)$module_id . "'");
		
		if ($query->row) {
			$module_data = json_decode($query->row['setting'], true);
		} else {
			$module_data = array();
		}

		$this->cache->set($cache_key, $module_data);

		return $module_data;
	}		
}
