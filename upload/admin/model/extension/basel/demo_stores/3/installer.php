<?php
class ModelExtensionBaselDemoStores3Installer extends Model {

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
	
	$current_banner_id = $this->model_extension_basel_demo_stores_base->getBannerId();
	
	$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"1140","height":"668","minheight":"380","fullwidth":"1","margin_bottom":"63px","speed":"10","loop":"0","slide_transition":"fade","nav_buttons":"simple-arrows","nav_bullets":"0","nav_timer_bar":"0","g_fonts":{"1":{"import":"Satisfy","name":"\'Satisfy\', cursive"}},"sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"0","bg_color":"","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo3\\/slideshow\\/slide1.jpg","groups":[{"type":"text","description":{"1":"New Collections","3":"New Collections","2":"New Collections"},"left":{"1":"457","3":"457","2":"457"},"top":{"1":"180","3":"180","2":"180"},"font":"\'Satisfy\', cursive","fontweight":"400","fontsize":"39px","color":"#ee4684","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"back(200)","easingin":"easeOutBack","durationin":"2500","transitionout":"back(200)","easingout":"ease","durationout":"800","sort_order":"1","p_index":"0","start":"400","end":"10000"},{"type":"text","description":{"1":"BRAND YOU MUST HAVE","3":"BRAND YOU MUST HAVE","2":"BRAND YOU MUST HAVE"},"left":{"1":"144","3":"144","2":"144"},"top":{"1":"274","3":"274","2":"274"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"72px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"back(200)","easingin":"ease","durationin":"900","transitionout":"back(200)","easingout":"ease","durationout":"900","sort_order":"2","p_index":"0","start":"600","end":"10000"},{"type":"text","description":{"1":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a.","3":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a.","2":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a."},"left":{"1":"278","3":"278","2":"278"},"top":{"1":"351","3":"351","2":"351"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"14px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"back(200)","easingin":"ease","durationin":"900","transitionout":"back(200)","easingout":"ease","durationout":"900","sort_order":"3","p_index":"0","start":"800","end":"10000"},{"type":"button","description":{"1":"Shop Now","3":"Shop Now","2":"Shop Now"},"left":{"1":"509","3":"509","2":"509"},"top":{"1":"420","3":"420","2":"420"},"button_class":"btn btn-light-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"ease","durationin":"700","transitionout":"bottom(short)","easingout":"ease","durationout":"700","sort_order":"4","p_index":"0","start":"900","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo3\\/slideshow\\/separator.png","3":"catalog\\/basel-demo\\/demo3\\/slideshow\\/separator.png","2":"catalog\\/basel-demo\\/demo3\\/slideshow\\/separator.png"},"left":{"1":"534","3":"534","2":"534"},"top":{"1":"221","3":"221","2":"221"},"minheight":"0","transitionin":"back(200)","easingin":"ease","durationin":"900","transitionout":"back(200)","easingout":"ease","durationout":"900","sort_order":"5","p_index":"0","start":"400","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo3\\/slideshow\\/salebadge.png","3":"catalog\\/basel-demo\\/demo3\\/slideshow\\/salebadge.png","2":"catalog\\/basel-demo\\/demo3\\/slideshow\\/salebadge.png"},"left":{"1":"915","3":"915","2":"915"},"top":{"1":"215","3":"215","2":"215"},"minheight":"0","transitionin":"front(1500)","easingin":"easeInOutQuart","durationin":"1500","transitionout":"rotatefront(300,800,c,true)","easingout":"easeInOutQuart","durationout":"500","sort_order":"6","p_index":"0","start":"600","end":"10000"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"0","bg_color":"","link":"","link_new_window":"0","thumb_image":"catalog\\/basel-demo\\/demo3\\/slideshow\\/slide2.jpg","groups":[{"type":"text","description":{"1":"New Collections","3":"New Collections","2":"New Collections"},"left":{"1":"457","3":"457","2":"457"},"top":{"1":"180","3":"180","2":"180"},"font":"\'Satisfy\', cursive","fontweight":"400","fontsize":"39px","color":"#ee4684","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"back(200)","easingin":"easeOutBack","durationin":"2500","transitionout":"back(200)","easingout":"ease","durationout":"800","sort_order":"1","p_index":"0","start":"400","end":"9500"},{"type":"text","description":{"1":"BEST LINGERIE EVER","3":"BEST LINGERIE EVER","2":"BEST LINGERIE EVER"},"left":{"1":"194","3":"194","2":"194"},"top":{"1":"274","3":"274","2":"274"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"72px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"back(200)","easingin":"ease","durationin":"900","transitionout":"back(200)","easingout":"ease","durationout":"900","sort_order":"2","p_index":"0","start":"600","end":"9500"},{"type":"text","description":{"1":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a.","3":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a.","2":"Suspendisse ridiculus parturient ac a dui cursus interdum dignissim netus habitant&lt;br&gt;\\r\\nultrices et mattis urna sem a euismod a adipiscing faucibus a."},"left":{"1":"278","3":"278","2":"278"},"top":{"1":"351","3":"351","2":"351"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"14px","color":"#ffffff","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"back(200)","easingin":"ease","durationin":"900","transitionout":"back(200)","easingout":"ease","durationout":"900","sort_order":"3","p_index":"0","start":"800","end":"9500"},{"type":"button","description":{"1":"Shop Now","3":"Shop Now","2":"Shop Now"},"left":{"1":"509","3":"509","2":"509"},"top":{"1":"420","3":"420","2":"420"},"button_class":"btn btn-light-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"ease","durationin":"700","transitionout":"bottom(short)","easingout":"ease","durationout":"700","sort_order":"4","p_index":"0","start":"900","end":"9500"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo3\\/slideshow\\/separator.png","3":"catalog\\/basel-demo\\/demo3\\/slideshow\\/separator.png","2":"catalog\\/basel-demo\\/demo3\\/slideshow\\/separator.png"},"left":{"1":"534","3":"534","2":"534"},"top":{"1":"221","3":"221","2":"221"},"minheight":"0","transitionin":"back(200)","easingin":"ease","durationin":"900","transitionout":"back(200)","easingout":"ease","durationout":"900","sort_order":"5","p_index":"0","start":"400","end":"9500"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo3\\/slideshow\\/salebadge.png","3":"catalog\\/basel-demo\\/demo3\\/slideshow\\/salebadge.png","2":"catalog\\/basel-demo\\/demo3\\/slideshow\\/salebadge.png"},"left":{"1":"850","3":"850","2":"850"},"top":{"1":"220","3":"220","2":"220"},"minheight":"0","transitionin":"front(1500)","easingin":"easeInOutQuart","durationin":"1500","transitionout":"rotatefront(300,800,c,true)","easingout":"easeInOutQuart","durationout":"500","sort_order":"6","p_index":"0","start":"600","end":"9500"}]}}}'),
  array('module_id' => '40','name' => 'Banner Group - 3x - Heading','code' => 'basel_content','setting' => '{"save":"stay","name":"Banner Group - 3x - Heading","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"-103","mr":"0","mb":"30","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo3\\/banner\\/banner1.jpg","data5":"#","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;HOT BIKINIS&lt;\\/b&gt;&lt;\\/h3&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;HOT BIKINIS&lt;\\/b&gt;&lt;\\/h3&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;HOT BIKINIS&lt;\\/b&gt;&lt;\\/h3&gt;"}},"2":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo3\\/banner\\/banner2.jpg","data5":"#","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;SEXY SWIMSUIT&lt;\\/b&gt;&lt;\\/h3&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;SEXY SWIMSUIT&lt;\\/b&gt;&lt;\\/h3&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;SEXY SWIMSUIT&lt;\\/b&gt;&lt;\\/h3&gt;"}},"3":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo3\\/banner\\/banner3.jpg","data5":"#","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;BEACH CLOTHING&lt;\\/b&gt;&lt;\\/h3&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;BEACH CLOTHING&lt;\\/b&gt;&lt;\\/h3&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;BEACH CLOTHING&lt;\\/b&gt;&lt;\\/h3&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Products','code' => 'basel_products','setting' => '{"name":"Home Page Products","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"MADE THE HARD WAY","3":"MADE THE HARD WAY","2":"MADE THE HARD WAY"},"title_m":{"1":"FEATURED PRODUCTS","3":"FEATURED PRODUCTS","2":"FEATURED PRODUCTS"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"8","image_width":"262","image_height":"334","columns":"4","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"1","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '42','name' => 'Home Page Latest News','code' => 'blog_latest','setting' => '{"name":"Home Page Latest News","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"BIKINI FASHION NEWS","3":"BIKINI FASHION NEWS","2":"BIKINI FASHION NEWS"},"title_b":{"1":"","3":"","2":""},"characters":"0","use_thumb":"1","width":"263","height":"161","limit":"4","columns":"4","carousel":"1","rows":"1","carousel_a":"0","carousel_b":"0","use_button":"0","use_margin":"1","margin":"10px"}'),
  array('module_id' => '43','name' => 'Full Width Block - Newsletter + Banner','code' => 'basel_content','setting' => '{"save":"stay","name":"Full Width Block - Newsletter + Banner","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"100","ml":"0","fw":"1","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left center","bg_repeat":"repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"1","block_css":"0","css":"","nm":"1","eh":"1"},"columns":{"1":{"w":"col-sm-6","w_sm":"col-xs-12","w_md":"col-sm-12","w_lg":"col-md-4","type":"html","data7":"vertical-middle text-center","data1":{"1":"&lt;div class=&quot;widget-border&quot; style=&quot;border-color:#f0fafd;&quot;&gt;&lt;\\/div&gt;\\r\\n&lt;div class=&quot;widget-border-wrap newsletter&quot;&gt;\\r\\n&lt;p class=&quot;spread small&quot;&gt;NEWSLETTER WIDGET&lt;\\/p&gt;\\r\\n&lt;h3&gt;&lt;b&gt;Join Our Newsletter&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p class=&quot;summary&quot;&gt;&lt;b&gt;Basel &amp; Company&lt;\\/b&gt; \\u2013 is a famous worldwide fashion store dipiscing dignissim euismod ad venenatis volutpat sociis feugiat purus ridiculus.&lt;\\/p&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;[subscribe_field]&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;widget-border&quot; style=&quot;border-color:#f0fafd;&quot;&gt;&lt;\\/div&gt;\\r\\n&lt;div class=&quot;widget-border-wrap newsletter&quot;&gt;\\r\\n&lt;p class=&quot;spread small&quot;&gt;NEWSLETTER WIDGET&lt;\\/p&gt;\\r\\n&lt;h3&gt;&lt;b&gt;Join Our Newsletter&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p class=&quot;summary&quot;&gt;&lt;b&gt;Basel &amp; Company&lt;\\/b&gt; \\u2013 is a famous worldwide fashion store dipiscing dignissim euismod ad venenatis volutpat sociis feugiat purus ridiculus.&lt;\\/p&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;[subscribe_field]&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;widget-border&quot; style=&quot;border-color:#f0fafd;&quot;&gt;&lt;\\/div&gt;\\r\\n&lt;div class=&quot;widget-border-wrap newsletter&quot;&gt;\\r\\n&lt;p class=&quot;spread small&quot;&gt;NEWSLETTER WIDGET&lt;\\/p&gt;\\r\\n&lt;h3&gt;&lt;b&gt;Join Our Newsletter&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p class=&quot;summary&quot;&gt;&lt;b&gt;Basel &amp; Company&lt;\\/b&gt; \\u2013 is a famous worldwide fashion store dipiscing dignissim euismod ad venenatis volutpat sociis feugiat purus ridiculus.&lt;\\/p&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;[subscribe_field]&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo3\\/banner\\/banner-lg-2.jpg","data5":"","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo3\\/newsletter-text.png&quot; alt=&quot;&quot; \\/&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo3\\/banner-img-overlay.png&quot; alt=&quot;&quot; \\/&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo3\\/banner-img-overlay.png&quot; alt=&quot;&quot; \\/&gt;"}}}}'),
  array('module_id' => '44','name' => 'Full Width Block - Banner + About','code' => 'basel_content','setting' => '{"save":"stay","name":"Full Width Block - Banner + About","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"10","mr":"0","mb":"0","ml":"0","fw":"1","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left center","bg_repeat":"repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"1","block_css":"0","css":"","nm":"1","eh":"1"},"columns":{"1":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo3\\/banner\\/banner-lg-1.jpg","data5":"","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo3\\/banner-img-overlay.png&quot; alt=&quot;&quot; \\/&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo3\\/banner-img-overlay.png&quot; alt=&quot;&quot; \\/&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo3\\/banner-img-overlay.png&quot; alt=&quot;&quot; \\/&gt;"}},"2":{"w":"col-sm-6","w_sm":"col-xs-12","w_md":"col-sm-12","w_lg":"col-md-4","type":"html","data7":"vertical-middle text-center","data1":{"1":"&lt;div class=&quot;widget-border&quot; style=&quot;border-color:#f0fafd;&quot;&gt;&lt;\\/div&gt;\\r\\n&lt;div class=&quot;widget-border-wrap&quot;&gt;\\r\\n&lt;div class=&quot;about-widget&quot;&gt;\\r\\n&lt;p&gt;&lt;img src=&quot;image\\/catalog\\/basel-demo\\/sample-logo.png&quot; alt=&quot;&quot; \\/&gt;&lt;\\/p&gt;\\r\\n&lt;p class=&quot;spread&quot;&gt;MINIMALISTIC AJAX E-COMMERCE THEME&lt;\\/p&gt;\\r\\n&lt;p style=&quot;margin-bottom:25px;&quot;&gt;Adipiscing dignissim euismod ad venenatis volutpat sociis feugiat purus ridiculus rhoncus nullam sodales euismod ad venenatis volutpa ridiculus.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-primary btn-tiny&quot;&gt;Purchase Basel&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;widget-border&quot; style=&quot;border-color:#f0fafd;&quot;&gt;&lt;\\/div&gt;\\r\\n&lt;div class=&quot;widget-border-wrap&quot;&gt;\\r\\n&lt;div class=&quot;about-widget&quot;&gt;\\r\\n&lt;p&gt;&lt;img src=&quot;image\\/catalog\\/basel-demo\\/sample-logo.png&quot; alt=&quot;&quot; \\/&gt;&lt;\\/p&gt;\\r\\n&lt;p class=&quot;spread&quot;&gt;MINIMALISTIC AJAX E-COMMERCE THEME&lt;\\/p&gt;\\r\\n&lt;p style=&quot;margin-bottom:25px;&quot;&gt;Adipiscing dignissim euismod ad venenatis volutpat sociis feugiat purus ridiculus rhoncus nullam sodales euismod ad venenatis volutpa ridiculus.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-primary btn-tiny&quot;&gt;Purchase Basel&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;widget-border&quot; style=&quot;border-color:#f0fafd;&quot;&gt;&lt;\\/div&gt;\\r\\n&lt;div class=&quot;widget-border-wrap&quot;&gt;\\r\\n&lt;div class=&quot;about-widget&quot;&gt;\\r\\n&lt;p&gt;&lt;img src=&quot;image\\/catalog\\/basel-demo\\/sample-logo.png&quot; alt=&quot;&quot; \\/&gt;&lt;\\/p&gt;\\r\\n&lt;p class=&quot;spread&quot;&gt;MINIMALISTIC AJAX E-COMMERCE THEME&lt;\\/p&gt;\\r\\n&lt;p style=&quot;margin-bottom:25px;&quot;&gt;Adipiscing dignissim euismod ad venenatis volutpat sociis feugiat purus ridiculus rhoncus nullam sodales euismod ad venenatis volutpa ridiculus.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-primary btn-tiny&quot;&gt;Purchase Basel&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '45','name' => 'Instagram Feed','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed","status":"1","full_width":"1","use_title":"1","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"FOLLOW @INSTAGRAM","3":"FOLLOW @INSTAGRAM","2":"FOLLOW @INSTAGRAM"},"title_b":{"1":"","3":"","2":""},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"12","resolution":"0","columns":"6","columns_md":"6","columns_sm":"4","padding":"0","use_margin":"1","margin":"-50px"}'),
  array('module_id' => '46','name' => 'Column Product List','code' => 'basel_products','setting' => '{"name":"Column Product List","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Popular Items","3":"Popular Items","2":"Popular Items"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"4","image_width":"60","image_height":"76","columns":"list","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '47','name' => 'Brands Carousel','code' => 'basel_carousel','setting' => '{"name":"Brands Carousel","status":"1","contrast":"0","use_title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"banner_id":"'.$current_banner_id.'","image_width":"200","image_height":"50","columns":"6","rows":"1","carousel_a":"0","carousel_b":"0","use_margin":"0","margin":""}'),
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
	  array('layout_module_id' => '250','layout_id' => '1','code' => 'basel_carousel.47','position' => 'top','sort_order' => '3'),
	  array('layout_module_id' => '201','layout_id' => '3','code' => 'category','position' => 'column_left','sort_order' => '1'),
	  array('layout_module_id' => '255','layout_id' => '1','code' => 'basel_instagram.45','position' => 'top','sort_order' => '8'),
	  array('layout_module_id' => '202','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
	  array('layout_module_id' => '254','layout_id' => '1','code' => 'blog_latest.42','position' => 'top','sort_order' => '7'),
	  array('layout_module_id' => '253','layout_id' => '1','code' => 'basel_content.43','position' => 'top','sort_order' => '6'),
	  array('layout_module_id' => '252','layout_id' => '1','code' => 'basel_content.44','position' => 'top','sort_order' => '5'),
	  array('layout_module_id' => '205','layout_id' => '14','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
	  array('layout_module_id' => '203','layout_id' => '3','code' => 'basel_instagram.48','position' => 'column_left','sort_order' => '3'),
	  array('layout_module_id' => '206','layout_id' => '14','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3'),
	  array('layout_module_id' => '251','layout_id' => '1','code' => 'basel_products.41','position' => 'top','sort_order' => '4'),
	  array('layout_module_id' => '249','layout_id' => '1','code' => 'basel_content.40','position' => 'top','sort_order' => '2'),
	  array('layout_module_id' => '248','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'top','sort_order' => '1')
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
  array('setting_id' => '1061','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '1060','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
  array('setting_id' => '1054','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'title_in_bc extra_tall_height_bc','serialized' => '0'),
  array('setting_id' => '2136','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '2135','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '0','serialized' => '0'),
  array('setting_id' => '1057','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
  array('setting_id' => '1058','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '1059','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
  array('setting_id' => '1053','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'minimal_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '1051','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'minimal_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '1052','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'title_in_bc','serialized' => '0'),
  array('setting_id' => '2137','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '0','serialized' => '0'),
  array('setting_id' => '1048','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'minimal_bc full_width_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '1049','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'title_in_bc tall_height_bc','serialized' => '0'),
  array('setting_id' => '1050','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'title_in_bc extra_tall_height_bc','serialized' => '0'),
  array('setting_id' => '1046','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-left','serialized' => '0'),
  array('setting_id' => '1045','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '2139','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1040','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '1','serialized' => '0'),
  array('setting_id' => '2138','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1042','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '1043','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '1044','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1038','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '1034','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2141','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1036','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '2140','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '2147','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '1025','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '1026','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '4','serialized' => '0'),
  array('setting_id' => '2146','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '2145','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '2144','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '2143','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '2142','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '1032','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '1033','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '1022','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1023','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1021','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '2148','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '1019','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1017','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2149','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '2150','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '1269','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '2151','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '1271','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '1273','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '2152','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '2153','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '2154','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '2155','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '1007','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '2156','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1004','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2157','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '2158','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
  array('setting_id' => '1002','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '999','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '1000','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '1001','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '998','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '1288','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '1289','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '995','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-bag','serialized' => '0'),
  array('setting_id' => '2159','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '0','serialized' => '0'),
  array('setting_id' => '993','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '1','serialized' => '0'),
  array('setting_id' => '1102','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '1','serialized' => '0'),
  array('setting_id' => '2161','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '0','serialized' => '0'),
  array('setting_id' => '991','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '2160','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '988','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '2162','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '986','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '985','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '984','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '983','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '981','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '982','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '979','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '980','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#222222','serialized' => '0'),
  array('setting_id' => '978','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '977','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '976','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#ee4684','serialized' => '0'),
  array('setting_id' => '975','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '974','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '973','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
  array('setting_id' => '972','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#ee4684','serialized' => '0'),
  array('setting_id' => '971','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '970','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '967','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top center','serialized' => '0'),
  array('setting_id' => '1343','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => 'catalog/basel-demo/demo3/title-bg.jpg','serialized' => '0'),
  array('setting_id' => '969','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '966','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'cover','serialized' => '0'),
  array('setting_id' => '965','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '964','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'fixed','serialized' => '0'),
  array('setting_id' => '963','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#ee4684','serialized' => '0'),
  array('setting_id' => '961','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '962','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '960','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '959','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '958','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '954','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '955','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '956','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#d62e6c','serialized' => '0'),
  array('setting_id' => '957','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#212121','serialized' => '0'),
  array('setting_id' => '953','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '950','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '951','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#ee4684','serialized' => '0'),

  array('setting_id' => '952','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '949','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '2163','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '947','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#cccccc','serialized' => '0'),
  array('setting_id' => '946','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '944','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
  array('setting_id' => '945','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '943','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '942','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '941','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '940','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '939','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '938','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '937','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '936','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '935','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '932','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '933','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '934','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
  array('setting_id' => '923','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '924','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '925','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '926','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '927','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '928','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '931','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '930','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '929','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '922','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '921','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '920','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '919','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '918','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '917','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '916','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '16px','serialized' => '0'),
  array('setting_id' => '915','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '914','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0.75px','serialized' => '0'),
  array('setting_id' => '913','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '912','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '911','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '910','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '909','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '908','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '906','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '907','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '905','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '1582','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1583','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '.header-main .sign-in:after {
opacity: 0.18;
}
.widget .widget-title .main-title {
font-weight:700;
}
','serialized' => '0'),
  array('setting_id' => '2165','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '2164','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1386','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '1385','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '898','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
  array('setting_id' => '897','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '1272','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1'),
  array('setting_id' => '2134','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '0','serialized' => '0'),
  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
  array('setting_id' => '1063','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '1064','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-left','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '64','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '64','serialized' => '0'),  array('setting_id' => '1066','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '1067','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '90','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '1068','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '2133','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '0','serialized' => '0'),
  array('setting_id' => '1070','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header1','serialized' => '0'),
  array('setting_id' => '1071','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '1485','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => 'nav-tabs-lg text-center','serialized' => '0'),
  array('setting_id' => '1072','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '0','serialized' => '0'),
  array('setting_id' => '1074','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0')
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