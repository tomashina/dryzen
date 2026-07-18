<?php
class ModelExtensionBaselDemoStores11Installer extends Model {

	public function demoSetup() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->model('extension/basel/demo_stores_base');

		//--------------- GLOBAL ITEMS ----------------
		
		// Add Sample Categories
		$this->model_extension_basel_demo_stores_base->addSampleCategories();
		
		// Add Sample Products
		$this->model_extension_basel_demo_stores_base->addSampleProducts();
		
		// Add Sample Blog Posts
		$this->model_extension_basel_demo_stores_base->addSampleBlogs();
		
		// Add Sample Product Groups
		$this->model_extension_basel_demo_stores_base->addSampleProductGroups();
		
		// Add Sample Testimonials
		$this->model_extension_basel_demo_stores_base->addSampleTestimonials();
		
		// Add Sample Menu
		$this->model_extension_basel_demo_stores_base->addSampleMenuItems();
		
		// Add Sample Banners
		$this->model_extension_basel_demo_stores_base->addSampleBanners();
		
		// DEMO STORE SPECIFIC ITEMS ----------
		$this->alterExtensionTable();
		
		$this->addSampleModules();
		
		$this->addSampleLayouts();
		
		$this->addSampleLayoutModules();
		
		$this->addSampleLayoutRoutes();
		
		$this->addSampleSettings();
		
		$this->cache->delete('megamenu');
		$this->cache->delete('language');
		$this->cache->delete('basel_instagram');
		$this->cache->delete('basel_styles_cache');
		$this->cache->delete('basel_fonts_cache');
		$this->cache->delete('basel_mandatory_css');

		$this->session->data['success'] = 'Demo store was successfully imported.';
		
		$this->response->redirect($this->url->link('extension/basel/basel', $token_prefix . '=' . $this->session->data[$token_prefix], true));
		
	}
	
	/*--------------------------------------------
	------- ADD ENTRIES TO EXTENSION TABLE -------
	--------------------------------------------*/
	public function alterExtensionTable() {
	$query = $this->db->query("DELETE FROM `".DB_PREFIX."extension` WHERE type='module' AND code != 'digitalElephantFilter' AND code != 'basel_installer'");
	
$oc_extension = array(
  array('extension_id' => '572','type' => 'module','code' => 'basel_instagram'),
  array('extension_id' => '571','type' => 'module','code' => 'category'),
  array('extension_id' => '570','type' => 'module','code' => 'account'),
  array('extension_id' => '569','type' => 'module','code' => 'basel_content'),
  array('extension_id' => '568','type' => 'module','code' => 'basel_layerslider'),
  array('extension_id' => '567','type' => 'module','code' => 'basel_megamenu'),
  array('extension_id' => '566','type' => 'module','code' => 'basel_products')
);
	
	foreach ($oc_extension as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "extension` SET "
				. "type = '" . $result['type'] . "', "
                . "code = '" . $result['code'] . "'");
    	}

	}
	
	
	
	/*--------------------------------------------
	----------------- ADD LAYOUTS ----------------
	--------------------------------------------*/
	public function addSampleLayouts() {
	$this->db->query("TRUNCATE TABLE `".DB_PREFIX."layout`");
	
$oc_layout = array(
  array('layout_id' => '1','name' => 'Home'),
  array('layout_id' => '2','name' => 'Product'),
  array('layout_id' => '3','name' => 'Category'),
  array('layout_id' => '4','name' => 'Default'),
  array('layout_id' => '14','name' => 'Blog'),
  array('layout_id' => '6','name' => 'Account'),
  array('layout_id' => '7','name' => 'Checkout'),
  array('layout_id' => '8','name' => 'Contact'),
  array('layout_id' => '9','name' => 'Sitemap'),
  array('layout_id' => '10','name' => 'Affiliate'),
  array('layout_id' => '11','name' => 'Information'),
  array('layout_id' => '12','name' => 'Compare'),
  array('layout_id' => '15','name' => 'Login Pages')
);
	
	foreach ($oc_layout as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET "
				. "layout_id = '" . $result['layout_id'] . "', "
                . "name = '" . $result['name'] . "'");
    	}
	}
	
	/*--------------------------------------------
	-------------- ADD LAYOUT MODULES ------------
	--------------------------------------------*/
	public function addSampleLayoutModules() {
	$this->db->query("TRUNCATE TABLE `".DB_PREFIX."layout_module`");
	
$oc_layout_module = array(
  array('layout_module_id' => '204','layout_id' => '14','code' => 'category','position' => 'column_right','sort_order' => '1'),
  array('layout_module_id' => '69','layout_id' => '10','code' => 'affiliate','position' => 'column_right','sort_order' => '1'),
  array('layout_module_id' => '68','layout_id' => '6','code' => 'account','position' => 'column_right','sort_order' => '1'),
  array('layout_module_id' => '201','layout_id' => '3','code' => 'category','position' => 'column_left','sort_order' => '1'),
  array('layout_module_id' => '301','layout_id' => '1','code' => 'basel_content.51','position' => 'top','sort_order' => '4'),
  array('layout_module_id' => '202','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
  array('layout_module_id' => '205','layout_id' => '14','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
  array('layout_module_id' => '203','layout_id' => '3','code' => 'basel_instagram.48','position' => 'column_left','sort_order' => '3'),
  array('layout_module_id' => '206','layout_id' => '14','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3'),
  array('layout_module_id' => '302','layout_id' => '1','code' => 'basel_content.40','position' => 'top','sort_order' => '5'),
  array('layout_module_id' => '300','layout_id' => '1','code' => 'basel_products.41','position' => 'top','sort_order' => '3'),
  array('layout_module_id' => '299','layout_id' => '1','code' => 'basel_content.43','position' => 'top','sort_order' => '2'),
  array('layout_module_id' => '298','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'top','sort_order' => '1')
);
	
	foreach ($oc_layout_module as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_module` SET "
				. "layout_module_id = '" . $result['layout_module_id'] . "', "
				. "layout_id = '" . $result['layout_id'] . "', "
				. "code = '" . $result['code'] . "', "
				. "position = '" . $result['position'] . "', "
                . "sort_order = '" . $result['sort_order'] . "'");
    	}
	}
	
	/*--------------------------------------------
	-------------- ADD LAYOUT ROUTES -------------
	--------------------------------------------*/
	public function addSampleLayoutRoutes() {
	$this->db->query("TRUNCATE TABLE `".DB_PREFIX."layout_route`");
	
$oc_layout_route = array(
  array('layout_route_id' => '38','layout_id' => '6','store_id' => '0','route' => 'account/%'),
  array('layout_route_id' => '17','layout_id' => '10','store_id' => '0','route' => 'affiliate/%'),
  array('layout_route_id' => '92','layout_id' => '3','store_id' => '0','route' => 'product/category'),
  array('layout_route_id' => '98','layout_id' => '1','store_id' => '0','route' => 'common/home'),
  array('layout_route_id' => '95','layout_id' => '2','store_id' => '0','route' => 'product/product'),
  array('layout_route_id' => '71','layout_id' => '11','store_id' => '0','route' => 'information/information'),
  array('layout_route_id' => '23','layout_id' => '7','store_id' => '0','route' => 'checkout/%'),
  array('layout_route_id' => '31','layout_id' => '8','store_id' => '0','route' => 'information/contact'),
  array('layout_route_id' => '32','layout_id' => '9','store_id' => '0','route' => 'information/sitemap'),
  array('layout_route_id' => '34','layout_id' => '4','store_id' => '0','route' => ''),
  array('layout_route_id' => '81','layout_id' => '14','store_id' => '0','route' => 'extension/blog/%'),
  array('layout_route_id' => '52','layout_id' => '12','store_id' => '0','route' => 'product/compare'),
  array('layout_route_id' => '91','layout_id' => '3','store_id' => '0','route' => 'product/manufacturer'),
  array('layout_route_id' => '99','layout_id' => '3','store_id' => '0','route' => 'product/manufacturer/info'),
  array('layout_route_id' => '90','layout_id' => '3','store_id' => '0','route' => 'product/search'),
  array('layout_route_id' => '93','layout_id' => '3','store_id' => '0','route' => 'product/special'),
  array('layout_route_id' => '96','layout_id' => '15','store_id' => '0','route' => 'account/login'),
  array('layout_route_id' => '97','layout_id' => '15','store_id' => '0','route' => 'affiliate/login')
);
	
	foreach ($oc_layout_route as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET "
				. "layout_route_id = '" . $result['layout_route_id'] . "', "
				. "layout_id = '" . $result['layout_id'] . "', "
				. "store_id = '" . $result['store_id'] . "', "
                . "route = '" . $result['route'] . "'");
    	}
	}
	
	/*--------------------------------------------
	---------------- ADD MODULES -----------------
	--------------------------------------------*/
	public function addSampleModules() {
	
// `basel`.`oc_module`
$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"1140","height":"506","minheight":"200","fullwidth":"0","margin_bottom":"45px","slide_transition":"fade","speed":"10","loop":"0","nav_buttons":"0","nav_bullets":"1","nav_timer_bar":"0","sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"0","bg_color":"","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo11\\/slideshow\\/slide-bg-1.jpg","groups":[{"type":"text","description":{"1":"BRILLIANTS COLLECTION SPRINT","3":"BRILLIANTS COLLECTION SPRINT","2":"BRILLIANTS COLLECTION SPRINT"},"left":{"1":"63","3":"63","2":"63"},"top":{"1":"148","3":"148","2":"148"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"15px","color":"#cccccc","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"back(200)","easingout":"easeOutQuart","durationout":"1000","sort_order":"1","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"Jewellery Diamonds","3":"Jewellery Diamonds","2":"Jewellery Diamonds"},"left":{"1":"43","3":"43","2":"43"},"top":{"1":"195","3":"195","2":"195"},"font":"\'Lora\', serif","fontweight":"400","fontsize":"44px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(short)","easingin":"easeOutQuint","durationin":"1200","transitionout":"left(short)","easingout":"easeOutQuint","durationout":"1200","sort_order":"2","p_index":"0","start":"900","end":"10000"},{"type":"text","description":{"1":"Suspendisse ridiculus parturient ac a dui cursus interdum &lt;br&gt;\\r\\ndignissim netus habitant ultrices et mattis urna sem a euismod &lt;br&gt;\\r\\na adipiscing faucibus a.","3":"Suspendisse ridiculus parturient ac a dui cursus interdum &lt;br&gt;\\r\\ndignissim netus habitant ultrices et mattis urna sem a euismod &lt;br&gt;\\r\\na adipiscing faucibus a.","2":"Suspendisse ridiculus parturient ac a dui cursus interdum &lt;br&gt;\\r\\ndignissim netus habitant ultrices et mattis urna sem a euismod &lt;br&gt;\\r\\na adipiscing faucibus a."},"left":{"1":"43","3":"43","2":"43"},"top":{"1":"266","3":"266","2":"266"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"14px","color":"#cccccc","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"1000","sort_order":"3","p_index":"0","start":"1200","end":"10000"},{"type":"button","description":{"1":"Shop Now","3":"Shop Now","2":"Shop Now"},"left":{"1":"59","3":"59","2":"59"},"top":{"1":"346","3":"346","2":"346"},"button_class":"btn btn-light-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuart","durationout":"1000","sort_order":"4","p_index":"0","start":"1500","end":"10000"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"0","bg_color":"","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo11\\/slideshow\\/slide-bg-2.jpg","groups":[{"type":"text","description":{"1":"BRILLIANTS COLLECTION SPRINT","3":"BRILLIANTS COLLECTION SPRINT","2":"BRILLIANTS COLLECTION SPRINT"},"left":{"1":"65","3":"65","2":"65"},"top":{"1":"175","3":"175","2":"175"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"15px","color":"#ffffff","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"back(200)","easingout":"easeOutQuart","durationout":"1000","sort_order":"1","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"Jewellery Diamonds","3":"Jewellery Diamonds","2":"Jewellery Diamonds"},"left":{"1":"47","3":"47","2":"47"},"top":{"1":"220","3":"220","2":"220"},"font":"\'Lora\', serif","fontweight":"400","fontsize":"44px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(short)","easingin":"easeOutQuint","durationin":"1200","transitionout":"left(short)","easingout":"easeOutQuint","durationout":"1200","sort_order":"2","p_index":"0","start":"900","end":"10000"},{"type":"text","description":{"1":"Only for you","3":"Only for you","2":"Only for you"},"left":{"1":"43","3":"43","2":"43"},"top":{"1":"277","3":"277","2":"277"},"font":"\'Lora\', serif","fontweight":"400","fontsize":"44px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"1000","sort_order":"3","p_index":"0","start":"1200","end":"10000"},{"type":"button","description":{"1":"Shop Now","3":"Shop Now","2":"Shop Now"},"left":{"1":"65","3":"65","2":"65"},"top":{"1":"346","3":"346","2":"346"},"button_class":"btn btn-light-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuart","durationout":"1000","sort_order":"4","p_index":"0","start":"1500","end":"10000"}]}}}'),
  array('module_id' => '40','name' => 'Newsletter Subscribe Block','code' => 'basel_content','setting' => '{"save":"stay","name":"Newsletter Subscribe Block","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"25","mr":"0","mb":"-60","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"1","bg_par":"0","bg_pos":"center bottom","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"catalog\\/basel-demo\\/demo11\\/signup-bg.jpg","c_setting":{"fw":"0","block_css":"1","css":"padding-bottom:70px;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-middle text-center","data1":{"1":"&lt;div class=&quot;wide-signup dark&quot;&gt;\\r\\n&lt;p class=&quot;spread light&quot;&gt;\\r\\nJoin our Newsletter\\r\\n&lt;\\/p&gt;\\r\\n&lt;h3 style=&quot;font-size:26px;margin-bottom:5px;&quot;&gt;\\r\\nBe always updated\\r\\n&lt;\\/h3&gt;\\r\\n&lt;span class=&quot;x-separator soft&quot;&gt;&lt;i class=&quot;icon-line-cross&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;wide-signup dark&quot;&gt;\\r\\n&lt;p class=&quot;spread light&quot;&gt;\\r\\nJoin our Newsletter\\r\\n&lt;\\/p&gt;\\r\\n&lt;h3 style=&quot;font-size:26px;margin-bottom:5px;&quot;&gt;\\r\\nBe always updated\\r\\n&lt;\\/h3&gt;\\r\\n&lt;span class=&quot;x-separator soft&quot;&gt;&lt;i class=&quot;icon-line-cross&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n[unsubscribe_btn]\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;wide-signup dark&quot;&gt;\\r\\n&lt;p class=&quot;spread light&quot;&gt;\\r\\nJoin our Newsletter\\r\\n&lt;\\/p&gt;\\r\\n&lt;h3 style=&quot;font-size:26px;margin-bottom:5px;&quot;&gt;\\r\\nBe always updated\\r\\n&lt;\\/h3&gt;\\r\\n&lt;span class=&quot;x-separator soft&quot;&gt;&lt;i class=&quot;icon-line-cross&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n[unsubscribe_btn]\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Featured','code' => 'basel_products','setting' => '{"name":"Home Page Featured","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"MADE THE HARD WAY","3":"MADE THE HARD WAY","2":"MADE THE HARD WAY"},"title_m":{"1":"FEATURED PRODUCTS","3":"FEATURED PRODUCTS","2":"FEATURED PRODUCTS"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"8","image_width":"262","image_height":"334","columns":"4","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"1","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"1","margin":"20px"}'),
  array('module_id' => '43','name' => '3x - Banner + Info','code' => 'basel_content','setting' => '{"save":"stay","name":"3x - Banner + Info","status":"1","b_setting":{"title":"1","title_pl":{"1":"WELCOME TO OUR SHOP","3":"WELCOME TO OUR SHOP","2":"WELCOME TO OUR SHOP"},"title_m":{"1":"SIMPLE THING TO ENJOY LIFE","3":"SIMPLE THING TO ENJOY LIFE","2":"SIMPLE THING TO ENJOY LIFE"},"title_b":{"1":"Ante vitae potenti aenean sem lectus ligula adipiscing ullamcorper natoque metus mi cum nam mus dui at.","3":"Ante vitae potenti aenean sem lectus ligula adipiscing ullamcorper natoque metus mi cum nam mus dui at.","2":"Ante vitae potenti aenean sem lectus ligula adipiscing ullamcorper natoque metus mi cum nam mus dui at."},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"1","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"border-bottom:1px solid #ededed;","nm":"0","eh":"1"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-12","w_md":"col-sm-12","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo11\\/banner\\/1.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h4 style=&quot;font-size:22px;margin-top:25px;margin-bottom:8px;&quot;&gt;\\r\\nBeautiful Earrings\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#666666;line-height:1.7;margin-bottom:17px;&quot;&gt;\\r\\nAnte vitae potenti aenean&lt;br&gt;\\r\\nligula adipiscing.\\r\\n&lt;\\/p&gt;\\r\\n&lt;span class=&quot;hover-slidein-top&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;View Store&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;","3":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo11\\/banner\\/1.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h4 style=&quot;font-size:22px;margin-top:25px;margin-bottom:8px;&quot;&gt;\\r\\nBeautiful Earrings\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#666666;line-height:1.7;margin-bottom:17px;&quot;&gt;\\r\\nAnte vitae potenti aenean&lt;br&gt;\\r\\nligula adipiscing.\\r\\n&lt;\\/p&gt;\\r\\n&lt;span class=&quot;hover-slidein-top&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;View Store&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;","2":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo11\\/banner\\/1.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h4 style=&quot;font-size:22px;margin-top:25px;margin-bottom:8px;&quot;&gt;\\r\\nBeautiful Earrings\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#666666;line-height:1.7;margin-bottom:17px;&quot;&gt;\\r\\nAnte vitae potenti aenean&lt;br&gt;\\r\\nligula adipiscing.\\r\\n&lt;\\/p&gt;\\r\\n&lt;span class=&quot;hover-slidein-top&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;View Store&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;"}},"2":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo11\\/banner\\/2.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h4 style=&quot;font-size:22px;margin-top:25px;margin-bottom:8px;&quot;&gt;\\r\\nBeautiful Earrings\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#666666;line-height:1.7;margin-bottom:17px;&quot;&gt;\\r\\nAnte vitae potenti aenean&lt;br&gt;\\r\\nligula adipiscing.\\r\\n&lt;\\/p&gt;\\r\\n&lt;span class=&quot;hover-slidein-top&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;View Store&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;","3":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo11\\/banner\\/2.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h4 style=&quot;font-size:22px;margin-top:25px;margin-bottom:8px;&quot;&gt;\\r\\nBeautiful Earrings\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#666666;line-height:1.7;margin-bottom:17px;&quot;&gt;\\r\\nAnte vitae potenti aenean&lt;br&gt;\\r\\nligula adipiscing.\\r\\n&lt;\\/p&gt;\\r\\n&lt;span class=&quot;hover-slidein-top&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;View Store&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;","2":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo11\\/banner\\/2.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h4 style=&quot;font-size:22px;margin-top:25px;margin-bottom:8px;&quot;&gt;\\r\\nBeautiful Earrings\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#666666;line-height:1.7;margin-bottom:17px;&quot;&gt;\\r\\nAnte vitae potenti aenean&lt;br&gt;\\r\\nligula adipiscing.\\r\\n&lt;\\/p&gt;\\r\\n&lt;span class=&quot;hover-slidein-top&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;View Store&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;"}},"3":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo11\\/banner\\/3.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h4 style=&quot;font-size:22px;margin-top:25px;margin-bottom:8px;&quot;&gt;\\r\\nBeautiful Earrings\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#666666;line-height:1.7;margin-bottom:17px;&quot;&gt;\\r\\nAnte vitae potenti aenean&lt;br&gt;\\r\\nligula adipiscing.\\r\\n&lt;\\/p&gt;\\r\\n&lt;span class=&quot;hover-slidein-top&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;View Store&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;","3":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo11\\/banner\\/3.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h4 style=&quot;font-size:22px;margin-top:25px;margin-bottom:8px;&quot;&gt;\\r\\nBeautiful Earrings\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#666666;line-height:1.7;margin-bottom:17px;&quot;&gt;\\r\\nAnte vitae potenti aenean&lt;br&gt;\\r\\nligula adipiscing.\\r\\n&lt;\\/p&gt;\\r\\n&lt;span class=&quot;hover-slidein-top&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;View Store&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;","2":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo11\\/banner\\/3.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h4 style=&quot;font-size:22px;margin-top:25px;margin-bottom:8px;&quot;&gt;\\r\\nBeautiful Earrings\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#666666;line-height:1.7;margin-bottom:17px;&quot;&gt;\\r\\nAnte vitae potenti aenean&lt;br&gt;\\r\\nligula adipiscing.\\r\\n&lt;\\/p&gt;\\r\\n&lt;span class=&quot;hover-slidein-top&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;View Store&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;"}}}}'),
  array('module_id' => '46','name' => 'Column Product List','code' => 'basel_products','setting' => '{"name":"Column Product List","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Popular Items","3":"Popular Items","2":"Popular Items"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"4","image_width":"60","image_height":"76","columns":"list","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '48','name' => 'Instagram Feed (Column Style)','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed (Column Style)","status":"1","full_width":"0","use_title":"1","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"@Instagram","3":"@Instagram","2":"@Instagram"},"title_b":{"1":"","3":"","2":""},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"9","resolution":"0","columns":"3","columns_md":"3","columns_sm":"3","padding":"5","use_margin":"0","margin":"60px"}'),
  array('module_id' => '51','name' => 'About Us Block','code' => 'basel_content','setting' => '{"save":"stay","name":"About Us Block","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"1","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"border-top:1px solid #ededed;\\r\\npadding-top:60px;","nm":"0","eh":"1"},"columns":{"1":{"w":"col-sm-6","w_sm":"col-xs-12","w_md":"col-sm-12","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo11\\/banner\\/about.jpg","data5":"","data7":"vertical-top text-left","data1":{"1":"","3":"","2":""}},"2":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-middle text-left","data1":{"1":"&lt;div class=&quot;overlapping-about&quot;&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:20px&quot;&gt;\\r\\nAbout Us | Online Shop\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;color:#555555;margin-bottom:20px&quot;&gt;\\r\\nDui habitasse at ac a a scelerisque parturient leo a ac parturient fusce taciti cum fames adipiscing aenean cras ut urna leo et conubia non.Quam duis ullamcorper suspendisse a laoreet nibh a dapibus condimentum est convallis quis in est nullam libero parturient nascetur leo.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-contrast btn-tiny&quot;&gt;Visit Shop Page&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;overlapping-about&quot;&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:20px&quot;&gt;\\r\\nAbout Us | Online Shop\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;color:#555555;margin-bottom:20px&quot;&gt;\\r\\nDui habitasse at ac a a scelerisque parturient leo a ac parturient fusce taciti cum fames adipiscing aenean cras ut urna leo et conubia non.Quam duis ullamcorper suspendisse a laoreet nibh a dapibus condimentum est convallis quis in est nullam libero parturient nascetur leo.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-contrast btn-tiny&quot;&gt;Visit Shop Page&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;overlapping-about&quot;&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:20px&quot;&gt;\\r\\nAbout Us | Online Shop\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;color:#555555;margin-bottom:20px&quot;&gt;\\r\\nDui habitasse at ac a a scelerisque parturient leo a ac parturient fusce taciti cum fames adipiscing aenean cras ut urna leo et conubia non.Quam duis ullamcorper suspendisse a laoreet nibh a dapibus condimentum est convallis quis in est nullam libero parturient nascetur leo.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-contrast btn-tiny&quot;&gt;Visit Shop Page&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}}}}')
);

    $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "module`");

    foreach ($oc_module as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET "
                . "name = '" . $result['name'] . "', "
				. "module_id = '" . $result['module_id'] . "', "
                . "code = '" . $result['code'] . "', "
                . "setting = '" . addslashes($result['setting']) . "'");
    	}
	}
	
	/*--------------------------------------------
	---------------- ADD SETTINGS ----------------
	--------------------------------------------*/
	public function addSampleSettings() {
	
	$this->db->query("DELETE FROM `".DB_PREFIX."setting` WHERE `code` = 'basel'");
		
$oc_setting = array(
array('setting_id' => '4529','store_id' => '0','code' => 'basel','key' => 'top_line_height','value' => '41','serialized' => '0'),
  array('setting_id' => '17020','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1'),
  array('setting_id' => '17019','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
  array('setting_id' => '17018','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '17003','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '17004','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '17005','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '17006','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '17007','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '17008','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '17017','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '17016','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '17009','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '17010','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '17011','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '17299','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '1','serialized' => '0'),
  array('setting_id' => '17300','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '.common-home .header-wrapper {
border-bottom:1px solid #ededed;
margin-bottom:30px;
}','serialized' => '0'),
  array('setting_id' => '18164','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '18165','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '17002','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '17001','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '17000','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '17px','serialized' => '0'),
  array('setting_id' => '16999','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '16998','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => '\'Lora\', serif','serialized' => '0'),
  array('setting_id' => '16997','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '16996','store_id' => '0','code' => 'basel','key' => 'widget_lg_','value' => '0px','serialized' => '0'),
  array('setting_id' => '16994','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '16995','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '16992','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '16993','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => '\'Lora\', serif','serialized' => '0'),
  array('setting_id' => '16991','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '16989','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '16990','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '16988','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => '\'Lora\', serif','serialized' => '0'),
  array('setting_id' => '16987','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '16986','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '16985','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '16984','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '16983','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => '\'Lora\', serif','serialized' => '0'),
  array('setting_id' => '16982','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
  array('setting_id' => '16981','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '16980','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '16979','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => '\'Lora\', serif','serialized' => '0'),
  array('setting_id' => '16978','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '16977','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '16976','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '16975','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '16974','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '16961','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '16962','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '16963','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '16964','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '16965','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#31262a','serialized' => '0'),
  array('setting_id' => '16966','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '16967','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '17436','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '1','serialized' => '0'),
  array('setting_id' => '16969','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#cccccc','serialized' => '0'),
  array('setting_id' => '16970','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '16971','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
  array('setting_id' => '16972','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '16973','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => '\'Lora\', serif','serialized' => '0'),
  array('setting_id' => '16944','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#d41212','serialized' => '0'),
  array('setting_id' => '16960','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#fbbc34','serialized' => '0'),
  array('setting_id' => '16959','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#31262a','serialized' => '0'),
  array('setting_id' => '16958','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '16957','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#31262a','serialized' => '0'),
  array('setting_id' => '16956','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '16955','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '16951','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '16952','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '16953','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#31262a','serialized' => '0'),
  array('setting_id' => '16954','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#31262a','serialized' => '0'),
  array('setting_id' => '16950','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '16949','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '18163','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '16947','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#31262a','serialized' => '0'),
  array('setting_id' => '16946','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '16945','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#31262a','serialized' => '0'),
  array('setting_id' => '16939','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#000000','serialized' => '0'),
  array('setting_id' => '16940','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#31262a','serialized' => '0'),
  array('setting_id' => '16941','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '16942','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '16943','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
  array('setting_id' => '18160','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '0','serialized' => '0'),
  array('setting_id' => '16927','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '1','serialized' => '0'),
  array('setting_id' => '16928','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '18162','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '16930','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '16931','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '16932','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '16933','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '16934','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '16935','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '16936','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#31262a','serialized' => '0'),
  array('setting_id' => '16937','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '16938','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '16925','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '18158','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '18159','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '0','serialized' => '0'),
  array('setting_id' => '17149','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '1','serialized' => '0'),
  array('setting_id' => '16921','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '16920','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-handbag','serialized' => '0'),
  array('setting_id' => '16919','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '16918','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '16917','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '16916','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '16915','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '18157','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
  array('setting_id' => '16914','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '18156','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '0','serialized' => '0'),
  array('setting_id' => '18155','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '18154','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '18152','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '16908','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '18153','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '18151','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '18150','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '18148','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '16903','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '18149','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '16901','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '16900','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '16899','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '18147','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '16897','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '18146','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '16895','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '16894','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '16893','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '18161','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '16891','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '18144','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '18145','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '16890','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '1 names-c','serialized' => '0'),
  array('setting_id' => '18143','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '18142','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '18141','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
  array('setting_id' => '18140','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '16882','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '16883','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '16880','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '18139','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '16878','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '18138','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '16876','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '1','serialized' => '0'),
  array('setting_id' => '18137','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '16874','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '16873','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '16872','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '16871','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '16870','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-left','serialized' => '0'),
  array('setting_id' => '16869','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '18136','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '0','serialized' => '0'),
  array('setting_id' => '16867','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '16866','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '16865','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '16864','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '16863','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '16858','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"&lt;i&gt;Premium Jewellery Opencart theme&lt;\\/i&gt;","3":"&lt;i&gt;Premium Jewellery Opencart theme&lt;\\/i&gt;","2":"&lt;i&gt;Premium Jewellery Opencart theme&lt;\\/i&gt;"}','serialized' => '1'),
  array('setting_id' => '16859','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
  array('setting_id' => '18134','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '0','serialized' => '0'),
  array('setting_id' => '18135','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '16862','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '16849','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '90','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '16850','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'boxed','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '50','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '44','serialized' => '0'),  array('setting_id' => '16852','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-center','serialized' => '0'),
  array('setting_id' => '16853','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '18133','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '0','serialized' => '0'),
  array('setting_id' => '16855','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
  array('setting_id' => '16856','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '16857','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
  array('setting_id' => '16848','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'boxed','serialized' => '0'),
  array('setting_id' => '16847','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '1','serialized' => '0'),
  array('setting_id' => '16846','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header3','serialized' => '0'),
  array('setting_id' => '16845','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => 'nav-tabs-lg text-center','serialized' => '0'),
  array('setting_id' => '16844','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '16843','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '16841','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0'),
  array('setting_id' => '16842','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '17051','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
  array('setting_id' => '17437','store_id' => '0','code' => 'basel','key' => 'basel_fonts','value' => '{"1":{"import":"Karla:400,400i,700","name":"\'Karla\', sans-serif"},"2":{"import":"Lora:400,400i,700","name":"\'Lora\', serif"}}','serialized' => '1')
);

	
	foreach ($oc_setting as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET "
				. "code = '" . $result['code'] . "', "
				. "`key` = '" . $result['key'] . "', "
				. "value = '" . addslashes($result['value']) . "', "
                . "serialized = '" . $result['serialized'] . "'");
    	}
	}

	
	
		
}