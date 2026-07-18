<?php
class ModelExtensionModuleBasePlugin extends Model {
	public function install(){		
		$this->load->model('user/user_group');

		if (version_compare(VERSION,'2.2.0.0','<=')) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/hbapps');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/hbapps');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/hbseo');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/hbseo');
		}else{
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/extension/hbapps');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/extension/hbapps');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/extension/hbseo');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/extension/hbseo');
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "modification` WHERE `code` = 'huntbee_assign_apps_permission'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "modification` WHERE `code` = 'huntbee_base_plugin'");
		
		if ((version_compare(VERSION,'2.0.0.0','>=' )) and (version_compare(VERSION,'2.2.0.0','<=' ))) {
			$ocmod_filename = 'ocmod_base_plugin_22xx.txt';
			$ocmod_name = 'Base Plugin from HuntBee [2000 - 2200]';
		}else if ((version_compare(VERSION,'2.3.0.0','>=' )) and (version_compare(VERSION,'3.0.0.0','<' ))) {
			$ocmod_filename = 'ocmod_base_plugin_23xx.txt';
			$ocmod_name = 'Base Plugin from HuntBee [23xx]';
		}else if (version_compare(VERSION,'3.0.0.0','>=' )) {
			$ocmod_filename = 'ocmod_base_plugin_3xxx.txt';
			$ocmod_name = 'Base Plugin from HuntBee [3xxx]';
		}

		$ocmod_version 	= EXTENSION_VERSION;
		$ocmod_code 	= 'huntbee_base_plugin';	
		$ocmod_author 	= 'HuntBee OpenCart Services';
		$ocmod_link 	= 'https://www.huntbee.com/';
		
		$file = DIR_APPLICATION . 'view/template/extension/module/ocmod/'.$ocmod_filename;
		if (file_exists($file)) {
			$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			$ocmod_xml = str_replace('{huntbee_version}',$ocmod_version,$ocmod_xml);
			$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
		}

		$this->db->query("INSERT INTO `".DB_PREFIX."setting` (`code`, `key`, `value`, `serialized`) VALUES ('module_base_plugin','module_base_plugin_status', '1','0')");
	}
	
	public function uninstall(){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "modification` WHERE `code` = 'huntbee_base_plugin'");
		$this->db->query("DELETE FROM `".DB_PREFIX."setting` WHERE `key` = 'module_base_plugin_status'");
	}

}
?>