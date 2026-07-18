<?php
class ModelExtensionBaselDemoStores5Installer extends Model {

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
	  array('extension_id' => '35','type' => 'module','code' => 'basel_carousel'),
	  array('extension_id' => '33','type' => 'module','code' => 'blog_latest'),
	  array('extension_id' => '32','type' => 'module','code' => 'basel_products'),
	  array('extension_id' => '31','type' => 'module','code' => 'basel_megamenu'),
	  array('extension_id' => '30','type' => 'module','code' => 'basel_layerslider'),
	  array('extension_id' => '29','type' => 'module','code' => 'basel_content'),
	  array('extension_id' => '14','type' => 'module','code' => 'account'),
	  array('extension_id' => '13','type' => 'module','code' => 'category'),
	  array('extension_id' => '34','type' => 'module','code' => 'basel_instagram'),
	  array('extension_id' => '36','type' => 'module','code' => 'basel_categories')
	);
	
	foreach ($oc_extension as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "extension` SET "
				. "type = '" . $result['type'] . "', "
                . "code = '" . $result['code'] . "'");
    	}

	}
	
	/*--------------------------------------------
	---------------- ADD MODULES -----------------
	--------------------------------------------*/
	public function addSampleModules() {
	
	$get_category_id = $this->model_extension_basel_demo_stores_base->getCategoryId1();
	
	$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"848","height":"539","minheight":"320","fullwidth":"0","margin_bottom":"35px","speed":"10","loop":"0","slide_transition":"fade","nav_buttons":"0","nav_bullets":"1","nav_timer_bar":"0","g_fonts":{"1":{"import":"Satisfy","name":"\'Satisfy\', cursive"}},"sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"0","bg_color":"","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo5\\/slideshow\\/slide1.jpg","groups":[{"type":"text","description":{"1":"NEW","3":"NEW","2":"NEW"},"left":{"1":"174","3":"174","2":"174"},"top":{"1":"168","3":"168","2":"168"},"font":"\'Montserrat\', sans-serif","fontweight":"700","fontsize":"80px","color":"#ffffff","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"1","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"ARRIVALS","3":"ARRIVALS","2":"ARRIVALS"},"left":{"1":"61","3":"61","2":"61"},"top":{"1":"249","3":"249","2":"249"},"font":"\'Montserrat\', sans-serif","fontweight":"700","fontsize":"78px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"2","p_index":"0","start":"600","end":"10000"},{"type":"text","description":{"1":"SUMMER COLLECTION","3":"SUMMER COLLECTION","2":"SUMMER COLLECTION"},"left":{"1":"72","3":"72","2":"72"},"top":{"1":"314","3":"314","2":"314"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"38px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"3","p_index":"0","start":"700","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo5\\/slideshow\\/separator.png","3":"catalog\\/basel-demo\\/demo5\\/slideshow\\/separator.png","2":"catalog\\/basel-demo\\/demo5\\/slideshow\\/separator.png"},"left":{"1":"248","3":"248","2":"248"},"top":{"1":"361","3":"361","2":"361"},"minheight":"0","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"4","p_index":"0","start":"800","end":"10000"},{"type":"text","description":{"1":"Hot Trending","3":"Hot Trending","2":"Hot Trending"},"left":{"1":"159","3":"159","2":"159"},"top":{"1":"407","3":"407","2":"407"},"font":"\'Satisfy\', cursive","fontweight":"400","fontsize":"40px","color":"#fb9090","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"bottom(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"bottom(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"5","p_index":"0","start":"900","end":"10000"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"0","bg_color":"","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo5\\/slideshow\\/slide2.jpg","groups":[{"type":"text","description":{"1":"NEW","3":"NEW","2":"NEW"},"left":{"1":"325","3":"325","2":"325"},"top":{"1":"168","3":"168","2":"168"},"font":"\'Montserrat\', sans-serif","fontweight":"700","fontsize":"80px","color":"#ffffff","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"1","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"ARRIVALS","3":"ARRIVALS","2":"ARRIVALS"},"left":{"1":"208","3":"208","2":"208"},"top":{"1":"249","3":"249","2":"249"},"font":"\'Montserrat\', sans-serif","fontweight":"700","fontsize":"78px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"2","p_index":"0","start":"600","end":"10000"},{"type":"text","description":{"1":"SUMMER COLLECTION","3":"SUMMER COLLECTION","2":"SUMMER COLLECTION"},"left":{"1":"220","3":"220","2":"220"},"top":{"1":"314","3":"314","2":"314"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"38px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"3","p_index":"0","start":"700","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo5\\/slideshow\\/separator.png","3":"catalog\\/basel-demo\\/demo5\\/slideshow\\/separator.png","2":"catalog\\/basel-demo\\/demo5\\/slideshow\\/separator.png"},"left":{"1":"401","3":"401","2":"401"},"top":{"1":"361","3":"361","2":"361"},"minheight":"0","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"4","p_index":"0","start":"800","end":"10000"},{"type":"text","description":{"1":"Hot Trending","3":"Hot Trending","2":"Hot Trending"},"left":{"1":"310","3":"310","2":"310"},"top":{"1":"407","3":"407","2":"407"},"font":"\'Satisfy\', cursive","fontweight":"400","fontsize":"40px","color":"#fb9090","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"bottom(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"bottom(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"5","p_index":"0","start":"900","end":"10000"}]}}}'),
  array('module_id' => '38','name' => 'Banner Group - 3x - Heading','code' => 'basel_content','setting' => '{"save":"stay","name":"Banner Group - 3x - Heading","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"-30","mr":"0","mb":"60","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo5\\/banner\\/banner1.jpg","data5":"#","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4&gt;&lt;b&gt;SALE&lt;br&gt;HANDMADE SHOES&lt;\\/b&gt;&lt;\\/h4&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4&gt;&lt;b&gt;SALE&lt;br&gt;HANDMADE SHOES&lt;\\/b&gt;&lt;\\/h4&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4&gt;&lt;b&gt;SALE&lt;br&gt;HANDMADE SHOES&lt;\\/b&gt;&lt;\\/h4&gt;"}},"2":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo5\\/banner\\/banner2.jpg","data5":"#","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4&gt;&lt;b&gt;WOMAN&lt;br&gt;COLLECTION&lt;\\/b&gt;&lt;\\/h4&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4&gt;&lt;b&gt;WOMAN&lt;br&gt;COLLECTION&lt;\\/b&gt;&lt;\\/h4&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4&gt;&lt;b&gt;WOMAN&lt;br&gt;COLLECTION&lt;\\/b&gt;&lt;\\/h4&gt;"}},"3":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo5\\/banner\\/banner3.jpg","data5":"#","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4&gt;&lt;b&gt;2017&lt;br&gt;ACCESSORIES&lt;\\/b&gt;&lt;\\/h4&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4&gt;&lt;b&gt;2017&lt;br&gt;ACCESSORIES&lt;\\/b&gt;&lt;\\/h4&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h4&gt;&lt;b&gt;2017&lt;br&gt;ACCESSORIES&lt;\\/b&gt;&lt;\\/h4&gt;"}}}}'),
  array('module_id' => '49','name' => 'Home Categories Slider','code' => 'basel_categories','setting' => '{"name":"Home Categories Slider","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"TOP CATEGORIES","3":"TOP CATEGORIES","2":"TOP CATEGORIES"},"title_b":{"1":"Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad","3":"Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad","2":"Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad"},"category":["'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'"],"image_width":"262","image_height":"335","subs":"0","limit":"5","count":"1","columns":"4","carousel":"1","rows":"1","carousel_a":"1","carousel_b":"1","use_margin":"0","margin":"60px"}'),
  array('module_id' => '39','name' => 'Selling Points - 4x - Style 2','code' => 'basel_content','setting' => '{"save":"stay","name":"Selling Points - 4x - Style 2","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-thumbs-up&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;20,000+ SATISFIED CUSTOMERS&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-thumbs-up&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;20,000+ SATISFIED CUSTOMERS&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-thumbs-up&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;20,000+ SATISFIED CUSTOMERS&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;FREE WORLDWIDE SHIPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;FREE WORLDWIDE SHIPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;FREE WORLDWIDE SHIPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"3":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-undo&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;30-DAY RETURN POLICY&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-undo&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;30-DAY RETURN POLICY&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-undo&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;30-DAY RETURN POLICY&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"4":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-truck&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;TAX FREE SHOPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-truck&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;TAX FREE SHOPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;promo-style-2&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-truck&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;TAX FREE SHOPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet &lt;br&gt;consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Products','code' => 'basel_products','setting' => '{"name":"Home Page Products","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"LATEST PRODUCTS","3":"LATEST PRODUCTS","2":"LATEST PRODUCTS"},"title_b":{"1":"Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad","3":"Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad","2":"Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad"},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"8","image_width":"262","image_height":"334","columns":"4","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"1","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"1","margin":"35px"}'),
  array('module_id' => '42','name' => 'Home Page Latest News','code' => 'blog_latest','setting' => '{"name":"Home Page Latest News","status":"1","contrast":"1","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"FASHION MAGAZINE","3":"FASHION MAGAZINE","2":"FASHION MAGAZINE"},"title_b":{"1":"Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad","3":"Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad","2":"Lorem ipsum dolor sit amet, consectetur adipiscin lorem solo tempor incididunt ut labore et sina tornad"},"characters":"0","use_thumb":"1","width":"360","height":"220","limit":"3","columns":"3","carousel":"1","rows":"1","carousel_a":"0","carousel_b":"1","use_button":"0","use_margin":"0","margin":"60px"}'),
  array('module_id' => '45','name' => 'Instagram Feed','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed","status":"1","full_width":"1","use_title":"1","title_inline":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"INSTAGRAM","3":"INSTAGRAM","2":"INSTAGRAM"},"title_b":{"1":"Posuere eget ut sed consectetur litora&lt;br&gt;\\r\\nlobortis cras&lt;br&gt;\\r\\n&lt;a href=&quot;http:\\/\\/www.instagram.com\\/envato&quot; class=&quot;btn btn-primary btn-tiny external&quot;&gt;View Profile&lt;\\/a&gt;\\r\\n","3":"Posuere eget ut sed consectetur litora&lt;br&gt;\\r\\nlobortis cras&lt;br&gt;\\r\\n&lt;a href=&quot;http:\\/\\/www.instagram.com\\/envato&quot; class=&quot;btn btn-primary btn-tiny external&quot;&gt;View Profile&lt;\\/a&gt;","2":"Posuere eget ut sed consectetur litora&lt;br&gt;\\r\\nlobortis cras&lt;br&gt;\\r\\n&lt;a href=&quot;http:\\/\\/www.instagram.com\\/envato&quot; class=&quot;btn btn-primary btn-tiny external&quot;&gt;View Profile&lt;\\/a&gt;"},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"12","resolution":"0","columns":"6","columns_md":"4","columns_sm":"3","padding":"0","use_margin":"1","margin":"-50px"}'),
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
	  array('layout_module_id' => '247','layout_id' => '1','code' => 'basel_content.39','position' => 'bottom','sort_order' => '5'),
	  array('layout_module_id' => '251','layout_id' => '3','code' => 'basel_instagram.48','position' => 'column_left','sort_order' => '3'),
	  array('layout_module_id' => '246','layout_id' => '1','code' => 'blog_latest.42','position' => 'bottom','sort_order' => '4'),
	  array('layout_module_id' => '245','layout_id' => '1','code' => 'basel_categories.49','position' => 'bottom','sort_order' => '3'),
	  array('layout_module_id' => '244','layout_id' => '1','code' => 'basel_products.41','position' => 'bottom','sort_order' => '2'),
	  array('layout_module_id' => '250','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
	  array('layout_module_id' => '243','layout_id' => '1','code' => 'basel_content.38','position' => 'bottom','sort_order' => '1'),
	  array('layout_module_id' => '242','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'content_top','sort_order' => '1'),
	  array('layout_module_id' => '205','layout_id' => '14','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
	  array('layout_module_id' => '249','layout_id' => '3','code' => 'basel_megamenu.36','position' => 'column_left','sort_order' => '1'),
	  array('layout_module_id' => '206','layout_id' => '14','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3'),
	  array('layout_module_id' => '241','layout_id' => '1','code' => 'basel_megamenu.36','position' => 'column_left','sort_order' => '1'),
	  array('layout_module_id' => '248','layout_id' => '1','code' => 'basel_instagram.45','position' => 'bottom','sort_order' => '6'),
	  array('layout_module_id' => '253','layout_id' => '2','code' => 'basel_megamenu.36','position' => 'column_left','sort_order' => '0'),
	  array('layout_module_id' => '254','layout_id' => '2','code' => 'basel_products.46','position' => 'column_left','sort_order' => '0')
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
	  array('layout_route_id' => '89','layout_id' => '1','store_id' => '0','route' => 'common/home'),
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
	---------------- ADD SETTINGS ----------------
	--------------------------------------------*/
	public function addSampleSettings() {
	
	$this->db->query("DELETE FROM `".DB_PREFIX."setting` WHERE `code` = 'basel'");
		
	$oc_setting = array(
array('setting_id' => '4529','store_id' => '0','code' => 'basel','key' => 'top_line_height','value' => '41','serialized' => '0'),
  array('setting_id' => '2187','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0'),
  array('setting_id' => '2186','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '2185','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2602','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
  array('setting_id' => '2174','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '5262','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '0','serialized' => '0'),
  array('setting_id' => '2176','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '2177','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-center','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '64','serialized' => '0'),
array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '64','serialized' => '0'),  array('setting_id' => '2179','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'boxed','serialized' => '0'),
  array('setting_id' => '2180','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '104','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '2181','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'boxed','serialized' => '0'),
  array('setting_id' => '2182','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '1','serialized' => '0'),
  array('setting_id' => '2183','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header2','serialized' => '0'),
  array('setting_id' => '2184','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '1485','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => '','serialized' => '0'),
  array('setting_id' => '2172','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
  array('setting_id' => '2171','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '2170','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
  array('setting_id' => '3897','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '36','serialized' => '0'),
  array('setting_id' => '5263','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '2167','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '2166','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '2165','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '2164','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '2163','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '2162','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '2155','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '3757','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '2157','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '3686','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '2159','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-bottom','serialized' => '0'),
  array('setting_id' => '5264','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '0','serialized' => '0'),
  array('setting_id' => '2161','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '2147','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '5269','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '2149','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '5268','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '2151','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '5267','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '5266','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '0','serialized' => '0'),
  array('setting_id' => '5265','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '5271','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '5270','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '2145','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '2146','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '5272','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '5274','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '5273','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '2139','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '1','serialized' => '0'),
  array('setting_id' => '2138','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '5275','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '2136','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2135','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2134','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '5277','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '2132','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '5276','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '2130','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '5278','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '2128','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '5279','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '2126','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '2125','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '5280','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '5281','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '5283','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '2428','store_id' => '0','code' => 'basel','key' => 'basel_fonts','value' => '{"1":{"import":"Lora:400,400i","name":"\'Lora\', serif"},"2":{"import":"Montserrat:400,600,700","name":"\'Montserrat\', sans-serif"},"3":{"import":"Karla:400,400i,700","name":"\'Karla\', sans-serif"}}','serialized' => '1'),
  array('setting_id' => '5282','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '2120','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '5284','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '5285','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '5286','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2114','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '5287','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
  array('setting_id' => '2115','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '2113','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '2111','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '2112','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '2110','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '2109','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '5288','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '0','serialized' => '0'),
  array('setting_id' => '2108','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-basket','serialized' => '0'),
  array('setting_id' => '5290','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '0','serialized' => '0'),
  array('setting_id' => '5289','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '2104','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '2356','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2355','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '2','serialized' => '0'),
  array('setting_id' => '2101','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '5291','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '2099','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '2098','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '2097','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '2096','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '2095','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '2094','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '2093','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '2091','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '2092','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '2090','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#000000','serialized' => '0'),
  array('setting_id' => '2088','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '2089','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#111111','serialized' => '0'),
  array('setting_id' => '2087','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '2086','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
  array('setting_id' => '2085','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#d41212','serialized' => '0'),
  array('setting_id' => '2084','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '2083','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '2082','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#fafafa','serialized' => '0'),
  array('setting_id' => '5292','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '2079','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '2080','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '2078','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '2077','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '2074','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '2075','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '2076','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#333333','serialized' => '0'),
  array('setting_id' => '2073','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '2072','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '2069','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#fbbc34','serialized' => '0'),
  array('setting_id' => '2070','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#212121','serialized' => '0'),
  array('setting_id' => '2071','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '2067','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '2068','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '2066','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '2065','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '2064','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '2063','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '2062','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '2059','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '2427','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2060','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#666666','serialized' => '0'),
  array('setting_id' => '3345','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2058','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '2056','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => '\'Lora\', serif','serialized' => '0'),
  array('setting_id' => '2055','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '2052','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '2053','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '2054','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '2048','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '2049','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '2050','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => '\'Montserrat\', sans-serif','serialized' => '0'),
  array('setting_id' => '2051','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '2047','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
  array('setting_id' => '2046','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => '\'Montserrat\', sans-serif','serialized' => '0'),
  array('setting_id' => '2045','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '2044','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '2043','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '2042','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '2041','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => '\'Montserrat\', sans-serif','serialized' => '0'),
  array('setting_id' => '2040','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '2039','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '2038','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '2037','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '2036','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => '\'Montserrat\', sans-serif','serialized' => '0'),
  array('setting_id' => '2035','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '2034','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '2032','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '2033','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '2031','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => '\'Montserrat\', sans-serif','serialized' => '0'),
  array('setting_id' => '2030','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '2029','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '16px','serialized' => '0'),
  array('setting_id' => '2028','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '2027','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0.75px','serialized' => '0'),
  array('setting_id' => '2026','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '2024','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '2025','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '2023','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '2022','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '2021','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '2020','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '5294','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '','serialized' => '0'),
  array('setting_id' => '5293','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '0','serialized' => '0'),
  array('setting_id' => '2018','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '2019','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '5296','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '5295','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '2011','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
  array('setting_id' => '1386','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
  array('setting_id' => '1385','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '2010','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '2009','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1')
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