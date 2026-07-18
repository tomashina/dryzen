<?php
class ControllerExtensionModuleBaselInstaller extends Controller {
	private $error = array();

	public function index() {
		
		if ((float)VERSION >= 3.0) {
			$token_prefix = 'user_token';
			$modules_url = 'marketplace/extension';
		} else {
			$token_prefix = 'token';
			$modules_url = 'extension/extension';
		}
		
		$this->load->language('extension/module/basel_installer');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $token_prefix . '=' . $this->session->data[$token_prefix], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/basel_installer', $token_prefix . '=' . $this->session->data[$token_prefix], true)
		);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/basel_installer', $data));
	}
	
	
	public function install() {
		// ##################################
		// Add Basel Modification
		// ##################################
        
		if ((float)VERSION >= 3.0) {
		$this->load->model('setting/modification');
		$modification_prefix = 'model_setting_modification';
		$xml = file_get_contents(DIR_APPLICATION . '/view/javascript/basel/main_ocmod/basel_theme_3X.ocmod.xml');
		} else {
		$this->load->model('extension/modification');
		$modification_prefix = 'model_extension_modification';
		$xml = file_get_contents(DIR_APPLICATION . '/view/javascript/basel/main_ocmod/basel_theme_23.ocmod.xml');
		}
        
		$data = array(
            'name' => 'Basel Theme',
            'code' => 'basel_theme',
			'id' => 'basel_theme',
			'extension_install_id' => 'basel_theme',
            'author' => 'Openthemer.com',
            'version' => 'v. 1.3.1.0',
            'xml' => $xml,
            'link' => '',
            'status' => '1',
        );
		
		$mod = $this->$modification_prefix->getModificationByCode('basel_theme');
        if (!$mod) {
        $this->$modification_prefix->addModification($data);
		}
		
		
		// ##################################
		// Set User Permissions
		// ##################################
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/basel/basel');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/basel/basel');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/basel/product_tabs');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/basel/product_tabs');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/basel/productgroups');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/basel/productgroups');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/basel/question');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/basel/question');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/basel/subscriber');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/basel/subscriber');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/basel/testimonial');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/basel/testimonial');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/blog/blog');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/blog/blog');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/blog/blog_category');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/blog/blog_category');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/blog/blog_comment');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/blog/blog_comment');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/blog/blog_setting');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/blog/blog_setting');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/basel_carousel');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/basel_carousel');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/basel_categories');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/basel_categories');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/basel_content');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/basel_content');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/basel_instagram');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/basel_instagram');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/basel_layerslider');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/basel_layerslider');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/basel_megamenu');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/basel_megamenu');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/basel_products');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/basel_products');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/blog_latest');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/blog_latest');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/blog_category');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/blog_category');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/onepagecheckout');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/onepagecheckout');
		
		// ##################################
		// Default Blog Settings
		// ##################################
		$this->load->model('setting/setting');
		$blog_data = array(
			'blogsetting_post_date_added' => '1',
			'blogsetting_post_comments_count' => '1',
			'blogsetting_post_page_view' => '1',
			'blogsetting_post_author' => '1',
			'blogsetting_share' => '1',
			'blogsetting_post_thumb' => '1',
			'blogsetting_date_added' => '1',
			'blogsetting_comments_count' => '1',
			'blogsetting_page_view' => '1',
			'blogsetting_author' => '1',
			'blogsetting_rel_thumb' => '0',
			'blogsetting_rel_blog_per_row' => '2',
			'blogsetting_rel_prod_per_row' => '3',
			'blogsetting_rel_thumbs_w' => '570',
			'blogsetting_rel_thumbs_h' => '350',
			'blogsetting_rel_prod_height' => '334',
			'blogsetting_rel_prod_width' => '262',
			'blogsetting_blogs_per_page' => '5',
			'blogsetting_thumbs_w' => '1140',
			'blogsetting_thumbs_h' => '700',
			'blogsetting_date_added' => '1',
			'blogsetting_comments_count' => '1',
			'blogsetting_page_view' => '1',
			'blogsetting_author' => '1',
			'blogsetting_layout' => '1'
		);
		$this->model_setting_setting->editSetting('blogsetting', $blog_data);
		
		
		// ##################################
		// Add database tables
		// ##################################
		$this->session->data['install_error'] = '';
		
		/*
		if($this->is_table_exist(DB_PREFIX . "newsletter")) {
			$this->session->data['install_error'] = '"INSTALLATION FAILED: You already have a database table named ".DB_PREFIX."newsletter. That table is probably used by a previously installed extension. Please delete the table (and possible related tables) before installing the theme"';
		}
		
		if($this->is_table_exist(DB_PREFIX . "testimonial")) {
			$this->session->data['install_error'] = "INSTALLATION FAILED: You already have a database table named ".DB_PREFIX."testimonial. That table is probably used by a previously installed extension. Please delete the table (and possible related tables) before installing the theme";
		}
		
		if($this->is_table_exist(DB_PREFIX . "product_tabs")) {
			$this->session->data['install_error'] = "INSTALLATION FAILED: You already have a database table named ".DB_PREFIX."product_tabs. That table is probably used by a previously installed extension. Please delete the table (and possible related tables) before installing the theme";
		}
		
		if($this->is_table_exist(DB_PREFIX . "question")) {
			$this->session->data['install_error'] = "INSTALLATION FAILED: You already have a database table named ".DB_PREFIX."question. That table is probably used by a previously installed extension. Please delete the table (and possible related tables) before installing the theme";
		}
		
		if($this->is_table_exist(DB_PREFIX . "mega_menu")) {
			$this->session->data['install_error'] = "INSTALLATION FAILED: You already have a database table named ".DB_PREFIX."mega_menu. That table is probably used by a previously installed extension. Please delete the table (and possible related tables) before installing the theme";
		}
		
		if($this->is_table_exist(DB_PREFIX . "blog")) {
			$this->session->data['install_error'] = "INSTALLATION FAILED: You already have a database table named ".DB_PREFIX."blog. That table is probably used by a previously installed extension. Please delete the table (and possible related tables) before installing the theme";
		}
		
		// If everything is ok, run installation
		if (!$this->session->data['install_error']) 
		*/
		$this->createTables();
	
	} 
	
	
	
	public function createTables() {

		$sql  = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."newsletter` ( ";
		$sql .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$sql .= "`email` varchar(96) COLLATE utf8_bin DEFAULT NULL, ";
		$sql .= "PRIMARY KEY (`id`) ";
		$sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
		
		$this->db->query($sql);
		
		$sql  = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."testimonial` ( ";
		$sql .= "`testimonial_id` int(11) NOT NULL AUTO_INCREMENT, ";
		$sql .= "`name` varchar(64) COLLATE utf8_bin NOT NULL, ";
		$sql .= "`image` varchar(255) COLLATE utf8_bin NOT NULL, ";
		$sql .= "`org` varchar(64) COLLATE utf8_bin DEFAULT NULL, "; 
		$sql .= "`status` int(1) NOT NULL DEFAULT '0', ";
		$sql .= "PRIMARY KEY (`testimonial_id`) ";
		$sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		$this->db->query($sql);

		$sql  = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."testimonial_description` ( ";
		$sql .= "`testimonial_id` int(11) NOT NULL, ";
		$sql .= "`language_id` int(11) NOT NULL, ";
		$sql .= "`description` text COLLATE utf8_unicode_ci NOT NULL, ";
		$sql .= "PRIMARY KEY (`testimonial_id`,`language_id`) ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$this->db->query($sql);
		
		$sql  = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."testimonial_to_store` ( ";
		$sql .= "`testimonial_id` int(11) NOT NULL, ";
		$sql .= "`store_id` int(11) NOT NULL, ";
		$sql .= "PRIMARY KEY (`testimonial_id`, `store_id`) ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$this->db->query($sql);
		
		$sql_tab = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_tabs` (
			`tab_id` int(11) NOT NULL AUTO_INCREMENT, 
			`sort_order` int(3) NOT NULL DEFAULT '0', 
			`status` tinyint(1) NOT NULL,
			`global` tinyint(1) NOT NULL, 
			PRIMARY KEY (`tab_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
				
		$this->db->query($sql_tab);
				
		$sql_tab_desc = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_tabs_description` (
			`tab_id` int(11) NOT NULL, 
			`language_id` int(11) NOT NULL, 
			`name` varchar(128) COLLATE utf8_general_ci NOT NULL, 
			`description` text COLLATE utf8_general_ci NOT NULL, 
			PRIMARY KEY (`tab_id`, `language_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
				
		$this->db->query($sql_tab_desc);
			
		$sql_tab_prod = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_tabs_to_product` (
			`tab_id` int(11) NOT NULL,
			`product_id` int(11) NOT NULL,
			PRIMARY KEY (`tab_id`, `product_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
				
		$this->db->query($sql_tab_prod);
		
		$sql_tab_cat = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_tabs_to_category` (
			`tab_id` int(11) NOT NULL,
			`category_id` int(11) NOT NULL,
			PRIMARY KEY (`tab_id`, `category_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
				
		$this->db->query($sql_tab_cat);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "question` ( ";
		$sql .= "`question_id` int(11) NOT NULL AUTO_INCREMENT, ";
		$sql .= "`product_id` int(1) NOT NULL, ";
		$sql .= "`customer_id` int(11) NOT NULL, ";
		$sql .= "`author` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`text` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`answer` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`status` tinyint(1) NOT NULL, ";
		$sql .= "`notify` tinyint(1) NOT NULL DEFAULT '1', ";
		$sql .= "`date_added` datetime DEFAULT '0000-00-00 00:00:00', ";
		$sql .= "`date_modified` datetime DEFAULT '0000-00-00 00:00:00', ";
		$sql .= "PRIMARY KEY (`question_id`) ";
		$sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ; ";
		$this->db->query($sql);
		
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
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog` ( ";
		$sql .= "`blog_id` int(11) NOT NULL AUTO_INCREMENT, ";
		$sql .= "`allow_comment` int(1) NOT NULL DEFAULT '1', ";
		$sql .= "`count_read` int(11) NOT NULL DEFAULT '0', ";
		$sql .= "`sort_order` int(3) NOT NULL, ";
		$sql .= "`status` int(1) NOT NULL DEFAULT '1', ";
		$sql .= "`author` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL, ";
		$sql .= "`date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$sql .= "`image` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL, ";
		$sql .= "PRIMARY KEY (`blog_id`) ";
		$sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ; ";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_category` ( ";
		$sql .= "`blog_category_id` int(11) NOT NULL AUTO_INCREMENT, ";
		$sql .= "`parent_id` int(11) NOT NULL DEFAULT '0', ";
		$sql .= "`sort_order` int(3) NOT NULL DEFAULT '0', ";
		$sql .= "`date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$sql .= "`status` int(1) NOT NULL DEFAULT '1', ";
		$sql .= "PRIMARY KEY (`blog_category_id`) ";
		$sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=49 ; ";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_category_description` ( ";
		$sql .= "`blog_category_id` int(11) NOT NULL, ";
		$sql .= "`language_id` int(11) NOT NULL, ";
		$sql .= "`name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '', ";
		$sql .= "`page_title` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '', ";
		$sql .= "`meta_keywords` varchar(255) COLLATE utf8_bin NOT NULL, ";
		$sql .= "`meta_description` varchar(255) COLLATE utf8_bin NOT NULL, ";
		$sql .= "`description` text COLLATE utf8_bin NOT NULL, ";
		$sql .= "PRIMARY KEY (`blog_category_id`,`language_id`), ";
		$sql .= "KEY `name` (`name`) ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin; ";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_category_to_layout` ( ";
		$sql .= "`blog_category_id` int(11) NOT NULL, ";
		$sql .= "`store_id` int(11) NOT NULL, ";
		$sql .= "`layout_id` int(11) NOT NULL, ";
		$sql .= "PRIMARY KEY (`blog_category_id`,`store_id`) ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin; ";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_category_to_store` ( ";
		$sql .= "`blog_category_id` int(11) NOT NULL, ";
		$sql .= "`store_id` int(11) NOT NULL, ";
		$sql .= "PRIMARY KEY (`blog_category_id`,`store_id`) ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin; ";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_comment` ( ";
		$sql .= "`blog_comment_id` int(11) NOT NULL AUTO_INCREMENT, ";
		$sql .= "`blog_id` int(11) NOT NULL DEFAULT '0', ";
		$sql .= "`name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`comment` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`date_added` datetime DEFAULT '0000-00-00 00:00:00', ";
		$sql .= "`status` int(1) NOT NULL DEFAULT '1', ";
		$sql .= "PRIMARY KEY (`blog_comment_id`) ";
		$sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ; ";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_description` ( ";
		$sql .= "`blog_id` int(11) NOT NULL, ";
		$sql .= "`language_id` int(11) NOT NULL, ";
		$sql .= "`title` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`page_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`meta_keyword` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`meta_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`short_description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, ";
		$sql .= "`tags` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin; ";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_related` ( ";
		$sql .= "`parent_blog_id` int(11) NOT NULL DEFAULT '0', ";
		$sql .= "`child_blog_id` int(11) NOT NULL DEFAULT '0' ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1; ";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_to_category` ( ";
		$sql .= "`blog_id` int(11) NOT NULL, ";
		$sql .= "`blog_category_id` int(11) NOT NULL, ";
		$sql .= "PRIMARY KEY (`blog_id`,`blog_category_id`) ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin; ";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_to_layout` ( ";
		$sql .= "`blog_id` int(11) NOT NULL, ";
		$sql .= "`store_id` int(11) NOT NULL, ";
		$sql .= "`layout_id` int(11) NOT NULL, ";
		$sql .= "PRIMARY KEY (`blog_id`,`store_id`) ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin; ";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_related_products` ( ";
		$sql .= "`blog_id` int(11) NOT NULL, ";
		$sql .= "`related_id` int(11) NOT NULL, ";
		$sql .= "PRIMARY KEY (`blog_id`,`related_id`) ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin; ";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_to_store` ( ";
		$sql .= "`blog_id` int(11) NOT NULL, ";
		$sql .= "`store_id` int(11) NOT NULL ";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1; ";
		$this->db->query($sql);
	
	}
	
	
	public function is_table_exist($table){
        $query = $this->db->query("SHOW TABLES LIKE '".$table."'");
        if( count($query->rows) <= 0 ) {
            return false;
        }
        return true;
    }
	

}