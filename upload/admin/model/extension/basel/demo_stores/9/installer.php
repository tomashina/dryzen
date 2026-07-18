<?php
class ModelExtensionBaselDemoStores9Installer extends Model {

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
  array('extension_id' => '475','type' => 'module','code' => 'basel_instagram'),
  array('extension_id' => '474','type' => 'module','code' => 'category'),
  array('extension_id' => '473','type' => 'module','code' => 'account'),
  array('extension_id' => '472','type' => 'module','code' => 'basel_content'),
  array('extension_id' => '471','type' => 'module','code' => 'basel_layerslider'),
  array('extension_id' => '470','type' => 'module','code' => 'basel_megamenu'),
  array('extension_id' => '469','type' => 'module','code' => 'basel_products'),
  array('extension_id' => '468','type' => 'module','code' => 'blog_latest'),
  array('extension_id' => '467','type' => 'module','code' => 'basel_carousel')
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
  array('layout_module_id' => '288','layout_id' => '1','code' => 'basel_instagram.45','position' => 'top','sort_order' => '8'),
  array('layout_module_id' => '201','layout_id' => '3','code' => 'category','position' => 'column_left','sort_order' => '1'),
  array('layout_module_id' => '287','layout_id' => '1','code' => 'blog_latest.42','position' => 'top','sort_order' => '6'),
  array('layout_module_id' => '202','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
  array('layout_module_id' => '286','layout_id' => '1','code' => 'basel_carousel.47','position' => 'top','sort_order' => '5'),
  array('layout_module_id' => '285','layout_id' => '1','code' => 'basel_products.41','position' => 'top','sort_order' => '4'),
  array('layout_module_id' => '205','layout_id' => '14','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
  array('layout_module_id' => '203','layout_id' => '3','code' => 'basel_instagram.48','position' => 'column_left','sort_order' => '3'),
  array('layout_module_id' => '206','layout_id' => '14','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3'),
  array('layout_module_id' => '284','layout_id' => '1','code' => 'basel_content.49','position' => 'top','sort_order' => '3'),
  array('layout_module_id' => '283','layout_id' => '1','code' => 'basel_content.44','position' => 'top','sort_order' => '2'),
  array('layout_module_id' => '282','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'top','sort_order' => '1'),
  array('layout_module_id' => '289','layout_id' => '1','code' => 'basel_content.50','position' => 'top','sort_order' => '7')
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
  array('layout_route_id' => '102','layout_id' => '1','store_id' => '0','route' => 'common/home'),
  array('layout_route_id' => '95','layout_id' => '2','store_id' => '0','route' => 'product/product'),
  array('layout_route_id' => '71','layout_id' => '11','store_id' => '0','route' => 'information/information'),
  array('layout_route_id' => '23','layout_id' => '7','store_id' => '0','route' => 'checkout/%'),
  array('layout_route_id' => '31','layout_id' => '8','store_id' => '0','route' => 'information/contact'),
  array('layout_route_id' => '32','layout_id' => '9','store_id' => '0','route' => 'information/sitemap'),
  array('layout_route_id' => '34','layout_id' => '4','store_id' => '0','route' => ''),
  array('layout_route_id' => '81','layout_id' => '14','store_id' => '0','route' => 'extension/blog/%'),
  array('layout_route_id' => '52','layout_id' => '12','store_id' => '0','route' => 'product/compare'),
  array('layout_route_id' => '91','layout_id' => '3','store_id' => '0','route' => 'product/manufacturer'),
  array('layout_route_id' => '98','layout_id' => '3','store_id' => '0','route' => 'product/manufacturer/info'),
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
	
	$current_banner_id = $this->model_extension_basel_demo_stores_base->getBannerId();
	
$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"1140","height":"668","minheight":"350","fullwidth":"1","margin_bottom":"63px","slide_transition":"fade","speed":"10","loop":"0","nav_buttons":"simple-arrows","nav_bullets":"0","nav_timer_bar":"0","g_fonts":{"1":{"import":"Satisfy","name":"\'Satisfy\', cursive"}},"sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"zoom-left-light","bg_color":"","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo9\\/slideshow\\/slide1.jpg","groups":[{"type":"text","description":{"1":"New Collections","3":"New Collections","2":"New Collections"},"left":{"1":"457","3":"457","2":"457"},"top":{"1":"245","3":"245","2":"245"},"font":"\'Satisfy\', cursive","fontweight":"400","fontsize":"39px","color":"#11b287","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"back(200)","easingout":"easeOutQuart","durationout":"1000","sort_order":"1","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"BRAND YOU MUST HAVE","3":"BRAND YOU MUST HAVE","2":"BRAND YOU MUST HAVE"},"left":{"1":"144","3":"144","2":"144"},"top":{"1":"340","3":"340","2":"340"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"72px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(short)","easingin":"easeOutQuint","durationin":"1200","transitionout":"left(short)","easingout":"easeOutQuint","durationout":"1200","sort_order":"2","p_index":"0","start":"900","end":"10000"},{"type":"text","description":{"1":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a.","3":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a.","2":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a."},"left":{"1":"278","3":"278","2":"278"},"top":{"1":"416","3":"416","2":"416"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"14px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"1000","sort_order":"3","p_index":"0","start":"1200","end":"10000"},{"type":"button","description":{"1":"Shop Now","3":"Shop Now","2":"Shop Now"},"left":{"1":"509","3":"509","2":"509"},"top":{"1":"485","3":"485","2":"485"},"button_class":"btn btn-light-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuart","durationout":"1000","sort_order":"4","p_index":"0","start":"1500","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo3\\/slideshow\\/separator.png","3":"catalog\\/basel-demo\\/demo3\\/slideshow\\/separator.png","2":"catalog\\/basel-demo\\/demo3\\/slideshow\\/separator.png"},"left":{"1":"534","3":"534","2":"534"},"top":{"1":"286","3":"286","2":"286"},"minheight":"0","transitionin":"back(200)","easingin":"easeOutQuint","durationin":"1000","transitionout":"back(200)","easingout":"easeOutQuint","durationout":"1000","sort_order":"5","p_index":"0","start":"700","end":"10000"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"0","bg_color":"","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo9\\/slideshow\\/slide2.jpg","groups":[{"type":"text","description":{"1":"New Collections","3":"New Collections","2":"New Collections"},"left":{"1":"30","3":"30","2":"30"},"top":{"1":"265","3":"265","2":"265"},"font":"\'Satisfy\', cursive","fontweight":"400","fontsize":"39px","color":"#11b287","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"top(short)","easingout":"easeOutQuint","durationout":"1000","sort_order":"1","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"STYLE UP YOURSELF","3":"STYLE UP YOURSELF","2":"STYLE UP YOURSELF"},"left":{"1":"7","3":"7","2":"7"},"top":{"1":"340","3":"340","2":"340"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"72px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(short)","easingin":"easeOutQuint","durationin":"1200","transitionout":"left(short)","easingout":"easeOutQuint","durationout":"1200","sort_order":"2","p_index":"0","start":"900","end":"10000"},{"type":"text","description":{"1":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a.","3":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a.","2":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a."},"left":{"1":"17","3":"17","2":"17"},"top":{"1":"416","3":"416","2":"416"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"14px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"1000","sort_order":"3","p_index":"0","start":"1200","end":"10000"},{"type":"button","description":{"1":"Shop Now","3":"Shop Now","2":"Shop Now"},"left":{"1":"30","3":"30","2":"30"},"top":{"1":"485","3":"485","2":"485"},"button_class":"btn btn-light-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"1000","sort_order":"4","p_index":"0","start":"1500","end":"10000"}]}}}'),
  array('module_id' => '50','name' => 'Opening Hours','code' => 'basel_content','setting' => '{"save":"stay","name":"Opening Hours","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"0","block_bg":"0","bg_color":"","block_bgi":"1","bg_par":"0","bg_pos":"center top","bg_repeat":"repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"catalog\\/basel-demo\\/demo9\\/opening-hours.jpg","c_setting":{"fw":"0","block_css":"1","css":"padding:155px 10px 125px;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;opening-times dark&quot;&gt;\\r\\n&lt;h5 class=&quot;contrast-font&quot;&gt;\\r\\nVISIT OUR STORE IN PARIS\\r\\n&lt;\\/h5&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\n &lt;p class=&quot;opening-title&quot;&gt;\\r\\n&lt;i class=&quot;fa fa-clock-o&quot;&gt;&lt;\\/i&gt; OPENING HOURS:\\r\\n&lt;\\/p&gt;\\r\\n &lt;p class=&quot;times&quot;&gt;\\r\\nMon \\u2013 Fri &lt;b&gt;8am \\u2013 6.30pm&lt;\\/b&gt;&lt;br&gt;\\r\\nSaturday &lt;b&gt;8am \\u2013 6pm&lt;\\/b&gt;&lt;br&gt;\\r\\nSunday &lt;b&gt;CLOSED&lt;\\/b&gt;\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n","3":"&lt;div class=&quot;opening-times dark&quot;&gt;\\r\\n&lt;h5 class=&quot;contrast-font&quot;&gt;\\r\\nVISIT OUR STORE IN PARIS\\r\\n&lt;\\/h5&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\n &lt;p class=&quot;opening-title&quot;&gt;\\r\\n&lt;i class=&quot;fa fa-clock-o&quot;&gt;&lt;\\/i&gt; OPENING HOURS:\\r\\n&lt;\\/p&gt;\\r\\n &lt;p class=&quot;times&quot;&gt;\\r\\nMon \\u2013 Fri &lt;b&gt;8am \\u2013 6.30pm&lt;\\/b&gt;&lt;br&gt;\\r\\nSaturday &lt;b&gt;8am \\u2013 6pm&lt;\\/b&gt;&lt;br&gt;\\r\\nSunday &lt;b&gt;CLOSED&lt;\\/b&gt;\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;opening-times dark&quot;&gt;\\r\\n&lt;h5 class=&quot;contrast-font&quot;&gt;\\r\\nVISIT OUR STORE IN PARIS\\r\\n&lt;\\/h5&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\n &lt;p class=&quot;opening-title&quot;&gt;\\r\\n&lt;i class=&quot;fa fa-clock-o&quot;&gt;&lt;\\/i&gt; OPENING HOURS:\\r\\n&lt;\\/p&gt;\\r\\n &lt;p class=&quot;times&quot;&gt;\\r\\nMon \\u2013 Fri &lt;b&gt;8am \\u2013 6.30pm&lt;\\/b&gt;&lt;br&gt;\\r\\nSaturday &lt;b&gt;8am \\u2013 6pm&lt;\\/b&gt;&lt;br&gt;\\r\\nSunday &lt;b&gt;CLOSED&lt;\\/b&gt;\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Featured Products','code' => 'basel_products','setting' => '{"name":"Home Page Featured Products","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"FEATURED PRODUCTS","3":"FEATURED PRODUCTS","2":"FEATURED PRODUCTS"},"title_b":{"1":"&lt;span style=&quot;font-size:16px&quot;&gt;Ullamcper aliquam praesent suspendisse platea ullamcorper cras condimentum nequ taciti lorem enim.&lt;\\/span&gt;","3":"&lt;span style=&quot;font-size:16px&quot;&gt;Ullamcper aliquam praesent suspendisse platea ullamcorper cras condimentum nequ taciti lorem enim.&lt;\\/span&gt;","2":"&lt;span style=&quot;font-size:16px&quot;&gt;Ullamcper aliquam praesent suspendisse platea ullamcorper cras condimentum nequ taciti lorem enim.&lt;\\/span&gt;"},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"8","image_width":"262","image_height":"334","columns":"4","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"1","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"1","margin":"50px"}'),
  array('module_id' => '42','name' => 'Home Page Latest News','code' => 'blog_latest','setting' => '{"name":"Home Page Latest News","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"FASHION NEWS","3":"FASHION NEWS","2":"FASHION NEWS"},"title_b":{"1":"&lt;span style=&quot;font-size:16px&quot;&gt;Ullamcper aliquam praesent suspendisse platea ullamcorper cras condimentum nequ taciti lorem enim.&lt;\\/span&gt;","3":"&lt;span style=&quot;font-size:16px&quot;&gt;Ullamcper aliquam praesent suspendisse platea ullamcorper cras condimentum nequ taciti lorem enim.&lt;\\/span&gt;","2":"&lt;span style=&quot;font-size:16px&quot;&gt;Ullamcper aliquam praesent suspendisse platea ullamcorper cras condimentum nequ taciti lorem enim.&lt;\\/span&gt;"},"characters":"120","use_thumb":"1","width":"360","height":"220","limit":"4","columns":"3","carousel":"1","rows":"1","carousel_a":"1","carousel_b":"1","use_button":"0","use_margin":"1","margin":"80px"}'),
  array('module_id' => '49','name' => 'Video Background Jumbotron','code' => 'basel_content','setting' => '{"save":"stay","name":"Video Background Jumbotron","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"1","block_bg":"1","bg_color":"rgba(0,0,0,0.5)","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"1","bg_video":"VH7__ZLzUGw","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"padding:104px 10px 70px;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;video-jumbotron light&quot;&gt;\\r\\n&lt;h4&gt;#MyStylesheet&lt;\\/h4&gt;\\r\\n&lt;h2 class=&quot;contrast-font&quot;&gt;\\r\\nDusty &lt;span class=&quot;primary-color&quot;&gt;Cromwell&lt;\\/span&gt;&lt;br&gt;\\r\\nprovide a New Collection\\r\\n&lt;\\/h2&gt;\\r\\n&lt;p&gt;\\r\\nCommodo pulvinar amet parturient mus sociis dis parturient conubia suspendisse turient&lt;br&gt;\\r\\nmus sociis dis parturient conubia suspendisse nullam\\r\\n&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-tiny btn-light&quot;&gt;Purchase Basel&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;\\r\\n","3":"&lt;div class=&quot;video-jumbotron light&quot;&gt;\\r\\n&lt;h4&gt;#MyStylesheet&lt;\\/h4&gt;\\r\\n&lt;h2 class=&quot;contrast-font&quot;&gt;\\r\\nDusty &lt;span class=&quot;primary-color&quot;&gt;Cromwell&lt;\\/span&gt;&lt;br&gt;\\r\\nprovide a New Collection\\r\\n&lt;\\/h2&gt;\\r\\n&lt;p&gt;\\r\\nCommodo pulvinar amet parturient mus sociis dis parturient conubia suspendisse turient&lt;br&gt;\\r\\nmus sociis dis parturient conubia suspendisse nullam\\r\\n&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-tiny btn-light&quot;&gt;Purchase Basel&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;\\r\\n","2":"&lt;div class=&quot;video-jumbotron light&quot;&gt;\\r\\n&lt;h4&gt;#MyStylesheet&lt;\\/h4&gt;\\r\\n&lt;h2 class=&quot;contrast-font&quot;&gt;\\r\\nDusty &lt;span class=&quot;primary-color&quot;&gt;Cromwell&lt;\\/span&gt;&lt;br&gt;\\r\\nprovide a New Collection\\r\\n&lt;\\/h2&gt;\\r\\n&lt;p&gt;\\r\\nCommodo pulvinar amet parturient mus sociis dis parturient conubia suspendisse turient&lt;br&gt;\\r\\nmus sociis dis parturient conubia suspendisse nullam\\r\\n&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-tiny btn-light&quot;&gt;Purchase Basel&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;\\r\\n"}}}}'),
  array('module_id' => '44','name' => 'Home Page Banner Wall','code' => 'basel_content','setting' => '{"save":"stay","name":"Home Page Banner Wall","status":"1","b_setting":{"title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Shop Categories","3":"Shop Categories","2":"Shop Categories"},"title_b":{"1":"&lt;span style=&quot;font-size:16px&quot;&gt;Ullamcper aliquam praesent suspendisse platea ullamcorper cras condimentum nequ taciti lorem enim.&lt;\\/span&gt;","3":"&lt;span style=&quot;font-size:16px&quot;&gt;Ullamcper aliquam praesent suspendisse platea ullamcorper cras condimentum nequ taciti lorem enim.&lt;\\/span&gt;","2":"&lt;span style=&quot;font-size:16px&quot;&gt;Ullamcper aliquam praesent suspendisse platea ullamcorper cras condimentum nequ taciti lorem enim.&lt;\\/span&gt;"},"custom_m":"1","mt":"0","mr":"0","mb":"80","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-3","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo9\\/banner\\/banner1.jpg","data5":"#","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Woman&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Woman&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Woman&lt;\\/i&gt;"},"data4":"catalog\\/basel-demo\\/demo9\\/banner\\/banner2.jpg","data6":"#","data8":"vertical-bottom text-center","data3":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Shoes&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Shoes&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Shoes&lt;\\/i&gt;"}},"2":{"w":"col-sm-3","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo9\\/banner\\/banner3.jpg","data5":"#","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Man&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Man&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Man&lt;\\/i&gt;"},"data4":"catalog\\/basel-demo\\/demo9\\/banner\\/banner4.jpg","data6":"#","data8":"vertical-bottom text-center","data3":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Jewellery&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Jewellery&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Jewellery&lt;\\/i&gt;"}},"3":{"w":"col-sm-3","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo9\\/banner\\/banner5.jpg","data5":"#","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Watches&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Watches&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Watches&lt;\\/i&gt;"}},"4":{"w":"col-sm-3","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo9\\/banner\\/banner6.jpg","data5":"#","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Bags&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Bags&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Bags&lt;\\/i&gt;"},"data4":"catalog\\/basel-demo\\/demo9\\/banner\\/banner7.jpg","data6":"#","data8":"vertical-bottom text-center","data3":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Belts&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Belts&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Belts&lt;\\/i&gt;"}}}}'),
  array('module_id' => '45','name' => 'Instagram Feed','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed","status":"1","full_width":"1","use_title":"1","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"&lt;span style=&quot;font-size:22px;&quot;&gt;&lt;i class=&quot;icon-social-instagram&quot; style=&quot;margin-right: 7px; font-size: 18px;&quot;&gt;&lt;\\/i&gt;FOLLOW @INSTAGRAM&lt;\\/span&gt;","3":"&lt;span style=&quot;font-size:22px;&quot;&gt;&lt;i class=&quot;icon-social-instagram&quot; style=&quot;margin-right: 7px; font-size: 18px;&quot;&gt;&lt;\\/i&gt;FOLLOW @INSTAGRAM&lt;\\/span&gt;","2":"&lt;span style=&quot;font-size:22px;&quot;&gt;&lt;i class=&quot;icon-social-instagram&quot; style=&quot;margin-right: 7px; font-size: 18px;&quot;&gt;&lt;\\/i&gt;FOLLOW @INSTAGRAM&lt;\\/span&gt;"},"title_b":{"1":"","3":"","2":""},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"6","resolution":"0","columns":"6","columns_md":"3","columns_sm":"3","padding":"0","use_margin":"1","margin":"-50px"}'),
  array('module_id' => '46','name' => 'Column Product List','code' => 'basel_products','setting' => '{"name":"Column Product List","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Popular Items","3":"Popular Items","2":"Popular Items"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"4","image_width":"60","image_height":"76","columns":"list","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '47','name' => 'Brands Carousel','code' => 'basel_carousel','setting' => '{"name":"Brands Carousel","status":"1","contrast":"1","use_title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"banner_id":"'.$current_banner_id.'","image_width":"200","image_height":"50","columns":"5","rows":"1","carousel_a":"0","carousel_b":"0","use_margin":"0","margin":""}'),
  array('module_id' => '48','name' => 'Instagram Feed (Column Style)','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed (Column Style)","status":"1","full_width":"0","use_title":"1","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"@Instagram","3":"@Instagram","2":"@Instagram"},"title_b":{"1":"","3":"","2":""},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"9","resolution":"0","columns":"3","columns_md":"3","columns_sm":"3","padding":"5","use_margin":"0","margin":"60px"}')
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
  array('setting_id' => '13416','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => 'nav-tabs-lg text-center','serialized' => '0'),
  array('setting_id' => '13415','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '13414','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header1','serialized' => '0'),
  array('setting_id' => '13420','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '1','serialized' => '0'),
  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
  array('setting_id' => '13412','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '13411','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '104','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '13410','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'full-width','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '64','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '64','serialized' => '0'),  array('setting_id' => '13408','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-left','serialized' => '0'),
  array('setting_id' => '13407','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '13518','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '1','serialized' => '0'),
  array('setting_id' => '13405','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1'),
  array('setting_id' => '13404','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '13403','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
  array('setting_id' => '13402','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '14067','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '13401','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '14068','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '13398','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '
','serialized' => '0'),
  array('setting_id' => '13384','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '13385','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '16px','serialized' => '0'),
  array('setting_id' => '13386','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '13387','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0.75px','serialized' => '0'),
  array('setting_id' => '13388','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '13389','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '13390','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '13391','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '13392','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '13393','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '13394','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '13395','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '13396','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '13397','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '0','serialized' => '0'),
  array('setting_id' => '13382','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '13383','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '13381','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '13379','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '13380','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '36px','serialized' => '0'),
  array('setting_id' => '13378','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '13377','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '13376','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '36px','serialized' => '0'),
  array('setting_id' => '13375','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '13374','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '13373','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '13372','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '13371','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '13370','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '13369','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
  array('setting_id' => '13367','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '13368','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '13366','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '13365','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '13364','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '13363','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '13362','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '13361','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '13360','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '13359','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '13350','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#179a94','serialized' => '0'),
  array('setting_id' => '13351','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '13352','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '14036','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '1','serialized' => '0'),
  array('setting_id' => '13354','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#cccccc','serialized' => '0'),
  array('setting_id' => '13355','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '13358','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => '\'Lora\', serif','serialized' => '0'),
  array('setting_id' => '13357','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '13356','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
  array('setting_id' => '13331','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '13332','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '14066','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '13675','store_id' => '0','code' => 'basel','key' => 'basel_fonts','value' => '{"1":{"import":"Karla:400,400i,700","name":"\'Karla\', sans-serif"},"2":{"import":"Lora:400,400i","name":"\'Lora\', serif"}}','serialized' => '1'),
  array('setting_id' => '13334','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '13335','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '13336','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '13337','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '13338','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#179a94','serialized' => '0'),
  array('setting_id' => '13339','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '13340','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '13341','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '13342','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '13343','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#179a94','serialized' => '0'),
  array('setting_id' => '13344','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '13345','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '13346','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#179a94','serialized' => '0'),
  array('setting_id' => '13347','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#212121','serialized' => '0'),
  array('setting_id' => '13348','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '13349','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '13330','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '13329','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#d41212','serialized' => '0'),
  array('setting_id' => '13328','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
  array('setting_id' => '13326','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '13327','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '13325','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#179a94','serialized' => '0'),
  array('setting_id' => '13324','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '13323','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#222222','serialized' => '0'),
  array('setting_id' => '13322','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '13321','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '13320','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '13315','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '13316','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '13317','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '13318','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '13319','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '13306','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-bag','serialized' => '0'),
  array('setting_id' => '14062','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '0','serialized' => '0'),
  array('setting_id' => '13308','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '1','serialized' => '0'),
  array('setting_id' => '13309','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '1','serialized' => '0'),
  array('setting_id' => '13608','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '2','serialized' => '0'),
  array('setting_id' => '13311','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '14063','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '13313','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '14065','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '13305','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '13304','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '13302','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '13303','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '13301','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '13300','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '13299','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '14061','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
  array('setting_id' => '14060','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '13296','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '1','serialized' => '0'),
  array('setting_id' => '14059','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '13294','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '14058','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '14057','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '14056','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '14055','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '13289','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '14054','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '13288','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '13286','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '14053','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '14052','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '13283','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '14051','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '13282','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '13280','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '13276','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '13277','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '13278','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '13279','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '14046','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '14048','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '14047','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '14049','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '14050','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '14064','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '13269','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '13270','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '1','serialized' => '0'),
  array('setting_id' => '14044','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '13250','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'default_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '14041','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '0','serialized' => '0'),
  array('setting_id' => '13252','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'default_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '13253','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'default_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '13254','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'default_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '13255','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-left','serialized' => '0'),
  array('setting_id' => '13256','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '14043','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '13258','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '1','serialized' => '0'),
  array('setting_id' => '14042','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '13260','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '13261','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '13262','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '13263','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '13264','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '14045','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '13266','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '13248','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'default_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '13249','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'default_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '13247','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
  array('setting_id' => '13246','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '13241','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
  array('setting_id' => '13242','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'default_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '14040','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '14039','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '0','serialized' => '0'),
  array('setting_id' => '13245','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
  array('setting_id' => '13240','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
  array('setting_id' => '13417','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '13418','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '13419','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0')
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