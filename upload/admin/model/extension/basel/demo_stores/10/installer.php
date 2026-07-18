<?php
class ModelExtensionBaselDemoStores10Installer extends Model {

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
  array('extension_id' => '492','type' => 'module','code' => 'blog_latest'),
  array('extension_id' => '491','type' => 'module','code' => 'basel_products'),
  array('extension_id' => '490','type' => 'module','code' => 'basel_megamenu'),
  array('extension_id' => '489','type' => 'module','code' => 'basel_layerslider'),
  array('extension_id' => '488','type' => 'module','code' => 'basel_content'),
  array('extension_id' => '487','type' => 'module','code' => 'account'),
  array('extension_id' => '486','type' => 'module','code' => 'category'),
  array('extension_id' => '485','type' => 'module','code' => 'basel_instagram')
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
  array('layout_module_id' => '294','layout_id' => '1','code' => 'basel_content.50','position' => 'top','sort_order' => '5'),
  array('layout_module_id' => '293','layout_id' => '1','code' => 'blog_latest.42','position' => 'top','sort_order' => '6'),
  array('layout_module_id' => '299','layout_id' => '3','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
  array('layout_module_id' => '292','layout_id' => '1','code' => 'basel_products.41','position' => 'top','sort_order' => '4'),
  array('layout_module_id' => '205','layout_id' => '14','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
  array('layout_module_id' => '298','layout_id' => '3','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3'),
  array('layout_module_id' => '206','layout_id' => '14','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3'),
  array('layout_module_id' => '300','layout_id' => '3','code' => 'category','position' => 'column_right','sort_order' => '1'),
  array('layout_module_id' => '291','layout_id' => '1','code' => 'basel_content.44','position' => 'top','sort_order' => '2'),
  array('layout_module_id' => '290','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'top','sort_order' => '1')
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
  array('layout_route_id' => '111','layout_id' => '3','store_id' => '0','route' => 'product/special'),
  array('layout_route_id' => '103','layout_id' => '1','store_id' => '0','route' => 'common/home'),
  array('layout_route_id' => '95','layout_id' => '2','store_id' => '0','route' => 'product/product'),
  array('layout_route_id' => '71','layout_id' => '11','store_id' => '0','route' => 'information/information'),
  array('layout_route_id' => '23','layout_id' => '7','store_id' => '0','route' => 'checkout/%'),
  array('layout_route_id' => '31','layout_id' => '8','store_id' => '0','route' => 'information/contact'),
  array('layout_route_id' => '32','layout_id' => '9','store_id' => '0','route' => 'information/sitemap'),
  array('layout_route_id' => '34','layout_id' => '4','store_id' => '0','route' => ''),
  array('layout_route_id' => '81','layout_id' => '14','store_id' => '0','route' => 'extension/blog/%'),
  array('layout_route_id' => '52','layout_id' => '12','store_id' => '0','route' => 'product/compare'),
  array('layout_route_id' => '110','layout_id' => '3','store_id' => '0','route' => 'product/category'),
  array('layout_route_id' => '109','layout_id' => '3','store_id' => '0','route' => 'product/manufacturer'),
  array('layout_route_id' => '112','layout_id' => '3','store_id' => '0','route' => 'product/manufacturer/info'),
  array('layout_route_id' => '96','layout_id' => '15','store_id' => '0','route' => 'account/login'),
  array('layout_route_id' => '97','layout_id' => '15','store_id' => '0','route' => 'affiliate/login'),
  array('layout_route_id' => '108','layout_id' => '3','store_id' => '0','route' => 'product/search')
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
	
$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"1140","height":"530","minheight":"200","fullwidth":"0","margin_bottom":"30px","slide_transition":"fade","speed":"20","loop":"0","nav_buttons":"0","nav_bullets":"1","nav_timer_bar":"0","sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"0","bg_color":"#f4f4f4","link":"","link_new_window":"0","thumb_image":"","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo10\\/slideshow\\/man.jpg","3":"catalog\\/basel-demo\\/demo10\\/slideshow\\/man.jpg","2":"catalog\\/basel-demo\\/demo10\\/slideshow\\/man.jpg"},"left":{"1":"714","3":"714","2":"714"},"top":{"1":"263","3":"263","2":"263"},"minheight":"200","transitionin":"right(short)","easingin":"easeOutQuint","durationin":"2000","transitionout":"right(short)","easingout":"easeOutQuint","durationout":"2000","sort_order":"1","p_index":"0","start":"800","end":"10000"},{"type":"text","description":{"1":"&lt;i&gt;Latest summer collection from&lt;\\/i&gt;","3":"&lt;i&gt;Latest summer collection from&lt;\\/i&gt;","2":"&lt;i&gt;Latest summer collection from&lt;\\/i&gt;"},"left":{"1":"165","3":"165","2":"165"},"top":{"1":"97","3":"97","2":"97"},"font":"Georgia, Times, Times New Roman, serif","fontweight":"300","fontsize":"20px","color":"#bbbbbb","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"back(200)","easingout":"easeOutQuart","durationout":"1000","sort_order":"2","p_index":"0","start":"900","end":"10000"},{"type":"text","description":{"1":"DUSTY CROMWELL","3":"DUSTY CROMWELL","2":"DUSTY CROMWELL"},"left":{"1":"52","3":"52","2":"52"},"top":{"1":"159","3":"159","2":"159"},"font":"\'Lato\', sans-serif","fontweight":"300","fontsize":"54px","color":"#000000","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(short)","easingin":"easeOutQuint","durationin":"1200","transitionout":"left(short)","easingout":"easeOutQuint","durationout":"1200","sort_order":"3","p_index":"0","start":"1100","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo10\\/slideshow\\/glasses.jpg","3":"catalog\\/basel-demo\\/demo10\\/slideshow\\/glasses.jpg","2":"catalog\\/basel-demo\\/demo10\\/slideshow\\/glasses.jpg"},"left":{"1":"149","3":"149","2":"149"},"top":{"1":"295","3":"295","2":"295"},"minheight":"0","transitionin":"back(200)","easingin":"easeOutQuint","durationin":"1800","transitionout":"back(200)","easingout":"easeOutQuint","durationout":"1800","sort_order":"4","p_index":"0","start":"1300","end":"10000"},{"type":"text","description":{"1":"&lt;i&gt;-$139.00&lt;\\/i&gt;","3":"&lt;i&gt;-$139.00&lt;\\/i&gt;","2":"&lt;i&gt;-$139.00&lt;\\/i&gt;"},"left":{"1":"163","3":"163","2":"163"},"top":{"1":"425","3":"425","2":"425"},"font":"\'Lato\', sans-serif","fontweight":"400","fontsize":"29px","color":"#e62419","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"1000","sort_order":"5","p_index":"0","start":"1500","end":"10000"},{"type":"button","description":{"1":"Buy Now","3":"Buy Now","2":"Buy Now"},"left":{"1":"321","3":"321","2":"321"},"top":{"1":"423","3":"423","2":"423"},"button_class":"btn btn-primary","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuart","durationout":"1000","sort_order":"6","p_index":"0","start":"1700","end":"10000"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"0","bg_color":"#f4f4f4","link":"","link_new_window":"0","thumb_image":"","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo10\\/slideshow\\/man2.jpg","3":"catalog\\/basel-demo\\/demo10\\/slideshow\\/man2.jpg","2":"catalog\\/basel-demo\\/demo10\\/slideshow\\/man2.jpg"},"left":{"1":"19","3":"19","2":"19"},"top":{"1":"264","3":"264","2":"264"},"minheight":"200","transitionin":"left(short)","easingin":"easeOutQuint","durationin":"2000","transitionout":"left(short)","easingout":"easeOutQuint","durationout":"2000","sort_order":"1","p_index":"0","start":"800","end":"10000"},{"type":"text","description":{"1":"&lt;i&gt;Latest summer collection from&lt;\\/i&gt;","3":"&lt;i&gt;Latest summer collection from&lt;\\/i&gt;","2":"&lt;i&gt;Latest summer collection from&lt;\\/i&gt;"},"left":{"1":"678","3":"678","2":"678"},"top":{"1":"74","3":"74","2":"74"},"font":"Georgia, Times, Times New Roman, serif","fontweight":"300","fontsize":"20px","color":"#bbbbbb","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"back(200)","easingout":"easeOutQuart","durationout":"1000","sort_order":"2","p_index":"0","start":"900","end":"10000"},{"type":"text","description":{"1":"DUSTY CROMWELL","3":"DUSTY CROMWELL","2":"DUSTY CROMWELL"},"left":{"1":"552","3":"552","2":"552"},"top":{"1":"136","3":"136","2":"136"},"font":"\'Lato\', sans-serif","fontweight":"300","fontsize":"54px","color":"#000000","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(short)","easingin":"easeOutQuint","durationin":"1200","transitionout":"left(short)","easingout":"easeOutQuint","durationout":"1200","sort_order":"3","p_index":"0","start":"1100","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo10\\/slideshow\\/hat.jpg","3":"catalog\\/basel-demo\\/demo10\\/slideshow\\/hat.jpg","2":"catalog\\/basel-demo\\/demo10\\/slideshow\\/hat.jpg"},"left":{"1":"692","3":"692","2":"692"},"top":{"1":"289","3":"289","2":"289"},"minheight":"0","transitionin":"back(200)","easingin":"easeOutQuint","durationin":"1800","transitionout":"back(200)","easingout":"easeOutQuint","durationout":"1800","sort_order":"4","p_index":"0","start":"1300","end":"10000"},{"type":"text","description":{"1":"&lt;i&gt;-$139.00&lt;\\/i&gt;","3":"&lt;i&gt;-$139.00&lt;\\/i&gt;","2":"&lt;i&gt;-$139.00&lt;\\/i&gt;"},"left":{"1":"684","3":"684","2":"684"},"top":{"1":"456","3":"456","2":"456"},"font":"\'Lato\', sans-serif","fontweight":"400","fontsize":"29px","color":"#e62419","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"1000","sort_order":"5","p_index":"0","start":"1500","end":"10000"},{"type":"button","description":{"1":"Buy Now","3":"Buy Now","2":"Buy Now"},"left":{"1":"850","3":"850","2":"850"},"top":{"1":"458","3":"458","2":"458"},"button_class":"btn btn-primary","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"1000","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"1000","sort_order":"6","p_index":"0","start":"1700","end":"10000"}]}}}'),
  array('module_id' => '50','name' => '3x Selling Points','code' => 'basel_content','setting' => '{"save":"stay","name":"3x Selling Points","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"0","block_bg":"1","bg_color":"#f7f7f7","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"padding:25px;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;promo-style-1&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-truck&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;Free Shipping &amp; &lt;br&gt;Delivery&lt;\\/h3&gt;\\r\\n&lt;p&gt;Quam natoque magna conubia odio &lt;br&gt;vestibulum dui sagittis.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline primary-color-border&quot;&gt;View Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;promo-style-1&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-truck&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;Free Shipping &amp; &lt;br&gt;Delivery&lt;\\/h3&gt;\\r\\n&lt;p&gt;Quam natoque magna conubia odio &lt;br&gt;vestibulum dui sagittis.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline primary-color-border&quot;&gt;View Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;promo-style-1&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-truck&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;Free Shipping &amp; &lt;br&gt;Delivery&lt;\\/h3&gt;\\r\\n&lt;p&gt;Quam natoque magna conubia odio &lt;br&gt;vestibulum dui sagittis.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline primary-color-border&quot;&gt;View Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;promo-style-1&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-diamond2&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;Brilliant Quality &lt;br&gt;Templates&lt;\\/h3&gt;\\r\\n&lt;p&gt;Quam natoque magna conubia odio &lt;br&gt;vestibulum dui sagittis.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline primary-color-border&quot;&gt;View Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;promo-style-1&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-diamond2&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;Brilliant Quality &lt;br&gt;Templates&lt;\\/h3&gt;\\r\\n&lt;p&gt;Quam natoque magna conubia odio &lt;br&gt;vestibulum dui sagittis.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline primary-color-border&quot;&gt;View Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;promo-style-1&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-diamond2&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;Brilliant Quality &lt;br&gt;Templates&lt;\\/h3&gt;\\r\\n&lt;p&gt;Quam natoque magna conubia odio &lt;br&gt;vestibulum dui sagittis.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline primary-color-border&quot;&gt;View Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"3":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;promo-style-1&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-camera2&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;Awesome Products &lt;br&gt;Photo&lt;\\/h3&gt;\\r\\n&lt;p&gt;Quam natoque magna conubia odio &lt;br&gt;vestibulum dui sagittis.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline primary-color-border&quot;&gt;Read more&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;promo-style-1&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-camera2&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;Awesome Products &lt;br&gt;Photo&lt;\\/h3&gt;\\r\\n&lt;p&gt;Quam natoque magna conubia odio &lt;br&gt;vestibulum dui sagittis.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline primary-color-border&quot;&gt;Read more&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;promo-style-1&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-camera2&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;Awesome Products &lt;br&gt;Photo&lt;\\/h3&gt;\\r\\n&lt;p&gt;Quam natoque magna conubia odio &lt;br&gt;vestibulum dui sagittis.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline primary-color-border&quot;&gt;Read more&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Featured Products','code' => 'basel_products','setting' => '{"name":"Home Page Featured Products","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"AMAZING SHOP","3":"AMAZING SHOP","2":"AMAZING SHOP"},"title_m":{"1":"FEATURED PRODUCTS","3":"FEATURED PRODUCTS","2":"FEATURED PRODUCTS"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"8","image_width":"262","image_height":"334","columns":"4","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"1","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"1","margin":"40px"}'),
  array('module_id' => '42','name' => 'Home Page Featured Posts','code' => 'blog_latest','setting' => '{"name":"Home Page Featured Posts","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"FROM OUR BLOG","3":"FROM OUR BLOG","2":"FROM OUR BLOG"},"title_m":{"1":"Featured Posts","3":"Featured Posts","2":"Featured Posts"},"title_b":{"1":"","3":"","2":""},"characters":"120","use_thumb":"1","width":"360","height":"220","limit":"4","columns":"3","carousel":"1","rows":"1","carousel_a":"1","carousel_b":"1","use_button":"0","use_margin":"1","margin":"25px"}'),
  array('module_id' => '44','name' => '3x Banners','code' => 'basel_content','setting' => '{"save":"stay","name":"3x Banners","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"50","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo10\\/banner\\/banner1.jpg","data5":"#","data7":"vertical-top text-right","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark&quot;&gt;\\r\\n&lt;h4 style=&quot;font-size:20px;margin-bottom:12px&quot;&gt;\\r\\n&lt;b&gt;AUTUMN COLLECTION&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark&quot;&gt;\\r\\n&lt;h4 style=&quot;font-size:20px;margin-bottom:12px&quot;&gt;\\r\\n&lt;b&gt;AUTUMN COLLECTION&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark&quot;&gt;\\r\\n&lt;h4 style=&quot;font-size:20px;margin-bottom:12px&quot;&gt;\\r\\n&lt;b&gt;AUTUMN COLLECTION&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo10\\/banner\\/banner2.jpg","data5":"#","data7":"vertical-top text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark&quot;&gt;\\r\\n&lt;h4 style=&quot;font-size:20px;margin-bottom:12px&quot;&gt;\\r\\n&lt;b&gt;#YOUMUSTHAVE&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark&quot;&gt;\\r\\n&lt;h4 style=&quot;font-size:20px;margin-bottom:12px&quot;&gt;\\r\\n&lt;b&gt;#YOUMUSTHAVE&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark&quot;&gt;\\r\\n&lt;h4 style=&quot;font-size:20px;margin-bottom:12px&quot;&gt;\\r\\n&lt;b&gt;#YOUMUSTHAVE&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"3":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo10\\/banner\\/banner3.jpg","data5":"#","data7":"vertical-top text-left","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark&quot;&gt;\\r\\n&lt;h4 style=&quot;font-size:20px;margin-bottom:12px;color:#ff6464;&quot;&gt;\\r\\n&lt;b&gt;HOT SALE -30%&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark&quot;&gt;\\r\\n&lt;h4 style=&quot;font-size:20px;margin-bottom:12px;color:#ff6464;&quot;&gt;\\r\\n&lt;b&gt;HOT SALE -30%&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark&quot;&gt;\\r\\n&lt;h4 style=&quot;font-size:20px;margin-bottom:12px;color:#ff6464;&quot;&gt;\\r\\n&lt;b&gt;HOT SALE -30%&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '45','name' => 'Instagram Feed','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed","status":"1","full_width":"1","use_title":"1","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"&lt;span style=&quot;font-size:22px;&quot;&gt;&lt;i class=&quot;icon-social-instagram&quot; style=&quot;margin-right: 7px; font-size: 18px;&quot;&gt;&lt;\\/i&gt;FOLLOW @INSTAGRAM&lt;\\/span&gt;","3":"&lt;span style=&quot;font-size:22px;&quot;&gt;&lt;i class=&quot;icon-social-instagram&quot; style=&quot;margin-right: 7px; font-size: 18px;&quot;&gt;&lt;\\/i&gt;FOLLOW @INSTAGRAM&lt;\\/span&gt;","2":"&lt;span style=&quot;font-size:22px;&quot;&gt;&lt;i class=&quot;icon-social-instagram&quot; style=&quot;margin-right: 7px; font-size: 18px;&quot;&gt;&lt;\\/i&gt;FOLLOW @INSTAGRAM&lt;\\/span&gt;"},"title_b":{"1":"","3":"","2":""},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"6","resolution":"0","columns":"6","columns_md":"3","columns_sm":"3","padding":"0","use_margin":"1","margin":"-50px"}'),
  array('module_id' => '46','name' => 'Column Product List','code' => 'basel_products','setting' => '{"name":"Column Product List","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Popular Items","3":"Popular Items","2":"Popular Items"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"4","image_width":"60","image_height":"76","columns":"list","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
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
  array('setting_id' => '14414','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '14415','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '14416','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '15359','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '14418','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '14419','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'default_bc','serialized' => '0'),
  array('setting_id' => '14420','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'default_bc','serialized' => '0'),
  array('setting_id' => '14421','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
  array('setting_id' => '15354','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '0','serialized' => '0'),
  array('setting_id' => '14404','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'default_bc','serialized' => '0'),
  array('setting_id' => '14405','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'default_bc','serialized' => '0'),
  array('setting_id' => '14406','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'default_bc','serialized' => '0'),
  array('setting_id' => '14407','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-left','serialized' => '0'),
  array('setting_id' => '14408','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '15357','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '15356','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '0','serialized' => '0'),
  array('setting_id' => '15355','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '14412','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '14413','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '14402','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'default_bc','serialized' => '0'),
  array('setting_id' => '15358','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '14400','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '1','serialized' => '0'),
  array('setting_id' => '15364','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '15378','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '14399','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '15361','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '15363','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '15362','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '15360','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '14392','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '14391','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '14390','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '14389','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '14388','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '15365','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '14387','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '15367','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '15366','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '14385','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '14379','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '15368','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '14381','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '14382','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '15370','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '15369','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '15371','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '15372','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '14372','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '1','serialized' => '0'),
  array('setting_id' => '14374','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '15373','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '15374','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '15375','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
  array('setting_id' => '14369','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '14368','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '14367','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '14366','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '14365','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '14364','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '14363','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '15380','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '15376','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '14361','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '14359','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '15379','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '0','serialized' => '0'),
  array('setting_id' => '14358','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '3','serialized' => '0'),
  array('setting_id' => '15377','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '0','serialized' => '0'),
  array('setting_id' => '14348','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '14349','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '14455','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '1','serialized' => '0'),
  array('setting_id' => '14354','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-basket','serialized' => '0'),
  array('setting_id' => '14353','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '14352','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '14351','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '14350','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '14346','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#179a94','serialized' => '0'),
  array('setting_id' => '14347','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '14345','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#222222','serialized' => '0'),
  array('setting_id' => '14344','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '14343','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#179a94','serialized' => '0'),
  array('setting_id' => '14341','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '14342','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '14340','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
  array('setting_id' => '14339','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#d41212','serialized' => '0'),
  array('setting_id' => '14338','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '14337','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '14336','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '14335','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#212121','serialized' => '0'),
  array('setting_id' => '14334','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#179a94','serialized' => '0'),
  array('setting_id' => '14333','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '14332','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '14331','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#179a94','serialized' => '0'),
  array('setting_id' => '14330','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '14328','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '14329','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '14327','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '14326','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#179a94','serialized' => '0'),
  array('setting_id' => '14325','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '14323','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '14324','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '14322','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '14321','store_id' => '0','code' => 'basel','key' => 'basel_fonts','value' => '{"1":{"import":"Lato:300,300i,400,700","name":"\'Lato\', sans-serif"}}','serialized' => '1'),
  array('setting_id' => '15381','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '14318','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '14319','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '15382','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '0','serialized' => '0'),
  array('setting_id' => '14316','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '14315','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => '\'Lato\', sans-serif','serialized' => '0'),
  array('setting_id' => '14314','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => '\'Lato\', sans-serif','serialized' => '0'),
  array('setting_id' => '14313','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#cccccc','serialized' => '0'),
  array('setting_id' => '14311','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '14312','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '1','serialized' => '0'),
  array('setting_id' => '14310','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '14309','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#179a94','serialized' => '0'),
  array('setting_id' => '14308','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '14307','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '14306','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '14305','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '14304','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '14303','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => '\'Lato\', sans-serif','serialized' => '0'),
  array('setting_id' => '14299','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '300','serialized' => '0'),
  array('setting_id' => '14300','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => '\'Lato\', sans-serif','serialized' => '0'),
  array('setting_id' => '14301','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '14302','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '14280','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '14281','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '14282','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '15383','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '0','serialized' => '0'),
  array('setting_id' => '14284','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '14285','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => '\'Lato\', sans-serif','serialized' => '0'),
  array('setting_id' => '14286','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '14287','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '14288','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '14289','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '14290','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '14291','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '36px','serialized' => '0'),
  array('setting_id' => '14292','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => '\'Lato\', sans-serif','serialized' => '0'),
  array('setting_id' => '14293','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '14294','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '14295','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '14296','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '14297','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => '\'Lato\', sans-serif','serialized' => '0'),
  array('setting_id' => '14298','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
  array('setting_id' => '14279','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '14278','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '14277','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '14276','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '14275','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '14274','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => '\'Lato\', sans-serif','serialized' => '0'),
  array('setting_id' => '14273','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0.75px','serialized' => '0'),
  array('setting_id' => '14272','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '14271','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '16px','serialized' => '0'),
  array('setting_id' => '14270','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '15384','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '','serialized' => '0'),
  array('setting_id' => '15386','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '14267','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '15385','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '14263','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '14264','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
  array('setting_id' => '14265','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '14262','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1'),
  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
  array('setting_id' => '15351','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '0','serialized' => '0'),
  array('setting_id' => '14260','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '14259','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-center','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '64','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '64','serialized' => '0'),  array('setting_id' => '14257','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '14256','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '104','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '14255','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'boxed','serialized' => '0'),
  array('setting_id' => '15350','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '0','serialized' => '0'),
  array('setting_id' => '14253','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header2','serialized' => '0'),
  array('setting_id' => '14252','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '14251','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => 'nav-tabs-sm text-center','serialized' => '0'),
  array('setting_id' => '14422','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '14423','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
  array('setting_id' => '14424','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'default_bc','serialized' => '0'),
  array('setting_id' => '15353','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '15352','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '0','serialized' => '0'),
  array('setting_id' => '14427','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
  array('setting_id' => '14428','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '14429','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '14430','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
  array('setting_id' => '14431','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0')
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