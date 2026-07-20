<?php
class ModelSettingExtension extends Model {
	public function getExtensions($type) {
		$cache_key = 'extension.catalog.' . md5($type);
		$extension_data = $this->cache->get($cache_key);

		if ($extension_data !== false) {
			return $extension_data;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");
		$extension_data = $query->rows;

		$this->cache->set($cache_key, $extension_data);

		return $extension_data;
	}
}
