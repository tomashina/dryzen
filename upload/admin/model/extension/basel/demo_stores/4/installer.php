<?php
class ModelExtensionBaselDemoStores4Installer extends Model {

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
	  array('extension_id' => '56','type' => 'module','code' => 'basel_instagram'),
  array('extension_id' => '55','type' => 'module','code' => 'category'),
  array('extension_id' => '53','type' => 'module','code' => 'basel_content'),
  array('extension_id' => '54','type' => 'module','code' => 'account'),
  array('extension_id' => '52','type' => 'module','code' => 'basel_layerslider'),
  array('extension_id' => '51','type' => 'module','code' => 'basel_megamenu'),
  array('extension_id' => '50','type' => 'module','code' => 'basel_products'),
  array('extension_id' => '57','type' => 'module','code' => 'basel_categories')
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
	
	// `basel`.`oc_module`
$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"1140","height":"476","minheight":"180","fullwidth":"1","margin_bottom":"35px","slide_transition":"fade","speed":"10","loop":"0","nav_buttons":"0","nav_bullets":"1","nav_timer_bar":"0","sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"0","bg_color":"#f9f9f9","link":"","link_new_window":"0","thumb_image":"","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo4\\/slideshow\\/bg-shape-grey.png","3":"catalog\\/basel-demo\\/demo4\\/slideshow\\/bg-shape-grey.png","2":"catalog\\/basel-demo\\/demo4\\/slideshow\\/bg-shape-grey.png"},"left":{"1":"625","3":"625","2":"625"},"top":{"1":"216","3":"216","2":"216"},"minheight":"0","transitionin":"right(long)","easingin":"easeInOutQuart","durationin":"3000","transitionout":"right(long)","easingout":"easeInOutQuart","durationout":"1800","sort_order":"1","p_index":"0","start":"600","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo4\\/slideshow\\/phone-back.png","3":"catalog\\/basel-demo\\/demo4\\/slideshow\\/phone-back.png","2":"catalog\\/basel-demo\\/demo4\\/slideshow\\/phone-back.png"},"left":{"1":"719","3":"719","2":"719"},"top":{"1":"295","3":"295","2":"295"},"minheight":"160","transitionin":"bottom(short)","easingin":"easeOutBack","durationin":"800","transitionout":"bottom(short)","easingout":"easeInOutQuart","durationout":"800","sort_order":"2","p_index":"0","start":"1200","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo4\\/slideshow\\/phone-front.png","3":"catalog\\/basel-demo\\/demo4\\/slideshow\\/phone-front.png","2":"catalog\\/basel-demo\\/demo4\\/slideshow\\/phone-front.png"},"left":{"1":"872","3":"872","2":"872"},"top":{"1":"286","3":"286","2":"286"},"minheight":"160","transitionin":"bottom(short)","easingin":"easeOutBack","durationin":"800","transitionout":"bottom(short)","easingout":"easeInOutQuart","durationout":"800","sort_order":"3","p_index":"1","start":"1400","end":"10000"},{"type":"text","description":{"1":"MEIZU ALWAYS HELP YOU","3":"MEIZU ALWAYS HELP YOU","2":"MEIZU ALWAYS HELP YOU"},"left":{"1":"399","3":"399","2":"399"},"top":{"1":"117","3":"117","2":"117"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"14px","color":"#0f8db3","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"linear","durationout":"500","sort_order":"4","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"MEIZU MX6","3":"MEIZU MX6","2":"MEIZU MX6"},"left":{"1":"353","3":"353","2":"353"},"top":{"1":"161","3":"161","2":"161"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"48px","color":"#111111","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"linear","durationout":"500","sort_order":"5","p_index":"0","start":"600","end":"10000"},{"type":"text","description":{"1":"Vel risus tincidunt dis Aenean ullamc orper blandit&lt;br&gt;\\r\\nlacinia parturient contum nisi const euisdmod","3":"Vel risus tincidunt dis Aenean ullamc orper blandit&lt;br&gt;\\r\\nlacinia parturient contum nisi const euisdmod","2":"Vel risus tincidunt dis Aenean ullamc orper blandit&lt;br&gt;\\r\\nlacinia parturient contum nisi const euisdmod"},"left":{"1":"335","3":"335","2":"335"},"top":{"1":"217","3":"217","2":"217"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"14px","color":"#777777","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"500","transitionout":"top(short)","easingout":"linear","durationout":"500","sort_order":"6","p_index":"0","start":"700","end":"10000"},{"type":"text","description":{"1":"$399.00","3":"$399.00","2":"$399.00"},"left":{"1":"348","3":"348","2":"348"},"top":{"1":"277","3":"277","2":"277"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"68px","color":"#dddddd","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"linear","durationout":"500","sort_order":"7","p_index":"0","start":"800","end":"10000"},{"type":"button","description":{"1":"&lt;b&gt;Read more&lt;\\/b&gt;","3":"&lt;b&gt;Read more&lt;\\/b&gt;","2":"&lt;b&gt;Read more&lt;\\/b&gt;"},"left":{"1":"439","3":"439","2":"439"},"top":{"1":"350","3":"350","2":"350"},"button_class":"btn btn-contrast-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutBack","durationin":"1000","transitionout":"bottom(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"8","p_index":"0","start":"1000","end":"10000"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"0","bg_color":"#f9f9f9","link":"","link_new_window":"0","thumb_image":"","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo4\\/slideshow\\/bg-shape-orange.png","3":"catalog\\/basel-demo\\/demo4\\/slideshow\\/bg-shape-orange.png","2":"catalog\\/basel-demo\\/demo4\\/slideshow\\/bg-shape-orange.png"},"left":{"1":"625","3":"625","2":"625"},"top":{"1":"216","3":"216","2":"216"},"minheight":"0","transitionin":"right(long)","easingin":"easeInOutQuart","durationin":"1800","transitionout":"right(long)","easingout":"easeInOutQuart","durationout":"1800","sort_order":"1","p_index":"1","start":"600","end":"10000"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo4\\/slideshow\\/watch.png","3":"catalog\\/basel-demo\\/demo4\\/slideshow\\/watch.png","2":"catalog\\/basel-demo\\/demo4\\/slideshow\\/watch.png"},"left":{"1":"706","3":"706","2":"706"},"top":{"1":"254","3":"254","2":"254"},"minheight":"160","transitionin":"bottom(short)","easingin":"easeOutBack","durationin":"800","transitionout":"bottom(short)","easingout":"easeInOutQuart","durationout":"800","sort_order":"2","p_index":"0","start":"1400","end":"10000"},{"type":"text","description":{"1":"APPLE ALWAYS HELP YOU","3":"APPLE ALWAYS HELP YOU","2":"APPLE ALWAYS HELP YOU"},"left":{"1":"405","3":"405","2":"405"},"top":{"1":"117","3":"117","2":"117"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"14px","color":"#0f8db3","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"linear","durationout":"500","sort_order":"3","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"APPLE WATCH","3":"APPLE WATCH","2":"APPLE WATCH"},"left":{"1":"332","3":"332","2":"332"},"top":{"1":"161","3":"161","2":"161"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"48px","color":"#111111","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"linear","durationout":"500","sort_order":"4","p_index":"0","start":"600","end":"10000"},{"type":"text","description":{"1":"Vel risus tincidunt dis Aenean ullamc orper blandit&lt;br&gt;\\r\\nlacinia parturient contum nisi const euisdmod","3":"Vel risus tincidunt dis Aenean ullamc orper blandit&lt;br&gt;\\r\\nlacinia parturient contum nisi const euisdmod","2":"Vel risus tincidunt dis Aenean ullamc orper blandit&lt;br&gt;\\r\\nlacinia parturient contum nisi const euisdmod"},"left":{"1":"345","3":"345","2":"345"},"top":{"1":"217","3":"217","2":"217"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"14px","color":"#777777","bg":"","padding":"0px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"500","transitionout":"top(short)","easingout":"linear","durationout":"500","sort_order":"5","p_index":"0","start":"700","end":"10000"},{"type":"text","description":{"1":"$799.00","3":"$799.00","2":"$799.00"},"left":{"1":"360","3":"360","2":"360"},"top":{"1":"277","3":"277","2":"277"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"68px","color":"#dddddd","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"","transitionin":"top(short)","easingin":"easeInOutQuart","durationin":"800","transitionout":"top(short)","easingout":"linear","durationout":"500","sort_order":"6","p_index":"0","start":"800","end":"10000"},{"type":"button","description":{"1":"&lt;b&gt;Read more&lt;\\/b&gt;","3":"&lt;b&gt;Read more&lt;\\/b&gt;","2":"&lt;b&gt;Read more&lt;\\/b&gt;"},"left":{"1":"450","3":"450","2":"450"},"top":{"1":"350","3":"350","2":"350"},"button_class":"btn btn-contrast-outline","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutBack","durationin":"1000","transitionout":"bottom(short)","easingout":"easeInOutQuart","durationout":"500","sort_order":"7","p_index":"0","start":"1400","end":"10000"}]}}}'),
  array('module_id' => '41','name' => 'Home Page Product Tabs','code' => 'basel_products','setting' => '{"name":"Home Page Product Tabs","status":"1","contrast":"0","use_title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1","2","3"]},"tabstyle":"0","limit":"6","image_width":"262","image_height":"334","columns":"3","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"1","margin":"35px"}'),
  array('module_id' => '43','name' => '2 x Banner Home Page','code' => 'basel_content','setting' => '{"save":"stay","name":"2 x Banner Home Page","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"40","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo4\\/banner\\/banner1.jpg","data5":"","data7":"vertical-top text-left","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;p class=&quot;primary-color&quot; style=&quot;margin-bottom:3px;&quot;&gt;&lt;b&gt;\\r\\nTRUE 360 SOUND\\r\\n&lt;\\/b&gt;&lt;\\/p&gt;\\r\\n&lt;h2 style=&quot;margin-bottom:9px;color:#111111;&quot;&gt;&lt;b&gt;\\r\\nPOWERFUL&lt;br&gt;\\r\\nSOUND\\r\\n&lt;\\/b&gt;&lt;\\/h2&gt;\\r\\n&lt;p class=&quot;hidden-xs hidden-sm&quot; style=&quot;color:#111111&quot;&gt;\\r\\nLorem ipsum dolor &lt;br&gt;\\r\\nsit amet consectetur.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;btn btn-contrast btn-tiny&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;p class=&quot;primary-color&quot; style=&quot;margin-bottom:3px;&quot;&gt;&lt;b&gt;\\r\\nTRUE 360 SOUND\\r\\n&lt;\\/b&gt;&lt;\\/p&gt;\\r\\n&lt;h2 style=&quot;margin-bottom:9px;color:#111111;&quot;&gt;&lt;b&gt;\\r\\nPOWERFUL&lt;br&gt;\\r\\nSOUND\\r\\n&lt;\\/b&gt;&lt;\\/h2&gt;\\r\\n&lt;p class=&quot;hidden-xs hidden-sm&quot; style=&quot;color:#111111&quot;&gt;\\r\\nLorem ipsum dolor &lt;br&gt;\\r\\nsit amet consectetur.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;btn btn-contrast btn-tiny&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;p class=&quot;primary-color&quot; style=&quot;margin-bottom:3px;&quot;&gt;&lt;b&gt;\\r\\nTRUE 360 SOUND\\r\\n&lt;\\/b&gt;&lt;\\/p&gt;\\r\\n&lt;h2 style=&quot;margin-bottom:9px;color:#111111;&quot;&gt;&lt;b&gt;\\r\\nPOWERFUL&lt;br&gt;\\r\\nSOUND\\r\\n&lt;\\/b&gt;&lt;\\/h2&gt;\\r\\n&lt;p class=&quot;hidden-xs hidden-sm&quot; style=&quot;color:#111111&quot;&gt;\\r\\nLorem ipsum dolor &lt;br&gt;\\r\\nsit amet consectetur.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;btn btn-contrast btn-tiny&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;"}},"2":{"w":"col-sm-6","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo4\\/banner\\/banner2.jpg","data5":"","data7":"vertical-top text-left","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;p class=&quot;primary-color&quot; style=&quot;margin-bottom:3px;&quot;&gt;&lt;b&gt;\\r\\nPOWERFUL CAMERA\\r\\n&lt;\\/b&gt;&lt;\\/p&gt;\\r\\n&lt;h2 style=&quot;margin-bottom:9px;color:#111111;&quot;&gt;&lt;b&gt;\\r\\nACCURATE&lt;br&gt;\\r\\nSHOTS\\r\\n&lt;\\/b&gt;&lt;\\/h2&gt;\\r\\n&lt;p class=&quot;hidden-xs hidden-sm&quot; style=&quot;color:#111111&quot;&gt;\\r\\nLorem ipsum dolor &lt;br&gt;\\r\\nsit amet consectetur.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;btn btn-contrast btn-tiny&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;p class=&quot;primary-color&quot; style=&quot;margin-bottom:3px;&quot;&gt;&lt;b&gt;\\r\\nPOWERFUL CAMERA\\r\\n&lt;\\/b&gt;&lt;\\/p&gt;\\r\\n&lt;h2 style=&quot;margin-bottom:9px;color:#111111;&quot;&gt;&lt;b&gt;\\r\\nACCURATE&lt;br&gt;\\r\\nSHOTS\\r\\n&lt;\\/b&gt;&lt;\\/h2&gt;\\r\\n&lt;p class=&quot;hidden-xs hidden-sm&quot; style=&quot;color:#111111&quot;&gt;\\r\\nLorem ipsum dolor &lt;br&gt;\\r\\nsit amet consectetur.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;btn btn-contrast btn-tiny&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;p class=&quot;primary-color&quot; style=&quot;margin-bottom:3px;&quot;&gt;&lt;b&gt;\\r\\nPOWERFUL CAMERA\\r\\n&lt;\\/b&gt;&lt;\\/p&gt;\\r\\n&lt;h2 style=&quot;margin-bottom:9px;color:#111111;&quot;&gt;&lt;b&gt;\\r\\nACCURATE&lt;br&gt;\\r\\nSHOTS\\r\\n&lt;\\/b&gt;&lt;\\/h2&gt;\\r\\n&lt;p class=&quot;hidden-xs hidden-sm&quot; style=&quot;color:#111111&quot;&gt;\\r\\nLorem ipsum dolor &lt;br&gt;\\r\\nsit amet consectetur.\\r\\n&lt;\\/p&gt;\\r\\n&lt;a class=&quot;btn btn-contrast btn-tiny&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;"}}}}'),
  array('module_id' => '46','name' => 'Column Product List','code' => 'basel_products','setting' => '{"name":"Column Product List","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Popular Items","3":"Popular Items","2":"Popular Items"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"0","limit":"4","image_width":"60","image_height":"76","columns":"list","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '48','name' => 'Instagram Feed (Column Style)','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed (Column Style)","status":"1","full_width":"0","use_title":"1","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"@Instagram","3":"@Instagram","2":"@Instagram"},"title_b":{"1":"","3":"","2":""},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"9","resolution":"0","columns":"3","columns_md":"3","columns_sm":"3","padding":"5","use_margin":"0","margin":"60px"}'),
  array('module_id' => '49','name' => 'Banner Column Style','code' => 'basel_content','setting' => '{"save":"stay","name":"Banner Column Style","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"1","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo4\\/banner\\/banner3.jpg","data5":"","data7":"vertical-top text-left","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;div class=&quot;dark&quot;&gt;\\r\\n&lt;h2 style=&quot;margin-bottom:9px&quot;&gt;\\r\\n&lt;b&gt;NOW&lt;\\/b&gt;&lt;br&gt;\\r\\n&lt;b&gt;WIRELESS&lt;\\/b&gt;\\r\\n&lt;\\/h2&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Designed for sound.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Shop Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#2389e0;&quot;&gt;LABEL&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:9px&quot;&gt;&lt;b&gt;HEADING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Lorem ipsum dolor &lt;br&gt;sit amet consectetur.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Link Text&lt;\\/a&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#2389e0;&quot;&gt;LABEL&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:9px&quot;&gt;&lt;b&gt;HEADING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Lorem ipsum dolor &lt;br&gt;sit amet consectetur.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Link Text&lt;\\/a&gt;"}}}}'),
  array('module_id' => '53','name' => 'Home Page Best Seller','code' => 'basel_products','setting' => '{"name":"Home Page Best Seller","status":"1","contrast":"1","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"BEST SELLERS","3":"BEST SELLERS","2":"BEST SELLERS"},"title_b":{"1":"&lt;span style=&quot;font-size:16px;&quot;&gt;\\r\\nPenatibus tristique parturient metus ostra etiam primis nibh ante risus fames parturient in a odio adipiscing nulla.\\r\\n&lt;\\/span&gt;","3":"Penatibus tristique parturient metus ostra etiam primis nibh ante risus fames parturient in a odio adipiscing nulla.","2":"Penatibus tristique parturient metus ostra etiam primis nibh ante risus fames parturient in a odio adipiscing nulla."},"selected_tabs":{"tabs":["1"]},"tabstyle":"0","limit":"3","image_width":"360","image_height":"459","columns":"3","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"1","link_title":{"1":"View All Products","3":"View All Products","2":"View All Products"},"link_href":"#","use_margin":"1","margin":"80px"}'),
  array('module_id' => '52','name' => 'Info Block With Video','code' => 'basel_content','setting' => '{"save":"stay","name":"Info Block With Video","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"0","block_bg":"1","bg_color":"#000000","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"padding:50px 30px 20px;","nm":"0","eh":"1"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/image-tall-dark.png","data5":"","data7":"vertical-bottom text-center","data1":{"1":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;h5 style=&quot;letter-spacing:3px;color:#009fdf;&quot;&gt;INTRODUCING&lt;\\/h5&gt;\\r\\n&lt;h3 style=&quot;font-size:44px;margin-bottom:8px;&quot;&gt;&lt;b&gt;Liquid X2&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;letter-spacing:1px;margin-bottom:20px;&quot;&gt;THIS IS YOUR LIFE.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;&quot; class=&quot;btn btn-outline-light btn-tiny&quot;&gt;Buy Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;h5 style=&quot;letter-spacing:3px;color:#009fdf;&quot;&gt;INTRODUCING&lt;\\/h5&gt;\\r\\n&lt;h3 style=&quot;font-size:44px;margin-bottom:8px;&quot;&gt;&lt;b&gt;Liquid X2&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;letter-spacing:1px;margin-bottom:20px;&quot;&gt;THIS IS YOUR LIFE.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;&quot; class=&quot;btn btn-outline-light btn-tiny&quot;&gt;Buy Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;light&quot;&gt;\\r\\n&lt;h5 style=&quot;letter-spacing:3px;color:#009fdf;&quot;&gt;INTRODUCING&lt;\\/h5&gt;\\r\\n&lt;h3 style=&quot;font-size:44px;margin-bottom:8px;&quot;&gt;&lt;b&gt;Liquid X2&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p style=&quot;letter-spacing:1px;margin-bottom:20px;&quot;&gt;THIS IS YOUR LIFE.&lt;\\/p&gt;\\r\\n&lt;a href=&quot;&quot; class=&quot;btn btn-outline-light btn-tiny&quot;&gt;Buy Now&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"col-sm-8","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-middle text-center","data1":{"1":"&lt;div class=&quot;video-wrapper&quot;&gt;\\r\\n&lt;iframe width=&quot;640&quot; height=&quot;360&quot; src=&quot;https:\\/\\/www.youtube.com\\/embed\\/BX0j9H_RsvQ?rel=0&amp;amp;controls=0&amp;amp;showinfo=0&quot; frameborder=&quot;0&quot; allowfullscreen&gt;&lt;\\/iframe&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;video-wrapper&quot;&gt;\\r\\n&lt;iframe width=&quot;640&quot; height=&quot;360&quot; src=&quot;https:\\/\\/www.youtube.com\\/embed\\/BX0j9H_RsvQ?rel=0&amp;amp;controls=0&amp;amp;showinfo=0&quot; frameborder=&quot;0&quot; allowfullscreen&gt;&lt;\\/iframe&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;video-wrapper&quot;&gt;\\r\\n&lt;iframe width=&quot;640&quot; height=&quot;360&quot; src=&quot;https:\\/\\/www.youtube.com\\/embed\\/BX0j9H_RsvQ?rel=0&amp;amp;controls=0&amp;amp;showinfo=0&quot; frameborder=&quot;0&quot; allowfullscreen&gt;&lt;\\/iframe&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '54','name' => 'Category Wall','code' => 'basel_categories','setting' => '{"name":"Category Wall","status":"1","contrast":"1","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"ALL CATEGORIES","3":"ALL CATEGORIES","2":"ALL CATEGORIES"},"title_b":{"1":"&lt;span style=&quot;font-size:16px;&quot;&gt;\\r\\nPenatibus tristique parturient metus ostra etiam primis nibh ante risus fames parturient in a odio adipiscing nulla.\\r\\n&lt;\\/span&gt;","3":"&lt;span style=&quot;font-size:16px;&quot;&gt;\\r\\nPenatibus tristique parturient metus ostra etiam primis nibh ante risus fames parturient in a odio adipiscing nulla.\\r\\n&lt;\\/span&gt;","2":"&lt;span style=&quot;font-size:16px;&quot;&gt;\\r\\nPenatibus tristique parturient metus ostra etiam primis nibh ante risus fames parturient in a odio adipiscing nulla.\\r\\n&lt;\\/span&gt;"},"category":["'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'"],"image_width":"131","image_height":"167","subs":"1","limit":"6","count":"0","columns":"3","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","use_margin":"1","margin":"-50px"}')
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
	  array('layout_module_id' => '325','layout_id' => '1','code' => 'basel_products.53','position' => 'bottom','sort_order' => '1'),
	  array('layout_module_id' => '202','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
	  array('layout_module_id' => '205','layout_id' => '14','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
	  array('layout_module_id' => '203','layout_id' => '3','code' => 'basel_instagram.48','position' => 'column_left','sort_order' => '3'),
	  array('layout_module_id' => '206','layout_id' => '14','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3'),
	  array('layout_module_id' => '324','layout_id' => '1','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
	  array('layout_module_id' => '323','layout_id' => '1','code' => 'basel_content.49','position' => 'column_right','sort_order' => '1'),
	  array('layout_module_id' => '321','layout_id' => '1','code' => 'basel_content.43','position' => 'top','sort_order' => '2'),
	  array('layout_module_id' => '322','layout_id' => '1','code' => 'basel_products.41','position' => 'content_top','sort_order' => '1'),
	  array('layout_module_id' => '320','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'top','sort_order' => '1'),
	  array('layout_module_id' => '326','layout_id' => '1','code' => 'basel_content.52','position' => 'bottom','sort_order' => '2'),
	  array('layout_module_id' => '327','layout_id' => '1','code' => 'basel_categories.54','position' => 'bottom','sort_order' => '3')
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
	---------------- ADD SETTINGS ----------------
	--------------------------------------------*/
	public function addSampleSettings() {
	
	$this->db->query("DELETE FROM `".DB_PREFIX."setting` WHERE `code` = 'basel'");
		
	$oc_setting = array(
array('setting_id' => '4529','store_id' => '0','code' => 'basel','key' => 'top_line_height','value' => '41','serialized' => '0'),
  array('setting_id' => '925','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '893','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
  array('setting_id' => '1272','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1'),
  array('setting_id' => '892','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '884','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '885','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '994','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1706','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '.grid .single-product .product-name {
font-weight:700;
}','serialized' => '0'),
  array('setting_id' => '1867','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1868','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '1386','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '1385','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '872','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '873','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '874','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '16px','serialized' => '0'),
  array('setting_id' => '875','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '876','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '877','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0.75px','serialized' => '0'),
  array('setting_id' => '878','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '879','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '880','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '881','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '882','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '883','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '867','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '871','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '870','store_id' => '0','code' => 'basel','key' => 'widget_lg_','value' => '0px','serialized' => '0'),
  array('setting_id' => '869','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '36px','serialized' => '0'),
  array('setting_id' => '868','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '865','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '866','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '864','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '28px','serialized' => '0'),
  array('setting_id' => '861','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '862','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '863','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '860','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '859','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '28px','serialized' => '0'),
  array('setting_id' => '858','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '857','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '856','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
  array('setting_id' => '855','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '854','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '853','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '851','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '852','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '850','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '848','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '849','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '847','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '846','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '845','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
  array('setting_id' => '844','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '843','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#cccccc','serialized' => '0'),
  array('setting_id' => '1576','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '1','serialized' => '0'),
  array('setting_id' => '841','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '840','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '837','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '839','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#0f8db3','serialized' => '0'),
  array('setting_id' => '838','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '836','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '835','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '834','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#fbbc34','serialized' => '0'),
  array('setting_id' => '833','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#0f8db3','serialized' => '0'),
  array('setting_id' => '832','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#0f8db3','serialized' => '0'),
  array('setting_id' => '831','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '830','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '823','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '828','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#0f8db3','serialized' => '0'),
  array('setting_id' => '829','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '827','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#0f8db3','serialized' => '0'),
  array('setting_id' => '826','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '825','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '824','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '821','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#f9f9f9','serialized' => '0'),
  array('setting_id' => '1866','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '820','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '817','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'light-search','serialized' => '0'),
  array('setting_id' => '819','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#0c6d8a','serialized' => '0'),
  array('setting_id' => '818','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#d41212','serialized' => '0'),
  array('setting_id' => '816','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '814','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#0f8db3','serialized' => '0'),
  array('setting_id' => '815','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#0f8db3','serialized' => '0'),
  array('setting_id' => '813','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '812','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#0f8db3','serialized' => '0'),
  array('setting_id' => '810','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#0c6d8a','serialized' => '0'),
  array('setting_id' => '811','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '809','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '808','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '807','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '805','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '806','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '804','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '1865','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '802','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '800','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '2','serialized' => '0'),
  array('setting_id' => '801','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '1','serialized' => '0'),
  array('setting_id' => '799','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '1863','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '1864','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '0','serialized' => '0'),
  array('setting_id' => '1862','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '0','serialized' => '0'),
  array('setting_id' => '1289','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '794','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-shoppingbag','serialized' => '0'),
  array('setting_id' => '1288','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '792','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '791','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '790','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '789','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '788','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '1860','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1861','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
  array('setting_id' => '1852','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '1854','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1855','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '1857','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '782','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '1856','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '1858','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '1859','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '774','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1271','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '1853','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '1269','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '765','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '1849','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '767','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '768','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '769','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '1850','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '771','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1851','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '1273','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '764','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '2','serialized' => '0'),
  array('setting_id' => '1848','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '1847','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '1846','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '1845','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '1844','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '758','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '757','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '756','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1843','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '754','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '1842','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '752','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '1841','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '750','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '1','serialized' => '0'),
  array('setting_id' => '1840','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '748','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '747','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '746','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '1290','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '744','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-left','serialized' => '0'),
  array('setting_id' => '743','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '1839','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '0','serialized' => '0'),
  array('setting_id' => '741','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '740','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '739','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '738','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '1225','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
  array('setting_id' => '732','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '733','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
  array('setting_id' => '734','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '35','serialized' => '0'),
  array('setting_id' => '1838','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '736','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '737','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '730','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '729','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
  array('setting_id' => '1837','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '0','serialized' => '0'),
  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
  array('setting_id' => '727','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '726','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-center','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '64','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '64','serialized' => '0'),  array('setting_id' => '724','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'boxed','serialized' => '0'),
  array('setting_id' => '723','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '115','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '722','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'boxed','serialized' => '0'),
  array('setting_id' => '721','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '1','serialized' => '0'),
  array('setting_id' => '720','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header6','serialized' => '0'),
  array('setting_id' => '719','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '718','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '717','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '716','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
  array('setting_id' => '1485','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => 'nav-tabs-lg text-center','serialized' => '0'),
  array('setting_id' => '1577','store_id' => '0','code' => 'basel','key' => 'basel_fonts','value' => '{"1":{"import":"Karla:400,400i,700,700i","name":"\'Karla\', sans-serif"}}','serialized' => '1')
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