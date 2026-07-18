<?php
class ModelExtensionMazaCatalogAttributeGroup extends Model {
	public function getAttributeGroup($attribute_group_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_group a LEFT JOIN " . DB_PREFIX . "attribute_group_description ad ON (a.attribute_group_id = ad.attribute_group_id) WHERE ad.language_id = '" . (INT)$this->config->get('config_language_id') . "' AND a.attribute_group_id = '" . (int)$attribute_group_id . "'");

		return $query->row;
	}
}