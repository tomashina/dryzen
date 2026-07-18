<?php
class ModelExtensionBaselDemoStores2Installer extends Model {

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
			
	$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"1140","height":"476","minheight":"180","fullwidth":"1","margin_bottom":"63px","speed":"10","loop":"0","slide_transition":"basic","nav_buttons":"0","nav_bullets":"1","nav_timer_bar":"0","sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"0","bg_color":"#f4f4f4","link":"","link_new_window":"0","thumb_image":"","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo2\\/slideshow\\/arrow-back.png","3":"catalog\\/basel-demo\\/demo2\\/slideshow\\/arrow-back.png","2":"catalog\\/basel-demo\\/demo2\\/slideshow\\/arrow-back.png"},"left":{"1":"582","3":"582","2":"582"},"top":{"1":"243","3":"243","2":"243"},"minheight":"0","transitionin":"right(long)","easingin":"easeInOutQuart","durationin":"1500","transitionout":"right(long)","easingout":"easeInOutQuart","durationout":"1000","sort_order":"1","p_index":"1","start":"0","end":"9500"},{"type":"text","description":{"1":"CEILING LIGHT","3":"CEILING LIGHT","2":"CEILING LIGHT"},"left":{"1":"338","3":"338","2":"338"},"top":{"1":"167","3":"167","2":"167"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"48px","color":"#111111","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"left(long)","easingin":"easeInOutQuart","durationin":"600","transitionout":"left(long)","easingout":"easeInOutQuart","durationout":"500","sort_order":"3","p_index":"0","start":"500","end":"10000"},{"type":"button","description":{"1":"Shop other","3":"Shop other","2":"Shop other"},"left":{"1":"470","3":"470","2":"470"},"top":{"1":"327","3":"327","2":"327"},"button_class":"btn btn-link","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeInOut","durationin":"500","transitionout":"bottom(short)","easingout":"easeInOut","durationout":"500","sort_order":"4","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"&lt;i&gt;Best furniture for your castle&lt;\\/i&gt;","3":"&lt;i&gt;Best furniture for your castle&lt;\\/i&gt;","2":"&lt;i&gt;Best furniture for your castle&lt;\\/i&gt;"},"left":{"1":"368","3":"368","2":"368"},"top":{"1":"117","3":"117","2":"117"},"font":"\'Lora\', serif","fontweight":"400","fontsize":"20px","color":"#2f8661","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"fade()","easingin":"linear","durationin":"500","transitionout":"fade()","easingout":"linear","durationout":"500","sort_order":"5","p_index":"0","start":"600","end":"9500"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo2\\/slideshow\\/lamp1.png","3":"catalog\\/basel-demo\\/demo2\\/slideshow\\/lamp1.png","2":"catalog\\/basel-demo\\/demo2\\/slideshow\\/lamp1.png"},"left":{"1":"983","3":"983","2":"983"},"top":{"1":"54","3":"54","2":"54"},"minheight":"160","transitionin":"top(short)","easingin":"easeOutBack","durationin":"500","transitionout":"top(long)","easingout":"easeInOutQuart","durationout":"500","sort_order":"6","p_index":"1","start":"600","end":"9500"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo2\\/slideshow\\/lamp2.png","3":"catalog\\/basel-demo\\/demo2\\/slideshow\\/lamp2.png","2":"catalog\\/basel-demo\\/demo2\\/slideshow\\/lamp2.png"},"left":{"1":"683","3":"683","2":"683"},"top":{"1":"69","3":"69","2":"69"},"minheight":"160","transitionin":"top(short)","easingin":"easeOutBack","durationin":"600","transitionout":"top(long)","easingout":"easeInOutQuart","durationout":"500","sort_order":"7","p_index":"2","start":"700","end":"9500"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo2\\/slideshow\\/lamp3.png","3":"catalog\\/basel-demo\\/demo2\\/slideshow\\/lamp3.png","2":"catalog\\/basel-demo\\/demo2\\/slideshow\\/lamp3.png"},"left":{"1":"835","3":"835","2":"835"},"top":{"1":"162","3":"162","2":"162"},"minheight":"160","transitionin":"top(short)","easingin":"easeOutBack","durationin":"700","transitionout":"top(long)","easingout":"easeInOutQuart","durationout":"500","sort_order":"8","p_index":"3","start":"800","end":"9500"},{"type":"text","description":{"1":"$199","3":"$199","2":"$199"},"left":{"1":"378","3":"378","2":"378"},"top":{"1":"252","3":"252","2":"252"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"120px","color":"#e5e5e5","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOut","durationin":"500","transitionout":"top(short)","easingout":"easeInOut","durationout":"500","sort_order":"2","p_index":"0","start":"500","end":"10000"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"0","bg_color":"#f4f4f4","link":"","link_new_window":"0","thumb_image":"","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo2\\/slideshow\\/arrow-back.png","3":"catalog\\/basel-demo\\/demo2\\/slideshow\\/arrow-back.png","2":"catalog\\/basel-demo\\/demo2\\/slideshow\\/arrow-back.png"},"left":{"1":"582","3":"582","2":"582"},"top":{"1":"243","3":"243","2":"243"},"minheight":"0","transitionin":"right(long)","easingin":"easeInOutQuart","durationin":"500","transitionout":"right(long)","easingout":"easeInOutQuart","durationout":"1000","sort_order":"1","p_index":"1","start":"200","end":"9500"},{"type":"text","description":{"1":"YELLOW SOFA","3":"YELLOW SOFA","2":"YELLOW SOFA"},"left":{"1":"343","3":"343","2":"343"},"top":{"1":"167","3":"167","2":"167"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"48px","color":"#111111","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"600","transitionout":"top(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"3","p_index":"0","start":"1000","end":"10000"},{"type":"button","description":{"1":"Shop other","3":"Shop other","2":"Shop other"},"left":{"1":"470","3":"470","2":"470"},"top":{"1":"327","3":"327","2":"327"},"button_class":"btn btn-link","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutBack","durationin":"500","transitionout":"bottom(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"4","p_index":"0","start":"1200","end":"10000"},{"type":"text","description":{"1":"&lt;i&gt;Best furniture for your castle&lt;\\/i&gt;","3":"&lt;i&gt;Best furniture for your castle&lt;\\/i&gt;","2":"&lt;i&gt;Best furniture for your castle&lt;\\/i&gt;"},"left":{"1":"368","3":"368","2":"368"},"top":{"1":"117","3":"117","2":"117"},"font":"\'Lora\', serif","fontweight":"400","fontsize":"20px","color":"#2f8661","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"500","transitionout":"top(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"5","p_index":"0","start":"900","end":"9500"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo2\\/slideshow\\/chair.png","3":"catalog\\/basel-demo\\/demo2\\/slideshow\\/chair.png","2":"catalog\\/basel-demo\\/demo2\\/slideshow\\/chair.png"},"left":{"1":"668","3":"668","2":"668"},"top":{"1":"236","3":"236","2":"236"},"minheight":"130","transitionin":"right(1200)","easingin":"easeInOutQuart","durationin":"700","transitionout":"right(1200)","easingout":"easeInOutQuart","durationout":"500","sort_order":"8","p_index":"3","start":"1200","end":"9500"},{"type":"text","description":{"1":"$799","3":"$799","2":"$799"},"left":{"1":"368","3":"368","2":"368"},"top":{"1":"253","3":"253","2":"253"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"120px","color":"#e5e5e5","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeOutBack","durationin":"500","transitionout":"top(short)","easingout":"easeInOut","durationout":"500","sort_order":"2","p_index":"0","start":"1100","end":"10000"}]}}}'),
  array('module_id' => '40','name' => 'Newsletter Subscribe - Wide Signup Block','code' => 'basel_content','setting' => '{"save":"stay","name":"Newsletter Subscribe - Wide Signup Block","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"-50","ml":"0","fw":"1","block_bg":"1","bg_color":"#f2f2f2","block_bgi":"1","bg_par":"0","bg_pos":"left center","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"catalog\\/basel-demo\\/envelope.png","c_setting":{"fw":"0","block_css":"1","css":"padding:43px 20px 27px 20px;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-middle text-center","data1":{"1":"&lt;div class=&quot;wide-signup dark&quot;&gt;\\r\\n&lt;h3&gt;Our Newsletter&lt;\\/h3&gt;\\r\\n&lt;p&gt;It only takes a second to be the first to find out about our latest&lt;br&gt;\\r\\nnews and promotions\\u2026&lt;\\/p&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;wide-signup dark&quot;&gt;\\r\\n&lt;h3&gt;Our Newsletter&lt;\\/h3&gt;\\r\\n&lt;p&gt;It only takes a second to be the first to find out about our latest&lt;br&gt;\\r\\nnews and promotions\\u2026&lt;\\/p&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;wide-signup dark&quot;&gt;\\r\\n&lt;h3&gt;Our Newsletter&lt;\\/h3&gt;\\r\\n&lt;p&gt;It only takes a second to be the first to find out about our latest&lt;br&gt;\\r\\nnews and promotions\\u2026&lt;\\/p&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Featured','code' => 'basel_products','setting' => '{"name":"Home Page Featured","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"MADE THE HARD WAY","3":"MADE THE HARD WAY","2":"MADE THE HARD WAY"},"title_m":{"1":"FEATURED PRODUCTS","3":"FEATURED PRODUCTS","2":"FEATURED PRODUCTS"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"6","image_width":"262","image_height":"334","columns":"4","carousel":"1","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '42','name' => 'Home Page Latest News','code' => 'blog_latest','setting' => '{"name":"Home Page Latest News","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"USEFUL ARTICLES","3":"USEFUL ARTICLES","2":"USEFUL ARTICLES"},"title_m":{"1":"FROM OUR BLOG","3":"FROM OUR BLOG","2":"FROM OUR BLOG"},"title_b":{"1":"","3":"","2":""},"characters":"140","use_thumb":"1","width":"360","height":"220","limit":"4","columns":"3","carousel":"1","rows":"1","carousel_a":"1","carousel_b":"1","use_button":"0","use_margin":"0","margin":"60px"}'),
  array('module_id' => '43','name' => 'Banner Group','code' => 'basel_content','setting' => '{"save":"stay","name":"Banner Group","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"50","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-3","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo2\\/banners\\/banner1.jpg","data5":"","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;contrast-caption&quot;&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot;&gt;&lt;i&gt;Design idea&lt;\\/i&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;Choice for you&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;contrast-caption&quot;&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot;&gt;&lt;i&gt;Design idea&lt;\\/i&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;Choice for you&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;contrast-caption&quot;&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot;&gt;&lt;i&gt;Design idea&lt;\\/i&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;Choice for you&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo2\\/banners\\/banner2.jpg","data5":"","data7":"vertical-bottom text-left","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;contrast-caption&quot;&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot;&gt;&lt;i&gt;Furniture de Lus&lt;\\/i&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;Look at the entire collection&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;contrast-caption&quot;&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot;&gt;&lt;i&gt;Furniture de Lus&lt;\\/i&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;Look at the entire collection&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;contrast-caption&quot;&gt;\\r\\n&lt;h4 class=&quot;contrast-font&quot;&gt;&lt;i&gt;Furniture de Lus&lt;\\/i&gt;&lt;\\/h4&gt;\\r\\n&lt;p&gt;Look at the entire collection&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;"}},"3":{"w":"col-sm-3","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo2\\/banners\\/banner3.jpg","data5":"","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#339059;&quot;&gt;Interesting&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:12px&quot;&gt;&lt;b&gt;SHOPPER GUIDE&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read more&lt;\\/a&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#339059;&quot;&gt;Interesting&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:12px&quot;&gt;&lt;b&gt;SHOPPER GUIDE&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read more&lt;\\/a&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#339059;&quot;&gt;Interesting&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:12px&quot;&gt;&lt;b&gt;SHOPPER GUIDE&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read more&lt;\\/a&gt;"}}}}'),
  array('module_id' => '46','name' => 'Column Product List','code' => 'basel_products','setting' => '{"name":"Column Product List","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Popular Items","3":"Popular Items","2":"Popular Items"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"4","image_width":"60","image_height":"76","columns":"list","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '48','name' => 'Instagram Feed (Column Style)','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed (Column Style)","status":"1","full_width":"0","use_title":"1","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"@Instagram","3":"@Instagram","2":"@Instagram"},"title_b":{"1":"","3":"","2":""},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"9","resolution":"0","columns":"3","columns_md":"3","columns_sm":"3","padding":"5","use_margin":"0","margin":"60px"}'),
  array('module_id' => '49','name' => 'Follow Us Promo Bar','code' => 'basel_content','setting' => '{"save":"stay","name":"Follow Us Promo Bar","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"-30","mr":"0","mb":"60","ml":"0","fw":"0","block_bg":"1","bg_color":"#2f8653","block_bgi":"1","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"catalog\\/basel-demo\\/gift.png","c_setting":{"fw":"0","block_css":"0","css":"","nm":"1","eh":"1"},"columns":{"1":{"w":"col-sm-8","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-middle text-left","data1":{"1":"&lt;h4 class=&quot;follow-title&quot;&gt;Share This Page To Get 30% Discount New Collection&lt;\\/h4&gt;","3":"&lt;h4 class=&quot;follow-title&quot;&gt;Share This Page To Get 30% Discount New Collection&lt;\\/h4&gt;","2":"&lt;h4 class=&quot;follow-title&quot;&gt;Share This Page To Get 30% Discount New Collection&lt;\\/h4&gt;"}},"2":{"w":"col-sm-4","w_sm":"hidden-xs","w_md":"col-sm-6","w_lg":"col-md-6","type":"html","data7":"vertical-middle text-right","data1":{"1":"&lt;div class=&quot;follow-icons&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;fa fa-facebook&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;fa fa-twitter&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;icon-google-plus&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;fa fa-instagram&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;follow-icons&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;fa fa-facebook&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;fa fa-twitter&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;icon-google-plus&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;fa fa-instagram&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;follow-icons&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;fa fa-facebook&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;fa fa-twitter&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;icon-google-plus&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;external&quot;&gt;&lt;i class=&quot;fa fa-instagram&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '50','name' => 'Home Page Selected Items','code' => 'basel_products','setting' => '{"name":"Home Page Selected Items","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"JUST ARRIVED","3":"JUST ARRIVED","2":"JUST ARRIVED"},"title_m":{"1":"SELECTED ITEMS","3":"SELECTED ITEMS","2":"SELECTED ITEMS"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"8","image_width":"262","image_height":"334","columns":"4","carousel":"1","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '51','name' => 'Furniture Categories (Background)','code' => 'basel_content','setting' => '{"save":"stay","name":"Furniture Categories (Background)","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"-25","mr":"0","mb":"-330","ml":"0","fw":"1","block_bg":"0","bg_color":"","block_bgi":"1","bg_par":"0","bg_pos":"center top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"1","css":"height:375px;"},"bg_image":"catalog\\/basel-demo\\/demo2\\/categories-wall-bg.jpg","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"}}'),
  array('module_id' => '52','name' => 'Furniture Categories','code' => 'basel_content','setting' => '{"save":"stay","name":"Furniture Categories","status":"1","b_setting":{"title":"1","title_pl":{"1":"MADE THE HARD WAY","3":"MADE THE HARD WAY","2":"MADE THE HARD WAY"},"title_m":{"1":"FEATURED CATEGORIES","3":"FEATURED CATEGORIES","2":"FEATURED CATEGORIES"},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"1","css":"background:#ffffff;\\r\\npadding:30px 30px 0 30px;"},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"img","data2":"catalog\\/basel-demo\\/demo2\\/banners\\/category-decoration.jpg","data5":"","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Decoration&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Decoration&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Decoration&lt;\\/i&gt;"}},"2":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"img","data2":"catalog\\/basel-demo\\/demo2\\/banners\\/category-accessories.jpg","data5":"","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Accessories&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Accessories&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot; &gt;Accessories&lt;\\/i&gt;"}},"3":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"img","data2":"catalog\\/basel-demo\\/demo2\\/banners\\/category-furniture.jpg","data5":"","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Furniture&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Furniture&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Furniture&lt;\\/i&gt;"}},"4":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"img","data2":"catalog\\/basel-demo\\/demo2\\/banners\\/category-watches.jpg","data5":"","data7":"vertical-bottom text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Watches&lt;\\/i&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Watches&lt;\\/i&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;i class=&quot;simple-caption contrast-font&quot;&gt;Watches&lt;\\/i&gt;"}}}}')
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
	  array('layout_module_id' => '201','layout_id' => '3','code' => 'category','position' => 'column_left','sort_order' => '1'),
	  array('layout_module_id' => '297','layout_id' => '1','code' => 'basel_content.40','position' => 'top','sort_order' => '9'),
	  array('layout_module_id' => '296','layout_id' => '1','code' => 'blog_latest.42','position' => 'top','sort_order' => '8'),
	  array('layout_module_id' => '202','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
	  array('layout_module_id' => '205','layout_id' => '14','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
	  array('layout_module_id' => '203','layout_id' => '3','code' => 'basel_instagram.48','position' => 'column_left','sort_order' => '3'),
	  array('layout_module_id' => '206','layout_id' => '14','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3'),
	  array('layout_module_id' => '295','layout_id' => '1','code' => 'basel_content.52','position' => 'top','sort_order' => '7'),
	  array('layout_module_id' => '294','layout_id' => '1','code' => 'basel_content.51','position' => 'top','sort_order' => '6'),
	  array('layout_module_id' => '293','layout_id' => '1','code' => 'basel_products.41','position' => 'top','sort_order' => '5'),
	  array('layout_module_id' => '292','layout_id' => '1','code' => 'basel_content.49','position' => 'top','sort_order' => '4'),
	  array('layout_module_id' => '291','layout_id' => '1','code' => 'basel_products.50','position' => 'top','sort_order' => '3'),
	  array('layout_module_id' => '289','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'top','sort_order' => '1'),
	  array('layout_module_id' => '290','layout_id' => '1','code' => 'basel_content.43','position' => 'top','sort_order' => '2')
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
	  array('setting_id' => '4529','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0'),
	  array('setting_id' => '4528','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
	  array('setting_id' => '4527','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
	  array('setting_id' => '4526','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
	  array('setting_id' => '1485','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => 'nav-tabs-lg text-center','serialized' => '0'),
	  array('setting_id' => '4525','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header6','serialized' => '0'),
	  array('setting_id' => '4524','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '1','serialized' => '0'),
	  array('setting_id' => '4523','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'boxed','serialized' => '0'),
	  array('setting_id' => '4522','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '115','serialized' => '0'),
	  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
	  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
	  array('setting_id' => '4521','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'boxed','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '64','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '64','serialized' => '0'),	  array('setting_id' => '4519','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-center','serialized' => '0'),
	  array('setting_id' => '4518','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
	  array('setting_id' => '5387','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '0','serialized' => '0'),
	  array('setting_id' => '4515','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
	  array('setting_id' => '4516','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
	  array('setting_id' => '1225','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
	  array('setting_id' => '4513','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
	  array('setting_id' => '4512','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
	  array('setting_id' => '4531','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '35','serialized' => '0'),
	  array('setting_id' => '5388','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
	  array('setting_id' => '4508','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'title_in_bc tall_height_bc','serialized' => '0'),
	  array('setting_id' => '4509','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'title_in_bc tall_height_bc','serialized' => '0'),
	  array('setting_id' => '4506','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'title_in_bc tall_height_bc','serialized' => '0'),
	  array('setting_id' => '4507','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'title_in_bc tall_height_bc','serialized' => '0'),
	  array('setting_id' => '4505','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'title_in_bc tall_height_bc','serialized' => '0'),
	  array('setting_id' => '4504','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'title_in_bc tall_height_bc','serialized' => '0'),
	  array('setting_id' => '5389','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '0','serialized' => '0'),
	  array('setting_id' => '4503','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'title_in_bc tall_height_bc','serialized' => '0'),
	  array('setting_id' => '4501','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-left','serialized' => '0'),
	  array('setting_id' => '4500','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
	  array('setting_id' => '4499','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
	  array('setting_id' => '4498','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
	  array('setting_id' => '4497','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
	  array('setting_id' => '5390','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
	  array('setting_id' => '4495','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '1','serialized' => '0'),
	  array('setting_id' => '5391','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
	  array('setting_id' => '4493','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
	  array('setting_id' => '5392','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
	  array('setting_id' => '4491','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
	  array('setting_id' => '5393','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
	  array('setting_id' => '4489','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
	  array('setting_id' => '4488','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
	  array('setting_id' => '4487','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
	  array('setting_id' => '5394','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
	  array('setting_id' => '5395','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
	  array('setting_id' => '5396','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
	  array('setting_id' => '5397','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
	  array('setting_id' => '5398','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
	  array('setting_id' => '4481','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '1','serialized' => '0'),
	  array('setting_id' => '4480','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
	  array('setting_id' => '5399','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
	  array('setting_id' => '4478','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
	  array('setting_id' => '4477','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
	  array('setting_id' => '4476','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
	  array('setting_id' => '5400','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
	  array('setting_id' => '4474','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
	  array('setting_id' => '5401','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
	  array('setting_id' => '1273','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
	  array('setting_id' => '4472','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
	 array('setting_id' => '1271','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
	  array('setting_id' => '5403','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
	  array('setting_id' => '1269','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
	  array('setting_id' => '5402','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
	  array('setting_id' => '5404','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
	  array('setting_id' => '5405','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
	  array('setting_id' => '5407','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
	  array('setting_id' => '4462','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
	  array('setting_id' => '5406','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
	  array('setting_id' => '5408','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
	  array('setting_id' => '5409','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
	  array('setting_id' => '5410','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '0','serialized' => '0'),
	  array('setting_id' => '5411','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
	  array('setting_id' => '4457','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
	  array('setting_id' => '4453','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
	  array('setting_id' => '4454','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
	  array('setting_id' => '4455','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
	  array('setting_id' => '4456','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
	  array('setting_id' => '1288','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
	  array('setting_id' => '4450','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-basket','serialized' => '0'),
	  array('setting_id' => '1289','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
	  array('setting_id' => '5412','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '0','serialized' => '0'),
	  array('setting_id' => '4448','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '1','serialized' => '0'),
	  array('setting_id' => '5413','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
	  array('setting_id' => '4446','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
	  array('setting_id' => '5046','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '2','serialized' => '0'),
	  array('setting_id' => '5047','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '1','serialized' => '0'),
	  array('setting_id' => '4443','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#ffffff','serialized' => '0'),
	  array('setting_id' => '5414','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
	  array('setting_id' => '4441','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
	  array('setting_id' => '4440','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
	  array('setting_id' => '4439','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
	  array('setting_id' => '4438','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
	  array('setting_id' => '4437','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
	  array('setting_id' => '4436','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
	  array('setting_id' => '4435','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#339059','serialized' => '0'),
	  array('setting_id' => '4434','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
	  array('setting_id' => '4433','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#ffffff','serialized' => '0'),
	  array('setting_id' => '4432','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#000000','serialized' => '0'),
	  array('setting_id' => '4431','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#339059','serialized' => '0'),
	  array('setting_id' => '4430','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#ffffff','serialized' => '0'),
	  array('setting_id' => '4429','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
	  array('setting_id' => '4428','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
	  array('setting_id' => '4427','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#d41212','serialized' => '0'),
	  array('setting_id' => '4426','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#339059','serialized' => '0'),
	  array('setting_id' => '4425','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#111111','serialized' => '0'),
	  array('setting_id' => '4424','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#f9f9f9','serialized' => '0'),
	  array('setting_id' => '5415','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
	  array('setting_id' => '4422','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
	  array('setting_id' => '4421','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
	  array('setting_id' => '4420','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
	  array('setting_id' => '4419','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
	  array('setting_id' => '4418','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#339059','serialized' => '0'),
	  array('setting_id' => '4417','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#339059','serialized' => '0'),
	  array('setting_id' => '4416','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
	  array('setting_id' => '4415','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
	  array('setting_id' => '4414','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
	  array('setting_id' => '4413','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#339059','serialized' => '0'),
	  array('setting_id' => '4412','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#339059','serialized' => '0'),
	  array('setting_id' => '4411','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#fbbc34','serialized' => '0'),
	  array('setting_id' => '4410','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
	  array('setting_id' => '4409','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
	  array('setting_id' => '4408','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
	  array('setting_id' => '4407','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
	  array('setting_id' => '4406','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#339059','serialized' => '0'),
	  array('setting_id' => '4405','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
	  array('setting_id' => '4404','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
	  array('setting_id' => '5416','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '0','serialized' => '0'),
	  array('setting_id' => '4402','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#cccccc','serialized' => '0'),
	  array('setting_id' => '4401','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
	  array('setting_id' => '4399','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
	  array('setting_id' => '4400','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '600','serialized' => '0'),
	  array('setting_id' => '4398','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
	  array('setting_id' => '4397','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
	  array('setting_id' => '4396','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
	  array('setting_id' => '4395','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
	  array('setting_id' => '4394','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
	  array('setting_id' => '4393','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
	  array('setting_id' => '4392','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
	  array('setting_id' => '4391','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
	  array('setting_id' => '4390','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '600','serialized' => '0'),
	  array('setting_id' => '4389','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
	  array('setting_id' => '4388','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
	  array('setting_id' => '4387','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '600','serialized' => '0'),
	  array('setting_id' => '4386','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '34px','serialized' => '0'),
	  array('setting_id' => '4385','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'none','serialized' => '0'),
	  array('setting_id' => '4384','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
	  array('setting_id' => '4383','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
	  array('setting_id' => '4382','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '600','serialized' => '0'),
	  array('setting_id' => '4381','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '34px','serialized' => '0'),
	  array('setting_id' => '4380','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'none','serialized' => '0'),
	  array('setting_id' => '4379','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
	  array('setting_id' => '4378','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
	  array('setting_id' => '4377','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '600','serialized' => '0'),
	  array('setting_id' => '4376','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '26px','serialized' => '0'),
	  array('setting_id' => '4374','store_id' => '0','code' => 'basel','key' => 'widget_lg_','value' => '0px','serialized' => '0'),
	  array('setting_id' => '4375','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'none','serialized' => '0'),
	  array('setting_id' => '4373','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
	  array('setting_id' => '4372','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '600','serialized' => '0'),
	  array('setting_id' => '4371','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '16px','serialized' => '0'),
	  array('setting_id' => '4370','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'none','serialized' => '0'),
	  array('setting_id' => '4368','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
	  array('setting_id' => '4369','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0.75px','serialized' => '0'),
	  array('setting_id' => '4367','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '400','serialized' => '0'),
	  array('setting_id' => '4366','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
	  array('setting_id' => '4365','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'none','serialized' => '0'),
	  array('setting_id' => '4364','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
	  array('setting_id' => '4363','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
	  array('setting_id' => '4362','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
	  array('setting_id' => '4361','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
	  array('setting_id' => '4360','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
	  array('setting_id' => '5417','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '0','serialized' => '0'),
	  array('setting_id' => '5418','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '','serialized' => '0'),
	  array('setting_id' => '5419','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
	  array('setting_id' => '5420','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
	  array('setting_id' => '1386','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '1385','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
	  array('setting_id' => '4352','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
	  array('setting_id' => '4353','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
	  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
	  array('setting_id' => '1272','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1')
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