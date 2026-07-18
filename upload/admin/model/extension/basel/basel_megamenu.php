<?php
class ModelExtensionBaselBaselMegamenu extends Model {
    private $errors = array();
    public function generate_nestable_list($lang_id) {
		
		if ((float)VERSION >= 3.0) {
		$token_prefix = 'user_token';
		} else {
		$token_prefix = 'token';
		}
		
		$module_id = (isset($this->request->get['module_id']) && $this->request->get['module_id']) ? $this->request->get['module_id'] : 0;
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."mega_menu WHERE parent_id='0' AND module_id='".$module_id."' ORDER BY rang");
        if (!isset($this->request->get['module_id'])) {
            $action = $this->url->link('extension/module/basel_megamenu&', $token_prefix . '=' . $this->session->data[$token_prefix], true);
        } else {
            $action = $this->url->link('extension/module/basel_megamenu&', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true);
        }
        $output = '<div class="cf nestable-lists">';
        $output .= '<div class="dd" id="nestable">';
        $output .= '<ol class="dd-list">';
        foreach ($query->rows as $row) {
            $json = unserialize($row['name']);
            if(isset($json[$lang_id])) {
                $name = $this->skrut($json[$lang_id], 18);
            } else {
                $name = 'Set name';
            }
            if ($row['status']==0)
                $class ='fa fa-check-square';
            else
                $class ='fa fa-square-o';
            $output .= '<li class="dd-item" data-id="'.$row['id'].'">';
			$output .= '<a data-toggle="tooltip" title="Delete" href="'.$action.'&delete='.$row['id'].'" onclick="return confirm(\'Are you sure you want to delete this item?\')" class="fa fa-trash-o fa-fw"></a>';
			$output .= '<a data-toggle="tooltip" title="Duplicate" href="'.$action.'&duplicate='.$row['id'].'"  class="fa fa-clone"></a>';
            $output .= '<a data-toggle="tooltip" title="Change Status" href="'.$action.'&changestatus='.$row['id'].'"  class="'.$class.'"></a>';
			$output .= '<a data-toggle="tooltip" title="Edit" href="'.$action.'&edit='.$row['id'].'" class="fa fa-pencil fa-fw"></a>';
            $output .= '<div class="dd-handle">'.$name.' <span style="color:#aaaaaa">(ID: '.$row['id'].')</span></div>';
            $output .= $this->menu_showNested($row['id'], $lang_id);
            $output .= '</li>';
        }
        $output .= '</ol>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }

    public function menu_showNested($parentID, $lang_id) {
		if ((float)VERSION >= 3.0) {
		$token_prefix = 'user_token';
		} else {
		$token_prefix = 'token';
		}
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."mega_menu WHERE parent_id='".$parentID."' ORDER BY rang");
        if (!isset($this->request->get['module_id'])) {
            $action = $this->url->link('extension/module/basel_megamenu&', $token_prefix . '=' . $this->session->data[$token_prefix], true);
        } else {
            $action = $this->url->link('extension/module/basel_megamenu&', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true);
        }
        $output = false;
        if (count($query->rows) > 0) {
            $output .= "<ol class='dd-list'>\n";
            foreach ($query->rows as $row) {
                $output .= "\n";
                $json = unserialize($row['name']);
                if(isset($json[$lang_id])) {
                    $name = $this->skrut($json[$lang_id], 18);
                } else {
                    $name = 'Set name';
                }
                if ($row['status']==0)
                    $class ='fa fa-check-square';
                else
                    $class ='fa fa-square-o';
                $output .= "<li class='dd-item' data-id='{$row['id']}'>\n";
                $output .= '<a  data-toggle="tooltip" title="Delete" href="'.$action.'&delete='.$row['id'].'" onclick="return confirm(\'Are you sure?\')" class="fa fa-trash-o fa-fw"></a>';
				$output .= '<a data-toggle="tooltip" title="Duplicate" href="'.$action.'&duplicate='.$row['id'].'"  class="fa fa-clone"></a>';
                $output .= '<a data-toggle="tooltip" title="Change Status" href="'.$action.'&changestatus='.$row['id'].'" class="'.$class.'" ></a>';
                
                $output .= "<a data-toggle='tooltip' title='Edit'  href='".$action."&edit=".$row['id']."' class='fa fa-pencil fa-fw'></a><div class='dd-handle'>{$name} <span style=\"color:#bbbbbb\">(ID: {$row['id']})</span></div>\n";
                $output .= $this->menu_showNested($row['id'], $lang_id);
                $output .= "</li>\n";
				
				
				
            }
            $output .= "</ol>\n";
        }
        return $output;
    }

    public  function getSubMenu($parentID){
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."mega_menu WHERE parent_id='".$parentID."' ORDER BY rang");
        return $query->rows;
    }
    public function save_rang($parent_id, $id, $rang) {
        $this->db->query("UPDATE " . DB_PREFIX . "mega_menu SET parent_id = '" . $parent_id . "', rang = '" . $rang . "' WHERE id = '" . $id . "'");
    }



    
	public function addMenu($data) {
		$data['parent_id'] = (isset($data['parent_id']) && $data['parent_id']) ? $data['parent_id'] : 0;
		if(isset($data['module_id']) && $data['module_id'])
			$module_id = $data['module_id'];
		else
			$module_id = (isset($this->request->get['module_id']) && $this->request->get['module_id']) ? $this->request->get['module_id'] : 0;
        //$data['content']['categories']['categories'] = @json_decode(html_entity_decode($data['content']['categories']['categories']), true);
        
		$this->db->query("INSERT INTO " . DB_PREFIX . "mega_menu SET name = '" . $this->db->escape(serialize($data['name'])) . "', description = '" . $this->db->escape(serialize($data['description'])) . "', icon = '" . $data['icon'] . "', parent_id = '". $data['parent_id'] ."', module_id = '" . $module_id . "', item_type = '".$data['item_type']."', show_title = '".$data['show_title']."', disp_mobile_item = '".$data['disp_mobile_item']."', link = '" . $data['link'] . "', new_window = '" . $data['new_window'] . "', status = '" . $data['status'] . "', position = '" . $data['position'] . "', submenu_width = '" . $data['submenu_width'] . "', rang='1000', content_width='" . $data['content_width'] . "', content_type='" . $data['content_type'] . "', content='" . $this->db->escape(json_encode($data['content'],true)) . "'");
        return $this->db->getLastId();
    }
	

    public function saveMenu($data) {
		$module_id = (isset($this->request->get['module_id']) && $this->request->get['module_id']) ? $this->request->get['module_id'] : 0;
        $data['content']['categories']['categories'] = json_decode(html_entity_decode($data['content']['categories']['categories']), true);
        $this->db->query("UPDATE " . DB_PREFIX . "mega_menu SET name = '" . $this->db->escape(serialize($data['name'])) . "', icon_font = '".$data['icon_font']."',class_menu = '".$data['class_menu']."', disp_mobile_item = '".$data['disp_mobile_item']."', item_type = '".$data['item_type']."', show_title = '".$data['show_title']."', description = '" . $this->db->escape(serialize($data['description'])) . "', icon = '" . $data['icon'] ."',module_id = '" . $module_id . "', link = '" . $data['link'] ."', new_window = '" . $data['new_window'] ."', status = '" . $data['status'] ."', position = '" . $data['position'] ."', submenu_width = '" . $data['submenu_width'] ."', content_width = '" . $data['content_width'] ."', content_type = '" . $data['content_type'] ."', content = '" . $this->db->escape(json_encode($data['content'],true)) . "' WHERE id = '" . $data['id'] . "'");
    }
    public function UpdatePosition($data) {
        $this->db->query("UPDATE " . DB_PREFIX . "mega_menu SET  status = '" . $data['status'] ."' WHERE id = '" . $data['id'] . "'");
    }

    public function deleteMenu($id) {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."mega_menu WHERE id='".$id."'");
        if(count($query->rows) > 0) {
            $query = $this->db->query("SELECT * FROM ".DB_PREFIX."mega_menu WHERE parent_id='".$id."'");
            if(count($query->rows) > 0) {
                $this->errors[] = "Menu wasn't removed because it contains submenu.";
            } else {
                $this->db->query("DELETE FROM " . DB_PREFIX . "mega_menu WHERE id = '" . $id . "'");
                return true;
            }
        } else {
            $this->errors[] = 'This menu does not exist!';
        }
        return false;
    }

    public function getMenu($id) {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."mega_menu WHERE id='".$id."'");
        if(count($query->rows) > 0) {
            $data = array();
            foreach ($query->rows as $result) {
                $data = array(
                    'name' => unserialize($result['name']),
                    'description' => unserialize($result['description']),
                    'icon' => $result['icon'],
                    'link' => $result['link'],
                    'icon_font' => $result['icon_font'],
					'class_menu' => $result['class_menu'],
                    'new_window' => $result['new_window'],
                    'status' => $result['status'],
					'disp_mobile_item' => $result['disp_mobile_item'],
					'item_type' => $result['item_type'],
					'show_title' => $result['show_title'],
                    'position' => $result['position'],
                    'submenu_width' => $result['submenu_width'],
                    'content_width' => $result['content_width'],
                    'content_type' => $result['content_type'],
                    'content' => json_decode($result['content'],true)
                );
            }
            return $data;
        }
        return false;
    }


    public function getProducts($array = array()) {
        $output = '';
        if(!empty($array)) {
            foreach($array as $row) {
                $output .= '<li class="dd-item" data-id="'.$row['id'].'" data-name="'.$row['name'].'">';
                $output .= '<a class="fa fa-times"></a>';
                $output .= '<div class="dd-handle">'.$row['name'].'</div>';
                $output .= '</li>';
            }
        }
        return $output;
    }
	
	public function getCategories($array = array()) {
        $output = '';
        if(is_array($array) && !empty($array) && count($array)>0) {
            foreach($array as $row) {
                $output .= '<li class="dd-item" data-id="'.$row['id'].'" data-name="'.$row['name'].'">';
                $output .= '<a class="fa fa-times"></a>';
                $output .= '<div class="dd-handle">'.$row['name'].'</div>';
                if(isset($row['children'])) {
                    if(!empty($row['children'])) {
                        $output .= $this->getCategoriesChildren($row['children']);
                    }
                }
                $output .= '</li>';
            }
        }
        return $output;
    }

    public function getCategoriesChildren($array = array()) {
        $output = '';
        $output .= '<ol class="dd-list">';
        foreach($array as $row) {
            $output .= '<li class="dd-item" data-id="'.$row['id'].'" data-name="'.$row['name'].'">';
            $output .= '<a class="fa fa-times"></a>';
            $output .= '<div class="dd-handle">'.$row['name'].'</div>';
            if(isset($row['children'])) {
                if(!empty($row['children'])) {
                    $output .= $this->getCategoriesChildren($row['children']);
                }
            }
            $output .= '</li>';
        }
        $output .= '</ol>';
        return $output;
    }

    public function displayError() {
        $errors = '';
        foreach ($this->errors as $error) {
            $errors .= '<div>'.$error.'</div>';
        }
        return $errors;
    }

    public function install() {
        if($this->is_table_exist(DB_PREFIX . "mega_menu")) {
            $query = $this->db->query("
				CREATE TABLE IF NOT EXISTS `".DB_PREFIX."mega_menu` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`parent_id` int(11) NOT NULL,
					`rang` int(11) NOT NULL,
					`icon` varchar(255) NOT NULL DEFAULT '',
					`name` text,
					`module_id` int(11),
					`link` text,
					`description` text,
					`new_window` int(11) NOT NULL DEFAULT '0',
					`status` int(11) NOT NULL DEFAULT '0',
					`disp_mobile_item` int(11) NOT NULL DEFAULT '1',
					`item_type` int(11) NOT NULL DEFAULT '1',
					`show_title` int(11) NOT NULL DEFAULT '0',
					`position` text,
					`submenu_width` text,
					`submenu_type` int(11) NOT NULL DEFAULT '0',
					`content_width` int(11) NOT NULL DEFAULT '12',
					`content_type` int(11) NOT NULL DEFAULT '0',
					`content` text,
					`icon_font` varchar(255) NOT NULL DEFAULT '',
					`class_menu` varchar(255),
					PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
			");
			
            
        }
        return false;
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mega_menu`");
    }

    public function skrut($c,$d) {
        if(strlen($c) > $d) {
            $ciag = substr($c,0,$d);
            $ciag .="...";
            return $ciag;
        } else {
            return $c;
        }
    }

    public function is_table_exist($table){
        $query = $this->db->query("SHOW TABLES LIKE '".$table."'");
        if( count($query->rows) <= 0 ) {
            return true;
        }
        return false;
    }
	public function getModuleId() {
		$sql = " SHOW TABLE STATUS LIKE '" . DB_PREFIX . "module'" ;
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function duplicateModule($module_id,$import_module){
		$parent_menu = $this->getMenuByIdModule($import_module);
		if($parent_menu){
			foreach ($parent_menu as $menu) {
				$dane = $this->model_extension_basel_basel_megamenu->getMenu(intval($menu['id']));
				$dane['module_id'] = $module_id;
				$id_parent_add = $this->model_extension_basel_basel_megamenu->addMenu($dane);
				$subcategories = $this->model_extension_basel_basel_megamenu->getSubMenu(intval($menu['id']));
				if($subcategories){
				foreach ($subcategories as $result) {
					$data = array(
							'parent_id' => $id_parent_add,
							'name' => unserialize($result['name']),
							'description' => unserialize($result['description']),
							'icon' => $result['icon'],
							'module_id' => $module_id,
							'link' => $result['link'],
							'new_window' => $result['new_window'],
							'status' => $result['status'],
							'position' => $result['position'],
							'show_title' => $result['show_title'],
							'submenu_width' => $result['submenu_width'],
							'disp_mobile_item' => $result['disp_mobile_item'],
							'content_width' => $result['content_width'],
							'content_type' => $result['content_type'],
							'content' => json_decode($result['content'],true),
							'list_categories' => (isset($result['content']['categories']['categories']) && $result['content']['categories']['categories']) ? $this->model_extension_basel_basel_megamenu->getCategories(json_decode($result['content']['categories']['categories'],true)) : '',
						);
						$this->model_extension_basel_basel_megamenu->addMenu($data);
					}
				}
			}
		}
	}
	public function getMenuByIdModule($module_id){
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."mega_menu WHERE  module_id = '".$module_id."' AND parent_id=0");
		return $query->rows;
	}
	public function duplicateMenu($id_duplicate){
		$dane = $this->model_extension_basel_basel_megamenu->getMenu(intval($id_duplicate));
		$id_parent_add = $this->model_extension_basel_basel_megamenu->addMenu($dane);
		$subcategories = $this->model_extension_basel_basel_megamenu->getSubMenu(intval($id_duplicate));
		if($subcategories){
			foreach ($subcategories as $result) {
				$data = array(
					'parent_id' => $id_parent_add,
					'name' => unserialize($result['name']),
					'description' => unserialize($result['description']),
					'icon' => $result['icon'],
					'link' => $result['link'],
					'new_window' => $result['new_window'],
					'status' => $result['status'],
					'position' => $result['position'],
					'show_title' => $result['show_title'],
					'submenu_width' => $result['submenu_width'],
					'content_width' => $result['content_width'],
					'disp_mobile_item' => $result['disp_mobile_item'],
					'content_type' => $result['content_type'],
					'content' => json_decode($result['content'],true),
					'list_categories' => (isset($result['content']['categories']['categories']) && $result['content']['categories']['categories']) ? $this->model_extension_basel_basel_megamenu->getCategories(json_decode($result['content']['categories']['categories'],true)) : ''
				);
				$this->model_extension_basel_basel_megamenu->addMenu($data);
			}
		}
		return $id_parent_add;
	}
}