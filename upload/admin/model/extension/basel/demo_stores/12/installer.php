<?php
class ModelExtensionBaselDemoStores12Installer extends Model {

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
  array('extension_id' => '668','type' => 'module','code' => 'basel_instagram'),
  array('extension_id' => '667','type' => 'module','code' => 'category'),
  array('extension_id' => '666','type' => 'module','code' => 'account'),
  array('extension_id' => '665','type' => 'module','code' => 'basel_content'),
  array('extension_id' => '664','type' => 'module','code' => 'basel_layerslider'),
  array('extension_id' => '663','type' => 'module','code' => 'basel_megamenu'),
  array('extension_id' => '662','type' => 'module','code' => 'basel_products'),
  array('extension_id' => '660','type' => 'module','code' => 'basel_carousel')
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
  array('layout_module_id' => '225','layout_id' => '1','code' => 'basel_carousel.47','position' => 'top','sort_order' => '6'),
  array('layout_module_id' => '224','layout_id' => '1','code' => 'basel_content.40','position' => 'top','sort_order' => '7'),
  array('layout_module_id' => '223','layout_id' => '1','code' => 'basel_products.41','position' => 'top','sort_order' => '5'),
  array('layout_module_id' => '222','layout_id' => '1','code' => 'basel_content.44','position' => 'top','sort_order' => '4'),
  array('layout_module_id' => '202','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
  array('layout_module_id' => '221','layout_id' => '1','code' => 'basel_content.39','position' => 'top','sort_order' => '3'),
  array('layout_module_id' => '220','layout_id' => '1','code' => 'basel_content.38','position' => 'top','sort_order' => '2'),
  array('layout_module_id' => '219','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'top','sort_order' => '1'),
  array('layout_module_id' => '205','layout_id' => '14','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
  array('layout_module_id' => '203','layout_id' => '3','code' => 'basel_instagram.48','position' => 'column_left','sort_order' => '3'),
  array('layout_module_id' => '206','layout_id' => '14','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3')
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
  array('layout_route_id' => '100','layout_id' => '1','store_id' => '0','route' => 'common/home'),
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
	
// `basel`.`oc_module`
$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"1140","height":"600","minheight":"180","fullwidth":"1","margin_bottom":"0px","slide_transition":"fade","speed":"25","loop":"0","nav_buttons":"simple-arrows","nav_bullets":"0","nav_timer_bar":"0","sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"0","bg_color":"#000000","link":"","link_new_window":"0","thumb_image":"","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo12\\/slideshow\\/watch1.jpg","3":"catalog\\/basel-demo\\/demo12\\/slideshow\\/watch1.jpg","2":"catalog\\/basel-demo\\/demo12\\/slideshow\\/watch1.jpg"},"left":{"1":"485","3":"485","2":"485"},"top":{"1":"291","3":"291","2":"291"},"minheight":"","transitionin":"back(200)","easingin":"easeInOut","durationin":"1600","transitionout":"back(200)","easingout":"easeInOut","durationout":"1000","sort_order":"1","p_index":"0","start":"1500","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo12\\/slideshow\\/logo.jpg","3":"catalog\\/basel-demo\\/demo12\\/slideshow\\/logo.jpg","2":"catalog\\/basel-demo\\/demo12\\/slideshow\\/logo.jpg"},"left":{"1":"163","3":"163","2":"163"},"top":{"1":"134","3":"134","2":"134"},"minheight":"0","transitionin":"top(short)","easingin":"easeInOut","durationin":"900","transitionout":"top(short)","easingout":"easeInOut","durationout":"900","sort_order":"2","p_index":"0","start":"400","end":"10000"},{"type":"text","description":{"1":"TIME IS NOW","3":"TIME IS NOW","2":"TIME IS NOW"},"left":{"1":"12","3":"12","2":"12"},"top":{"1":"205","3":"205","2":"205"},"font":"\'Lora\', serif","fontweight":"400","fontsize":"72px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"right(short)","easingin":"easeInOut","durationin":"900","transitionout":"right(short)","easingout":"easeInOut","durationout":"900","sort_order":"3","p_index":"0","start":"800","end":"10000"},{"type":"text","description":{"1":"Spring has arrived. Stock up with our latest t-shirts &lt;br&gt;denim with our and sweatshirts.","3":"Spring has arrived. Stock up with our latest t-shirts &lt;br&gt;denim with our and sweatshirts.","2":"Spring has arrived. Stock up with our latest t-shirts &lt;br&gt;denim with our and sweatshirts."},"left":{"1":"58","3":"58","2":"58"},"top":{"1":"281","3":"281","2":"281"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"16px","color":"#cccccc","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"bottom(short)","easingin":"easeInOut","durationin":"900","transitionout":"bottom(short)","easingout":"easeInOut","durationout":"900","sort_order":"4","p_index":"0","start":"1200","end":"10000"},{"type":"button","description":{"1":"Shop Collection","3":"Shop Collection","2":"Shop Collection"},"left":{"1":"179","3":"179","2":"179"},"top":{"1":"350","3":"350","2":"350"},"button_class":"btn btn-light-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeInOut","durationin":"900","transitionout":"bottom(short)","easingout":"easeInOut","durationout":"900","sort_order":"5","p_index":"0","start":"1600","end":"10000"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"0","bg_color":"#000000","link":"","link_new_window":"0","thumb_image":"","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo12\\/slideshow\\/watch2.jpg","3":"catalog\\/basel-demo\\/demo12\\/slideshow\\/watch2.jpg","2":"catalog\\/basel-demo\\/demo12\\/slideshow\\/watch2.jpg"},"left":{"1":"226","3":"226","2":"226"},"top":{"1":"369","3":"369","2":"369"},"minheight":"","transitionin":"bottom(short)","easingin":"easeInOut","durationin":"1400","transitionout":"bottom(short)","easingout":"easeInOut","durationout":"1000","sort_order":"1","p_index":"0","start":"1400","end":"10000"},{"type":"text","description":{"1":"&lt;i&gt;- John Lennon&lt;\\/i&gt;","3":"&lt;i&gt;- John Lennon&lt;\\/i&gt;","2":"&lt;i&gt;- John Lennon&lt;\\/i&gt;"},"left":{"1":"468","3":"468","2":"468"},"top":{"1":"105","3":"105","2":"105"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"28px","color":"#eeeeee","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"bottom(short)","easingin":"easeInOut","durationin":"1500","transitionout":"bottom(short)","easingout":"easeInOut","durationout":"800","sort_order":"2","p_index":"0","start":"1000","end":"10000"},{"type":"text","description":{"1":"TIME YOU ENJOY WASTING ISN\'T WASTED TIME","3":"TIME YOU ENJOY WASTING ISN\'T WASTED TIME","2":"TIME YOU ENJOY WASTING ISN\'T WASTED TIME"},"left":{"1":"62","3":"62","2":"62"},"top":{"1":"54","3":"54","2":"54"},"font":"\'Lora\', serif","fontweight":"400","fontsize":"42px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"back(200)","easingin":"easeInOut","durationin":"1500","transitionout":"left(long)","easingout":"easeInOutQuart","durationout":"500","sort_order":"3","p_index":"0","start":"600","end":"10000"}]}}}'),
  array('module_id' => '38','name' => 'Home Page Selling Points','code' => 'basel_content','setting' => '{"save":"stay","name":"Home Page Selling Points","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"40","ml":"0","fw":"1","block_bg":"1","bg_color":"#b0f4da","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"1","css":"border-top:2px solid #000000;\\r\\nborder-bottom:2px solid #000000;"},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"1","eh":"0"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;tiny-promo&quot; style=&quot;font-size:13px;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-thumbs-o-up&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;b&gt;20,000+ SATISFIED CUSTOMERS&lt;\\/b&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;tiny-promo&quot; style=&quot;font-size:13px;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-thumbs-o-up&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;b&gt;20,000+ SATISFIED CUSTOMERS&lt;\\/b&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;tiny-promo&quot; style=&quot;font-size:13px;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-thumbs-o-up&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;b&gt;20,000+ SATISFIED CUSTOMERS&lt;\\/b&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;tiny-promo&quot; style=&quot;font-size:13px;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;b&gt;FREE WORLDWIDE SHIPPING&lt;\\/b&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;tiny-promo&quot; style=&quot;font-size:13px;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;b&gt;FREE WORLDWIDE SHIPPING&lt;\\/b&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;tiny-promo&quot; style=&quot;font-size:13px;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;b&gt;FREE WORLDWIDE SHIPPING&lt;\\/b&gt;\\r\\n&lt;\\/div&gt;"}},"3":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;tiny-promo&quot; style=&quot;font-size:13px;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-money&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;b&gt;TAX FREE SHOPPING&lt;\\/b&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;tiny-promo&quot; style=&quot;font-size:13px;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-money&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;b&gt;TAX FREE SHOPPING&lt;\\/b&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;tiny-promo&quot; style=&quot;font-size:13px;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-money&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;b&gt;TAX FREE SHOPPING&lt;\\/b&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '39','name' => '3x - Banners + Info','code' => 'basel_content','setting' => '{"save":"stay","name":"3x - Banners + Info","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"45","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left center","bg_repeat":"repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;instruction-box clock-banner&quot;&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.themeforest.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo12\\/banner\\/1.jpg&quot; alt=&quot;&quot;&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;\\r\\n&lt;h5&gt;&lt;b&gt;\\r\\nCARBON ORIGINAL\\r\\n&lt;\\/b&gt;&lt;\\/h5&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;hover-underline&quot;&gt;Read more..&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;p&gt;\\r\\nViverra rutrum fringilla curabitur at&lt;br&gt;\\r\\nlitora etiam leo donec.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;instruction-box clock-banner&quot;&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.themeforest.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo12\\/banner\\/1.jpg&quot; alt=&quot;&quot;&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;\\r\\n&lt;h5&gt;&lt;b&gt;\\r\\nCARBON ORIGINAL\\r\\n&lt;\\/b&gt;&lt;\\/h5&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;hover-underline&quot;&gt;Read more..&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;p&gt;\\r\\nViverra rutrum fringilla curabitur at&lt;br&gt;\\r\\nlitora etiam leo donec.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;instruction-box clock-banner&quot;&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.themeforest.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo12\\/banner\\/1.jpg&quot; alt=&quot;&quot;&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;\\r\\n&lt;h5&gt;&lt;b&gt;\\r\\nCARBON ORIGINAL\\r\\n&lt;\\/b&gt;&lt;\\/h5&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;hover-underline&quot;&gt;Read more..&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;p&gt;\\r\\nViverra rutrum fringilla curabitur at&lt;br&gt;\\r\\nlitora etiam leo donec.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;instruction-box clock-banner&quot;&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.themeforest.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo12\\/banner\\/2.jpg&quot; alt=&quot;&quot;&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;\\r\\n&lt;h5&gt;&lt;b&gt;\\r\\nOFFICER SILVER\\r\\n&lt;\\/b&gt;&lt;\\/h5&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;hover-underline&quot;&gt;Read more..&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;p&gt;\\r\\nViverra rutrum fringilla curabitur at&lt;br&gt;\\r\\nlitora etiam leo donec.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;instruction-box clock-banner&quot;&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.themeforest.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo12\\/banner\\/2.jpg&quot; alt=&quot;&quot;&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;\\r\\n&lt;h5&gt;&lt;b&gt;\\r\\nOFFICER SILVER\\r\\n&lt;\\/b&gt;&lt;\\/h5&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;hover-underline&quot;&gt;Read more..&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;p&gt;\\r\\nViverra rutrum fringilla curabitur at&lt;br&gt;\\r\\nlitora etiam leo donec.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;instruction-box clock-banner&quot;&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.themeforest.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo12\\/banner\\/2.jpg&quot; alt=&quot;&quot;&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;\\r\\n&lt;h5&gt;&lt;b&gt;\\r\\nOFFICER SILVER\\r\\n&lt;\\/b&gt;&lt;\\/h5&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;hover-underline&quot;&gt;Read more..&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;p&gt;\\r\\nViverra rutrum fringilla curabitur at&lt;br&gt;\\r\\nlitora etiam leo donec.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;"}},"3":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;instruction-box clock-banner&quot;&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.themeforest.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo12\\/banner\\/3.jpg&quot; alt=&quot;&quot;&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;\\r\\n&lt;h5&gt;&lt;b&gt;\\r\\nGOLD FALCON\\r\\n&lt;\\/b&gt;&lt;\\/h5&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;hover-underline&quot;&gt;Read more..&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;p&gt;\\r\\nViverra rutrum fringilla curabitur at&lt;br&gt;\\r\\nlitora etiam leo donec.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;instruction-box clock-banner&quot;&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.themeforest.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo12\\/banner\\/3.jpg&quot; alt=&quot;&quot;&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;\\r\\n&lt;h5&gt;&lt;b&gt;\\r\\nGOLD FALCON\\r\\n&lt;\\/b&gt;&lt;\\/h5&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;hover-underline&quot;&gt;Read more..&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;p&gt;\\r\\nViverra rutrum fringilla curabitur at&lt;br&gt;\\r\\nlitora etiam leo donec.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;instruction-box clock-banner&quot;&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.themeforest.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo12\\/banner\\/3.jpg&quot; alt=&quot;&quot;&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;\\r\\n&lt;h5&gt;&lt;b&gt;\\r\\nGOLD FALCON\\r\\n&lt;\\/b&gt;&lt;\\/h5&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;hover-underline&quot;&gt;Read more..&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;p&gt;\\r\\nViverra rutrum fringilla curabitur at&lt;br&gt;\\r\\nlitora etiam leo donec.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '40','name' => 'Newsletter Subscribe Block','code' => 'basel_content','setting' => '{"save":"stay","name":"Newsletter Subscribe Block","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"-60","ml":"0","fw":"1","block_bg":"1","bg_color":"#000000","block_bgi":"1","bg_par":"0","bg_pos":"center top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"catalog\\/basel-demo\\/demo12\\/subscribe-bg.jpg","c_setting":{"fw":"0","block_css":"1","css":"padding:70px 10px 70px 10px;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-middle text-center","data1":{"1":"&lt;div class=&quot;wide-signup light&quot;&gt;\\r\\n&lt;h3&gt;NEWSLETTER&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore &lt;\\/br&gt;et dolore magna aliqua. Ut enim ad minim veniam&lt;\\/p&gt;\\r\\n&lt;div class=&quot;light_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;wide-signup light&quot;&gt;\\r\\n&lt;h3&gt;NEWSLETTER&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore &lt;\\/br&gt;et dolore magna aliqua. Ut enim ad minim veniam&lt;\\/p&gt;\\r\\n&lt;div class=&quot;light_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;wide-signup light&quot;&gt;\\r\\n&lt;h3&gt;NEWSLETTER&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore &lt;\\/br&gt;et dolore magna aliqua. Ut enim ad minim veniam&lt;\\/p&gt;\\r\\n&lt;div class=&quot;light_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Products','code' => 'basel_products','setting' => '{"name":"Home Page Products","status":"1","contrast":"0","use_title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"8","image_width":"262","image_height":"334","columns":"4","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"1","margin":"40px"}'),
  array('module_id' => '44','name' => 'Full Width Simple Promo Message','code' => 'basel_content','setting' => '{"save":"stay","name":"Full Width Simple Promo Message","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"1","block_bg":"1","bg_color":"#000000","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"padding:38px 10px 23px;","nm":"1","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;h3 class=&quot;contrast-font&quot; style=&quot;font-size:22px;letter-spacing:1.5px;margin-bottom:20px;&quot;&gt;\\r\\nSWISS WATCHMAKING ART CONCENTRATING ON THE ESSENTIALS\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;opacity:0.8&quot;&gt;\\r\\nLES CLASSIQUES TRADITION GENTS STEEL\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;h3 class=&quot;contrast-font&quot; style=&quot;font-size:22px;letter-spacing:1.5px;margin-bottom:20px;&quot;&gt;\\r\\nSWISS WATCHMAKING ART CONCENTRATING ON THE ESSENTIALS\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;opacity:0.8&quot;&gt;\\r\\nLES CLASSIQUES TRADITION GENTS STEEL\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;h3 class=&quot;contrast-font&quot; style=&quot;font-size:22px;letter-spacing:1.5px;margin-bottom:20px;&quot;&gt;\\r\\nSWISS WATCHMAKING ART CONCENTRATING ON THE ESSENTIALS\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;opacity:0.8&quot;&gt;\\r\\nLES CLASSIQUES TRADITION GENTS STEEL\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '46','name' => 'Column Product List','code' => 'basel_products','setting' => '{"name":"Column Product List","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Popular Items","3":"Popular Items","2":"Popular Items"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"4","image_width":"60","image_height":"76","columns":"list","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '47','name' => 'Brands Carousel','code' => 'basel_carousel','setting' => '{"name":"Brands Carousel","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"&lt;b&gt;OUR FAVORITE BRANDS&lt;\\/b&gt;","3":"&lt;b&gt;OUR FAVORITE BRANDS&lt;\\/b&gt;","2":"&lt;b&gt;OUR FAVORITE BRANDS&lt;\\/b&gt;"},"title_b":{"1":"","3":"","2":""},"banner_id":"'.$current_banner_id.'","image_width":"200","image_height":"50","columns":"6","rows":"1","carousel_a":"0","carousel_b":"0","use_margin":"0","margin":"60px"}'),
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
  array('setting_id' => '20163','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => 'nav-tabs-lg text-center','serialized' => '0'),
  array('setting_id' => '20164','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '20165','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '20166','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0'),
  array('setting_id' => '20151','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
  array('setting_id' => '20152','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '20202','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '0','serialized' => '0'),
  array('setting_id' => '20154','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
  array('setting_id' => '20155','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-left','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '64','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '64','serialized' => '0'),  array('setting_id' => '20157','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '20158','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '104','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '20159','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '20160','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '1','serialized' => '0'),
  array('setting_id' => '20161','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header1','serialized' => '0'),
  array('setting_id' => '20162','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '20150','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
  array('setting_id' => '20148','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
  array('setting_id' => '20149','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '20204','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '20203','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '0','serialized' => '0'),
  array('setting_id' => '20145','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'default_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '20144','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'default_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '20143','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '20139','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'default_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '20142','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '20141','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '20140','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '20138','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '20137','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-left','serialized' => '0'),
  array('setting_id' => '20136','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '20135','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '20134','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '20133','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '20205','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '20131','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '1','serialized' => '0'),
  array('setting_id' => '20206','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '20129','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '20207','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '20127','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '20208','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '20125','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '20124','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '20123','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '20209','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '20210','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '20211','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '20212','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '20213','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '20117','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '1','serialized' => '0'),
  array('setting_id' => '20116','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '20227','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '20114','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '20112','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '20113','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '20214','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '20110','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '20216','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '20108','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '20215','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '20104','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '20217','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '20106','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '20103','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '20218','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '20219','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '20221','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '20220','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '20098','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '20222','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '20223','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '20095','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '1','serialized' => '0'),
  array('setting_id' => '20224','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
  array('setting_id' => '20093','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '20092','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '20091','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '20090','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '20089','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '20226','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '20084','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '1','serialized' => '0'),
  array('setting_id' => '20225','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '0','serialized' => '0'),
  array('setting_id' => '20086','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-basket','serialized' => '0'),
  array('setting_id' => '20087','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '20088','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '20082','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '20192','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '3','serialized' => '0'),
  array('setting_id' => '20228','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '0','serialized' => '0'),
  array('setting_id' => '20079','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#ececec','serialized' => '0'),
  array('setting_id' => '20077','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '20229','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '20076','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '20075','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '20074','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '20073','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '20072','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '20071','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '20069','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '20070','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '20068','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#000000','serialized' => '0'),
  array('setting_id' => '20067','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '20062','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '20063','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#d41212','serialized' => '0'),
  array('setting_id' => '20064','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
  array('setting_id' => '20065','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '20066','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '20061','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '20060','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#000000','serialized' => '0'),
  array('setting_id' => '20055','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '20230','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '20058','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '20057','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '20056','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '20054','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '20053','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '20052','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '20051','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '20050','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '20049','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '20048','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#212121','serialized' => '0'),
  array('setting_id' => '20047','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#fbbc34','serialized' => '0'),
  array('setting_id' => '20046','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '20045','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '20044','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '20043','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '20042','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '20041','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '20040','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '20231','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '20038','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#cccccc','serialized' => '0'),
  array('setting_id' => '20037','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '20035','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
  array('setting_id' => '20036','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '20034','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '20033','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '20032','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '20031','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '20030','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '20029','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '20028','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '20027','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '20026','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '20025','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
  array('setting_id' => '20024','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '20023','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '20019','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '20021','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '20022','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '20020','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '20018','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '20017','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '20016','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '20015','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '20014','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '20013','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '20012','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '20011','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '20008','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '20009','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '20010','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '20007','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '16px','serialized' => '0'),
  array('setting_id' => '20006','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '20003','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '20004','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '20005','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0.75px','serialized' => '0'),
  array('setting_id' => '20002','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '20001','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '20000','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '19999','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '19998','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '19997','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '19996','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '20235','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '20234','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '20233','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '','serialized' => '0'),
  array('setting_id' => '20232','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '0','serialized' => '0'),
  array('setting_id' => '19991','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '19989','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
  array('setting_id' => '19990','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
  array('setting_id' => '19987','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1'),
  array('setting_id' => '19988','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1')
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