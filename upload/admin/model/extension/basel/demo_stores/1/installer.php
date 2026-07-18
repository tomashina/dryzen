<?php
class ModelExtensionBaselDemoStores1Installer extends Model {

	public function demoSetup() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->model('extension/basel/demo_stores_base');
		
		unset($this->session->data['success']);
				
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
		
		
		//--------- DEMO STORE SPECIFIC ITEMS ----------
		
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
	  array('extension_id' => '34','type' => 'module','code' => 'basel_instagram')
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
	
	$current_banner_id = $this->model_extension_basel_demo_stores_base->getBannerId();
		
	$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"1140","height":"600","margin_bottom":"63px","speed":"25","loop":"0","minheight":"320","fullwidth":"1","slide_transition":"basic","nav_buttons":"circle-arrows","nav_bullets":"0","nav_timer_bar":"0","g_fonts":{"1":{"import":"Caveat:700","name":"\'Caveat\', cursive"}},"sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"zoom-light","bg_color":"","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo1\\/slideshow\\/background.jpg","groups":[{"type":"text","description":{"1":"2017","3":"2017","2":"2017"},"left":{"1":"0","3":"0","2":"0"},"top":{"1":"254","3":"254","2":"254"},"font":"\'Lora\', serif","fontweight":"400","fontsize":"240px","color":"rgba(255,255,221,0.25)","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOut","durationin":"500","transitionout":"top(short)","easingout":"easeInOut","durationout":"500","sort_order":"1","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"MAN BAGS","3":"MAN BAGS","2":"MAN BAGS"},"left":{"1":"-24","3":"-24","2":"-24"},"top":{"1":"254","3":"254","2":"254"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"130px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(long)","easingin":"easeInOutQuart","durationin":"600","transitionout":"left(long)","easingout":"easeInOutQuart","durationout":"500","sort_order":"2","p_index":"0","start":"600","end":"10000"},{"type":"text","description":{"1":"New Men\'s Collection","3":"New Men\'s Collection","2":"New Men\'s Collection"},"left":{"1":"-16","3":"-16","2":"-16"},"top":{"1":"350","3":"350","2":"350"},"font":"\'Caveat\', cursive","fontweight":"700","fontsize":"80px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"bottom(short)","easingin":"easeInOut","durationin":"500","transitionout":"bottom(short)","easingout":"easeOut","durationout":"500","sort_order":"3","p_index":"0","start":"500","end":"10000"},{"type":"button","description":{"1":"Learn more","3":"Learn more","2":"Learn more"},"left":{"1":"2","3":"2","2":"2"},"top":{"1":"433","3":"433","2":"433"},"button_class":"btn btn-lg btn-light-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeInOut","durationin":"500","transitionout":"bottom(short)","easingout":"easeInOut","durationout":"500","sort_order":"4","p_index":"0","start":"500","end":"10000"},{"type":"button","description":{"1":"Open Shop","3":"Open Shop","2":"Open Shop"},"left":{"1":"184","3":"184","2":"184"},"top":{"1":"433","3":"433","2":"433"},"button_class":"btn btn-lg btn-contrast","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeInOut","durationin":"500","transitionout":"bottom(short)","easingout":"easeInOut","durationout":"500","sort_order":"5","p_index":"0","start":"500","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo1\\/slideshow\\/bag.png","3":"catalog\\/basel-demo\\/demo1\\/slideshow\\/bag.png","2":"catalog\\/basel-demo\\/demo1\\/slideshow\\/bag.png"},"left":{"1":"663","3":"663","2":"663"},"top":{"1":"253","3":"253","2":"253"},"minheight":"400","transitionin":"right(long)","easingin":"easeOutBack","durationin":"500","transitionout":"right(long)","easingout":"easeOutBack","durationout":"500","sort_order":"6","p_index":"2","start":"600","end":"10000"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"0","bg_color":"#eeeeee","link":"","link_new_window":"0","thumb_image":"","groups":[{"type":"text","description":{"1":"2017","3":"2017","2":"2017"},"left":{"1":"-10","3":"-10","2":"-10"},"top":{"1":"264","3":"264","2":"264"},"font":"\'Lora\', serif","fontweight":"400","fontsize":"240px","color":"rgba(0,0,0,0.05)","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(1200)","easingin":"easeInOutQuart","durationin":"1000","transitionout":"left(1200)","easingout":"easeInOutQuart","durationout":"1000","sort_order":"1","p_index":"0","start":"400","end":"10000"},{"type":"text","description":{"1":"Boys\'","3":"Boys\'","2":"Boys\'"},"left":{"1":"10","3":"10","2":"10"},"top":{"1":"216","3":"216","2":"216"},"font":"\'Lora\', serif","fontweight":"400","fontsize":"80px","color":"#111111","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(1200)","easingin":"easeInOutQuart","durationin":"1000","transitionout":"left(1200)","easingout":"easeInOutQuart","durationout":"1000","sort_order":"2","p_index":"0","start":"440","end":"10000"},{"type":"text","description":{"1":"Collections","3":"Collections","2":"Collections"},"left":{"1":"1","3":"1","2":"1"},"top":{"1":"307","3":"307","2":"307"},"font":"\'Lora\', serif","fontweight":"400","fontsize":"80px","color":"#111111","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(1200)","easingin":"easeInOutQuart","durationin":"1000","transitionout":"left(1200)","easingout":"easeInOutQuart","durationout":"1000","sort_order":"3","p_index":"0","start":"480","end":"10000"},{"type":"text","description":{"1":"Spring has arrived. Stock up with our denim and sweatshirts&lt;br&gt;\\r\\nlatest t-shirts, denim with our and sweatshirts.","3":"Spring has arrived. Stock up with our denim and sweatshirts&lt;br&gt;\\r\\nlatest t-shirts, denim with our and sweatshirts.","2":"Spring has arrived. Stock up with our denim and sweatshirts&lt;br&gt;\\r\\nlatest t-shirts, denim with our and sweatshirts."},"left":{"1":"3","3":"3","2":"3"},"top":{"1":"382","3":"382","2":"382"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"16px","color":"#777777","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(1200)","easingin":"easeInOutQuart","durationin":"1000","transitionout":"left(1200)","easingout":"easeInOutQuart","durationout":"1000","sort_order":"4","p_index":"0","start":"520","end":"10000"},{"type":"button","description":{"1":"Shop Now","3":"Shop Now","2":"Shop Now"},"left":{"1":"17","3":"17","2":"17"},"top":{"1":"442","3":"442","2":"442"},"button_class":"btn btn-primary","button_href":"","button_target":"0","transitionin":"left(1200)","easingin":"easeInOutQuart","durationin":"1000","transitionout":"left(1200)","easingout":"easeInOutQuart","durationout":"1000","sort_order":"5","p_index":"0","start":"560","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo1\\/slideshow\\/boy.png","3":"catalog\\/basel-demo\\/demo1\\/slideshow\\/boy.png","2":"catalog\\/basel-demo\\/demo1\\/slideshow\\/boy.png"},"left":{"1":"535","3":"535","2":"535"},"top":{"1":"300","3":"300","2":"300"},"minheight":"335","transitionin":"right(1200)","easingin":"easeInOutQuart","durationin":"1000","transitionout":"right(1200)","easingout":"easeInOutQuart","durationout":"1000","sort_order":"6","p_index":"1","start":"600","end":"10000"}]}}}'),
  array('module_id' => '38','name' => 'Banner Group - Wall style 1','code' => 'basel_content','setting' => '{"save":"stay","name":"Banner Group - Wall style 1","status":"1","b_setting":{"title":"1","title_pl":{"1":"MADE THE HARD WAY","3":"MADE THE HARD WAY","2":"MADE THE HARD WAY"},"title_m":{"1":"FEATURED CATEGORIES","3":"FEATURED CATEGORIES","2":"FEATURED CATEGORIES"},"title_b":{"1":"&lt;i class=&quot;contrast-font&quot; style=&quot;font-size:16px;&quot;&gt;\\r\\nBasel &amp; Co. is a powerful eCommerce theme for WordPress. Visit our shop page to see all main features for \\r\\n&lt;a href=&quot;#&quot; class=&quot;primary-color&quot;&gt;Your Store&lt;\\/a&gt;\\r\\n&lt;\\/i&gt;","3":"&lt;i class=&quot;contrast-font&quot; style=&quot;font-size:16px;&quot;&gt;\\r\\nBasel &amp; Co. is a powerful eCommerce theme for WordPress. Visit our shop page to see all main features for \\r\\n&lt;a href=&quot;#&quot; class=&quot;primary-color&quot;&gt;Your Store&lt;\\/a&gt;\\r\\n&lt;\\/i&gt;","2":"&lt;i class=&quot;contrast-font&quot; style=&quot;font-size:16px;&quot;&gt;\\r\\nBasel &amp; Co. is a powerful eCommerce theme for WordPress. Visit our shop page to see all main features for \\r\\n&lt;a href=&quot;#&quot; class=&quot;primary-color&quot;&gt;Your Store&lt;\\/a&gt;\\r\\n&lt;\\/i&gt;"},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo1\\/banners\\/bag.jpg","data5":"#","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Bags&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Bags&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Bags&lt;\\/i&gt;"}},"2":{"w":"custom","w_sm":"col-xs-6","w_md":"col-sm-3","w_lg":"col-md-3","type":"img","data2":"catalog\\/basel-demo\\/demo1\\/banners\\/shoe.jpg","data5":"#","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Shoes&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Shoes&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Shoes&lt;\\/i&gt;"},"data4":"catalog\\/basel-demo\\/demo1\\/banners\\/woman.jpg","data6":"#","data8":"vertical-bottom text-center","data3":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Woman&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Woman&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Woman&lt;\\/i&gt;"}},"3":{"w":"custom","w_sm":"col-xs-6","w_md":"col-sm-3","w_lg":"col-md-3","type":"img","data2":"catalog\\/basel-demo\\/demo1\\/banners\\/watch.jpg","data5":"#","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Watches&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Watches&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Watches&lt;\\/i&gt;"}}}}'),
  array('module_id' => '39','name' => 'Simple Promo Block','code' => 'basel_content','setting' => '{"save":"stay","name":"Simple Promo Block","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"-65","mr":"0","mb":"60","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"background:#000000;\\r\\npadding:14px;","nm":"1","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;span style=&quot;color:#ffffff;letter-spacing:1px;&quot;&gt;\\r\\nUP TO 70% OFF THE ENTIRE STORE! \\u00b7 MADE WITH LOVE by: \\r\\n&lt;a href=&quot;#&quot; class=&quot;u-lined light&quot;&gt;OpenThemer&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n","3":"&lt;span style=&quot;color:#ffffff;letter-spacing:1px;&quot;&gt;\\r\\nUP TO 70% OFF THE ENTIRE STORE! \\u00b7 MADE WITH LOVE by: \\r\\n&lt;a href=&quot;#&quot; class=&quot;u-lined light&quot;&gt;OpenThemer&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n","2":"&lt;span style=&quot;color:#ffffff;letter-spacing:1px;&quot;&gt;\\r\\nUP TO 70% OFF THE ENTIRE STORE! \\u00b7 MADE WITH LOVE by: \\r\\n&lt;a href=&quot;#&quot; class=&quot;u-lined light&quot;&gt;OpenThemer&lt;\\/a&gt;\\r\\n&lt;\\/span&gt;\\r\\n"}}}}'),
  array('module_id' => '40','name' => 'Newsletter Subscribe - Large Signup Block','code' => 'basel_content','setting' => '{"save":"stay","name":"Newsletter Subscribe - Large Signup Block","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"1","block_bg":"0","bg_color":"","block_bgi":"1","bg_par":"6","bg_pos":"center top","bg_repeat":"repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"catalog\\/basel-demo\\/demo1\\/newsletter-bg.jpg","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-middle text-center","data1":{"1":"&lt;div class=&quot;wide-signup light large&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/newsletter-text.png&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h3&gt;&lt;b&gt;SAVE 70% OFF SALE&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p class=&quot;contrast-font&quot;&gt;&lt;i&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod.&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;div class=&quot;light_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;wide-signup light large&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/newsletter-text.png&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h3&gt;&lt;b&gt;SAVE 70% OFF SALE&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p class=&quot;contrast-font&quot;&gt;&lt;i&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod.&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;div class=&quot;light_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n[unsubscribe_btn]\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;wide-signup light large&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/newsletter-text.png&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h3&gt;&lt;b&gt;SAVE 70% OFF SALE&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p class=&quot;contrast-font&quot;&gt;&lt;i&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod.&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;div class=&quot;light_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n[unsubscribe_btn]\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Products','code' => 'basel_products','setting' => '{"name":"Home Page Products","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"MADE THE HARD WAY","3":"MADE THE HARD WAY","2":"MADE THE HARD WAY"},"title_m":{"1":"FEATURED PRODUCTS","3":"FEATURED PRODUCTS","2":"FEATURED PRODUCTS"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"10","image_width":"262","image_height":"334","columns":"4","carousel":"1","rows":"2","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '42','name' => 'Home Page Latest News','code' => 'blog_latest','setting' => '{"name":"Home Page Latest News","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"LATEST NEWS","3":"LATEST NEWS","2":"LATEST NEWS"},"title_b":{"1":"","3":"","2":""},"characters":"140","use_thumb":"1","width":"360","height":"220","limit":"3","columns":"3","carousel":"1","rows":"1","carousel_a":"1","carousel_b":"0","use_button":"1","use_margin":"0","margin":"60px"}'),
  array('module_id' => '43','name' => '[Default Skin] - About Us Block','code' => 'basel_content','setting' => '{"save":"stay","name":"[Default Skin] - About Us Block","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"1","block_bg":"0","bg_color":"","block_bgi":"1","bg_par":"0","bg_pos":"center center","bg_repeat":"repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"catalog\\/basel-demo\\/demo1\\/infoblock-bg.jpg","c_setting":{"fw":"0","block_css":"1","css":"background:#ffffff;\\r\\nborder:5px solid #efefef;\\r\\nmargin:70px 4%;\\r\\npadding:40px 4% 10px 4%;","nm":"0","eh":"1"},"columns":{"1":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;about-widget left&quot;&gt;\\r\\n&lt;p&gt;&lt;img src=&quot;image\\/catalog\\/basel-demo\\/sample-logo.png&quot; alt=&quot;&quot; \\/&gt;&lt;\\/p&gt;\\r\\n&lt;p class=&quot;spread&quot;&gt;MINIMALISTIC AJAX E-COMMERCE THEME&lt;\\/p&gt;\\r\\n&lt;p style=&quot;margin-bottom:25px;&quot;&gt;Adipiscing dignissim euismod ad venenatis volutpat sociis feugiat purus ridiculus rhoncus nullam sodales euismod ad venenatis volutpa ridiculus.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-primary btn-tiny&quot;&gt;Purchase Basel&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;about-widget left&quot;&gt;\\r\\n&lt;p&gt;&lt;img src=&quot;image\\/catalog\\/basel-demo\\/sample-logo.png&quot; alt=&quot;&quot; \\/&gt;&lt;\\/p&gt;\\r\\n&lt;p class=&quot;spread&quot;&gt;MINIMALISTIC AJAX E-COMMERCE THEME&lt;\\/p&gt;\\r\\n&lt;p style=&quot;margin-bottom:25px;&quot;&gt;Adipiscing dignissim euismod ad venenatis volutpat sociis feugiat purus ridiculus rhoncus nullam sodales euismod ad venenatis volutpa ridiculus.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-primary btn-tiny&quot;&gt;Purchase Basel&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;about-widget left&quot;&gt;\\r\\n&lt;p&gt;&lt;img src=&quot;image\\/catalog\\/basel-demo\\/sample-logo.png&quot; alt=&quot;&quot; \\/&gt;&lt;\\/p&gt;\\r\\n&lt;p class=&quot;spread&quot;&gt;MINIMALISTIC AJAX E-COMMERCE THEME&lt;\\/p&gt;\\r\\n&lt;p style=&quot;margin-bottom:25px;&quot;&gt;Adipiscing dignissim euismod ad venenatis volutpat sociis feugiat purus ridiculus rhoncus nullam sodales euismod ad venenatis volutpa ridiculus.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-primary btn-tiny&quot;&gt;Purchase Basel&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"col-sm-6","w_sm":"hidden-xs","w_md":"col-sm-6","w_lg":"col-md-6","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;about-widget border-left&quot;&gt;\\r\\n&lt;h4 class=&quot;third-font&quot;&gt;&lt;i&gt;About the shop&lt;\\/i&gt;&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-heart-o&quot;&gt;&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;p style=&quot;margin-bottom:25px;&quot;&gt;&lt;b&gt;Adipiscing dignissim&lt;\\/b&gt; - euismod ad venenatis volutpat sociis feugiat purus ridiculus rhoncus nullam sodales euismod ad venenatis volutpa ridiculus.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-outline btn-tiny&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;about-widget border-left&quot;&gt;\\r\\n&lt;h4 class=&quot;third-font&quot;&gt;&lt;i&gt;About the shop&lt;\\/i&gt;&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-heart-o&quot;&gt;&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;p style=&quot;margin-bottom:25px;&quot;&gt;&lt;b&gt;Adipiscing dignissim&lt;\\/b&gt; - euismod ad venenatis volutpat sociis feugiat purus ridiculus rhoncus nullam sodales euismod ad venenatis volutpa ridiculus.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-outline btn-tiny&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;about-widget border-left&quot;&gt;\\r\\n&lt;h4 class=&quot;third-font&quot;&gt;&lt;i&gt;About the shop&lt;\\/i&gt;&lt;\\/h4&gt;\\r\\n&lt;p class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-heart-o&quot;&gt;&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;p style=&quot;margin-bottom:25px;&quot;&gt;&lt;b&gt;Adipiscing dignissim&lt;\\/b&gt; - euismod ad venenatis volutpat sociis feugiat purus ridiculus rhoncus nullam sodales euismod ad venenatis volutpa ridiculus.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-outline btn-tiny&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '44','name' => 'Testimonials Slider','code' => 'basel_content','setting' => '{"save":"stay","name":"Testimonials Slider","status":"1","b_setting":{"title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"WHAT THEY SAY ABOUT US","3":"WHAT THEY SAY ABOUT US","2":"WHAT THEY SAY ABOUT US"},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"10","mr":"0","mb":"60","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"tm","data1":"3","data7":"1","data8":"plain"}}}'),
  array('module_id' => '45','name' => 'Instagram Feed','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed","status":"1","full_width":"0","use_title":"0","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"6","resolution":"0","columns":"3","columns_md":"3","columns_sm":"3","padding":"10","use_margin":"0","margin":"60px"}'),
  array('module_id' => '46','name' => 'Column Product List','code' => 'basel_products','setting' => '{"name":"Column Product List","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Popular Items","3":"Popular Items","2":"Popular Items"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"4","image_width":"60","image_height":"76","columns":"list","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '47','name' => 'Brands Carousel','code' => 'basel_carousel','setting' => '{"name":"Brands Carousel","status":"1","contrast":"1","use_title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"banner_id":"'.$current_banner_id.'","image_width":"200","image_height":"50","columns":"6","rows":"1","carousel_a":"0","carousel_b":"0","use_margin":"1","margin":"-50px"}'),
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
	  array('layout_module_id' => '198','layout_id' => '1','code' => 'basel_carousel.47','position' => 'bottom','sort_order' => '1'),
	  array('layout_module_id' => '201','layout_id' => '3','code' => 'category','position' => 'column_left','sort_order' => '1'),
	  array('layout_module_id' => '197','layout_id' => '1','code' => 'basel_instagram.45','position' => 'bottom_half','sort_order' => '2'),
	  array('layout_module_id' => '196','layout_id' => '1','code' => 'basel_content.44','position' => 'bottom_half','sort_order' => '1'),
	  array('layout_module_id' => '194','layout_id' => '1','code' => 'blog_latest.42','position' => 'top','sort_order' => '6'),
	  array('layout_module_id' => '195','layout_id' => '1','code' => 'basel_content.43','position' => 'top','sort_order' => '7'),
	  array('layout_module_id' => '193','layout_id' => '1','code' => 'basel_content.40','position' => 'top','sort_order' => '5'),
	  array('layout_module_id' => '191','layout_id' => '1','code' => 'basel_content.39','position' => 'top','sort_order' => '3'),
	  array('layout_module_id' => '202','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
	  array('layout_module_id' => '192','layout_id' => '1','code' => 'basel_products.41','position' => 'top','sort_order' => '4'),
	  array('layout_module_id' => '190','layout_id' => '1','code' => 'basel_content.38','position' => 'top','sort_order' => '2'),
	  array('layout_module_id' => '189','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'top','sort_order' => '1'),
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
  array('setting_id' => '1272','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1'),
  array('setting_id' => '1387','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '1388','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
  array('setting_id' => '1386','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '1385','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '2237','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '2236','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '2235','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '','serialized' => '0'),
  array('setting_id' => '2234','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1380','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '1379','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '1378','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '1377','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '1376','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '1375','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '1373','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '1374','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '1372','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '1371','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0.75px','serialized' => '0'),
  array('setting_id' => '1370','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '1368','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '16px','serialized' => '0'),
  array('setting_id' => '1369','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '1367','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '1366','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '1365','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '1363','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '1364','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '1362','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '1361','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '1360','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '1358','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '1359','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '1357','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '1356','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '1355','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '1353','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '1354','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '1352','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '1351','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
  array('setting_id' => '1349','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '1350','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '1348','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '1347','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '1346','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '1345','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '1343','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '1344','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '1342','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '1340','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1341','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '1339','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '1337','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#cccccc','serialized' => '0'),
  array('setting_id' => '2233','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1336','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '1335','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '1334','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '1333','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '1332','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '1331','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '1330','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '1329','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#fbbc34','serialized' => '0'),
  array('setting_id' => '1328','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#212121','serialized' => '0'),
  array('setting_id' => '1327','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '1326','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '1325','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '1324','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '1323','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '1322','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '1321','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '1320','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '1319','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '1318','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '2232','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '1316','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#000000','serialized' => '0'),
  array('setting_id' => '1315','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '1314','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '1313','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#d41212','serialized' => '0'),
  array('setting_id' => '1312','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
  array('setting_id' => '1311','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '1310','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '1309','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '1308','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#000000','serialized' => '0'),
  array('setting_id' => '1307','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '1306','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '1305','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '1304','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '1303','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '1302','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '1301','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '1300','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '1299','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '2231','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '1297','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#ececec','serialized' => '0'),
  array('setting_id' => '2230','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '0','serialized' => '0'),
  array('setting_id' => '2229','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '0','serialized' => '0'),
  array('setting_id' => '1294','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '2228','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '1293','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '1','serialized' => '0'),
  array('setting_id' => '2227','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '0','serialized' => '0'),
  array('setting_id' => '1290','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-basket','serialized' => '0'),
  array('setting_id' => '1289','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '1288','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '1945','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '1286','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '1285','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '1284','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '1943','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '2226','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
  array('setting_id' => '1941','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2225','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '2224','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1278','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '2222','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '2223','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '2221','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '2220','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1273','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '1271','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '2219','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '1269','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '2218','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '1267','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2217','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '1265','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2216','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '1446','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '1262','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1261','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2215','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '1259','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '1258','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '1','serialized' => '0'),
  array('setting_id' => '2214','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '2213','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '2212','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '2211','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '2210','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '1252','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '1251','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '1250','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2209','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1248','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '2208','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1246','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '2207','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1244','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '1','serialized' => '0'),
  array('setting_id' => '2206','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1242','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '1435','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '1240','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1239','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '1238','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-left','serialized' => '0'),
  array('setting_id' => '1434','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '1236','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'default_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '1235','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '1234','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '1233','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '1232','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '1231','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'default_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '1230','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'default_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '2205','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '2204','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '0','serialized' => '0'),
  array('setting_id' => '1227','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
  array('setting_id' => '1226','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '1225','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
  array('setting_id' => '1224','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
  array('setting_id' => '1223','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '2203','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '0','serialized' => '0'),
  array('setting_id' => '1221','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '1219','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-left','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '68','serialized' => '0'),
array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '70','serialized' => '0'),  array('setting_id' => '1218','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '94','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '1216','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '1215','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '1','serialized' => '0'),
  array('setting_id' => '1214','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header1','serialized' => '0'),
  array('setting_id' => '1433','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '1485','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => 'nav-tabs-lg text-center','serialized' => '0'),
  array('setting_id' => '1447','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1715','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
  array('setting_id' => '1944','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0')
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