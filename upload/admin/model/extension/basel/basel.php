<?php
class ModelExtensionBaselBasel extends Model {
	
	public function getSettingValue($key, $store_id = 0) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");

        if ($query->num_rows) {
            $result = $query->row;

            if (!empty($result['serialized'])) {
                $result['value'] = json_decode($result['value'], true);
            }

            return $result['value'];
        } else {
            return null;
        }
    }
	
}