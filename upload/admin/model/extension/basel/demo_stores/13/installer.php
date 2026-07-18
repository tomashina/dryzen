<?php
class ModelExtensionBaselDemoStores13Installer extends Model {

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
  array('extension_id' => '717','type' => 'module','code' => 'category'),
  array('extension_id' => '716','type' => 'module','code' => 'account'),
  array('extension_id' => '715','type' => 'module','code' => 'basel_content'),
  array('extension_id' => '714','type' => 'module','code' => 'basel_layerslider'),
  array('extension_id' => '713','type' => 'module','code' => 'basel_megamenu'),
  array('extension_id' => '712','type' => 'module','code' => 'basel_products'),
  array('extension_id' => '718','type' => 'module','code' => 'basel_instagram')
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
  array('layout_module_id' => '212','layout_id' => '1','code' => 'basel_content.44','position' => 'top','sort_order' => '6'),
  array('layout_module_id' => '213','layout_id' => '1','code' => 'basel_instagram.45','position' => 'top','sort_order' => '7'),
  array('layout_module_id' => '211','layout_id' => '1','code' => 'basel_content.43','position' => 'top','sort_order' => '5'),
  array('layout_module_id' => '210','layout_id' => '1','code' => 'basel_content.40','position' => 'top','sort_order' => '4'),
  array('layout_module_id' => '202','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
  array('layout_module_id' => '209','layout_id' => '1','code' => 'basel_products.41','position' => 'top','sort_order' => '3'),
  array('layout_module_id' => '208','layout_id' => '1','code' => 'basel_content.38','position' => 'top','sort_order' => '2'),
  array('layout_module_id' => '207','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'top','sort_order' => '1'),
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
		
	$current_banner_id = $this->model_extension_basel_demo_stores_base->getBannerId();
	
// `basel`.`oc_module`
$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"1140","height":"600","minheight":"260","fullwidth":"1","margin_bottom":"53px","slide_transition":"basic","speed":"25","loop":"0","nav_buttons":"circle-arrows","nav_bullets":"0","nav_timer_bar":"0","g_fonts":{"1":{"import":"Playfair+Display:400i,700,700i,900,900i","name":"\'Playfair Display\', serif"}},"sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"zoom-light","bg_color":"#eeeeee","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo13\\/slideshow\\/background1.jpg","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo13\\/slideshow\\/square1.png","3":"catalog\\/basel-demo\\/demo13\\/slideshow\\/square1.png","2":"catalog\\/basel-demo\\/demo13\\/slideshow\\/square1.png"},"left":{"1":"725","3":"725","2":"725"},"top":{"1":"287","3":"287","2":"287"},"minheight":"0","transitionin":"fade()","easingin":"linear","durationin":"500","transitionout":"fade()","easingout":"linear","durationout":"500","sort_order":"1","p_index":"0","start":"0","end":"10000"},{"type":"text","description":{"1":"BEAUTY BY NATURE","3":"BEAUTY BY NATURE","2":"BEAUTY BY NATURE"},"left":{"1":"760","3":"760","2":"760"},"top":{"1":"214","3":"214","2":"214"},"font":"\'Playfair Display\', serif","fontweight":"600","fontsize":"28px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"letter-spacing:1px;","transitionin":"fade()","easingin":"linear","durationin":"500","transitionout":"fade()","easingout":"linear","durationout":"500","sort_order":"2","p_index":"0","start":"0","end":"10000"},{"type":"text","description":{"1":"Netus malesuada mi  a scelerisque vehicula&lt;br&gt;\\r\\n commodo tincint a diam a dibus fusce  fringilla&lt;br&gt;\\r\\na curae vulputate in eros.","3":"Netus malesuada mi  a scelerisque vehicula&lt;br&gt;\\r\\n commodo tincint a diam a dibus fusce  fringilla&lt;br&gt;\\r\\na curae vulputate in eros.","2":"Netus malesuada mi  a scelerisque vehicula&lt;br&gt;\\r\\n commodo tincint a diam a dibus fusce  fringilla&lt;br&gt;\\r\\na curae vulputate in eros."},"left":{"1":"749","3":"749","2":"749"},"top":{"1":"309","3":"309","2":"309"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"14px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"fade()","easingin":"linear","durationin":"500","transitionout":"fade()","easingout":"linear","durationout":"500","sort_order":"3","p_index":"0","start":"0","end":"10000"},{"type":"button","description":{"1":"Read More","3":"Read More","2":"Read More"},"left":{"1":"873","3":"873","2":"873"},"top":{"1":"383","3":"383","2":"383"},"button_class":"btn btn-tiny btn-light-outline","button_href":"","button_target":"0","transitionin":"fade()","easingin":"linear","durationin":"500","transitionout":"fade()","easingout":"linear","durationout":"500","sort_order":"4","p_index":"0","start":"0","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo13\\/slideshow\\/butterflies.png","3":"catalog\\/basel-demo\\/demo13\\/slideshow\\/butterflies.png","2":"catalog\\/basel-demo\\/demo13\\/slideshow\\/butterflies.png"},"left":{"1":"569","3":"569","2":"569"},"top":{"1":"284","3":"284","2":"284"},"minheight":"0","transitionin":"fade()","easingin":"linear","durationin":"500","transitionout":"fade()","easingout":"linear","durationout":"500","sort_order":"5","p_index":"1","start":"0","end":"10000"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"0","bg_color":"","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo13\\/slideshow\\/background2.jpg","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo13\\/slideshow\\/square2.png","3":"catalog\\/basel-demo\\/demo13\\/slideshow\\/square2.png","2":"catalog\\/basel-demo\\/demo13\\/slideshow\\/square2.png"},"left":{"1":"25","3":"25","2":"25"},"top":{"1":"297","3":"297","2":"297"},"minheight":"0","transitionin":"fade()","easingin":"linear","durationin":"500","transitionout":"fade()","easingout":"linear","durationout":"500","sort_order":"1","p_index":"0","start":"0","end":"10000"},{"type":"text","description":{"1":"BEAUTY BY NATURE","3":"BEAUTY BY NATURE","2":"BEAUTY BY NATURE"},"left":{"1":"60","3":"60","2":"60"},"top":{"1":"224","3":"224","2":"224"},"font":"\'Playfair Display\', serif","fontweight":"600","fontsize":"28px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"letter-spacing:1px;","transitionin":"fade()","easingin":"linear","durationin":"500","transitionout":"fade()","easingout":"linear","durationout":"500","sort_order":"2","p_index":"0","start":"0","end":"10000"},{"type":"text","description":{"1":"Netus malesuada mi  a scelerisque vehicula&lt;br&gt;\\r\\n commodo tincint a diam a dibus fusce  fringilla&lt;br&gt;\\r\\na curae vulputate in eros.","3":"Netus malesuada mi  a scelerisque vehicula&lt;br&gt;\\r\\n commodo tincint a diam a dibus fusce  fringilla&lt;br&gt;\\r\\na curae vulputate in eros.","2":"Netus malesuada mi  a scelerisque vehicula&lt;br&gt;\\r\\n commodo tincint a diam a dibus fusce  fringilla&lt;br&gt;\\r\\na curae vulputate in eros."},"left":{"1":"50","3":"50","2":"50"},"top":{"1":"319","3":"319","2":"319"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"14px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"fade()","easingin":"linear","durationin":"500","transitionout":"fade()","easingout":"linear","durationout":"500","sort_order":"3","p_index":"0","start":"0","end":"10000"},{"type":"button","description":{"1":"Read More","3":"Read More","2":"Read More"},"left":{"1":"173","3":"173","2":"173"},"top":{"1":"393","3":"393","2":"393"},"button_class":"btn btn-tiny btn-light-outline","button_href":"","button_target":"0","transitionin":"fade()","easingin":"linear","durationin":"500","transitionout":"fade()","easingout":"linear","durationout":"500","sort_order":"4","p_index":"0","start":"0","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo13\\/slideshow\\/butterflies.png","3":"catalog\\/basel-demo\\/demo13\\/slideshow\\/butterflies.png","2":"catalog\\/basel-demo\\/demo13\\/slideshow\\/butterflies.png"},"left":{"1":"-130","3":"-130","2":"-130"},"top":{"1":"294","3":"294","2":"294"},"minheight":"0","transitionin":"fade()","easingin":"linear","durationin":"500","transitionout":"fade()","easingout":"linear","durationout":"500","sort_order":"5","p_index":"1","start":"0","end":"10000"}]}}}'),
  array('module_id' => '38','name' => 'Banners + Newsletter Signup','code' => 'basel_content','setting' => '{"save":"stay","name":"Banners + Newsletter Signup","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"45","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"1"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo13\\/banner\\/banner1.jpg","data5":"#","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;LOVE &amp; ROMANCE&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;LOVE &amp; ROMANCE&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;LOVE &amp; ROMANCE&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing"},"data4":"catalog\\/basel-demo\\/demo13\\/banner\\/banner2.jpg","data6":"#","data8":"vertical-middle text-center","data3":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;WEDDING BOUQUET&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;WEDDING BOUQUET&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;WEDDING BOUQUET&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing"}},"2":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-middle text-center","data1":{"1":"&lt;div class=&quot;subscribe-box&quot; style=&quot;background:#9ce5dc;&quot;&gt;\\r\\n&lt;div class=&quot;subscribe-wrap&quot;&gt;\\r\\n&lt;h4 style=&quot;margin-bottom:3px&quot;&gt;NEWSLETTER&lt;\\/h4&gt;\\r\\n&lt;p  class=&quot;x-separator&quot;&gt;&lt;i class=&quot;icon-line-cross&quot;&gt;&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit &lt;\\/br&gt;amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;div class=&quot;light_field&quot;&gt;[subscribe_field] [unsubscribe_btn]&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;subscribe-box&quot; style=&quot;background:#9ce5dc;&quot;&gt;\\r\\n&lt;div class=&quot;subscribe-wrap&quot;&gt;\\r\\n&lt;h4 style=&quot;margin-bottom:3px&quot;&gt;NEWSLETTER&lt;\\/h4&gt;\\r\\n&lt;p  class=&quot;x-separator&quot;&gt;&lt;i class=&quot;icon-line-cross&quot;&gt;&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit &lt;\\/br&gt;amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;div class=&quot;light_field&quot;&gt;[subscribe_field] [unsubscribe_btn]&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;subscribe-box&quot; style=&quot;background:#9ce5dc;&quot;&gt;\\r\\n&lt;div class=&quot;subscribe-wrap&quot;&gt;\\r\\n&lt;h4 style=&quot;margin-bottom:3px&quot;&gt;NEWSLETTER&lt;\\/h4&gt;\\r\\n&lt;p  class=&quot;x-separator&quot;&gt;&lt;i class=&quot;icon-line-cross&quot;&gt;&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit &lt;\\/br&gt;amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;div class=&quot;light_field&quot;&gt;[subscribe_field] [unsubscribe_btn]&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}},"3":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo13\\/banner\\/banner3.jpg","data5":"#","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;SPRING COLLECTION&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;SPRING COLLECTION&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;SPRING COLLECTION&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing"},"data4":"catalog\\/basel-demo\\/demo13\\/banner\\/banner4.jpg","data6":"#","data8":"vertical-middle text-center","data3":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;BEST SELLERS&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;BEST SELLERS&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot; style=&quot;margin-bottom:10px&quot;&gt;BEST SELLERS&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;separator&quot;&gt;&lt;\\/p&gt;\\r\\nLorem ipsum dolor sit &lt;br&gt;amet, consectetur adipiscing"}}}}'),
  array('module_id' => '40','name' => 'Full Width Simple Promo Message','code' => 'basel_content','setting' => '{"save":"stay","name":"Full Width Simple Promo Message","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"1","block_bg":"1","bg_color":"#fc5162","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"padding:34px 10px 14px;","nm":"1","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;h3 class=&quot;contrast-font&quot; style=&quot;font-size:22px;letter-spacing:1px;margin-bottom:14px;&quot;&gt;\\r\\nSHOPPING PROCESS WITH OUR TEAM\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;opacity:0.8&quot;&gt;\\r\\nSociosqu accumsan curabitur cursus a platea inceptos magna tempor scelerisque.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;h3 class=&quot;contrast-font&quot; style=&quot;font-size:22px;letter-spacing:1px;margin-bottom:14px;&quot;&gt;\\r\\nSHOPPING PROCESS WITH OUR TEAM\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;opacity:0.8&quot;&gt;\\r\\nSociosqu accumsan curabitur cursus a platea inceptos magna tempor scelerisque.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;h3 class=&quot;contrast-font&quot; style=&quot;font-size:22px;letter-spacing:1px;margin-bottom:14px;&quot;&gt;\\r\\nSHOPPING PROCESS WITH OUR TEAM\\r\\n&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;opacity:0.8&quot;&gt;\\r\\nSociosqu accumsan curabitur cursus a platea inceptos magna tempor scelerisque.\\r\\n&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Products','code' => 'basel_products','setting' => '{"name":"Home Page Products","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"FLOWER BEST SELLERS","3":"FLOWER BEST SELLERS","2":"FLOWER BEST SELLERS"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"0","limit":"8","image_width":"262","image_height":"334","columns":"4","carousel":"0","rows":"2","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"1","margin":"40px"}'),
  array('module_id' => '43','name' => 'Instructions Banners','code' => 'basel_content','setting' => '{"save":"stay","name":"Instructions Banners","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left center","bg_repeat":"repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;instruction-box border-left&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;&lt;i&gt;\\r\\n1. Choose your gift\\r\\n&lt;\\/i&gt;&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;\\r\\nMollis consectetur ante vitae velit luctus a morbi ultrices erat habitasse turpis a duis consequat volutpat sem accumsan nam nullam pharetra eu.\\r\\n&lt;\\/p&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.google.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo13\\/banner\\/step1.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;b&gt;READ MORE&lt;\\/b&gt;&lt;\\/a&gt;&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;instruction-box border-left&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;&lt;i&gt;\\r\\n1. Choose your gift\\r\\n&lt;\\/i&gt;&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;\\r\\nMollis consectetur ante vitae velit luctus a morbi ultrices erat habitasse turpis a duis consequat volutpat sem accumsan nam nullam pharetra eu.\\r\\n&lt;\\/p&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.google.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo13\\/banner\\/step1.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;b&gt;READ MORE&lt;\\/b&gt;&lt;\\/a&gt;&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;instruction-box border-left&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;&lt;i&gt;\\r\\n1. Choose your gift\\r\\n&lt;\\/i&gt;&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;\\r\\nMollis consectetur ante vitae velit luctus a morbi ultrices erat habitasse turpis a duis consequat volutpat sem accumsan nam nullam pharetra eu.\\r\\n&lt;\\/p&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.google.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo13\\/banner\\/step1.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;b&gt;READ MORE&lt;\\/b&gt;&lt;\\/a&gt;&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;instruction-box border-left&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;&lt;i&gt;\\r\\n2. Gift packing box\\r\\n&lt;\\/i&gt;&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;\\r\\nMollis consectetur ante vitae velit luctus a morbi ultrices erat habitasse turpis a duis consequat volutpat sem accumsan nam nullam pharetra eu.\\r\\n&lt;\\/p&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.google.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo13\\/banner\\/step2.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;b&gt;READ MORE&lt;\\/b&gt;&lt;\\/a&gt;&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;instruction-box border-left&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;&lt;i&gt;\\r\\n2. Gift packing box\\r\\n&lt;\\/i&gt;&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;\\r\\nMollis consectetur ante vitae velit luctus a morbi ultrices erat habitasse turpis a duis consequat volutpat sem accumsan nam nullam pharetra eu.\\r\\n&lt;\\/p&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.google.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo13\\/banner\\/step2.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;b&gt;READ MORE&lt;\\/b&gt;&lt;\\/a&gt;&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;instruction-box border-left&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;&lt;i&gt;\\r\\n2. Gift packing box\\r\\n&lt;\\/i&gt;&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;\\r\\nMollis consectetur ante vitae velit luctus a morbi ultrices erat habitasse turpis a duis consequat volutpat sem accumsan nam nullam pharetra eu.\\r\\n&lt;\\/p&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.google.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo13\\/banner\\/step2.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;b&gt;READ MORE&lt;\\/b&gt;&lt;\\/a&gt;&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}},"3":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;instruction-box border-left&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;&lt;i&gt;\\r\\n3. Checkout &amp; Delivery\\r\\n&lt;\\/i&gt;&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;\\r\\nMollis consectetur ante vitae velit luctus a morbi ultrices erat habitasse turpis a duis consequat volutpat sem accumsan nam nullam pharetra eu.\\r\\n&lt;\\/p&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.google.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo13\\/banner\\/step3.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;b&gt;READ MORE&lt;\\/b&gt;&lt;\\/a&gt;&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;instruction-box border-left&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;&lt;i&gt;\\r\\n3. Checkout &amp; Delivery\\r\\n&lt;\\/i&gt;&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;\\r\\nMollis consectetur ante vitae velit luctus a morbi ultrices erat habitasse turpis a duis consequat volutpat sem accumsan nam nullam pharetra eu.\\r\\n&lt;\\/p&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.google.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo13\\/banner\\/step3.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;b&gt;READ MORE&lt;\\/b&gt;&lt;\\/a&gt;&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;instruction-box border-left&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;&lt;i&gt;\\r\\n3. Checkout &amp; Delivery\\r\\n&lt;\\/i&gt;&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;\\r\\nMollis consectetur ante vitae velit luctus a morbi ultrices erat habitasse turpis a duis consequat volutpat sem accumsan nam nullam pharetra eu.\\r\\n&lt;\\/p&gt;\\r\\n&lt;div class=&quot;type-img pointer&quot; onclick=&quot;location.href=\'http:\\/\\/www.google.com\';&quot;&gt;\\r\\n&lt;div class=&quot;banner_wrap hover-up&quot;&gt;\\r\\n&lt;div class=&quot;zoom_image_wrap&quot;&gt;\\r\\n&lt;img class=&quot;zoom_image&quot; src=&quot;image\\/catalog\\/basel-demo\\/demo13\\/banner\\/step3.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;span class=&quot;caption&quot;&gt;&lt;a href=&quot;#&quot;&gt;&lt;b&gt;READ MORE&lt;\\/b&gt;&lt;\\/a&gt;&lt;\\/span&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}},"4":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;a href=&quot;#&quot; class=&quot;btn btn-primary btn-tiny&quot; style=&quot;margin-top:40px&quot;&gt;More Details&lt;\\/a&gt;","3":"&lt;a href=&quot;#&quot; class=&quot;btn btn-primary btn-tiny&quot; style=&quot;margin-top:40px&quot;&gt;More Details&lt;\\/a&gt;","2":"&lt;a href=&quot;#&quot; class=&quot;btn btn-primary btn-tiny&quot; style=&quot;margin-top:40px&quot;&gt;More Details&lt;\\/a&gt;"}}}}'),
  array('module_id' => '44','name' => 'Contact Form + Map','code' => 'basel_content','setting' => '{"save":"stay","name":"Contact Form + Map","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"90","ml":"0","fw":"0","block_bg":"0","bg_color":"#222222","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"border:10px solid #f9f9f9;\\r\\npadding:40px 3.5% 0px 3.5%;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-left","data1":{"1":"&lt;div class=&quot;flower-about-block left&quot;&gt;\\r\\n&lt;h4&gt;\\r\\n&lt;b&gt;CONTACT FORM&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#555555;margin-bottom:35px;&quot;&gt;\\r\\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\\r\\n&lt;\\/p&gt;\\r\\n[contact_form]\\r\\n&lt;\\/div&gt;\\r\\n","3":"&lt;div class=&quot;flower-about-block left&quot;&gt;\\r\\n&lt;h4&gt;\\r\\n&lt;b&gt;CONTACT FORM&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#555555;margin-bottom:35px;&quot;&gt;\\r\\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\\r\\n&lt;\\/p&gt;\\r\\n[contact_form]\\r\\n&lt;\\/div&gt;\\r\\n","2":"&lt;div class=&quot;flower-about-block left&quot;&gt;\\r\\n&lt;h4&gt;\\r\\n&lt;b&gt;CONTACT FORM&lt;\\/b&gt;\\r\\n&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#555555;margin-bottom:35px;&quot;&gt;\\r\\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\\r\\n&lt;\\/p&gt;\\r\\n[contact_form]\\r\\n&lt;\\/div&gt;\\r\\n"}},"2":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-left","data1":{"1":"&lt;div class=&quot;flower-about-block right&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;ABOUT OUR SHOP&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#555555;margin-bottom:35px;&quot;&gt;\\r\\nHome to hundreds of products that can make life easier after a loss of sight, Shop CNIB is a retail enterprise created by the charity Canadian National Institute for the Blind (CNIB).&lt;\\/p&gt;\\r\\n[map]\\r\\n\\r\\n&lt;div class=&quot;row&quot;&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-envelope&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;Tel: 877-45-44-33&lt;br&gt;\\r\\nE-Mail: shop@store.uk&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-clock&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;Support forum open 24\\/7&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-cursor&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;20 Margaret St, London&lt;br&gt;\\r\\nGreat Britain, 3NM98-LK&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;Free standard shipping on all orders.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;flower-about-block right&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;ABOUT OUR SHOP&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#555555;margin-bottom:35px;&quot;&gt;\\r\\nHome to hundreds of products that can make life easier after a loss of sight, Shop CNIB is a retail enterprise created by the charity Canadian National Institute for the Blind (CNIB).&lt;\\/p&gt;\\r\\n[map]\\r\\n\\r\\n&lt;div class=&quot;row&quot;&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-envelope&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;Tel: 877-45-44-33&lt;br&gt;\\r\\nE-Mail: shop@store.uk&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-clock&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;Support forum open 24\\/7&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-cursor&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;20 Margaret St, London&lt;br&gt;\\r\\nGreat Britain, 3NM98-LK&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;Free standard shipping on all orders.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;flower-about-block right&quot;&gt;\\r\\n&lt;h4&gt;&lt;b&gt;ABOUT OUR SHOP&lt;\\/b&gt;&lt;\\/h4&gt;\\r\\n&lt;p style=&quot;color:#555555;margin-bottom:35px;&quot;&gt;\\r\\nHome to hundreds of products that can make life easier after a loss of sight, Shop CNIB is a retail enterprise created by the charity Canadian National Institute for the Blind (CNIB).&lt;\\/p&gt;\\r\\n[map]\\r\\n\\r\\n&lt;div class=&quot;row&quot;&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-envelope&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;Tel: 877-45-44-33&lt;br&gt;\\r\\nE-Mail: shop@store.uk&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-clock&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;Support forum open 24\\/7&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-cursor&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;20 Margaret St, London&lt;br&gt;\\r\\nGreat Britain, 3NM98-LK&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;div class=&quot;col-sm-6&quot;&gt;\\r\\n&lt;div class=&quot;promo-style-3&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;icon-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;p&gt;Free standard shipping on all orders.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;\\r\\n\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '45','name' => 'Instagram Feed','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed","status":"1","full_width":"1","use_title":"1","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"&lt;span class=&quot;contrast-font&quot; style=&quot;font-size:20px;color:#111111;&quot;&gt;INSTAGRAM &lt;i&gt;#bazel-flowers&lt;\\/i&gt;&lt;\\/span&gt;","3":"&lt;span class=&quot;contrast-font&quot; style=&quot;font-size:20px;color:#111111;&quot;&gt;INSTAGRAM &lt;i&gt;#bazel-flowers&lt;\\/i&gt;&lt;\\/span&gt;","2":"&lt;span class=&quot;contrast-font&quot; style=&quot;font-size:20px;color:#111111;&quot;&gt;INSTAGRAM &lt;i&gt;#bazel-flowers&lt;\\/i&gt;&lt;\\/span&gt;"},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"6","resolution":"0","columns":"6","columns_md":"3","columns_sm":"3","padding":"0","use_margin":"1","margin":"-50px"}'),
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
  array('setting_id' => '21414','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0'),
  array('setting_id' => '21413','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '21412','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '21411','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => 'nav-tabs-lg text-center','serialized' => '0'),
  array('setting_id' => '21408','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '1','serialized' => '0'),
  array('setting_id' => '21409','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header1','serialized' => '0'),
  array('setting_id' => '21410','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '21407','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '21406','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '104','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '21399','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
  array('setting_id' => '21400','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
  array('setting_id' => '21677','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '0','serialized' => '0'),
  array('setting_id' => '21402','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '21403','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-left','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '64','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '64','serialized' => '0'),
    array('setting_id' => '21405','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '21398','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
  array('setting_id' => '21397','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '21396','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
  array('setting_id' => '21678','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '0','serialized' => '0'),
  array('setting_id' => '21679','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '21393','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'default_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '21392','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'default_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '21391','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '21390','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '21389','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '21388','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '21387','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'default_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '21386','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '21385','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-left','serialized' => '0'),
  array('setting_id' => '21384','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '21383','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '21382','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '21381','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '21680','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '21379','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '1','serialized' => '0'),
  array('setting_id' => '21681','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '21377','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '21682','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '21375','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '21683','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '21690','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '21358','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '21689','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '21360','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '21361','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '21362','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '21703','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '21364','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '21365','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '1','serialized' => '0'),
  array('setting_id' => '21688','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '21687','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '21686','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '21685','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '21684','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '21371','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '21372','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '21373','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '21356','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '21691','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '21354','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '21692','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '21352','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '21695','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '21696','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '21694','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '21693','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '21351','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '21336','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '21337','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '21338','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '21339','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '21340','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '21341','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '21699','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
  array('setting_id' => '21343','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '1','serialized' => '0'),
  array('setting_id' => '21698','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '21697','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '21346','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '21335','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '21334','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-basket','serialized' => '0'),
  array('setting_id' => '21700','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '0','serialized' => '0'),
  array('setting_id' => '21332','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '1','serialized' => '0'),
  array('setting_id' => '21701','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '21330','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '21702','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '0','serialized' => '0'),
  array('setting_id' => '21475','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '1','serialized' => '0'),
  array('setting_id' => '21327','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#ececec','serialized' => '0'),
  array('setting_id' => '21704','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '21325','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '21324','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '21323','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '21322','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '21321','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '21320','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '21319','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#ff9ac5','serialized' => '0'),
  array('setting_id' => '21318','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '21317','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '21316','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#000000','serialized' => '0'),
  array('setting_id' => '21315','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#ff9ac5','serialized' => '0'),
  array('setting_id' => '21314','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '21313','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '21312','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
  array('setting_id' => '21311','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#ff9ac5','serialized' => '0'),
  array('setting_id' => '21310','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#ef7c0a','serialized' => '0'),
  array('setting_id' => '21308','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#000000','serialized' => '0'),
  array('setting_id' => '21309','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '21705','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '21306','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '21305','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '21303','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '21304','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '21302','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#ff9ac5','serialized' => '0'),
  array('setting_id' => '21299','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '21300','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '21301','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#ff9ac5','serialized' => '0'),
  array('setting_id' => '21298','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '21297','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#ff9ac5','serialized' => '0'),
  array('setting_id' => '21296','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#212121','serialized' => '0'),
  array('setting_id' => '21295','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#ff9ac5','serialized' => '0'),
  array('setting_id' => '21294','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '21293','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '21292','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '21291','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '21290','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#ff9ac5','serialized' => '0'),
  array('setting_id' => '21289','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '21288','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '21706','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '21286','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#cccccc','serialized' => '0'),
  array('setting_id' => '21285','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '21284','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '21283','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
  array('setting_id' => '21282','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '21280','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '21281','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '21279','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '21278','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '21277','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '21276','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '21275','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '21274','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '21273','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
  array('setting_id' => '21272','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '21271','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '21270','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '21269','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '21268','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '21264','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '21265','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '21266','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '21267','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '21263','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '21261','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '21262','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '21258','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '21259','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '21260','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '21253','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0.75px','serialized' => '0'),
  array('setting_id' => '21254','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '21255','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '16px','serialized' => '0'),
  array('setting_id' => '21256','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '21257','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '21245','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '21246','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '21247','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '21248','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '21249','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '21250','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '21251','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '21252','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '21244','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '21707','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '0','serialized' => '0'),
  array('setting_id' => '21708','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '','serialized' => '0'),
  array('setting_id' => '21709','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '21710','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '21239','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '21236','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '21235','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
  array('setting_id' => '21238','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '21237','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0')
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