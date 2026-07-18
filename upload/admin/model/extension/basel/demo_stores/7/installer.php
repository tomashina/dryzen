<?php
class ModelExtensionBaselDemoStores7Installer extends Model {

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
  array('extension_id' => '437','type' => 'module','code' => 'account'),
  array('extension_id' => '436','type' => 'module','code' => 'basel_content'),
  array('extension_id' => '435','type' => 'module','code' => 'basel_layerslider'),
  array('extension_id' => '434','type' => 'module','code' => 'basel_megamenu'),
  array('extension_id' => '433','type' => 'module','code' => 'basel_products'),
  array('extension_id' => '432','type' => 'module','code' => 'blog_latest'),
  array('extension_id' => '438','type' => 'module','code' => 'category'),
  array('extension_id' => '439','type' => 'module','code' => 'basel_instagram'),
  array('extension_id' => '440','type' => 'module','code' => 'basel_categories')
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
  array('layout_module_id' => '269','layout_id' => '1','code' => 'basel_content.39','position' => 'top','sort_order' => '4'),
  array('layout_module_id' => '278','layout_id' => '3','code' => 'basel_instagram.48','position' => 'column_left','sort_order' => '3'),
  array('layout_module_id' => '275','layout_id' => '1','code' => 'basel_instagram.45','position' => 'bottom','sort_order' => '6'),
  array('layout_module_id' => '273','layout_id' => '1','code' => 'basel_categories.49','position' => 'bottom','sort_order' => '3'),
  array('layout_module_id' => '277','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
  array('layout_module_id' => '274','layout_id' => '1','code' => 'blog_latest.42','position' => 'bottom','sort_order' => '4'),
  array('layout_module_id' => '272','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'top','sort_order' => '1'),
  array('layout_module_id' => '205','layout_id' => '14','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
  array('layout_module_id' => '276','layout_id' => '3','code' => 'category','position' => 'column_left','sort_order' => '1'),
  array('layout_module_id' => '206','layout_id' => '14','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3'),
  array('layout_module_id' => '271','layout_id' => '1','code' => 'basel_content.38','position' => 'top','sort_order' => '2'),
  array('layout_module_id' => '270','layout_id' => '1','code' => 'basel_products.41','position' => 'top','sort_order' => '3')
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
  array('layout_route_id' => '103','layout_id' => '3','store_id' => '0','route' => 'product/search'),
  array('layout_route_id' => '100','layout_id' => '1','store_id' => '0','route' => 'common/home'),
  array('layout_route_id' => '105','layout_id' => '2','store_id' => '0','route' => 'product/product'),
  array('layout_route_id' => '71','layout_id' => '11','store_id' => '0','route' => 'information/information'),
  array('layout_route_id' => '23','layout_id' => '7','store_id' => '0','route' => 'checkout/%'),
  array('layout_route_id' => '31','layout_id' => '8','store_id' => '0','route' => 'information/contact'),
  array('layout_route_id' => '32','layout_id' => '9','store_id' => '0','route' => 'information/sitemap'),
  array('layout_route_id' => '34','layout_id' => '4','store_id' => '0','route' => ''),
  array('layout_route_id' => '81','layout_id' => '14','store_id' => '0','route' => 'extension/blog/%'),
  array('layout_route_id' => '52','layout_id' => '12','store_id' => '0','route' => 'product/compare'),
  array('layout_route_id' => '102','layout_id' => '3','store_id' => '0','route' => 'product/manufacturer'),
  array('layout_route_id' => '106','layout_id' => '3','store_id' => '0','route' => 'product/manufacturer/info'),
  array('layout_route_id' => '101','layout_id' => '3','store_id' => '0','route' => 'product/category'),
  array('layout_route_id' => '96','layout_id' => '15','store_id' => '0','route' => 'account/login'),
  array('layout_route_id' => '97','layout_id' => '15','store_id' => '0','route' => 'affiliate/login'),
  array('layout_route_id' => '104','layout_id' => '3','store_id' => '0','route' => 'product/special')
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
	
$get_category_id = $this->model_extension_basel_demo_stores_base->getCategoryId1();

$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"1140","height":"980","minheight":"320","fullwidth":"1","margin_bottom":"25px","slide_transition":"fade","speed":"10","loop":"0","nav_buttons":"simple-arrows","nav_bullets":"0","nav_timer_bar":"0","sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"zoom-light","bg_color":"#555555","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo7\\/slideshow\\/slide1.jpg","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo7\\/slideshow\\/slider-bottle-2.png","3":"catalog\\/basel-demo\\/demo7\\/slideshow\\/slider-bottle-2.png","2":"catalog\\/basel-demo\\/demo7\\/slideshow\\/slider-bottle-2.png"},"left":{"1":"74","3":"74","2":"74"},"top":{"1":"485","3":"485","2":"485"},"minheight":"0","transitionin":"left(short)","easingin":"easeOutQuint","durationin":"800","transitionout":"left(short)","easingout":"easeOutQuint","durationout":"800","sort_order":"1","p_index":"0","start":"500","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo7\\/slideshow\\/slider-bottle-1.png","3":"catalog\\/basel-demo\\/demo7\\/slideshow\\/slider-bottle-1.png","2":"catalog\\/basel-demo\\/demo7\\/slideshow\\/slider-bottle-1.png"},"left":{"1":"188","3":"188","2":"188"},"top":{"1":"545","3":"545","2":"545"},"minheight":"0","transitionin":"left(short)","easingin":"easeOutQuint","durationin":"800","transitionout":"left(short)","easingout":"easeOutQuint","durationout":"800","sort_order":"2","p_index":"0","start":"700","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo7\\/slideshow\\/wine-vintage.png","3":"catalog\\/basel-demo\\/demo7\\/slideshow\\/wine-vintage.png","2":"catalog\\/basel-demo\\/demo7\\/slideshow\\/wine-vintage.png"},"left":{"1":"644","3":"644","2":"644"},"top":{"1":"354","3":"354","2":"354"},"minheight":"0","transitionin":"bottom(short)","easingin":"easeOutQuart","durationin":"800","transitionout":"bottom(short)","easingout":"easeOutQuart","durationout":"500","sort_order":"3","p_index":"0","start":"900","end":"10000"},{"type":"text","description":{"1":"Nothing makes the future look so rosy as to contemplate &lt;br&gt;it through a glass of Chambertin. ","3":"Nothing makes the future look so rosy as to contemplate &lt;br&gt;it through a glass of Chambertin. ","2":"Nothing makes the future look so rosy as to contemplate &lt;br&gt;it through a glass of Chambertin. "},"left":{"1":"386","3":"386","2":"386"},"top":{"1":"497","3":"497","2":"497"},"font":"\'Courgette\', cursive","fontweight":"400","fontsize":"26px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"bottom(short)","easingin":"easeOutQuart","durationin":"800","transitionout":"bottom(short)","easingout":"easeOutQuart","durationout":"500","sort_order":"4","p_index":"0","start":"1100","end":"10000"},{"type":"button","description":{"1":"Continue Reading","3":"Continue Reading","2":"Continue Reading"},"left":{"1":"621","3":"621","2":"621"},"top":{"1":"593","3":"593","2":"593"},"button_class":"btn btn-lg btn-light-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuart","durationin":"800","transitionout":"bottom(short)","easingout":"easeOutQuart","durationout":"500","sort_order":"5","p_index":"0","start":"1300","end":"10000"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"zoom-light","bg_color":"#555555","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo7\\/slideshow\\/slide2.jpg","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo7\\/slideshow\\/wine-logo.png","3":"catalog\\/basel-demo\\/demo7\\/slideshow\\/wine-logo.png","2":"catalog\\/basel-demo\\/demo7\\/slideshow\\/wine-logo.png"},"left":{"1":"438","3":"438","2":"438"},"top":{"1":"402","3":"402","2":"402"},"minheight":"0","transitionin":"bottom(short)","easingin":"easeOutQuart","durationin":"700","transitionout":"bottom(short)","easingout":"easeOutQuart","durationout":"700","sort_order":"1","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"Nothing makes the future look so rosy as to contemplate &lt;br&gt;it through a glass of Chambertin. ","3":"Nothing makes the future look so rosy as to contemplate &lt;br&gt;it through a glass of Chambertin. ","2":"Nothing makes the future look so rosy as to contemplate &lt;br&gt;it through a glass of Chambertin. "},"left":{"1":"244","3":"244","2":"244"},"top":{"1":"548","3":"548","2":"548"},"font":"\'Courgette\', cursive","fontweight":"700","fontsize":"26px","color":"#ffffff","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"bottom(short)","easingin":"easeOutQuart","durationin":"700","transitionout":"bottom(short)","easingout":"easeOutQuart","durationout":"700","sort_order":"2","p_index":"0","start":"600","end":"10000"},{"type":"button","description":{"1":"Continue Reading","3":"Continue Reading","2":"Continue Reading"},"left":{"1":"479","3":"479","2":"479"},"top":{"1":"645","3":"645","2":"645"},"button_class":"btn btn-lg btn-light-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuart","durationin":"700","transitionout":"bottom(short)","easingout":"easeOutQuart","durationout":"700","sort_order":"3","p_index":"0","start":"700","end":"9500"}]}}}'),
  array('module_id' => '38','name' => '2 x Banner Home Page','code' => 'basel_content','setting' => '{"save":"stay","name":"2 x Banner Home Page","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"15","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo7\\/banner\\/banner1.jpg","data5":"","data7":"vertical-middle text-left","data1":{"1":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;p style=&quot;font-size:17px;margin-bottom:10px;&quot;&gt;\\r\\n&lt;i&gt;New Arrivals&lt;\\/i&gt;\\r\\n&lt;\\/p&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:10px&quot;&gt;\\r\\n&lt;b&gt;Merlot Red&lt;\\/b&gt;\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:10px&quot;&gt;\\r\\nAdipiscing dignissim euismod&lt;br&gt;\\r\\nvolutpat sociis feugiat purus.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Continue Reading&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;p style=&quot;font-size:17px;margin-bottom:10px;&quot;&gt;\\r\\n&lt;i&gt;New Arrivals&lt;\\/i&gt;\\r\\n&lt;\\/p&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:10px&quot;&gt;\\r\\n&lt;b&gt;Merlot Red&lt;\\/b&gt;\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:10px&quot;&gt;\\r\\nAdipiscing dignissim euismod&lt;br&gt;\\r\\nvolutpat sociis feugiat purus.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Continue Reading&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;p style=&quot;font-size:17px;margin-bottom:10px;&quot;&gt;\\r\\n&lt;i&gt;New Arrivals&lt;\\/i&gt;\\r\\n&lt;\\/p&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:10px&quot;&gt;\\r\\n&lt;b&gt;Merlot Red&lt;\\/b&gt;\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:10px&quot;&gt;\\r\\nAdipiscing dignissim euismod&lt;br&gt;\\r\\nvolutpat sociis feugiat purus.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Continue Reading&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo7\\/banner\\/banner2.jpg","data5":"","data7":"vertical-middle text-left","data1":{"1":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;p style=&quot;font-size:17px;margin-bottom:10px;&quot;&gt;\\r\\n&lt;i&gt;New Arrivals&lt;\\/i&gt;\\r\\n&lt;\\/p&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:10px&quot;&gt;\\r\\n&lt;b&gt;Cabernet Sauvignon&lt;\\/b&gt;\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:10px&quot;&gt;\\r\\nAdipiscing dignissim euismod&lt;br&gt;\\r\\nvolutpat sociis feugiat purus.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Continue Reading&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;p style=&quot;font-size:17px;margin-bottom:10px;&quot;&gt;\\r\\n&lt;i&gt;New Arrivals&lt;\\/i&gt;\\r\\n&lt;\\/p&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:10px&quot;&gt;\\r\\n&lt;b&gt;Cabernet Sauvignon&lt;\\/b&gt;\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:10px&quot;&gt;\\r\\nAdipiscing dignissim euismod&lt;br&gt;\\r\\nvolutpat sociis feugiat purus.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Continue Reading&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;p style=&quot;font-size:17px;margin-bottom:10px;&quot;&gt;\\r\\n&lt;i&gt;New Arrivals&lt;\\/i&gt;\\r\\n&lt;\\/p&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:10px&quot;&gt;\\r\\n&lt;b&gt;Cabernet Sauvignon&lt;\\/b&gt;\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:10px&quot;&gt;\\r\\nAdipiscing dignissim euismod&lt;br&gt;\\r\\nvolutpat sociis feugiat purus.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Continue Reading&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '49','name' => 'Home Categories Slider','code' => 'basel_categories','setting' => '{"name":"Home Categories Slider","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Selected Categories","3":"Selected Categories","2":"Selected Categories"},"title_b":{"1":"&lt;span style=&quot;font-size:16px&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad&lt;\\/span&gt;","3":"&lt;span style=&quot;font-size:16px&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad&lt;\\/span&gt;","2":"&lt;span style=&quot;font-size:16px&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad&lt;\\/span&gt;"},"category":["'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'"],"image_width":"262","image_height":"335","subs":"0","limit":"5","count":"1","columns":"4","carousel":"1","rows":"1","carousel_a":"1","carousel_b":"1","use_margin":"0","margin":"60px"}'),
  array('module_id' => '39','name' => 'Full Width Wine Promo','code' => 'basel_content','setting' => '{"save":"stay","name":"Full Width Wine Promo","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"1","block_bg":"1","bg_color":"#f9f9f9","block_bgi":"0","bg_par":"0","bg_pos":"left center","bg_repeat":"repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"padding-top:30px;","nm":"0","eh":"1"},"columns":{"1":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-12","w_lg":"col-md-8","type":"html","data7":"vertical-middle text-center","data1":{"1":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo7\\/wine-bottle.png&quot; alt=&quot;&quot; \\/&gt;","3":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/wine-bottle.png&quot; alt=&quot;&quot; \\/&gt;","2":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/wine-bottle.png&quot; alt=&quot;&quot; \\/&gt;"}},"2":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-12","w_lg":"col-md-4","type":"html","data7":"vertical-middle text-left","data1":{"1":"&lt;div class=&quot;wine-promo-block&quot;&gt;\\r\\n&lt;h4 class=&quot;primary-color&quot;&gt;French&lt;\\/h4&gt;\\r\\n&lt;h3 class=&quot;primary-color&quot;&gt;Malbec&lt;\\/h3&gt;\\r\\n&lt;p&gt;A dictum condimentum parturient quam euismod venenatis a cursus iaculis vehicula pharetra dolor a vehicula integer ullamcorper suspendisse.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n","3":"&lt;div class=&quot;wine-promo-block&quot;&gt;\\r\\n&lt;h4 class=&quot;primary-color&quot;&gt;French&lt;\\/h4&gt;\\r\\n&lt;h3 class=&quot;primary-color&quot;&gt;Malbec&lt;\\/h3&gt;\\r\\n&lt;p&gt;A dictum condimentum parturient quam euismod venenatis a cursus iaculis vehicula pharetra dolor a vehicula integer ullamcorper suspendisse.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;wine-promo-block&quot;&gt;\\r\\n&lt;h4 class=&quot;primary-color&quot;&gt;French&lt;\\/h4&gt;\\r\\n&lt;h3 class=&quot;primary-color&quot;&gt;Malbec&lt;\\/h3&gt;\\r\\n&lt;p&gt;A dictum condimentum parturient quam euismod venenatis a cursus iaculis vehicula pharetra dolor a vehicula integer ullamcorper suspendisse.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Popular Red Wine','code' => 'basel_products','setting' => '{"name":"Home Page Popular Red Wine","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Popular Red Wine","3":"Popular Red Wine","2":"Popular Red Wine"},"title_b":{"1":"&lt;span style=&quot;font-size:16px&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad&lt;\\/span&gt;","3":"&lt;span style=&quot;font-size:16px&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad&lt;\\/span&gt;","2":"&lt;span style=&quot;font-size:16px&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad&lt;\\/span&gt;"},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"4","image_width":"262","image_height":"334","columns":"4","carousel":"1","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"1","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"1","margin":"35px"}'),
  array('module_id' => '42','name' => 'Home Page Latest News','code' => 'blog_latest','setting' => '{"name":"Home Page Latest News","status":"1","contrast":"1","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Wine News","3":"Wine News","2":"Wine News"},"title_b":{"1":"&lt;span style=&quot;font-size:16px&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad&lt;\\/span&gt;","3":"&lt;span style=&quot;font-size:16px&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad&lt;\\/span&gt;","2":"&lt;span style=&quot;font-size:16px&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad&lt;\\/span&gt;"},"characters":"120","use_thumb":"1","width":"360","height":"220","limit":"3","columns":"3","carousel":"1","rows":"1","carousel_a":"0","carousel_b":"1","use_button":"0","use_margin":"0","margin":"60px"}'),
  array('module_id' => '45','name' => 'Instagram Feed','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed","status":"1","full_width":"1","use_title":"1","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Wines @Instagram","3":"Wines @Instagram","2":"Wines @Instagram"},"title_b":{"1":"","3":"","2":""},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"12","resolution":"0","columns":"6","columns_md":"4","columns_sm":"3","padding":"0","use_margin":"1","margin":"-50px"}'),
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
  array('setting_id' => '12098','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '12097','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '12095','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
  array('setting_id' => '12096','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '12690','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '12092','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '12691','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '12091','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '12620','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '1','serialized' => '0'),
  array('setting_id' => '12621','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '.single-product .product-name,
.single-blog .blog-title {
font-family:\'Courgette\', cursive;
}','serialized' => '0'),
  array('setting_id' => '12088','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '12087','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '12086','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '12085','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '12083','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '12084','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '12082','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '12081','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '12080','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '12079','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '16px','serialized' => '0'),
  array('setting_id' => '12078','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '12077','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => '\'Merriweather\', serif','serialized' => '0'),
  array('setting_id' => '12076','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '12075','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '12073','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '12074','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '36px','serialized' => '0'),
  array('setting_id' => '12072','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => '\'Merriweather\', serif','serialized' => '0'),
  array('setting_id' => '12071','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '12070','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '12068','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '12069','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '32px','serialized' => '0'),
  array('setting_id' => '12066','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '12067','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => '\'Merriweather\', serif','serialized' => '0'),
  array('setting_id' => '12065','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '12064','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '12063','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '12061','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
  array('setting_id' => '12062','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => '\'Merriweather\', serif','serialized' => '0'),
  array('setting_id' => '12060','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '12059','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => '\'Merriweather\', serif','serialized' => '0'),
  array('setting_id' => '12058','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '12057','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '12055','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '12056','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '12054','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '12053','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '12052','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => '\'Merriweather\', serif','serialized' => '0'),
  array('setting_id' => '12051','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '12050','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
  array('setting_id' => '12049','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#666666','serialized' => '0'),
  array('setting_id' => '12047','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '12048','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '1','serialized' => '0'),
  array('setting_id' => '12045','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '12046','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '12044','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#a22c4d','serialized' => '0'),
  array('setting_id' => '12043','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '12042','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '12041','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '12040','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '12028','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '12029','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '12030','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '12031','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '12032','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '12033','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#a22c4d','serialized' => '0'),
  array('setting_id' => '12034','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#a22c4d','serialized' => '0'),
  array('setting_id' => '12035','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '12036','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '12037','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#a22c4d','serialized' => '0'),
  array('setting_id' => '12038','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#212121','serialized' => '0'),
  array('setting_id' => '12039','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#a22c4d','serialized' => '0'),
  array('setting_id' => '12022','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
  array('setting_id' => '12023','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#d41212','serialized' => '0'),
  array('setting_id' => '12024','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#a22c4d','serialized' => '0'),
  array('setting_id' => '12025','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '12026','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#fafafa','serialized' => '0'),
  array('setting_id' => '12689','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '12020','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#a22c4d','serialized' => '0'),
  array('setting_id' => '12021','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '12019','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '12018','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '12017','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '12016','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '12015','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#a22c4d','serialized' => '0'),
  array('setting_id' => '12685','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '12004','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '12005','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '1','serialized' => '0'),
  array('setting_id' => '12006','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '2','serialized' => '0'),
  array('setting_id' => '12007','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '12688','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '12009','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '12010','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '12011','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '12013','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '12014','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '12012','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '11999','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '12684','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '0','serialized' => '0'),
  array('setting_id' => '12686','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '0','serialized' => '0'),
  array('setting_id' => '12001','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-bag','serialized' => '0'),
  array('setting_id' => '11998','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '11997','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '11996','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '11995','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '11994','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '12683','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
  array('setting_id' => '11992','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '11991','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '1','serialized' => '0'),
  array('setting_id' => '12682','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '12681','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '11988','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '12679','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '11986','store_id' => '0','code' => 'basel','key' => 'basel_fonts','value' => '{"1":{"import":"Karla:400,400i,700","name":"\'Karla\', sans-serif"},"2":{"import":"Merriweather:400,700","name":"\'Merriweather\', serif"},"3":{"import":"Courgette","name":"\'Courgette\', cursive"}}','serialized' => '1'),
  array('setting_id' => '12680','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '12678','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '12677','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '11982','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '11981','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '12676','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '12670','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '12672','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '12671','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '11968','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '1 names-c','serialized' => '0'),
  array('setting_id' => '11969','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '12687','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '11971','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '11972','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '11973','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '12674','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '11975','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '12673','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '11977','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '12675','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '11979','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '11964','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '11963','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '12668','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '12662','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '12669','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '12663','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '0','serialized' => '0'),
  array('setting_id' => '12665','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '11957','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '12666','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '11955','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '12667','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '11953','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '11952','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '12661','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '0','serialized' => '0'),
  array('setting_id' => '11950','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-bottom','serialized' => '0'),
  array('setting_id' => '11949','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '11948','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '11947','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '11946','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '11945','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '11944','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '11943','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '11942','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '11940','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '11941','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '12660','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '12659','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '0','serialized' => '0'),
  array('setting_id' => '11936','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '11937','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
  array('setting_id' => '11930','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '12658','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '0','serialized' => '0'),
  array('setting_id' => '11932','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header2','serialized' => '0'),
  array('setting_id' => '11933','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '12664','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => '','serialized' => '0'),
  array('setting_id' => '11935','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
  array('setting_id' => '11929','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '90','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '11928','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'full-width','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '64','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '64','serialized' => '0'),  array('setting_id' => '11926','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-center','serialized' => '0'),
  array('setting_id' => '11925','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '12480','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '1','serialized' => '0'),
  array('setting_id' => '11923','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '11922','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
  array('setting_id' => '11921','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '11920','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '11919','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
  array('setting_id' => '12099','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1')
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